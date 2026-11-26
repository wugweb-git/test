/* Reveal fallback with no GSAP: only for browsers without CSS scroll-driven animations, on pages that did not load GSAP.
   Supported browsers are handled entirely by CSS (site-shell.css); GSAP pages by wugweb-motion.js. */
(function () {
  'use strict';
  clearTimeout(window.__revealWatchdog);
  if (window.gsap) { return; }
  if (window.CSS && CSS.supports && CSS.supports('animation-timeline: view()')) { return; }
  var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var els = Array.prototype.slice.call(document.querySelectorAll('[data-reveal], [data-scroll]'));
  if (reduced || !els.length || !('IntersectionObserver' in window)) { return; }
  var io = new IntersectionObserver(function (list) {
    list.forEach(function (e) {
      if (!e.isIntersecting) { return; }
      e.target.style.opacity = '1'; e.target.style.translate = '0 0'; io.unobserve(e.target);
    });
  }, { rootMargin: '0px 0px -12% 0px' });
  els.forEach(function (el) {
    var r = el.getBoundingClientRect();
    if (r.top < window.innerHeight * 0.88) { el.style.opacity = '1'; return; }
    el.style.opacity = '0'; el.style.translate = '0 24px';
    el.style.transition = 'opacity .6s ease, translate .6s ease';
    io.observe(el);
  });
})();
