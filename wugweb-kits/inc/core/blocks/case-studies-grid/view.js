document.querySelectorAll('.work-grid video[data-src]').forEach(function (v) {
  var io = new IntersectionObserver(function (es) { es.forEach(function (e) { if (e.isIntersecting) { v.src = v.dataset.src; v.play().catch(function () {}); io.disconnect(); } }); }, { rootMargin: '200px' });
  io.observe(v);
});
document.querySelectorAll('.work-grid a').forEach(function (card) {
  card.addEventListener('mousemove', function (e) {
    var r = card.getBoundingClientRect(), x = e.clientX - r.left, y = e.clientY - r.top;
    card.style.transform = 'perspective(800px) rotateX(' + (y - r.height / 2) / 20 + 'deg) rotateY(' + (r.width / 2 - x) / 20 + 'deg) scale(1.02)';
  });
  card.addEventListener('mouseleave', function () { card.style.transform = 'perspective(800px) rotateX(0deg) rotateY(0deg) scale(1)'; });
});
