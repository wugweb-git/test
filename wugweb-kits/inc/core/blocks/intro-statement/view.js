document.querySelectorAll('[data-venn]').forEach(function (v) {
  var st = v.querySelector('.venn-stage'), cs = v.querySelectorAll('[data-venn-c]');
  var calm = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (!('IntersectionObserver' in window)) { v.classList.add('is-in'); } else {
    var io = new IntersectionObserver(function (e) { if (e[0].isIntersecting) { v.classList.add('is-in'); io.disconnect(); } }, { threshold: 0.35 });
    io.observe(v);
  }
  cs.forEach(function (c, i) {
    var on = function () { v.setAttribute('data-active', String(i + 1)); };
    c.addEventListener('mouseenter', on);
    c.addEventListener('click', function () { v.getAttribute('data-active') === String(i + 1) ? v.removeAttribute('data-active') : on(); });
  });
  v.addEventListener('mouseleave', function () { v.removeAttribute('data-active'); });
  if (calm) return;
  var raf = 0;
  v.addEventListener('pointermove', function (e) {
    if (e.pointerType === 'touch') return;
    var r = st.getBoundingClientRect(), x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5;
    cancelAnimationFrame(raf);
    raf = requestAnimationFrame(function () { st.style.setProperty('--px', (x * 36).toFixed(1)); st.style.setProperty('--py', (y * 36).toFixed(1)); });
  });
  v.addEventListener('pointerleave', function () { st.style.setProperty('--px', 0); st.style.setProperty('--py', 0); });
});
