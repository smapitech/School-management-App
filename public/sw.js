const CACHE_NAME = 'school-portal-pwa-v1';
const PRECACHE_URLS = [
  '/offline.html',
  '/manifest.webmanifest',
  '/assets/pwa/icon-192.png',
  '/assets/pwa/icon-512.png',
  '/assets/pwa/apple-touch-icon.png',
];
const CACHEABLE_DESTINATIONS = new Set(['document', 'style', 'script', 'image', 'font']);

const getCacheKey = (request) => {
  const url = new URL(request.url);
  url.hash = '';
  url.search = '';
  return url.toString();
};

const putInCache = async (cache, request, response) => {
  if (response && response.ok && response.type !== 'opaque') {
    await cache.put(getCacheKey(request), response.clone());
  }
};

const handleRequest = async (request, isNavigation) => {
  const cache = await caches.open(CACHE_NAME);
  const cacheKey = getCacheKey(request);

  try {
    const response = await fetch(request);
    await putInCache(cache, request, response);
    return response;
  } catch (_) {
    const cached = await cache.match(cacheKey);
    if (cached) {
      return cached;
    }

    if (isNavigation) {
      const offline = await cache.match('/offline.html');
      if (offline) {
        return offline;
      }
    }

    throw new Error('Network request failed and no cache entry was available.');
  }
};

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_URLS)).then(() => self.skipWaiting())
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(keys.map((key) => (key === CACHE_NAME ? null : caches.delete(key))))
    ).then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', (event) => {
  const { request } = event;
  if (request.method !== 'GET') {
    return;
  }

  const url = new URL(request.url);
  if (url.origin !== self.location.origin) {
    return;
  }

  const isNavigation = request.mode === 'navigate' || request.destination === 'document';
  const isCacheable =
    isNavigation ||
    CACHEABLE_DESTINATIONS.has(request.destination) ||
    url.pathname.startsWith('/assets/') ||
    url.pathname === '/manifest.webmanifest' ||
    url.pathname === '/offline.html';

  if (!isCacheable) {
    return;
  }

  event.respondWith(handleRequest(request, isNavigation));
});
