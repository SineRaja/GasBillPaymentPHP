const OFFLINE_URL =
    ['https://igse.000webhostapp.com/offline.html',
      'https://igse.000webhostapp.com/img/offline.webp',
      'https://igse.000webhostapp.com/main.js'
    ];

addEventListener('install', (event) => {

  event.waitUntil(async function() {

    const cache = await caches.open('static-v1');

    await cache.addAll(OFFLINE_URL);

  }());

});

addEventListener('activate', event => {

  event.waitUntil(async function() {
    if (self.registration.navigationPreload) {
      await self.registration.navigationPreload.enable();
    }
  }());
});
addEventListener('fetch', (event) => {

  const { request } = event;

  if (request.headers.has('range')) return;

  event.respondWith(async function() {

    const cachedResponse = await caches.match(request);
    if (cachedResponse) return cachedResponse;

    try {
      const response = await event.preloadResponse;

      if (response) return response;
      return await fetch(request);
    } catch (err) {
      if (request.mode === 'navigate') {
        return caches.match('offline.html');
      }
      throw err;
    }
  }());
});