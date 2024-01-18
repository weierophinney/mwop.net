---
id: 2024-01-18-obs-studio-audio-choppiness
author: matthew
title: 'Fixing Audio Choppiness in OBS Studio on Linux'
draft: false
public: true
created: '2024-01-18T09:51:00-06:00'
updated: '2024-01-18T09:51:00-06:00'
tags:
    - linux
    - obs-studio
    - pulseaudio
---
I occasionally record screencasts for work — some of these are used for the website as demos/training material, and sometimes they're used internally by our various technical teams.
When I record, I use [OBS Studio](https://obsproject.com/), which works brilliantly.

However, since the last time I recorded, I've upgrade my operating system, as well as switched over to Wayland, and I discovered after doing a recording session that my audio was super choppy.

This is how I fixed it.

<!--- EXTENDED -->

### The root cause

I typically setup a PulseAudio input capture channel when recording.
PulseAudio allows me to change my default capture device(s), set the default levels, and more.

With my new OBS Studio install, the audio mixer panel was showing the PulseAudio capture, but two others as well.
My suspicion was that I was getting choppiness because OBS Studio was trying to combine multiple audio sources.

When I would go into the audio mixer settings, it showed three active devices, but there was no way to disable any of them.
I could mute them, or take their volume down completely, but test recordings would still have the choppiness.

I dug around, and I found the "Settings | Audio | Global Audio Devices" panel:

![OBS Studio Global Audio Devices settings](/images/blog/2024-01-18-obs-studio-settings-audio-devices.png)

When I disabled everything in this panel, it disabled the extra items in the audio mixer.
Subsequent recordings no longer exhibited audio choppiness!

> #### Alternate solution
> 
> I also discovered that I could enable the Mic/Auxiliary Audio item in that settings panel, and specify the appropriate microphone.
> If I did that and **removed** the PulseAudio input capture channel, I was left with a single audio device, and, once again, choppiness disappeared.

This was an interesting problem to solve.

And simultaneously, I'd love to regain the lost 45 minutes of my day.
