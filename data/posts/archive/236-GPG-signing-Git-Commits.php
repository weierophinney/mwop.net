<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('236-GPG-signing-Git-Commits');
$entry->setTitle('GPG-signing Git Commits');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1269448003);
$entry->setUpdated(1269514623);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'linux',
  1 => 'php',
));

$body =<<<'EOT'
<p>
    We're working on migrating <a href="http://framework.zend.com/">Zend
        Framework</a> to <a href="http://git-scm.org/">Git</a>. One issue we're
    trying to deal with is enforcing that commits come from <acronym
        title="Contributor License Agreement">CLA</acronym> signees.
</p>

<p>
    One possibility presented to us was the possibility of utilizing
    <acronym title="GNU Privacy Guard">GPG</acronym> signing of commit messages.
    Unfortunately, I was able to find little to no information on the 'net about
    how this might be done, so I started to experiment with some solutions.
</p>

<p>
    The approach I chose utilizes <a
        href="http://www.kernel.org/pub/software/scm/git/docs/githooks.html">git
        hooks</a>, specifically the <code>commit-msg</code> hook client-side,
    and the <code>pre-receive</code> hook server-side.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Client-side commit-msg hook</h2>

<p>
    The <code>commit-msg</code> hook receives a single argument, the path to the
    temporary file containing the commit message. This allows you to inspect it
    or modify it prior to completing the commit. Like all git hooks, a non-zero
    exit status will abort the commit.
</p>

<p>
    My <code>commit-msg</code> hook looks like the following:
</p>

<div class="example"><pre><code lang="bash">
#!/bin/sh
echo -n \&quot;GPG Signing message... \&quot;;
PASSPHRASE=$(git config --get hooks.gpg.passphrase)
if [ \&quot;\&quot; = \&quot;$PASSPHRASE\&quot; ];then
    echo \&quot;no passphrase found! Set it with git config --add hooks.gpg.passphrase &lt;passphrase&gt;\&quot;
    exit 1
fi
gpg --clearsign --yes --passphrase $PASSPHRASE -o $1.asc $1
mv $1.asc $1
echo \&quot;[DONE]\&quot;
</code></pre></div>

<p>
    This hook requires that you first add your GPG key's passphrase to your
    local git configuration, which can be done as follows:
</p>

<div class="example"><pre><code lang="bash">
% git config --add hooks.gpg.passphrase \&quot;mySecret\&quot;
</code></pre></div>

<p>
    Once this hook is in place, all commit messages are then clear-signed,
    leading to commit logs that look like the following:
</p>

<div class="example"><pre><code lang="bash">
commit f921f0defb18f8a5218d5c3346693dbb4179920e
Author: Matthew Weier O'Phinney &lt;somebody@example.com&gt;
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
</code></pre></div>

<h2>Server-side pre-receive hook</h2>

<p>
    The <code>pre-receive</code> hook is a lot less straight-forward. This hook
    receives input via <code>STDIN</code>. Each line consists of three items,
    separated by a single space:
</p>

<pre>
[previous commit's sha1] [new commit's sha1] [refspec]
</pre>

<p>
    Typically, only the new sha1 is of much use to us. Internally, git is
    actually keeping track of the new commit, even though it has not technically
    been accepted into the repository. This allows us to use tools such as
    <code>git show</code> to get information on the commit and act on that
    information.
</p>

<p>
    What I needed to do was inspect the commit message for a GPG-signed message;
    if none was found, reject the commit outright, but if one was present,
    validate it against my keyring, and abort if the signed message is invalid.
</p>

<p>
    I originally started by using <code>git show --pretty="format:%b"
        [sha1]</code> However, I discovered that git does something... odd... to
    commit messages. The first 50 characters or so are considered the commit's
    "subject" -- and any newlines found in the subject are silently stripped.
    This meant that I was getting, for my purposes, a truncated message that
    would never validate (as the GPG signature header was getting stripped);
    even including the subject in the format did not work, since the newlines
    within it were missing. The only way I found to get the full commit message
    was to use <code>git show --pretty=raw [sha1]</code>. This, however, gives
    me also the commit headers as well as the diff -- which means I have to
    parse the response.
</p>

<p>
    What follows is a PHP implementation I did that does exactly that: grabs the
    full message and redirects it to a temporary file, parses that file for the
    commit message, and then acts on it. 
</p>

<div class="example"><pre><code lang="php">
#!/usr/bin/php
&lt;?php
echo \&quot;Checking for GPG signature... \&quot;;
$fh     = fopen('php://stdin', 'r');
$tmpdir = sys_get_temp_dir();
while (!feof($fh)) {
    $line = fgets($fh);
    list($old, $new, $ref) = explode(' ', $line);

    // Create a tmp file with the commit log
    $logTmp   = tempnam($tmpdir, 'LOG_');
    $body     = shell_exec('git show --pretty=raw ' . $new . ' &gt; ' . $logTmp);

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
                $line = \&quot;\n\&quot;;
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
        echo \&quot;no GPG signature detected; commit aborted\n\&quot;;
        exit(1);
    }

    $verification = shell_exec('gpg --verify ' . $msgTmp . ' 2&gt;&amp;1');
    if (!preg_match('/Good signature/s', $verification)) {
        // Failed to verify signed message; report and abort
        unlink($logTmp);
        unlink($msgTmp);
        echo \&quot;invalid GPG signature; commit aborted\n\&quot;;
        exit(1);
    }

    unlink($logTmp);
    unlink($msgTmp);
}
echo \&quot;verified!\n\&quot;;
exit(0);
</code></pre></div>

<p>
    There are likely more elegant ways to accomplish this, including solutions
    in other languages. However, it works quite well.
</p>

<h2>Conclusions</h2>

<p>
    Git hooks are quite powerful, and delving into them has given me confidence
    that I can create some nice automation for the ZF git repository when we are
    ready to open it to the public.
</p>

<p>
    That said, I don't know if we'll actually use commit signing such as this,
    as it has a few drawbacks:
</p>

<ul>
    <li>The commit signing is not really cross-platform. This can likely be
    remedied, but it would require that people on different operating systems
    and using different tools (such as EGit, TortoiseGit, etc) develop and
    provide signing mechanisms for the client-side.</li>

    <li>It introduces complexity for those developing patches. If developers
    begin without having the <code>commit-msg</code> hook in place, they then
    have to create a new branch and a squashed commit afterwards in order to
    ensure the final patches can go into the canonical repository.</li>

    <li>The two reasons above kind of defeat the purpose of moving to a
    Distributed VCS in the first place -- which is to simplify development and
    make it more democratic.</li>
</ul>

<p>
    Regardless of whether or not we decide to use this technique, when
    researching the issue, I saw plenty of posts from people wanting to
    implement commit signing, but not sure how to accomplish it. Perhaps this
    post will serve as a starting point for many.
</p>
EOT;
$entry->setExtended($extended);

return $entry;