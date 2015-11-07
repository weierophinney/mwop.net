---
id: 24-Fun-with-Find
author: matthew
title: 'Fun with Find'
draft: false
public: true
created: '2004-02-04T13:40:36-05:00'
updated: '2004-09-20T13:38:44-04:00'
tags:
    - linux
    - personal
---
I've had occasion to need to grab a specific set of files from a large directory
— most recently, I needed to grab some specific access logs from our Apache
logfiles at work.

Enter `find`.

I needed to get all files newer than a specific date, and with the pattern
'sitename-access\_log.timestamp.gz'. I then needed to tar up these files and
grab them for processing. So, here's what I did:

- The `-newer filename` tells find to locate files newer than `filename`.
- The `-regex` flag tells find to locate files matching the regular expression.
  The regex that find uses is a little strange, however, and didn't follow many
  conventions I know; for one thing, it's assumed that the pattern you write
  will match against the entire string, and not just a portion of it. What I
  ended up using was `-regex '.*access_log.*gz'`, and that worked.
- The `-printf` flag tells find to format the printing. This is useful when
  using the output of find in another program. For instance, tar likes a list
  of filenames… so I used `-printf "%p "`, which separated each filename with a space.

I then backticked my full find statement and used it as the final argument to a
tar command; voila! instant tar file with the files I need!
