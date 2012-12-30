<?php
use PhlyBlog\AuthorEntity;
use PhlyBlog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2012-12-30-openshift-cron-and-naked-domains');
$entry->setTitle('OpenShift, Cron, and Naked Domains');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2012-12-30 09:11', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2012-12-30 09:11', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array(
  'php',
  'oop',
  'cloud',
));

$body =<<<'EOT'

<p>
    As an experiment, I migrated my website over to <a 
    href="http://openshift.redhat.com">OpenShift</a> yesterday. I've been hosting
    a pastebin there already, and have found the service to be both straightforward
    and flexible; it was time to put it to a more thorough test.
</p>

<p>
    In the process, I ran into a number of interesting issues, some of which took quite
    some time to resolve; this post is both to help inform other potential users of the
    service, as well as act as a reminder to myself.
</p>

EOT;
$entry->setBody($body);

$extended=<<<'EOT'

<h2>Cron</h2>

<p>
    OpenShift offers a <a href="http://en.wikipedia.org/wiki/Cron">Cron</a> cartridge,
    which I was excited to try out.<sup><a href="#f1">1</a></sup>
</p>

<p>
    The basics are quite easy. In your repository's <code>.openshift</code> 
    directory is a <code>cron</code> subdirectory, further divided into 
    <code>minutely</code>, <code>hourly</code>, <code>daily</code>, <code>weekly</code>,
    and <code>monthly</code> subdirectories. You drop a script you want to run
    into one of these directories, and push your changes upstream.
</p>

<p>
    The problem is: what if I want a job to run at a specific time daily? or on 
    the quarter hour? or on a specific day of the week?
</p>

<p>
    As it turns out, you can manage all of the above, just not quite as succinctly as
    you would in a normal crontab. Here, for example, is a script that I run at 
    5AM daily; I placed it in the <code>hourly</code> directory so that it can test
    more frequently:
</p>

<div class="example"><pre><code language="bash">
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
</code></pre></div>

<p>
    And here's one that runs on the quarter-hour, placed in the <code>minutely</code>
    directory:
</p>

<div class="example"><pre><code language="bash">
#!/bin/bash
MINUTES=`date +%M`
INTERVALS=("00" "15" "30" "45")

for i in "00" "15" "30" "45";do
    if [ "$MINUTES" == "$i" ];then
        (
            export PHP=/usr/local/zend/bin/php ;
            cd $OPENSHIFT_REPO_DIR ;
            $PHP public/index.php githubfeed fetch 
        )
    fi
done
</code></pre></div>

<p>
    The point is that if you need more specificity, push the script into the 
    next more specific directory, and test against the time of execution.
</p>

<h2>Naked Domains</h2>

<p>
    Naked domains are domains without a preceding subdomain. In my case, this
    means "mwop.net", vs. "www.mwop.net".
</p>

<p>
    The problem that cloud hosting presents is that the IP address on which you
    are hosted can change at any time, for a variety of reasons. As such, you
    typically cannot use DNS A records to point to your domain; the recommendation
    is to use a CNAME record that points the domain to a "virtual" domain 
    registered with your cloud hosting provider.
</p>

<p>
    However, most domain registrars and DNS providers do not let you do this for
    a naked domain, particularly if you also have MX or other records associated
    with that naked domain.
</p>

<p>
    Some registrars will allow you to forward the A record to a subdomain. I tried
    this, but had limited success; I personally found that I ended up in an infinite
    loop situation when doing the DNS lookup.
</p>

<p>
    Another solution is to have a redirect in place for your naked domain to the
    subdomain, which can then be a CNAME record. Typically, this would require you
    have a web server under your control with a fixed IP that then simply redirects
    to the subdomain. Fortunately, there's an easier solution: <a 
    href="http://wwwizer.com/naked-domain-redirect">wwwizer</a>. You simply point
    your naked domain A record to the wwwizer IP address, and they will do a 
    redirect to your <code>www</code> subdomain.
</p>

<p>
    I implemented wwwizer on my domain (which is why you'll see "www.mwop.net" in
    your location bar), and it's been working flawlessly since doing so.
</p>

<h4>Private repositories</h4>

<p>
    I keep my criticial site settings in a private repository, which allows me 
    to version them while keeping the credentials they hold out of the public eye.
    This means, however, that I need to use GitHub deploy keys on my server
    in order to retrieve changes.
</p>

<p>
    This was simple enough: I created an <code>ssh</code> subdirectory in my
    <code>$OPENSHIFT_DATA_DIR</code> directory, and generated a new SSH keypair.
</p>

<p>
    The problem was telling SSH to <em>use</em> this key when fetching changes.
</p>

<p>
    The solution is to use a combination of <code>ssh-agent</code> and <code>ssh-add</code>,
    and it looks something like this:
</p>

<div class="example"><pre><code language="bash">
#!/bin/bash
ssh-agent `ssh-add $OPENSHIFT_DATA_DIR/ssh/github-key && (
    cd $OPENSHIFT_DATA_DIR/config ; 
    git fetch origin ; 
    git rebase origin/mwop.net.config
)`
</code></pre></div>

<p>
    After testing the above, I put this in a <code>pre_build</code> script in 
    my OpenShift configuration so that I can autoupdate my private 
    configuration on each build. However, I discovered a new problem: when
    a build is being done, the <code>ssh-agent</code> is not available, which
    means the above cannot be executed. I'm still trying to find a solution.
</p>

<h2>Fin</h2>

<p>
    I'm pretty happy with the move. I don't have to do anything special
    to automate deployment, and all my cronjobs and deployment scripts are now
    self-contained in the repository, which makes my site more portable.
    While a few things could use more documentation, all the pieces are there
    and discoverable with a small amount of work.
</p>

<p>
    I'll likely give some other PaaS providers a try in the future, but for 
    the moment, I'm quite happy with the functionality and flexibility of 
    OpenShift.
</p>

<h4>Footnotes</h4>

<ul>
    <li id="f1">Zend Server's JobQueue can also be used as a cron replacement, 
    but I was not keen on exposing the job functionality via HTTP.</li>
</ul>

EOT;
$entry->setExtended($extended);

return $entry;
