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
})();
