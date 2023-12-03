---
id: 2023-12-02-advent-aws-ntp
author: matthew
title: 'Advent 2023: NTP on AWS'
draft: false
public: true
created: '2023-12-02T15:00:00-06:00'
updated: '2023-12-02T15:00:00-06:00'
tags:
    - advent2023
    - aws
    - ntp
---

Continuing my 2023 advent blogging, today is a tip on getting NTP to work on Amazon AWS EC2 instances.

<!--- EXTENDED -->

I discovered recently that Amazon blocks outgoing NTP requests, so it's possible for your servers to drift.
The way to fix it is to go into your NTP configuration and use 169.254.169.123 as the NTP server. 

On Ubuntu, you will need to ensure that the package `systemd-timesyncd` is installed, and edit the `/etc/systemd/timesyncd.conf` file to set this. Then run: 

```bash
sudo timedatectl set-ntp off
sudo timedatectl set-ntp on
```

This will restart the NTP sync daemon, and you should see any drift corrected.
