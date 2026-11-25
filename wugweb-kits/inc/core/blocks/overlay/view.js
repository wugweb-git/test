(function () {
  'use strict';
  var locked = 0, savedY = 0, reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  function lock() { if (locked++) return; savedY = window.scrollY; document.body.style.top = -savedY + 'px'; document.body.classList.add('wugweb-overlay-locked'); }
  function unlock() { if (--locked > 0) return; locked = 0; document.body.classList.remove('wugweb-overlay-locked'); document.body.style.top = ''; window.scrollTo(0, savedY); }
  function open(d, opener) {
    if (d.open) return;
    d.__opener = opener || document.activeElement;
    var banner = d.dataset.layout === 'banner-bottom';
    if (banner) d.show(); else { d.showModal(); lock(); d.__locked = true; }
    requestAnimationFrame(function () { requestAnimationFrame(function () { d.classList.add('is-open'); }); });
    var f = d.querySelector('[autofocus],.wugweb-overlay__body a,.wugweb-overlay__body button,.wugweb-overlay__body input,[data-overlay-close]'); if (f && !banner) f.focus();
  }
  function close(d) {
    if (!d.open) return;
    d.classList.remove('is-open');
    var done = function () {
      if (!d.open) return;
      d.close();
      if (d.__locked) { d.__locked = false; unlock(); }
      var o = d.__opener; if (o && o.focus && document.contains(o)) o.focus();
    };
    if (reduced) done(); else setTimeout(done, 400);
  }
  document.addEventListener('click', function (e) {
    var t = e.target.closest && e.target.closest('[data-overlay-target],a[href^="#"]');
    if (t) {
      var id = t.getAttribute('data-overlay-target') || (t.getAttribute('href') || '').slice(1);
      var d = id && document.getElementById(id);
      if (d && d.matches && d.matches('dialog[data-overlay]')) { e.preventDefault(); open(d, t); return; }
    }
    var c = e.target.closest && e.target.closest('[data-overlay-close]');
    if (c) { var dd = c.closest('dialog[data-overlay]'); if (dd) close(dd); return; }
    var dlg = e.target.matches && e.target.matches('dialog[data-overlay][data-backdrop-close]') ? e.target : null;
    if (dlg && dlg.dataset.layout !== 'banner-bottom') close(dlg);
  });
  document.addEventListener('cancel', function (e) { var d = e.target; if (d && d.matches && d.matches('dialog[data-overlay]')) { e.preventDefault(); close(d); } }, true);
  function init() {
    document.querySelectorAll('dialog[data-overlay][data-open-on-load]').forEach(function (d) { open(d); });
    if (location.hash) { var d = document.getElementById(location.hash.slice(1)); if (d && d.matches && d.matches('dialog[data-overlay]')) open(d); }
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
