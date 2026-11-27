document.querySelectorAll('.work-grid video[data-src]').forEach(function (v) {
  if (v.dataset.lb==='1') return; v.dataset.lb='1';
  var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { v.src = v.dataset.src; v.play().catch(function () {}); io.disconnect(); } }); }, { rootMargin: '200px' });
  io.observe(v);
});
