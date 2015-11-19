/* Ends with ':' so it can be used with cache identifiers */
var version = 'v0.3.4:';

/* Pages to cache by default */
var offline = [
    "/",
    "/blog",
    "/offline",
    "/resume",
    "/css/site.min.css",
    "/images/favicon/apple-touch-icon-57x57.png",
    "/images/favicon/apple-touch-icon-60x60.png",
    "/images/favicon/apple-touch-icon-72x72.png",
    "/images/favicon/favicon-32x32.png",
    "/images/favicon/favicon-16x16.png",
    "/images/logo.gif",
    "/manifest.json",
    "/js/bootstrap.min.js",
    "https://www.google.com/jsapi?ABQIAAAAGybdRRvLZwVUcF0dE3oVdBTO-MlgA7VGJpGqyqTOeDXlNzyZQxTGq17s-iAB0m0vwqLQ_A2dHhTg2Q",
    "https://code.jquery.com/jquery-1.10.2.min.js",
    "https://farm4.staticflickr.com/3315/3625794227_8d038eac5e_n.jpg",
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
  '/auth',
  '/auth/callback',
  '/auth/github',
  '/auth/github/oauth2callback',
  '/auth/google',
  '/auth/google/oauth2callback',
  '/auth/logout',
  '/auth/twitter',
  '/auth/twitter/oauth2callback',
  '/comics',
  '/contact',
  '/contact/thank-you'
];

var offsiteImageWhitelist = [
  'https://farm4.staticflickr.com/3315/3625794227_8d038eac5e_n.jpg'
];

/* Cache up to 25 pages locally */
var pageCacheLimit = 25;

/* Cache up to 10 images locally */
var imageCacheLimit = 10;

/* Update/install the static cache */
var updateStaticCache = function() {
  return Promise.all(offline.map(function(value) {
    var cacheName = version;

    if (value.indexOf('?') == -1 &&
        (value.indexOf('.') == -1 || value.match(/\.html$/))) {
      cacheName += 'pages';
    } else if (value.match(/\.(png|jpg|gif)$/)) {
      cacheName += 'images';
    } else {
      cacheName += 'assets';
    }

    return caches.open(cacheName).then(function(cache) {
      var request = new Request(value);
      var url = new URL(request.url);
      if (url.origin != location.origin) {
        request = new Request(value, {mode: 'no-cors'});
      }
      return fetch(request).then(function(response) {
        var cachedCopy = response.clone();
        return cache.put(request, cachedCopy);
      });
    });
  }));
};

/* Invalidate obsolete cache entries */
var clearOldCache = function() {
  return caches.keys().then(function(keys) {
    return Promise.all(
      keys
        .filter(function(key) {
          return key.indexOf(version) == -1;
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

  /* Passthrough; for assets that will never be cached */
  var passthrough = function(response) {
    return response;
  };

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

  /* If a data: request, we're done. */
  if (event.request.url.indexOf('data:') === 0) {
    return;
  }

  /* If this is a non-ssl request, we're done. */
  if (event.request.url.indexOf('http://') === 0) {
    return;
  }

  /* HTML requests: attempt to fetch from network first, falling back to cache */
  if (event.request.headers.get('Accept').indexOf('text/html') != -1) {
    /* Is the page in our "do not cache list"? If so, don't attempt to fetch it!  */
    url = new URL(event.request.url);
    if (neverCache.indexOf(url.pathname) != -1) {
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
    /* If it's from the same origin, or in the offsite image whitelist, attempt
     * to fetch from the cache first, then the network.
     */
    url = new URL(event.request.url);
    if (url.origin == location.origin ||
        offsiteImageWhitelist.indexOf(event.request.url) != -1) {
      event.respondWith(
          caches.match(event.request).then(function(cached) {
            return cached || fetch(event.request).then(fetchFromNetwork, fallback);
          })
      );
      return;
    }

    /* Otherwise, network, or offline fallback */
    event.respondWith(fetch(event.request).then(passthrough, fallback));
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
