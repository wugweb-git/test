(function () {
  'use strict';
  var KEY = 'wugweb_cookie_consent_v1';
  var banner = document.getElementById('wugweb-cookie-banner');
  var modal = document.getElementById('wugweb-cookie-modal');
  if (!banner || !modal) return;
  var $ = function (id) { return document.getElementById(id); };
  var tf = $('cookie-toggle-functional'), ta = $('cookie-toggle-analytics');
  var DEF = { necessary: true, functional: false, analytics: false };
  function saved() { try { var s = localStorage.getItem(KEY); return s ? JSON.parse(s) : null; } catch (e) { return null; } }
  /* Consent switches analytics on: scripts held as type=text/plain[data-wugweb-consent=analytics] are released in order. */
  function release() {
    var held = document.querySelectorAll('script[data-wugweb-consent="analytics"]');
    var chain = Promise.resolve();
    held.forEach(function (old) {
      chain = chain.then(function () {
        return new Promise(function (res) {
          var s = document.createElement('script');
          Array.prototype.forEach.call(old.attributes, function (at) { if (at.name !== 'type' && at.name !== 'data-wugweb-consent') s.setAttribute(at.name, at.value); });
          if (old.src) { s.onload = s.onerror = function () { res(); }; s.src = old.getAttribute('src'); } else { s.text = old.text; }
          old.parentNode.replaceChild(s, old);
          if (!old.src) res();
        });
      });
    });
    window.dispatchEvent(new CustomEvent('wugweb-analytics-allowed'));
  }
  function apply(c) { if (c && c.analytics) release(); }
  var opener = null, scrollY = 0;
  var s0 = saved();
  if (!s0) banner.hidden = false; else apply(s0);
  function focusables() { return modal.querySelectorAll('button:not([disabled]),input:not([disabled]),a[href]'); }
  function open() {
    var c = saved() || DEF;
    if (tf) tf.checked = !!c.functional;
    if (ta) ta.checked = !!c.analytics;
    opener = document.activeElement;
    modal.hidden = false;
    var f = focusables(); if (f.length) f[0].focus();
  }
  function close() {
    modal.hidden = true;
    if (opener && opener.focus && document.contains(opener) && !opener.hidden) opener.focus();
    opener = null;
  }
  window.openWugwebCookieSettings = open;
  function store(c) { try { localStorage.setItem(KEY, JSON.stringify(c)); } catch (e) {} apply(c); banner.hidden = true; close(); }
  function acceptAll() { store({ necessary: true, functional: true, analytics: true }); }
  function rejectOptional() { store({ necessary: true, functional: false, analytics: false }); }
  function on(id, fn) { var el = $(id); if (el) el.addEventListener('click', fn); }
  on('cookie-btn-accept-all', acceptAll); on('cookie-modal-accept', acceptAll);
  on('cookie-btn-reject-optional', rejectOptional); on('cookie-modal-reject', rejectOptional);
  on('cookie-btn-manage', open); on('cookie-modal-close', close);
  on('cookie-modal-save', function () { store({ necessary: true, functional: tf ? tf.checked : false, analytics: ta ? ta.checked : false }); });
  modal.addEventListener('click', function (e) { if (e.target === modal) close(); });
  document.addEventListener('keydown', function (e) {
    if (modal.hidden) return;
    if (e.key === 'Escape') { e.preventDefault(); close(); return; }
    if (e.key === 'Tab') {
      var f = focusables(); if (!f.length) return;
      var first = f[0], last = f[f.length - 1];
      if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    }
  });
  /* Footer "Cookie Settings" action (SiteFooter #btn-footer-cookie-settings), plus any [data-cookie-settings] / #cookie-settings trigger. */
  document.addEventListener('click', function (e) {
    var t = e.target.closest && e.target.closest('#btn-footer-cookie-settings,[data-cookie-settings],a[href="#cookie-settings"],.footer-action-btn');
    if (!t) return;
    if (t.classList.contains('footer-action-btn') && !/cookie/i.test(t.textContent)) return;
    e.preventDefault(); open();
  });
})();
