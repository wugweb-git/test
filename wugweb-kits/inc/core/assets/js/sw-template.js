const CACHE_VERSION='__CACHE_VERSION__';
const STATIC_CACHE=`${CACHE_VERSION}-static`;
const PAGE_CACHE=`${CACHE_VERSION}-pages`;
const OFFLINE='/offline/';
const MAX_PAGES=30,MAX_STATIC=120;
// Never touched by the worker: admin, login, REST, uploads, previews, the worker and manifest themselves.
const BYPASS=[/^\/wp-admin/,/^\/wp-login\.php/,/^\/wp-json/,/^\/wp-content\/uploads/,/^\/sw\.js/,/^\/manifest\.webmanifest/,/preview=true/,/customize_changeset_uuid/,/[?&]wc-ajax=/,/^\/\?rest_route=/];
// Responses worth keeping: OK, and not marked private/no-store, and not flagged as a logged-in render by the server.
const cacheable=res=>res&&res.status===200&&res.type!=='opaqueredirect'&&!res.redirected&&!res.headers.get('X-Wugweb-Auth')&&!/no-store|private/i.test(res.headers.get('Cache-Control')||'');
// Keep a cache to its newest `max` entries (cache keys come back in insertion order).
const trim=(name,max)=>caches.open(name).then(c=>c.keys().then(k=>k.length>max?Promise.all(k.slice(0,k.length-max).map(x=>c.delete(x))):0));
self.addEventListener('install',e=>{e.waitUntil(caches.open(PAGE_CACHE).then(c=>Promise.all(['/',OFFLINE].map(u=>c.add(u).catch(()=>{})))));self.skipWaiting()});
self.addEventListener('activate',e=>{e.waitUntil(Promise.all([
  caches.keys().then(keys=>Promise.all(keys.filter(k=>!k.startsWith(CACHE_VERSION)).map(k=>caches.delete(k)))),
  self.registration.navigationPreload?self.registration.navigationPreload.enable():0
]).then(()=>self.clients.claim()))});
self.addEventListener('fetch',e=>{
  const r=e.request;if(r.method!=='GET')return;
  const u=new URL(r.url);
  if(u.origin!==self.location.origin)return;
  if(BYPASS.some(p=>p.test(u.pathname+u.search)))return;
  if(/\.(?:css|js|woff2?|png|jpe?g|webp|avif|svg)$/i.test(u.pathname)){
    e.respondWith(caches.match(r).then(c=>c||fetch(r).then(res=>{if(cacheable(res)){const clone=res.clone();caches.open(STATIC_CACHE).then(cache=>cache.put(r,clone)).then(()=>trim(STATIC_CACHE,MAX_STATIC))}return res})));return;
  }
  if(r.mode==='navigate'){
    // Pages with a query string (search, utm, filters) are never stored; they are served live or fall back to the offline page.
    const store=u.search==='';
    e.respondWith((e.preloadResponse||Promise.resolve()).then(pre=>pre||fetch(r)).then(res=>{if(store&&cacheable(res)){const clone=res.clone();caches.open(PAGE_CACHE).then(cache=>cache.put(r,clone)).then(()=>trim(PAGE_CACHE,MAX_PAGES))}return res}).catch(()=>caches.match(r).then(c=>c||caches.match(OFFLINE))));
  }
});
