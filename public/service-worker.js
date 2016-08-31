/* Ends with ':' so it can be used with cache identifiers */
var version = 'v0.4.0:';

/* Pages to cache by default */
var offline = [
    "/",
    "/blog",
    "/offline",
    "/resume",
    "/css/blog.min.css",
    "/css/site.min.css",
    "/images/favicon/apple-touch-icon-57x57.png",
    "/images/favicon/apple-touch-icon-60x60.png",
    "/images/favicon/apple-touch-icon-72x72.png",
    "/images/favicon/favicon-32x32.png",
    "/images/favicon/favicon-16x16.png",
    "/images/logo.gif",
    "/images/mwop-coffee-dpc09.jpg",
    "/manifest.json",
    "/js/ga.js",
    "/js/blog.min.js",
    "/js/search_terms.json",
    "/js/site.min.js",
    "/js/twitter.js",
    "/blog/2016-08-17-zf-composer-autoloading.html",
    "/blog/2016-06-30-aws-codedeploy.html",
    "/blog/2016-05-16-programmatic-expressive.html",
    "/blog/2016-04-26-on-locators.html",
    "/blog/2016-04-17-react2psr7.html",
    "/blog/2016-01-29-automating-gh-pages.html",
    "/blog/2016-01-28-expressive-stable.html",
    "/blog/2015-12-14-secure-phar-automation.html",
    "/blog/2015-09-19-zend-10-year-anniversary.html",
    "/blog/2015-09-09-composer-root.html"
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

var offsiteImageWhitelist = [];

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
  var url = new URL(event.request.url);

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

  /* If not an HTTPS request, we're done. */
  if (url.protocol != 'https:') {
    return;
  }

  /* HTML requests: attempt to fetch from network first, falling back to cache */
  if (event.request.headers.get('Accept').indexOf('text/html') != -1) {
    /* Is the page in our "do not cache list"? If so, don't attempt to fetch it!  */
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
    if (url.origin == location.origin ||
        offsiteImageWhitelist.indexOf(url.toString()) != -1) {
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
