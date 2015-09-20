---
id: 2012-12-30-openshift-cron-and-naked-domains
author: matthew
title: 'OpenShift, Cron, and Naked Domains'
draft: false
public: true
created: '2012-12-30T09:52:00-06:00'
updated: '2012-12-30T09:52:00-06:00'
tags:
    - php
    - oop
    - cloud
---
As an experiment, I migrated my website over to
[OpenShift](http://openshift.redhat.com) yesterday. I've been hosting a
pastebin there already, and have found the service to be both straightforward
and flexible; it was time to put it to a more thorough test.

In the process, I ran into a number of interesting issues, some of which took
quite some time to resolve; this post is both to help inform other potential
users of the service, as well as act as a reminder to myself.

<!--- EXTENDED -->

Cron
----

OpenShift offers a [Cron](http://en.wikipedia.org/wiki/Cron) cartridge, which I
was excited to try out.<sup>[1](#f1)</sup>

The basics are quite easy. In your repository's `.openshift` directory is a
`cron` subdirectory, further divided into `minutely`, `hourly`, `daily`,
`weekly`, and `monthly` subdirectories. You drop a script you want to run into
one of these directories, and push your changes upstream.

The problem is: what if I want a job to run at a specific time daily? or on the
quarter hour? or on a specific day of the week?

As it turns out, you can manage all of the above, just not quite as succinctly
as you would in a normal crontab. Here, for example, is a script that I run at
5AM daily; I placed it in the `hourly` directory so that it can test more
frequently:

```bash
#!/bin/bash
if [ `date +%H` == "05" ]
then
    (
        export PHP=/usr/local/zend/bin/php ;
        cd $OPENSHIFT_REPO_DIR ; 
        $PHP public/index.php phlycomic fetch all ; 
        $PHP public/index.php phlysimplepage cache clear --page=pages/comics 
    )
fi
```

And here's one that runs on the quarter-hour, placed in the `minutely`
directory:

```bash
#!/bin/bash
MINUTES=`date +%M`

for i in "00" "15" "30" "45";do
    if [ "$MINUTES" == "$i" ];then
        (
            export PHP=/usr/local/zend/bin/php ;
            cd $OPENSHIFT_REPO_DIR ;
            $PHP public/index.php githubfeed fetch 
        )
    fi
done
```

The point is that if you need more specificity, push the script into the next
more specific directory, and test against the time of execution.

Naked Domains
-------------

Naked domains are domains without a preceding subdomain. In my case, this means
"mwop.net", vs. "www.mwop.net".

The problem that cloud hosting presents is that the IP address on which you are
hosted can change at any time, for a variety of reasons. As such, you typically
cannot use DNS A records to point to your domain; the recommendation is to use
a CNAME record that points the domain to a "virtual" domain registered with
your cloud hosting provider.

However, most domain registrars and DNS providers do not let you do this for a
naked domain, particularly if you also have MX or other records associated with
that naked domain.

Some registrars will allow you to forward the A record to a subdomain. I tried
this, but had limited success; I personally found that I ended up in an
infinite loop situation when doing the DNS lookup.

Another solution is to have a redirect in place for your naked domain to the
subdomain, which can then be a CNAME record. Typically, this would require you
have a web server under your control with a fixed IP that then simply redirects
to the subdomain. Fortunately, there's an easier solution:
[wwwizer](http://wwwizer.com/naked-domain-redirect). You simply point your
naked domain A record to the wwwizer IP address, and they will do a redirect to
your `www` subdomain.

I implemented wwwizer on my domain (which is why you'll see "www.mwop.net" in
your location bar), and it's been working flawlessly since doing so.

#### Private repositories

I keep my critical site settings in a private repository, which allows me to
version them while keeping the credentials they hold out of the public eye.
This means, however, that I need to use
[GitHub deploy keys](https://help.github.com/articles/managing-deploy-keys) on
my server in order to retrieve changes.

This was simple enough: I created an `ssh` subdirectory in my
`$OPENSHIFT_DATA_DIR` directory, and generated a new SSH keypair.

The problem was telling SSH to *use* this key when fetching changes.

The solution is to use a combination of `ssh-agent` and `ssh-add`, and it looks
something like this:

```bash
#!/bin/bash
ssh-agent `ssh-add $OPENSHIFT_DATA_DIR/ssh/github-key && (
    cd $OPENSHIFT_DATA_DIR/config ; 
    git fetch origin ; 
    git rebase origin/mwop.net.config
)`
```

After testing the above, I put this in a `pre_build` script in my OpenShift
configuration so that I can autoupdate my private configuration on each build.
However, I discovered a new problem: when a build is being done, the
`ssh-agent` is not available, which means the above cannot be executed. I'm
still trying to find a solution.

Fin
---

I'm pretty happy with the move. I don't have to do anything special to automate
deployment, and all my cronjobs and deployment scripts are now self-contained
in the repository, which makes my site more portable. While a few things could
use more documentation, all the pieces are there and discoverable with a small
amount of work.

I'll likely give some other PaaS providers a try in the future, but for the
moment, I'm quite happy with the functionality and flexibility of OpenShift.

#### Footnotes

- Zend Server's JobQueue can also be used as a cron replacement, but I was not
  keen on exposing the job functionality via HTTP.
