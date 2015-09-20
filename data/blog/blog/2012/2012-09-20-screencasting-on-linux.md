---
id: 2012-09-20-screencasting-on-linux
author: matthew
title: 'Screencasting on Linux'
draft: false
public: true
created: '2012-09-20T17:30:00-05:00'
updated: '2012-09-20T17:30:00-05:00'
tags:
    - linux
    - screencast
---
I've been wanting to do screencasts on Linux for some time now, and my big
stumbling block has been determining what tools to use.

The **tl;dr**:

- Use `recordMyDesktop` to record video clips, but afterwards, re-encode them
  to AVI ([see the script I used](#script))
- Record audio to WAV, or convert compressed audio to WAV format afterwards.
- Use OpenShot to stitch clips together and layer audio and video tracks.
- Remember to reset the video length if you change the playback rate.
- Export to a Web + Vimeo profile for best results.

<!--- EXTENDED -->

Stumbling Blocks
----------------

`recordMyDesktop` is a fairly simple tool, and allows you to record actions
you're taking, and simultaneously capture audio. However, it creates an ".ogv"
(Ogg Vorbis video file) — which is basically useless for anybody not on Linux
or FreeBSD. Additionally, I often like to record in segments; this makes it
less likely that I'll make mistakes, and, if I do, I only need to record a
small segment again, not the entire thing. `recordMyDesktop` is only for
creating screencasts, not merging them.

So, `recordMyDesktop` went into my toolbox for the purpose of recording the
video portion of my screencasts.

Which brings me to the next point: I also prefer to record the audio separately
from the screencast portion itself; this way I don't get typing sounds in the
recording, and I'm less likely to lose my train of thought as I'm speaking. To
this end, I ended up using quite simply the "Sound Recorder" utility
(`gnome-sound-recorder`). It's not great, but with a reasonable microphone, it
gets the job done. I chose to record the audio as MP3 files.

However, this means that I now have video and audio tracks. So my toolbox
needed a utility for overlaying tracks and laying them out on a timeline
independently.

I looked at a few different free tools for Linux, including `Avidemux`,
`Cinelerra`, and `PiTiVi`. `Avidemux` was not featurful enough, `Cinelerra` was
too difficult to learn (it's more of an advanced user's tool), and `PiTiVi`
kept crashing on me. So, I used the lazyweb, and tweeted a question asking what
others were using — and the unanimous response was `OpenShot`
([http://www.openshotvideo.com/](http://www.openshotvideo.com/)).

`OpenShot` hit the sweet spot for me -- it was easy to pick up, and didn't
crash. However, I discovered problems when I exported my project to a video
file. My video, regardless of whether or not I changed the playback rate,
always played at about 2X normal speed. The audio always truncated 1 to 2
seconds before completion.

In doing some research, I discovered:

- There are known issues with Ogg Vorbis video files. Evidently, the
  compression creates issues when re-encoding the video to another format.
- Similarly, compressed audio can lead to issues such as truncation.

Since `recordMyDesktop` doesn't allow you to select an alternate video codec, I
had to use `mencoder` to transcode it to another format. I chose AVI (Audio
Video Interleave, a video container format developed by Microsoft), as I knew
it had widespread support, using an mpeg4 codec (also widely supported). I used
the following script, found at
[http://askubuntu.com/questions/17309/video-converter-ogv-to-avi-or-another-more-common-format](http://askubuntu.com/questions/17309/video-converter-ogv-to-avi-or-another-more-common-format),
in order to encode my files:

```bash
for f in *.ogv;do
    newFile=${f%.*}
    mencoder "$f" -o "$newFile.avi" -oac mp3lame -lameopts fast:preset=standard -ovc lavc -lavcopts vcodec=mpeg4:vbitrate=4000
done
```

That solved the video issue, but I still had to solve the audio issues. I
quickly re-recorded one audio segment in Sound Recorder, and told it to use the
"Voice,Lossless (.wav type)". When I used this version of the audio, I had no
issues, other than the audio length being mis-reported within `OpenShot`.
Instead of re-recording all segments, I installed the "Sound Converter" utility
(`sudo aptitude install soundconverter`), and used that to convert all my MP3
files to WAV. Interestingly, `OpenShot` reported the audio lengths correctly
this time; go figure.

Once that was done, I was able to start stitching everything together. A few
notes, in the hopes others learn from my mistakes:

- Several times, I wanted my video to playback slower. This is very easy to do:
  right click on the clip, select "Properties", and select the "Speed" tab, and
  adjust as necessary. However, that's not all you need to do; you need to also
  re-adjust the *length* of the clip. Simply take the existing length, and
  divide it by the rate of play. As an example, if the length is 44 seconds,
  and you specify a 1/2 rate (0.5), you'd do 44 / 0.5 = 88, and set the length
  of the clip to 88s.
- If you find that `OpenShot` is reporting your audio clip lengths incorrectly,
  use another tool to find the accurate length, and then set the length to
  that. I typically rounded up to the next second, as most tools were giving
  the floor value from rounding.
- I chose to export using the Web + Vimeo HD profile. This worked perfectly for
  me. It created an mpeg4 file that I could preview in a browser, and then
  upload without issues. Your mileage may vary.

Hopefully, this will serve as a reasonable guide for others foraying into screencasts on Linux!
