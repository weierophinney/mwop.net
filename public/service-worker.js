/* Ends with ':' so it can be used with cache identifiers */
var version = 'v0.0.3:';

/* Pages to cache by default */
var offline = [
    "/",
    "/blog",
    "/offline",
    "/resume",
    "/blog/2015-09-19-zend-10-year-anniversary.html",
    "/blog/2015-09-09-composer-root.html",
    "/blog/2015-07-28-on-psr7-headers.html",
    "/blog/2015-06-08-php-is-20.html",
    "/blog/2015-05-18-psr-7-accepted.html",
    "/blog/2015-05-15-splitting-components-with-git.html",
    "/blog/2015-01-26-psr-7-by-example.html",
    "/blog/2015-01-08-on-http-middleware-and-psr-7.html",
    "/blog/2014-11-03-utopic-and-amd.html",
    "/blog/2014-09-18-zend-server-deployment-part-8.html"
];

/* Pages to NEVER cache */
var neverCache = [
  '/comics',
  '/contact',
  '/contact/thank-you',
];

/* Cache up to 25 pages locally */
var pageCacheLimit = 25;

/* Cache up to 10 images locally */
var imageCacheLimit = 10;

/* Update/install the static cache */
var updateStaticCache = function() {
  return caches.open(version + 'offline').then(function(cache) {
    return Promise.all(offline.map(function(value) {
      var request = new Request(value);
      var url = new URL(request.url);
      if (url.origin != location.origin) {
        request = new Request(value, {mode: 'no-cors'});
      }
      return fetch(request).then(function(response) {
        var cachedCopy = response.clone();
        return cache.put(request, cachedCopy);
      });
    }));
  });
};

/* Invalidate obsolete cache entries */
var clearOldCache = function() {
  return caches.keys().then(function(keys) {
    return Promise.all(
      keys
        .filter(function(key) {
          return key.indexOf(version);
        })
        .map(function(key) {
          return caches.delete(key);
        })
    );
  });
};

/* Ensure cache only retains a set number of items */
var limitCache = function(cache, maxItems) {
  cache.keys().then(function(items) {
    if (items.length > maxItems) {
      cache.delete(items[0]);
    }
  });
};

/* Install the service worker: populate the cache */
self.addEventListener('install', function(event) {
  event.waitUntil(updateStaticCache());
});

/* On activation: clear out old cache files */
self.addEventListener('activate', function(event) {
  event.waitUntil(clearOldCache());
});

/* Handle fetch events, but only from GET */
self.addEventListener('fetch', function(event) {
  var url;

  /* Fetch from site and cache on completion */
  var fetchFromNetwork = function(response) {
    var cacheCopy = response.clone();

    /* Caching an HTML page */
    if (event.request.headers.get('Accept').indexOf('text/html') != -1) {
      caches.open(version + 'pages').then(function(cache) {
        cache.put(event.request, cacheCopy).then(function() {
          limitCache(cache, pageCacheLimit);
        });
      });
      return response;
    }

    /* Caching an image */
    if (event.request.headers.get('Accept').indexOf('image/') != -1) {
      caches.open(version + 'images').then(function(cache) {
        cache.put(event.request, cacheCopy).then(function() {
          limitCache(cache, imageCacheLimit);
        });
      });
      return response;
    }

    /* All other assets */
    caches.open(version + 'assets').then(function(cache) {
      cache.put(event.request, cacheCopy);
    });
    return response;
  };

  /* Provide a fallback in the event of network failure/going offline */
  var fallback = function() {
    /* HTML pages; check if in cache, returning that, or /offline if not found */
    if (event.request.headers.get('Accept').indexOf('text/html') != -1) {
      return caches.match(event.request).then(function(response) {
        return response || caches.match('/offline');
      });
    }

    /* Images: placeholder indicating offline */
    if (event.request.headers.get('Accept').indexOf('image/') != -1) {
      return new Response(
        '<svg width="400" height="300" role="img" aria-labelledby="offline-title" viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg"><title id="offline-title">Offline</title><g fill="none" fill-rule="evenodd"><path fill="#D8D8D8" d="M0 0h400v300H0z"/><text fill="#9B9B9B" font-family="Helvetica Neue,Arial,Helvetica,sans-serif" font-size="72" font-weight="bold"><tspan x="93" y="172">offline</tspan></text></g></svg>',
        { 
          headers: { 
            'Content-Type': 'image/svg+xml'
          }
        }
      );
    }
  };

  /* If not a GET request, we're done; nothing to cache */
  if (event.request.method != 'GET') {
    return;
  }

  /* HTML requests: attempt to fetch from network first, falling back to cache */
  if (event.request.headers.get('Accept').indexOf('text/html') != -1) {
    /* Is the page in our "do not cache list"? If so, attempt to fetch from the
     * network, falling back to the offline page.
     */
    url = new URL(event.request.url);
    if (neverCache.indexOf(url.pathname) != -1) {
      event.respondWith(fetch(event.request).catch(fallback));
      return;
    }

    /* Attempt to fetch from the network; fallback if it cannot be done.
     *
     * Essentially, fetch() returns a promise, and we're using fetchFromNetwork
     * as the resolve callback, and fallback as the reject callback.
     */
    event.respondWith(fetch(event.request).then(fetchFromNetwork, fallback));
    return;
  }

  /* Image requests */
  if (event.request.headers.get('Accept').indexOf('image/') != -1) {
    /* If it's from the same origin, attempt to fetch from the cache first, then
     * the network.
     */
    url = new URL(event.request.url);
    if (url.origin == location.origin) {
      event.respondWith(
          caches.match(event.request).then(function(cached) {
            return cached || fetch(event.request).then(fetchFromNetwork, fallback);
          })
      );
      return;
    }

    /* Otherwise, network, then fallback */
    event.respondWith(fetch(event.request).catch(fallback));
    return;
  }

  /* Non-HTML/image requests: look for file in cache first */
  event.respondWith(
      caches.match(event.request).then(function(cached) {
        return cached || fetch(event.request).then(fetchFromNetwork, fallback);
      })
  );
});

/* See https://brandonrozek.com/2015/11/service-workers/ for full details! */
