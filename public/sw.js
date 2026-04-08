const CACHE_NAME = 'rgc-platform-v4';
const APP_SHELL = [
  '/',
  '/offline.html',
  '/manifest.webmanifest',
  '/icons/icon-192.png',
  '/icons/icon-512.png',
  '/icons/icon-180.png',
  '/images/rgc_logo.png',
];

const CACHEABLE_PATHS = new Set(APP_SHELL);
const STATIC_ASSET_PATTERN = /\.(?:js|css|png|jpg|jpeg|webp|gif|svg|ico|woff2?|ttf|eot|json|webmanifest)$/i;

const isCacheableAssetRequest = (request, url) => {
  if (request.method !== 'GET') {
    return false;
  }

  return CACHEABLE_PATHS.has(url.pathname) || STATIC_ASSET_PATTERN.test(url.pathname);
};

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => cache.addAll(APP_SHELL)),
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => Promise.all(
      keys
        .filter((key) => key !== CACHE_NAME)
        .map((key) => caches.delete(key)),
    )),
  );
  self.clients.claim();
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

  if (request.mode === 'navigate') {
    event.respondWith(
      fetch(request)
        .then((response) => response)
        .catch(async () => {
          if (url.pathname === '/' || url.pathname === '/offline.html') {
            const cachedHome = await caches.match('/');
            if (cachedHome) {
              return cachedHome;
            }
          }

          return caches.match('/offline.html');
        }),
    );

    return;
  }

  if (!isCacheableAssetRequest(request, url)) {
    return;
  }

  event.respondWith(
    caches.match(request).then((cached) => cached || fetch(request).then((response) => {
      if (response.ok) {
        const copy = response.clone();
        caches.open(CACHE_NAME).then((cache) => cache.put(request, copy));
      }

      return response;
    })),
  );
});
