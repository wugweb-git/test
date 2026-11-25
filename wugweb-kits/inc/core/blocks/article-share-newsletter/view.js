document.querySelectorAll('[data-copy]').forEach(function (el) {
  el.addEventListener('click', function (e) {
    e.preventDefault();
    var url = el.getAttribute('data-copy') || '';
    if (!navigator.clipboard) return;
    navigator.clipboard.writeText(url).then(function () {
      el.textContent = '✓';
      setTimeout(function () { el.textContent = '#'; }, 1200);
    }).catch(function () {});
  });
});
