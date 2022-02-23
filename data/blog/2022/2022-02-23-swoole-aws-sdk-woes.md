---
id: 2022-02-23-swoole-aws-sdk-woes
author: matthew
title: 'Getting OpenSwoole and the AWS SDK to Play Nice'
draft: false
public: true
created: '2022-02-23T10:43:00-06:00'
updated: '2022-02-23T10:43:00-06:00'
tags:
    - aws
    - mezzio
    - openswoole
    - php
    - swoole
image:
    url: https://live.staticflickr.com/1268/599820538_2871b1a5e9_b.jpg
    creator: 'grover_net'
    attribution_url: https://www.flickr.com/photos/9246159@N06/599820538
    alt_text: 'Storage Servers'
    license: 'BY-ND'
    license_url: https://creativecommons.org/licenses/by-nd/2.0/
---

I have some content that I store in S3-compatible object storage, and wanted to be able to (a) push to that storage, and (b) serve items from that storage.

Easey-peasey: use the [Flysystem AWS S3 adapter](https://flysystem.thephpleague.com/docs/adapter/aws-s3-v3/), point it to my storage, and be done!

Except for one monkey wrench: I'm using OpenSwoole.

<!--- EXTENDED -->

### The Problem

What's the issue, exactly?

By default, the AWS adapter uses the [AWS PHP SDK](https://aws.amazon.com/sdk-for-php/), which in turn uses [Guzzle](https://docs.guzzlephp.org/en/stable/).
Guzzle has a pluggable adapter system for HTTP handlers, but by default uses its `CurlMultiHandler` when the cURL extension is present and has support for multi-exec.
This is a sane choice, and gives optimal performance in most scenarios.

Internally, when the handler prepares to make some requests, it calls `curl_multi_init()`, and then memoizes the handle returned by that function.
This allows the handler to run many requests in parallel and wait for them each to complete, giving async capabilities even when not running in an async environment.

When using OpenSwoole, this state becomes an issue, particularly with _services_, which might be instantiated once, and re-used many times across many requests until the server is shutdown.
More specifically, it becomes an issue when coroutine support is enabled in OpenSwoole.

OpenSwoole has provided coroutine support for cURL for some time now.
However, when it comes to cURL's multi-exec support, _it only allows one multi-exec handle at a time_.
This was specifically where my problem originated: I'd have multiple requests come in at once, each requiring access to S3, and each resulting in an attempt to initialize a new multi-exec handle.
The end result was a locking issue, which led to exceptions, and thus error responses.

(And boy, was it difficult to debug and get to the root cause of these problems!)

### The solution

Guzzle allows you to specify your own handlers, thankfully, and the vanilla `CurlHandler`:

```php
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;

$client = new Client([
    'handler' => HandlerStack::create(new CurlHandler()),
]);
```

The next hurdle is getting the AWS S3 SDK to use this handler.
Fortunately, the S3 client constructor has an `http_handler` option that allows you to pass an HTTP client handler instance.
I can re-use the existing `GuzzleHandler` the SDK provides, passing it my client instance:

```php
use Aws\Handler\GuzzleV6\GuzzleHandler;
use Aws\S3\S3Client;

$storage = new S3Client([
    // .. connection options such as endpoint, region, and credentials
    'http_handler' => new GuzzleHandler($client),
]);
```

> While the namespace is `GuzzleV6`, the `GuzzleHandler` in that namespace also works for Guzzle v7.

I can then pass that to Flysystem, and I'm ready to go.

### But what about those async capabilities?

But doesn't switching to the vanilla `CurlHandler` mean I lose out on async capabilities?

The great part about the OpenSwoole coroutine support is that when the cURL hooks are available, you essentially get the parallelization benefits of multi-exec with the vanilla cURL functionality.
As such, the approach I outline both fixes runtime errors I encountered **and** increases performance.
I like easy wins like this!

### Bonus round: PSR-7 integration

Unrelated to the OpenSwoole + AWS SDK issue, I had another problem I wanted to solve.
While I love Flysystem, there's one place where using the AWS SDK for S3 directly is a really nice win: directly serving files.

When using Flysystem, I was using its `mimeType()` and `fileSize()` APIs to get file metadata for the response, and then copying the file to an in-memory (i.e. `php://temp`) PSR-7 `StreamInterface`.
The repeated calls meant I was querying the API multiple times for the same file, degrading performance.
And buffering to an in-memory stream had the potential for out-of-memory errors.

One alternative I tried was copying the file from storage to the local filesystem; this would allow me to use a standard filesystem stream with PSR-7, which is quite performant and doesn't require a lot of memory.
However, one point of having object storage was so that I could reduce the amount of local filesystem storage I was using.

As a result, for this specific use case, I switched to using the AWS S3 SDK directly and invoking its `getObject()` method.
The method returns an array/object mishmash that provides object metadata, including the MIME type and content length, and also includes a PSR-7 `StreamInterface` for the body.
Combined, you can then stream this directly back in a response:

```php
$result = $s3Client->getObject([
    'Bucket' => $bucket,
    'Key'    => $filename,
]);

/** @var Psr\Http\ResponseFactoryInterface $responseFactory */
return $responseFactory->createResponse(200)
    ->withHeader('Content-Type', $result['ContentType'])
    ->withHeader('Content-Length', $result['ContentLength'])
    ->withBody($result['Body']);
```

This new approach cut response times by 66% (files of ~400k now return in ~200ms), and reduced memory usage to the standard buffer size used by cURL.
Again, an easy win!
