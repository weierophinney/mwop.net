---
id: 236-GPG-signing-Git-Commits
author: matthew
title: 'GPG-signing Git Commits'
draft: false
public: true
created: '2010-03-24T12:26:43-04:00'
updated: '2010-03-25T06:57:03-04:00'
tags:
    - linux
    - php
---
We're working on migrating [Zend Framework](http://framework.zend.com/) to
[Git](http://git-scm.org/). One issue we're trying to deal with is enforcing
that commits come from CLA signees.

One possibility presented to us was the possibility of utilizing GPG signing of
commit messages. Unfortunately, I was able to find little to no information on
the 'net about how this might be done, so I started to experiment with some
solutions.

The approach I chose utilizes [git hooks](http://www.kernel.org/pub/software/scm/git/docs/githooks.html),
specifically the `commit-msg` hook client-side, and the `pre-receive` hook
server-side.

<!--- EXTENDED -->

Client-side commit-msg hook
---------------------------

The `commit-msg` hook receives a single argument, the path to the temporary
file containing the commit message. This allows you to inspect it or modify it
prior to completing the commit. Like all git hooks, a non-zero exit status will
abort the commit.

My `commit-msg` hook looks like the following:

```bash
#!/bin/sh
echo -n "GPG Signing message... ";
PASSPHRASE=$(git config --get hooks.gpg.passphrase)
if [ "" = "$PASSPHRASE" ];then
    echo "no passphrase found! Set it with git config --add hooks.gpg.passphrase <passphrase>"
    exit 1
fi
gpg --clearsign --yes --passphrase $PASSPHRASE -o $1.asc $1
mv $1.asc $1
echo "[DONE]"
```

This hook requires that you first add your GPG key's passphrase to your local
git configuration, which can be done as follows:

```bash
$ git config --add hooks.gpg.passphrase "mySecret"
```

Once this hook is in place, all commit messages are then clear-signed, leading
to commit logs that look like the following:

```git
commit f921f0defb18f8a5218d5c3346693dbb4179920e
Author: Matthew Weier O'Phinney <somebody@example.com>
Date:   Tue Mar 23 17:18:35 2010 -0400

    -----BEGIN PGP SIGNED MESSAGE-----
    Hash: SHA1
    
    how now, brown cow
    -----BEGIN PGP SIGNATURE-----
    Version: GnuPG v1.4.9 (GNU/Linux)
    
    iEYEARECAAYFAkupMCsACgkQtUV5aSPtKdqERQCeN5taRATpB4/XJZiP9Vs5FVNY
    PcoAn0OZbIIcn7nC01yxp9tY7HbxVVFu
    =C/Ju
    -----END PGP SIGNATURE-----
```

Server-side pre-receive hook
----------------------------

The `pre-receive` hook is a lot less straight-forward. This hook receives input
via `STDIN`. Each line consists of three items, separated by a single space:

```
[previous commit's sha1] [new commit's sha1] [refspec]
```

Typically, only the new sha1 is of much use to us. Internally, git is actually
keeping track of the new commit, even though it has not technically been
accepted into the repository. This allows us to use tools such as `git show` to
get information on the commit and act on that information.

What I needed to do was inspect the commit message for a GPG-signed message; if
none was found, reject the commit outright, but if one was present, validate it
against my keyring, and abort if the signed message is invalid.

I originally started by using `git show --pretty="format:%b" [sha1]` However, I
discovered that git does something… odd… to commit messages. The first 50
characters or so are considered the commit's "subject" — and any newlines found
in the subject are silently stripped. This meant that I was getting, for my
purposes, a truncated message that would never validate (as the GPG signature
header was getting stripped); even including the subject in the format did not
work, since the newlines within it were missing. The only way I found to get
the full commit message was to use `git show --pretty=raw [sha1]`. This,
however, gives me also the commit headers as well as the diff — which means I
have to parse the response.

What follows is a PHP implementation I did that does exactly that: grabs the
full message and redirects it to a temporary file, parses that file for the
commit message, and then acts on it.

```php
#!/usr/bin/php
<?php
echo "Checking for GPG signature... ";
$fh     = fopen('php://stdin', 'r');
$tmpdir = sys_get_temp_dir();
while (!feof($fh)) {
    $line = fgets($fh);
    list($old, $new, $ref) = explode(' ', $line);

    // Create a tmp file with the commit log
    $logTmp   = tempnam($tmpdir, 'LOG_');
    $body     = shell_exec('git show --pretty=raw ' . $new . ' > ' . $logTmp);

    $msgTmp   = tempnam($tmpdir, 'MESSAGE_');

    // Scan the commit log for a commit message
    $log = fopen($logTmp, 'r');
    $msg = fopen($msgTmp, 'a');
    $signatureDetected = false;
    while (!feof($log)) {
        $line = fgets($log);
        if (preg_match('/^(commit(ter)?|tree|parent|author)\s/', $line)) {
            // Skip the commit log headers
            continue;
        }
        if (preg_match('/^diff\s/', $line)) {
            // Stop scanning when we reach the diff
            break;
        }
        if (preg_match('/^\s+-+BEGIN [A-Z]+ SIGNED MESSAGE/', $line)) {
            // We have a signed message, so start appending it 
            // to a separate tmp file
            $signatureDetected = true;
            $line = preg_replace('/^\s+/', '', $line);
            fwrite($msg, $line);
            continue;
        }
        if ($signatureDetected) {
            // If we have detected a signed message, continue appending lines to
            // it. Commit message lines are indented, so strip indentation.
            $line = preg_replace('/^\s+/', '', $line);
            if ('' === $line) {
                $line = "\n";"
            }
            fwrite($msg, $line);
        }
    }
    fclose($log);
    fclose($msg);

    if (!signatureDetected) {
        // No signed message detected; report and abort
        unlink($logTmp);
        unlink($msgTmp);
        echo "no GPG signature detected; commit aborted\n";
        exit(1);
    }

    $verification = shell_exec('gpg --verify ' . $msgTmp . ' 2>&1');
    if (!preg_match('/Good signature/s', $verification)) {
        // Failed to verify signed message; report and abort
        unlink($logTmp);
        unlink($msgTmp);
        echo "invalid GPG signature; commit aborted\n";
        exit(1);
    }

    unlink($logTmp);
    unlink($msgTmp);
}
echo "verified!\n";
exit(0);
```

There are likely more elegant ways to accomplish this, including solutions in
other languages. However, it works quite well.

Conclusions
-----------

Git hooks are quite powerful, and delving into them has given me confidence
that I can create some nice automation for the ZF git repository when we are
ready to open it to the public.

That said, I don't know if we'll actually use commit signing such as this, as
it has a few drawbacks:

- The commit signing is not really cross-platform. This can likely be remedied,
  but it would require that people on different operating systems and using
  different tools (such as EGit, TortoiseGit, etc) develop and provide signing
  mechanisms for the client-side.
- It introduces complexity for those developing patches. If developers begin
  without having the `commit-msg` hook in place, they then have to create a new
  branch and a squashed commit afterwards in order to ensure the final patches
  can go into the canonical repository.
- The two reasons above kind of defeat the purpose of moving to a Distributed
  VCS in the first place — which is to simplify development and make it more
  democratic.

Regardless of whether or not we decide to use this technique, when researching
the issue, I saw plenty of posts from people wanting to implement commit
signing, but not sure how to accomplish it. Perhaps this post will serve as a
starting point for many.
