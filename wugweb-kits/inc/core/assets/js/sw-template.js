const CACHE_VERSION='wugweb-v1.1.0';
const STATIC_CACHE=`${CACHE_VERSION}-static`;
const PAGE_CACHE=`${CACHE_VERSION}-pages`;
// Never touched by the worker: admin, login, REST, uploads, previews, the worker and manifest themselves.
const BYPASS=[/^\/wp-admin/,/^\/wp-login\.php/,/^\/wp-json/,/^\/wp-content\/uploads/,/^\/sw\.js/,/^\/manifest\.webmanifest/,/preview=true/,/customize_changeset_uuid/,/[?&]wc-ajax=/,/^\/\?rest_route=/];
// Responses worth keeping: OK, and not marked private/no-store, and not flagged as a logged-in render by the server.
const cacheable=res=>res&&res.status===200&&res.type!=='opaqueredirect'&&!res.redirected&&!res.headers.get('X-Wugweb-Auth')&&!/no-store|private/i.test(res.headers.get('Cache-Control')||'');
self.addEventListener('install',e=>{e.waitUntil(caches.open(PAGE_CACHE).then(c=>c.add('/').catch(()=>{})));self.skipWaiting()});
self.addEventListener('activate',e=>{e.waitUntil(caches.keys().then(keys=>Promise.all(keys.filter(k=>!k.startsWith(CACHE_VERSION)).map(k=>caches.delete(k)))).then(()=>self.clients.claim()))});
self.addEventListener('fetch',e=>{
  const r=e.request;if(r.method!=='GET')return;
  const u=new URL(r.url);
  if(u.origin!==self.location.origin)return;
  if(BYPASS.some(p=>p.test(u.pathname+u.search)))return;
  if(/\.(?:css|js|woff2?|png|jpe?g|webp|avif|svg)$/i.test(u.pathname)){
    e.respondWith(caches.match(r).then(c=>c||fetch(r).then(res=>{if(cacheable(res)){const clone=res.clone();caches.open(STATIC_CACHE).then(cache=>cache.put(r,clone))}return res})));return;
  }
  if(r.mode==='navigate'){
    e.respondWith(fetch(r).then(res=>{if(cacheable(res)){const clone=res.clone();caches.open(PAGE_CACHE).then(cache=>cache.put(r,clone))}return res}).catch(()=>caches.match(r).then(c=>c||caches.match('/'))));
  }
});
