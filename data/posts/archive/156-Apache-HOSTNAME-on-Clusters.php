<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('156-Apache-HOSTNAME-on-Clusters');
$entry->setTitle('Apache HOSTNAME on Clusters');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1201300696);
$entry->setUpdated(1201353764);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'linux',
  1 => 'programming',
  2 => 'php',
));

$body =<<<'EOT'
<p>
    In an effort to debug issues on a cluster, I was trying to determine which
    machine on the cluster was causing the issue. My idea was that I could
    insert a header token identifying the server.
</p>

<p>
    My first idea was to add the directive 'Header add X-Server-Ip
    "%{SERVER_ADDR}e" in my httpd.conf. However, due to the nature of our load
    balancer, Apache was somehow resolving this to the load balancer IP address
    on all machines of the cluster -- which was really, really not useful.
</p>

<p>
    I finally stumbled on a good solution, however: you can set environment
    variables in apachectl, and then pass them into the Apache environment using
    the PassEnv directive from mod_env; once that's done, you can use the
    environment variable anywhere.
</p>

<p>
    In my apachectl, I added the line "export HOSTNAME=`hostname`". Then, in my
    httpd.conf, I added first the line "PassEnv HOSTNAME", followed by the
    directive 'Header add X-Server-Name "%{HOSTNAME}e"'. Voila! I now had the
    hostname in the header, which gave me the information I needed for
    debugging.
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;