(function () {
  'use strict';
  var g = window.gsap, ST = window.ScrollTrigger;
  if (!g || !ST) return;
  g.registerPlugin(ST);
  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  var D = 0.6, E = 'power2.out';
  function fmt(v, dec) { return v.toLocaleString('en-US', { minimumFractionDigits: dec, maximumFractionDigits: dec }); }
  document.querySelectorAll('.wugweb-chart:not([data-chart-bound])').forEach(function (root) {
    root.setAttribute('data-chart-bound', '1');
    var tl = g.timeline({ paused: true, defaults: { duration: D, ease: E } });
    root.querySelectorAll('[data-fill]').forEach(function (n, i) { tl.fromTo(n, { scaleX: 0 }, { scaleX: 1, clearProps: 'transform' }, i * 0.08); });
    root.querySelectorAll('[data-fill-v]').forEach(function (n, i) { tl.fromTo(n, { scaleY: 0 }, { scaleY: 1, clearProps: 'transform' }, i * 0.08); });
    root.querySelectorAll('[data-line]').forEach(function (n) { n.style.strokeDasharray = '1'; tl.fromTo(n, { strokeDashoffset: 1 }, { strokeDashoffset: 0, onComplete: function () { n.style.strokeDasharray = ''; n.style.strokeDashoffset = ''; } }, 0); });
    root.querySelectorAll('[data-area]').forEach(function (n) { tl.from(n, { opacity: 0 }, 0.2); });
    root.querySelectorAll('.chart-pt').forEach(function (n, i) { tl.from(n, { opacity: 0, scale: 0.4, transformOrigin: 'center', clearProps: 'all' }, 0.3 + i * 0.05); });
    root.querySelectorAll('[data-seg]').forEach(function (n, i) {
      var da = n.getAttribute('stroke-dasharray'), parts = da.split(' ');
      tl.fromTo(n, { strokeDasharray: '0.01 ' + parts[1] }, { strokeDasharray: da, clearProps: 'strokeDasharray' }, i * 0.08);
    });
    root.querySelectorAll('[data-count-to]').forEach(function (n) {
      var raw = n.getAttribute('data-count-to'), to = parseFloat(raw); if (isNaN(to)) return;
      var dec = (raw.split('.')[1] || '').length, o = { v: 0 }; n.textContent = fmt(0, dec);
      tl.to(o, { v: to, onUpdate: function () { n.textContent = fmt(o.v, dec); }, onComplete: function () { n.textContent = fmt(to, dec); } }, 0);
    });
    root.__tl = tl;
    ST.create({ trigger: root, start: 'top 88%', once: true, onEnter: function () { tl.play(); } });
  });
  /* Safety net: whatever is in view a few seconds after load is shown in its final state. */
  window.addEventListener('load', function () {
    setTimeout(function () {
      document.querySelectorAll('.wugweb-chart').forEach(function (r) {
        var b = r.getBoundingClientRect();
        if (r.__tl && r.__tl.progress() === 0 && b.top < window.innerHeight && b.bottom > 0) r.__tl.play();
      });
    }, 3000);
  });
})();
