var staticName = "pwa-v" + Date.now()
var fileCache = [
    "https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css",
    "https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css",
    "https://code.jquery.com/jquery-3.5.1.js",
    "https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js",
    "https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js",
    "https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js",
    "https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js",
    "https://cdn.jsdelivr.net/npm/sweetalert2@9",
    "https://kit.fontawesome.com/b10279cbf9.js",
    "import_template.xlsx",
    "/offline"
]

self.addEventListener('install', e => {
    this.skipWaiting()
    e.waitUntil(
        caches.open(staticName)
        .then(cache => {
            return cache.addAll(fileCache)
        })
    )
})

self.addEventListener('activate', e => {
    e.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                .filter(cacheName => (cacheName.startsWith("pwa-")))
                .filter(cacheName => (cacheName !== staticName))
                .map(cacheName => caches.delete(cacheName))
            )
        })
    )
})

self.addEventListener('fetch', e => {
    e.respondWith(
        caches.match(e.request).then(res => {
            return res || fetch(e.request).then(fetchRes => {
                return fetchRes
                // return caches.open(staticName).then(cache=> {
                //     cache.put(e.request.url, fetchRes.clone())
                // })
            })
        }).catch(() => {
            caches.match("offline")
        })
    )
})

