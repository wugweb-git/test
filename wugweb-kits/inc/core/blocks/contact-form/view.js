/* Contact form: stamps the load time (bot check), shows clear field errors before sending, stops double sends. */
(function () {
  var f = document.querySelector('[data-contact-form]');
  if (!f) return;
  var ts = f.querySelector('[data-ts]'); if (ts) ts.value = String(Date.now());
  var alertBox = document.querySelector('[data-form-alert]'); if (alertBox) alertBox.focus();
  var MIN = 20;
  var rules = [
    ['cf-name', function (v) { return v.trim() ? '' : 'Enter your name.'; }],
    ['cf-email', function (v) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim()) ? '' : 'Enter a work email, like name@company.com.'; }],
    ['cf-topic', function (v) { return v.trim().length >= MIN ? '' : 'Tell us a little more: at least ' + MIN + ' characters.'; }]
  ];
  function show(el, msg) {
    var id = 'err-' + el.name, old = document.getElementById(id);
    if (old) old.remove();
    if (!msg) { el.removeAttribute('aria-invalid'); el.removeAttribute('aria-describedby'); return; }
    var p = document.createElement('p'); p.className = 'field-error'; p.id = id; p.textContent = msg;
    el.parentNode.appendChild(p); el.setAttribute('aria-invalid', 'true'); el.setAttribute('aria-describedby', id);
  }
  rules.forEach(function (r) { var el = document.getElementById(r[0]); if (el) el.addEventListener('blur', function () { show(el, r[1](el.value)); }); });
  f.addEventListener('submit', function (e) {
    var first = null;
    rules.forEach(function (r) { var el = document.getElementById(r[0]); if (!el) return; var m = r[1](el.value); show(el, m); if (m && !first) first = el; });
    if (first) { e.preventDefault(); first.focus(); return; }
    var b = f.querySelector('.submit-btn'); if (b) { b.disabled = true; b.querySelector('span').textContent = 'Sending…'; }
  });
})();
