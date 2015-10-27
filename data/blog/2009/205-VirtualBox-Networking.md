---
id: 205-VirtualBox-Networking
author: matthew
title: 'VirtualBox Networking'
draft: false
public: true
created: '2009-01-17T10:43:40-05:00'
updated: '2009-01-17T10:43:40-05:00'
tags:
    - linux
    - virtualbox
---
I use Linux on the desktop (currently [Ubuntu](http://www.ubuntu.com/)), but
occasionally need to use Windows for things like webinars, OS-specific testing,
etc. I started using [VirtualBox](http://virtualbox.org/) for virtualization
around six months ago, and have been reasonably satisfied; Windows boots
quickly, and everything "just works." That is, until yesterday.

I was given a linux VM image running a web server and some applications I needed to review. On top of that, I needed to do so over WebEx, so that I could share my screen with somebody else. This meant I needed the following to work:

1. Internet access for my Windows VM
2. Access to my linux VM from my Windows VM
3. Ideally, access to both guest VMs from my linux host
4. Ideally, internet access for my linux host

<!--- EXTENDED -->

Since I'd only ever used one VM image at a time before this, I'd never had any
issues; I could use NAT networking in VirtualBox, and have communication
between my host and guest, as well as internet access for both. But NAT access
does not allow the VMs to communicate with each other — in fact, both received
the same exact same IP address from my host, which meant that I had internet
access from both, both could ping the host, but the host could not access
either machine, and neither could access each other.

I did some research, and started reading on using network bridges, something
I'd tried once before without success. Fortunately, the very first literature I
started reading this time pointed out the reason why I'd failed before: network
bridges over wireless adapters do not work, and I was using my wifi. I briefly
considered using a wired connection, but realized that this was not an option:
there are times I may need this sort of setup when I am unable to use a wired
connection.

I then found an article that detailed how to setup Host Interface networking
with VirtualBox. Host Interface networking was added in the 2.1.x series of
VirtualBox, and basically allows you to use your host machine as a network
gateway for your guest machines. The VirtualBox binaries available in Ubuntu
are 2.0.x… so I had to uninstall them and download the official binaries from
the VirtualBox site.

Setting up Host Interface networking worked for case 2 only; somehow, when it
was active, my routing got completely borked. So, I did more research. The next
thing I found suggested I needed to setup one or more [virtual network devices](http://vtun.sourceforge.net/tun/faq.html)
(TAP), which would allow each virtual machine to have its own IP address, and
communicate over the same network, while using the wifi adapter in my host
machine as a gateway to the internet.

All the instructions I found setup a separate TAP interface for each virtual
machine. I quickly discovered two things: first, I had to setup IP masquerading
in my host's iptables rules so that the VMs would have access to the internet,
and second, that while this would solve cases 2-4, the VMs still couldn't talk
to each other. In the end, I found that I needed to setup a single TAP
interface, and have all the VMs use this as their Host Interface — and
everything then worked. Almost. The other trick I discovered was that the TAP
address should be on a private network that you're not a member of already —
including the private network space your router might use. The instructions I
followed setup the network in the 10.0.1.X network, but this conflicted with my
DSL modem, which was assigned a 10.0.0.X address, and meant that the guest
machines had no access to the outside world; switching to 192.168.168.X fixed
all issues.

Here are the step-by-step instructions (linux host):

- **On the host:**
  - Make sure you have uml-utilities installed
    - On Debian-based systems, `sudo aptitude install uml-utilities`
  - Create a virtual network interface
    - `sudo tunctl -t tap0 -u $USER` (where `$USER` is the user initiating the VirtualBox sessions
      - Make sure the user is in the vboxusers group:
        - Edit `/etc/group`, look for the `vboxusers` entry, and ensure `$USER` is listed as a member of the group.
      - Make sure the vboxusers group has rights to tun devices:
        - `sudo chgrp vboxusers /dev/net/tun`
        - `sudo chmod 660 /dev/net/tun`
  - Enable the network interface and assign it an IP address
    - Make sure the IP is not on a netmask in use elsewhere in your networking; I used 192.168.168.1, which did not conflict with anything.
    - `sudo ifconfig tap0 192.168.168.1`
  - Set up NAT forwarding:
    - `sudo iptables -t nat -A POSTROUTING -o wlan0 -j MASQUERADE`
      - Substitute the appropriate network interface based on what you're using on your machine.
    - `sudo sysctl -w net.ipv4.ip_forward=1`
- **On your guest machines:**
  - Setup TCP/IP networking to use static IP addresses in the network you've defined for the virtual adapter on the host. For example, if you used 192.168.168.1 on your host:
    ```
    Address:
    192.168.168.[UNIQUE]
    Netmask:
    255.255.255.0
    Gateway:
    192.168.168.1
    ```
- Assign DNS servers based on what you're using on your linux host. Check `/etc/resolv.conf` if you're unsure.

Now, one caveat: your TAP device will disappear when you restart your host box. To solve this, I added the following lines to my `/etc/rc.local`:

```bash
echo -n "Setting up tap0 interface..."
tunctl -t tap0 -u matthew
ifconfig tap0 192.168.168.1
iptables -t nat -A POSTROUTING -o wlan0 -j MASQUERADE
sysctl -w net.ipv4.ip_forward=1
echo "DONE!"
```

This ensures that the TAP device is setup, and also that IP masquerading is enabled at boot time.

I'm writing this mainly for myself, but also hoping that it will save others the many hours of experimentation I had to go through to find the write combination of settings.
