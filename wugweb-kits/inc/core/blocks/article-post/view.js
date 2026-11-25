(function () {
  var bar = document.getElementById('reading-progress');
  if (!bar) return;
  function update() {
    var h = document.documentElement.scrollHeight - window.innerHeight;
    if (h > 0) bar.style.width = Math.min(100, Math.max(0, (window.scrollY / h) * 100)) + '%';
  }
  window.addEventListener('scroll', update, { passive: true });
  update();
})();

(function () {
  // Copy link button in the rail.
  document.querySelectorAll('[data-copy-link]').forEach(function (b) {
    b.addEventListener('click', function () {
      if (!navigator.clipboard) return;
      navigator.clipboard.writeText(b.getAttribute('data-copy-link') || '').then(function () {
        b.classList.add('is-copied'); b.setAttribute('aria-label', 'Link copied');
        setTimeout(function () { b.classList.remove('is-copied'); b.setAttribute('aria-label', 'Copy link'); }, 1500);
      }).catch(function () {});
    });
  });
  // Contents: collapsed on small screens, open on desktop; highlight the section in view.
  var det = document.querySelector('.toc-details');
  if (det) {
    var mq = window.matchMedia('(min-width: 1200px)');
    var sync = function () { det.open = mq.matches; };
    sync(); (mq.addEventListener ? mq.addEventListener('change', sync) : mq.addListener(sync));
    var links = [].slice.call(det.querySelectorAll('a[href^="#"]'));
    var map = links.map(function (a) { return [a, document.getElementById(a.getAttribute('href').slice(1))]; }).filter(function (p) { return p[1]; });
    var mark = function () {
      var cur = null;
      map.forEach(function (p) { if (p[1].getBoundingClientRect().top <= 140) cur = p[0]; });
      links.forEach(function (a) { a.classList.toggle('is-active', a === cur); if (a === cur) a.setAttribute('aria-current', 'location'); else a.removeAttribute('aria-current'); });
    };
    window.addEventListener('scroll', mark, { passive: true }); mark();
  }
})();
