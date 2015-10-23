---
id: 93-File_SMBPasswd-woes
author: matthew
title: 'File_SMBPasswd woes'
draft: false
public: true
created: '2005-09-07T16:20:00-04:00'
updated: '2005-09-08T09:50:23-04:00'
tags:
    - linux
    - php
---
I've been cobbling together a system at work for the last couple months to
allow a single place for changing all network passwords. This includes a
variety of database sources, as well as *passwd* files and *smbpasswd* files.
I've been making use of PEAR's
[File_Passwd](http://pear.php.net/package/File_Passwd) and
[File_SMBPasswd](http://pear.php.net/package/File_SMBPasswd), and they've
greatly simplified the task of updating passwords for those types of systems.
However, I've encountered some issues that I never would have expected.

I have the web user in a group called 'samba', and I have the *smbpasswd* file
owned by root:samba. I then set the *smbpasswd* file to be group +rw. Simple,
right? The web user should then be able to update the *smbpasswd* file without
a problem, right? Wrong.

I kept getting errors, and on investigation continually found that the
*smbpasswd* file permissions had reverted to 0600 — i.e., only the root user
could access it. I tried using 'chattr -i' on the off-chance that the file had
been made immutable (which didn't make sense, as I was able to see the
permissions change). No luck.

Based on observations of when the permissions reverted, it appears that the
various SMB processes will reset the permissions! An example is when someone
attempts to mount a resource from the server; this accesses the smbpasswd file
to perform authentication — and at this point the file permissions change. I
can find no documentation to support this; these are simply my observations.

So, to get around the behaviour, I created a script that will set the file
permissions to what I want them, and then gave *sudo* privileges to the samba
group for that script. This script is then called via *system()* in the update
script just before processing.

It's a hack, and could be made more secure, but it works.
