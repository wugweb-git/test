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
