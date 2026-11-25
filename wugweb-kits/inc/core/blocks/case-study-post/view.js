(function(){
  // Media that is not on storage yet 404s: swap the broken <img> for the grey placeholder box instead of showing a broken-image icon.
  const root = document.querySelector('.wugweb-case-study');
  if (!root) return;
  function swap(img) {
    if (img.dataset.swapped) return;
    img.dataset.swapped = '1';
    const ph = document.createElement('span');
    ph.className = 'wugweb-ph';
    ph.setAttribute('aria-hidden', 'true');
    ph.style.setProperty('--ph-ar', img.classList.contains('work-cover-media') ? '16/9' : '16/10');
    img.replaceWith(ph);
  }
  root.querySelectorAll('img').forEach((img) => { if (img.complete && img.naturalWidth === 0 && img.src) swap(img); });
  root.addEventListener('error', (e) => { if (e.target && e.target.tagName === 'IMG') swap(e.target); }, true);
  // Lazy videos (data-src): load when near the viewport, muted loop; reduced-motion = no autoplay; a failed file becomes the grey box.
  var still = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var vids = root.querySelectorAll('video[data-src]');
  function fail(v) { if (v.dataset.swapped) return; v.dataset.swapped = '1'; var f = v.closest('figure'); var ph = document.createElement('span'); ph.className = 'wugweb-ph'; ph.setAttribute('aria-hidden', 'true'); ph.style.setProperty('--ph-ar', '16/9'); v.replaceWith(ph); if (f) f.classList.remove('b-video'); }
  function load(v) {
    if (v.dataset.loaded) return; v.dataset.loaded = '1';
    v.muted = true; v.loop = true; v.playsInline = true; v.preload = 'metadata';
    v.addEventListener('error', function () { fail(v); });
    v.addEventListener('loadeddata', function () { if (!still) { var p = v.play(); if (p && p.catch) p.catch(function () {}); } });
    v.src = v.dataset.src;
  }
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { load(e.target); io.unobserve(e.target); } }); }, { rootMargin: '300px' });
    vids.forEach(function (v) { io.observe(v); });
  } else { vids.forEach(load); }
})();
