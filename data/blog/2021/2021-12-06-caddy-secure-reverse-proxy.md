---
id: 2021-12-06-caddy-secure-reverse-proxy
author: matthew
title: 'Caddy as a Secure Reverse Proxy'
draft: false
public: true
created: '2021-12-06T16:03:00-06:00'
updated: '2021-12-06T16:03:00-06:00'
tags:
    - caddy
    - php
---

I've been using [Caddy](https://caddyserver.com/) as a front-end reverse proxy for several years now, on the advice of [Marco Pivetta](https://github.com/Ocramius).
Somewhere along the line version 2 was released, and I updated at some point, but evidently didn't quite understand some of its configuration options, particularly around HSTS support and providing your proxied application information about how the client tried to connect.

<!--- EXTENDED -->

Caddy has always had a fairly declarative syntax, and tended towards sane defaults.
The syntax is like a hybrid of YAML and HCL, for better or worse, and includes placeholders for substituting in request or block-specific values.
Fortunately, you don't have to write much to get the most common scenarios to work correctly.
And v2 now provides a JSON syntax as well.
The JSON syntax gives full access to all configuration options, and is particularly useful to learn if you want to be able to update the configuration on the fly via Caddy 2's configuration API.
That said, the JSON syntax is incredibly verbose, and has quite a large set of nested members; I've found that for the bulk of my usage, the declarative HCL-like syntax tends to be easier to read and implement.

For instance, the documented way to create a reverse proxy to a service running on port 9000 of another machine, and that uses HTTPS by default is simply:

```javascript
your.host.name {
  reverse_proxy machine-running-actual-service:9000
}
```

Boom, done.

Even better: Caddy can serve _local_ IPs and addresses over HTTPS as well.
It will generate self-signed certificates using its own root certificate, which you then install into your system trust store.
The benefit is you can test your sites locally using TLS, which can help when testing JavaScript interactions, and reduce behavior differences with production.

## Securing reverse proxies

That said, I've run into some small issues when running reverse proxies:

- I assumed [HSTS headers](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Strict-Transport-Security) were in place.
  They were not.
  (This is true of any Caddy-served site, though, and not specific to reverse proxies.)
- I assumed things like the `X-Forwarded-Host` and `X-Real-IP` request headers were in place.
  They were not.
  That said: by default, Caddy:
  - Passes the `Host` header intact to the proxy.
    This is actually quite handy, as most application frameworks will prefer the `Host` header when present anyways.
  - Adds the `X-Forwarded-Proto` header; this is the one most standardly consumed by other web servers and web application frameworks.
  - Adds or updates the `X-Forwarded-For` header, which is used by load balancers.

Fortunately, adding configuration for these are relatively straight-forward

```javascript
your.host.name {
  reverse_proxy machine-running-actual-service:9000 {
    header_up X-Real-IP {remote}
    header_down Strict-Transport-Security max-age=31536000
  }
}
```

If you have quite a number of reverse proxies, you likely don't want to copy-paste those.
Caddy to the rescue again: configuration supports _snippets_.
These look like your host blocks, but the name will be in parentheses.
When a configuration block can re-use it, it can _import_ it by name.

```javascript
(reverseproxyheaders) {
    header_up X-Real-IP {remote}
    header_down Strict-Transport-Security max-age=31536000
}

your.host.name {
  reverse_proxy machine-running-actual-service:9000 {
    import reverseproxyheaders
  }
}
```

With these changes, my applications now:

- can resolve the client IP correctly
- provide HSTS headers to the client, helping protect users from MITM attacks

My own configuration defines three reverse proxies, two subdomains that redirect elsewhere, and defines one static site.
All in a total of 34 lines of configuration.

I'll take it.

## Endnote

Why use Caddy, particularly if you're comfortable and/or knowledgable with Apache or nginx?

For me, the decision comes down to sane defaults and ease of setup.
Setting up ACME with Apache or nginx, while it has become simpler, is not turn-key.
Caddy, however, assumes TLS by default, uses ACME to marshal a TLS certificate, and redirects non-TLS requests to TLS, all without requiring any additional configuration whatsoever.
Similarly, the fact that setting up a reverse proxy can often be as simple as pointing it to the proxy, and not require remembering to pass on common headers, sets it apart from the traditional web servers.
Finally, it's built for speed, and I've found that the performance overhead of running it as a reverse proxy is essentially negligible.

I've found it useful for my purposes, and it is particularly convenient when using Docker-based deployments, as it works well as a reverse proxy in front of other containers.
Your mileage may vary, obviously.
