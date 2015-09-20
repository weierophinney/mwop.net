---
id: 242-Creating-Zend_Tool-Providers
author: matthew
title: 'Creating Zend_Tool Providers'
draft: false
public: true
created: '2010-07-01T09:05:00-04:00'
updated: '2010-07-07T16:31:19-04:00'
tags:
    - php
    - 'zend framework'
---
When I was at [Symfony Live](http://www.symfony-live.com/) this past February, I
assisted [Stefan Koopmanschap](http://www.leftontheweb.com/) in a full-day
workshop on integrating Zend Framework in Symfony applications. During that
workshop, Stefan demonstrated creating Symfony "tasks". These are classes that
tie in to the Symfony command-line tooling — basically allowing you to tie in
to the CLI tool in order to create cronjobs, migration scripts, etc.

Of course, Zend Framework has an analogue to Symfony tasks in the
[Zend_Tool](http://framework.zend.com/manual/en/zend.tool.html) component's
"providers". In this post, I'll demonstrate how you can create a simple provider
that will return the most recent entry from an RSS or Atom feed.

<!--- EXTENDED -->

First things first
------------------

*Caveat: this entire post assumes you are using a unix-like operating system,
such as a Linux distribution or Mac OSX. Most of the instructions should work in
Windows, but I have not tested on that platform.*

First, a little setup. `Zend_Tool` needs some configuration. To get started, you
need to run the following command (if you haven't already):

```bash
$ zf create config
```

This will create a configuration in `$HOME/.zf.ini`. If you pop that file open,
you should see an entry for `php.include_path`. This is the `include_path`
`Zend_Tool` will use, and should include your ZF installation; any providers you
create should be on this path — or you should modify it to add a path to your
providers.

Create the provider
-------------------

Providers are incredibly simple. The easiest way to create one is to create a
class extending `Zend_Tool_Framework_Provider_Abstract`, and then to simply
start creating methods.

A few rules are good to know, however:

- If you need to throw an exception, throw a `Zend_Tool_Project_Exception`. This
  integrates with the CLI tooling to provide nice, colorful error messages.
- While you *can* `echo` directly from your methods, the suggested practice is
  to use the response object and append content to it. This will ensure that if
  we later write an XML-RPC, SOAP, or web frontend to `Zend_Tool`, you will not
  need to make any changes to your code. This is as easy as:

  ```php
  $response = $this->_registry->getResponse();
  $response->appendContent($content);
  ```

In my provider, I'm wanting to grab the first entry of a given feed. Instead of
needing to remember the feed URL, I'd like to use a mnemonic; this will be my
sole argument to the provider. I'll have it default to my own feed. The code
ends up looking like this:

```php
class Phly_Tool_Feed extends Zend_Tool_Framework_Provider_Abstract
{
    protected $_feeds = array(
        'weierophinney' => 'http://weierophinney.net/matthew/feeds/index.rss1',
        'planetphp'     => 'http://www.planet-php.net/rdf/',
    );

    /**
     * Read the first item of a feed
     * 
     * @param  string $feed Named identifier for a feed
     * @return bool
     */
    public function read($feed = 'weierophinney')
    {
        if (!array_key_exists($feed, $this->_feeds)) {
            throw new Zend_Tool_Project_Exception(sprintf(
                'Unknown feed "%s"', 
                $feed
            ));
        }

        $feed = Zend_Feed_Reader::import($this->_feeds[$feed]);
        $title = $desc = $link = '';
        foreach ($feed as $entry) {
            $title = $entry->getTitle();
            $desc  = $entry->getDescription();
            $link  = $entry->getLink();
            break;
        }
        $content = sprintf("%s\n%s\n\n%s\n", $title, strip_tags($desc), $link);

        $response = $this->_registry->getResponse();
        $response->appendContent($content);
        return true;
    }
}
```

I'm leveraging `Zend_Feed_Reader` here, and simply creating some formatted text
output.

Now that the provider is created, I need to put it in the file
`Phly/Tool/Feed.php`, relative to a directory in the `include_path` configured
by `Zend_Tool`.

Tying the provider to the tool
------------------------------

Now that we've got the provider written and somewhere `Zend_Tool` can
potentially find it, we need to tell `Zend_Tool` about it. Open up the
`$HOME/.zf.ini` file again, and add the following line:

```ini
basicloader.classes.1 = \"Phly_Tool_Feed\"
```

This tells `Zend_Tool` that there's an additional provider it should be aware
of. Note in particular the `.1` portion of the key; `basicloader.classes` is an
array. One gotcha I discovered is that, unlike `Zend_Config`, you cannot use the
`[]` notation. In other words, the following ***does not work***:

```ini
basicloader.classes[] = "Phly_Tool_Feed"
```

You need to specify keys manually, and they need to be unique.

Getting help
------------

Now, time to test out if it all works. If you've done the above steps, you can
now execute the following:

```bash
$ zf \? feed
```

*Note: I use zsh, and need to escape the question mark; you may not need to in
other shells.*

If all is well, you'll get the following:

```
Actions supported by provider "Feed"
  Feed
    zf read feed feed[=weierophinney]
```

If you're not seeing this, check to make sure that your provider is on an
`include_path` as defined in your `.zf.ini` file; if you still have issues, ask
on the [fw-general](http://zend-framework-community.634137.n4.nabble.com/Zend-Framework-f634138.html)
mailing list or in the \#zftalk IRC channel on [Freenode](http://freenode.net/).

Using the provider
------------------

Once your provider is working, fire it up:

```bash
$ zf read feed
```

or

```bash
$ zf read feed planetphp
```

You should get something that looks like this (the actual entry will vary):

```
State of Zend Framework 2.0

    
    The past few months have kept myself and my team quite busy, as we've turned
    our attentions from maintenance of the Zend Framework 1.X series to Zend
    Framework 2.0. I've been fielding questions regularly about ZF2 lately, and
    felt it was time to talk about the roadmap for ZF2, what we've done so far,
    and how the community can help.

 Continue reading "State of Zend Framework 2.0"
    

http://weierophinney.net/matthew/archives/241-State-of-Zend-Framework-2.0.html
```

Closing notes
-------------

One "gotcha" you may experience is that there is currently no support for
specifying project-specific providers within applications created with
`Zend_Tool` — a feature that would be quite useful for creating project-specific
tasks.<sup>\*</sup>

That said, `Zend_Tool` providers are an incredibly useful and easy way to write
CLI tools based on Zend Framework. Hopefully this post will help demystify the
component and its usage, and get you thinking about what tasks *you* would like
to write.

<sup>\*</sup> You *can* fake it by creating an alternate configuration file in
your project, informing the environment of it, and calling the `zf` commandline
tool — something that can be done in a single line:

```bash
$ ZF_CONFIG_FILE=./zf.ini; zf <action> <provider> ...
```
