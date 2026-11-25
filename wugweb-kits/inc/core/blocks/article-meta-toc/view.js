(function () {
  // Give article h2/h3 headings an id matching the TOC (slugify of their text),
  // unless they already carry one.
  function slugify(t) { return t.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, ''); }
  var toc = document.querySelector('.article-toc');
  if (!toc) return;
  var scope = document.querySelector('main') || document.body;
  scope.querySelectorAll('h2, h3').forEach(function (h) {
    if (h.id || toc.contains(h)) return;
    var id = slugify(h.textContent || '');
    if (id && !document.getElementById(id)) h.id = id;
  });
})();
