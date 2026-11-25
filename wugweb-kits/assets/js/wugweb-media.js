(function(){
  // Site media: honour prefers-reduced-motion (poster only) and fall back to the grey placeholder when a file 404s.
  var still = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  function ph(el, ar) {
    var s = document.createElement('span');
    s.className = 'wugweb-ph'; s.setAttribute('aria-hidden', 'true');
    if (ar) s.style.setProperty('--ph-ar', ar);
    el.replaceWith(s);
  }
  function calm(v) { if (still) { v.removeAttribute('autoplay'); v.autoplay = false; v.pause(); if (!v.getAttribute('poster')) v.preload = 'none'; } }
  document.querySelectorAll('video').forEach(function (v) {
    calm(v);
    v.addEventListener('error', function () { if (!v.dataset.swapped) { v.dataset.swapped = '1'; ph(v); } }, true);
  });
  var sel = '.team-photo img,.culture-visual,.bento-media img,.about-hero-frame img,.tag-media img';
  function check(img) { if (img.tagName === 'IMG' && img.complete && img.naturalWidth === 0 && img.src) ph(img, img.closest('.team-photo') ? '3/4' : ''); }
  document.querySelectorAll(sel).forEach(check);
  document.addEventListener('error', function (e) {
    var t = e.target; if (t && t.tagName === 'IMG' && t.matches && t.matches(sel) && !t.dataset.swapped) { t.dataset.swapped = '1'; ph(t, t.closest('.team-photo') ? '3/4' : ''); }
  }, true);
})();
