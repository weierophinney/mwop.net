---
id: 156-Apache-HOSTNAME-on-Clusters
author: matthew
title: 'Apache HOSTNAME on Clusters'
draft: false
public: true
created: '2008-01-25T17:38:16-05:00'
updated: '2008-01-26T08:22:44-05:00'
tags:
    - linux
    - programming
    - php
---
In an effort to debug issues on a cluster, I was trying to determine which
machine on the cluster was causing the issue. My idea was that I could insert a
header token identifying the server.

My first idea was to add the directive `Header add X-Server-Ip
"%{SERVER\_ADDR}e` in my `httpd.conf`. However, due to the nature of our load
balancer, Apache was somehow resolving this to the load balancer IP address on
all machines of the cluster — which was really, really not useful.

I finally stumbled on a good solution, however: you can set environment
variables in `apachectl`, and then pass them into the Apache environment using the
`PassEnv` directive from `mod_env`; once that's done, you can use the environment
variable anywhere.

In my `apachectl`, I added the line `export HOSTNAME=\`hostname\``. Then, in my
`httpd.conf`, I added first the line `PassEnv HOSTNAME`, followed by the
directive `Header add X-Server-Name "%{HOSTNAME}e"`. Voilá! I now had the
hostname in the header, which gave me the information I needed for debugging.
