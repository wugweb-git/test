/* Behaviour for service-impact-bento — port of src/lib/bento-block.ts. Idempotent per element. */
(function () {
  function initBentoVideo() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    var videos = Array.prototype.slice
      .call(document.querySelectorAll('[data-bento-video]'))
      .filter(function (v) { return v.dataset.bentoVideoBound !== '1'; });
    if (!videos.length || !('IntersectionObserver' in window)) return;
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        var v = e.target;
        if (e.isIntersecting) { var p = v.play(); if (p && p.catch) p.catch(function () {}); }
        else v.pause();
      });
    }, { rootMargin: '10% 0px', threshold: 0.15 });
    videos.forEach(function (v) { v.dataset.bentoVideoBound = '1'; io.observe(v); });
  }

  function initBentoCursor() {
    if (!window.matchMedia('(hover: hover)').matches) return;
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    document.querySelectorAll('.bento-block').forEach(function (block) {
      if (block.dataset.bentoCursorBound === '1') return;
      var dot = block.querySelector('[data-bento-cursor]');
      if (!dot) return;
      block.dataset.bentoCursorBound = '1';
      var x = 0, y = 0, queued = false;
      function paint() {
        queued = false;
        dot.style.transform = 'translate3d(' + x + 'px, ' + y + 'px, 0) scale(1)';
      }
      block.addEventListener('pointermove', function (e) {
        var r = block.getBoundingClientRect();
        x = e.clientX - r.left;
        y = e.clientY - r.top;
        if (!queued) { queued = true; requestAnimationFrame(paint); }
      });
      block.addEventListener('pointerenter', function () { block.classList.add('cursor-live'); });
      block.addEventListener('pointerleave', function () { block.classList.remove('cursor-live'); });
    });
  }

  function init() { initBentoVideo(); initBentoCursor(); }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();
