if (window.gsap && window.Draggable) { /* draggable-tags.ts (wugweb-motion.js) owns the field */ } else {
document.querySelectorAll('[data-tag-field]').forEach(function (field) {
  var tags = field.querySelectorAll('[data-tag]');
  tags.forEach(function (tag) {
    var face = tag.querySelector('.tag-face'), vid = tag.querySelector('video');
    face.addEventListener('click', function () {
      var open = !tag.classList.contains('is-open');
      tags.forEach(function (t) { t.classList.remove('is-open'); t.querySelector('.tag-face').setAttribute('aria-expanded', 'false'); });
      tag.classList.toggle('is-open', open); face.setAttribute('aria-expanded', String(open));
      if (vid) { if (open) { if (!vid.src) vid.src = vid.dataset.src; vid.play().catch(function () {}); } else vid.pause(); }
    });
  });
});
}
