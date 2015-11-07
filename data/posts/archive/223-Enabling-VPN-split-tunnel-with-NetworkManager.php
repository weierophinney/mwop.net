<?php
use Mwop\Blog\EntryEntity;

$entry = new EntryEntity();

$entry->setId('223-Enabling-VPN-split-tunnel-with-NetworkManager');
$entry->setTitle('Enabling VPN split tunnel with NetworkManager');
$entry->setAuthor('matthew');
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(1251747277);
$entry->setUpdated(1251747277);
$entry->setTimezone('America/New_York');
$entry->setMetadata(array (
  'ep_access' => 'public',
));
$entry->setTags(array (
  0 => 'linux',
  1 => 'security',
  2 => 'wifi',
));

$body =<<<'EOT'
<p>
    I've been using <a
        href="http://projects.gnome.org/NetworkManager/">NetworkManager</a> for
    some time now, and appreciate how easy it makes both connecting to wifi as
    well as VPNs. That said, I've had an issue with it that I only resolved
    today.
</p>

<p>
    When working from home, I prefer to use a VPN split tunnel setup -- I'm
    behind a firewall all the time, and it's useful to be able to run virtual
    machines while still connected to my VPN (e.g., when doing training or
    webinar sessions). However, I noticed some months ago that this wasn't
    working. I assumed at first it was a change in our network setup, but others
    reported that the split tunnel was working fine. It's been particularly
    problematic when on IRC -- if the VPN drops, I lose my IRC connection,
    meaning I have to re-connect and re-claim my nick.
</p>

<p>
    So, I did some searching, and found an interesting setting. In
    NetworkManager, "Configure..." then "Edit" your VPN connection,
    and navigate to the "IPv4 Settings" tab. Once there, click the button that
    says "Routes..." and select the checkbox next to "Use this connection only
    for resources on its network". Press Ok to close the dialog, then "Apply" to
    exit out of the VPN configuration. Re-connect to the VPN, and you should be
    all set.
</p>

<p>
    <em>Note: this will only work if your VPN server is configured to allow
    split tunnels. Additionally, only do so if you are behind a firewall.
    Practice safe networking.</em>
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'

EOT;
$entry->setExtended($extended);

return $entry;