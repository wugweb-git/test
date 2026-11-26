(function () {
  // Case-study layout v2, media blocks. Ports of src/lib/case-rail.ts, src/lib/case-lightbox.ts and the chips script in CaseScreens.astro.
  // Vanilla JS, no autoplay offscreen, no sound, no auto-advance. Loaded only on a page that has a rail, All screens, a wall, a loop or a scrollshot.
  var root = document.querySelector('.case-page');
  if (!root) return;
  var reduce = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)') : { matches: false };

  // ---------- Rail: W3C APG carousel. Buttons + live counter + keys; scroll-snap does the moving. ----------
  function initRail(el) {
    var track = el.querySelector('[data-rail-track]');
    var prev = el.querySelector('[data-rail-prev]');
    var next = el.querySelector('[data-rail-next]');
    var counter = el.querySelector('[data-rail-counter]');
    if (!track || !prev || !next) return;
    var slides = Array.prototype.slice.call(track.children);
    var total = slides.length;
    if (!total) return;
    if (total === 1) { var ctl = el.querySelector('.case-rail-controls'); if (ctl) ctl.hidden = true; }
    var current = 0;
    function update(i) {
      current = Math.max(0, Math.min(total - 1, i));
      if (counter) counter.textContent = (current + 1) + ' of ' + total;
      prev.disabled = current === 0;
      next.disabled = current === total - 1;
    }
    function go(i) {
      var t = slides[Math.max(0, Math.min(total - 1, i))];
      if (!t) return;
      var left = track.scrollLeft + (t.getBoundingClientRect().left - track.getBoundingClientRect().left);
      track.scrollTo({ left: left, behavior: reduce.matches ? 'auto' : 'smooth' });
      update(i);
    }
    prev.addEventListener('click', function () { go(current - 1); });
    next.addEventListener('click', function () { go(current + 1); });
    el.addEventListener('keydown', function (e) {
      if (e.altKey || e.ctrlKey || e.metaKey) return;
      if (e.key === 'ArrowLeft') { e.preventDefault(); go(current - 1); }
      else if (e.key === 'ArrowRight') { e.preventDefault(); go(current + 1); }
      else if (e.key === 'Home') { e.preventDefault(); go(0); }
      else if (e.key === 'End') { e.preventDefault(); go(total - 1); }
    });
    // A flick, a key and a button all keep the counter right.
    if ('IntersectionObserver' in window) {
      var io = new IntersectionObserver(function (entries) {
        var vis = entries.filter(function (x) { return x.isIntersecting; }).map(function (x) { return slides.indexOf(x.target); }).filter(function (x) { return x >= 0; }).sort(function (a, b) { return a - b; });
        if (vis.length) update(vis[0]);
      }, { root: track, threshold: 0.6 });
      slides.forEach(function (s) { io.observe(s); });
    }
    if (!el.hasAttribute('tabindex')) el.setAttribute('tabindex', '0');
    update(0);
  }
  root.querySelectorAll('[data-case-rail]').forEach(initRail);

  // ---------- Loops: nothing is requested or played until the loop is on screen; paused when it leaves or under reduced motion. ----------
  var loops = Array.prototype.slice.call(root.querySelectorAll('[data-case-loop]'));
  function loadLoop(v) { if (!v.getAttribute('src') && v.dataset.src) { v.preload = 'auto'; v.src = v.dataset.src; } }
  function stillFrame(v) {
    // Reduced motion with no poster: fetch just enough to show the first frame, never play.
    if (!v.getAttribute('poster') && !v.getAttribute('src') && v.dataset.src) { v.preload = 'metadata'; v.src = v.dataset.src + '#t=0.1'; }
  }
  function playLoop(v) { loadLoop(v); v.muted = true; var p = v.play(); if (p && p.catch) p.catch(function () {}); }
  if (loops.length) {
    var lio = 'IntersectionObserver' in window ? new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        var v = en.target;
        if (reduce.matches) { v.pause(); return; }
        if (en.isIntersecting && !document.hidden) playLoop(v); else v.pause();
      });
    }, { threshold: 0.25 }) : null;
    loops.forEach(function (v) {
      v.muted = true;
      v.addEventListener('error', function () { var f = v.closest('.case-loop'); if (f) f.hidden = true; });
      if (reduce.matches) stillFrame(v);
      if (lio) lio.observe(v);
    });
    document.addEventListener('visibilitychange', function () { if (document.hidden) loops.forEach(function (v) { v.pause(); }); });
    var onMotion = function () { loops.forEach(function (v) { if (reduce.matches) { v.pause(); stillFrame(v); } }); };
    if (reduce.addEventListener) reduce.addEventListener('change', onMotion); else if (reduce.addListener) reduce.addListener(onMotion);
  }

  // ---------- A thumbnail or tile that 404s drops out instead of showing a broken icon. ----------
  root.addEventListener('error', function (e) {
    var t = e.target;
    if (!t || t.tagName !== 'IMG' || !/case-(split|mosaic|wall)-media/.test(t.className)) return;
    var box = t.closest('.case-split-item, .case-mosaic-item, .case-wall-item');
    if (box) box.hidden = true;
  }, true);

  // ---------- All screens: set chips, one set at a time. ----------
  root.querySelectorAll('[data-case-screens]').forEach(function (sc) {
    var row = sc.querySelector('[data-screens-chips]');
    var chips = Array.prototype.slice.call(sc.querySelectorAll('[data-screens-chip]'));
    var sets = Array.prototype.slice.call(sc.querySelectorAll('[data-screens-set]'));
    if (!row || chips.length < 2) return;
    function select(id) {
      chips.forEach(function (c) { c.setAttribute('aria-pressed', c.dataset.screensChip === id ? 'true' : 'false'); });
      sets.forEach(function (s) { s.hidden = s.id !== id; });
    }
    row.hidden = false;
    sc.dataset.enhanced = 'true';
    select(chips[0].dataset.screensChip || sets[0].id);
    chips.forEach(function (c) { c.addEventListener('click', function () { select(c.dataset.screensChip || ''); }); });
    // A deep link to a screen in a set that is not showing selects that set first.
    sc.addEventListener('case-screens:reveal', function (ev) {
      var s = ev.target.closest ? ev.target.closest('[data-screens-set]') : null;
      if (s) select(s.id);
    });
  });

  // ---------- Viewer: native <dialog>, one screen at a time, position as "3 of 8, Member". ----------
  var triggers = Array.prototype.slice.call(root.querySelectorAll('[data-case-lightbox-src]'));
  if (!triggers.length || typeof HTMLDialogElement === 'undefined') return;
  var items = triggers.map(function (el) {
    return { el: el, src: el.dataset.caseLightboxSrc || '', caption: el.dataset.caseLightboxCaption || '', alt: el.dataset.caseLightboxAlt || el.getAttribute('aria-label') || '', set: el.dataset.caseLightboxSet || '', group: el.dataset.caseLightboxGroup || el.dataset.caseLightboxSet || '' };
  });
  var dialog = document.createElement('dialog');
  dialog.id = 'case-lightbox-dialog';
  dialog.className = 'case-lightbox';
  dialog.setAttribute('aria-label', 'Screen viewer');
  dialog.innerHTML = '<button type="button" class="case-lightbox-btn case-lightbox-close" aria-label="Close viewer">&times;</button>'
    + '<button type="button" class="case-lightbox-btn case-lightbox-prev" aria-label="Previous screen">&larr;</button>'
    + '<button type="button" class="case-lightbox-btn case-lightbox-next" aria-label="Next screen">&rarr;</button>'
    + '<figure class="case-lightbox-stage"><img class="case-lightbox-image" alt=""><figcaption class="case-lightbox-caption"></figcaption></figure>'
    + '<p class="case-lightbox-counter" aria-live="polite" aria-atomic="true"></p>';
  document.body.appendChild(dialog);
  var img = dialog.querySelector('.case-lightbox-image');
  var cap = dialog.querySelector('.case-lightbox-caption');
  var counter = dialog.querySelector('.case-lightbox-counter');
  var closeBtn = dialog.querySelector('.case-lightbox-close');
  var prevBtn = dialog.querySelector('.case-lightbox-prev');
  var nextBtn = dialog.querySelector('.case-lightbox-next');
  var group = [], cur = 0, opener = null, pushed = false;

  function setHash(it, push) {
    var url = it.el.id ? '#' + it.el.id : location.pathname + location.search;
    try { if (push) history.pushState({ lightbox: true }, '', url); else history.replaceState(history.state, '', url); } catch (e) {}
  }
  function render(i) {
    cur = Math.max(0, Math.min(group.length - 1, i));
    var it = group[cur];
    img.src = it.src; img.alt = it.alt;
    cap.textContent = it.caption; cap.hidden = !it.caption;
    counter.textContent = (cur + 1) + ' of ' + group.length + (it.set ? ', ' + it.set : '');
    prevBtn.disabled = cur === 0;
    nextBtn.disabled = cur === group.length - 1;
  }
  function go(i) { render(i); setHash(group[cur], false); }
  function open(item, fromHash) {
    group = items.filter(function (x) { return x.group === item.group; });
    opener = item.el;
    render(group.indexOf(item));
    if (dialog.open) return;
    dialog.showModal();
    if (fromHash) { pushed = false; } else { setHash(item, true); pushed = true; }
  }
  function finish() {
    if (dialog.open) dialog.close();
    img.removeAttribute('src');
    if (opener && document.contains(opener)) opener.focus({ preventScroll: true });
    opener = null;
  }
  function close() {
    if (!dialog.open) return;
    if (pushed) { pushed = false; history.back(); return; } // popstate below finishes the close
    try { history.replaceState(history.state, '', location.pathname + location.search); } catch (e) {}
    finish();
  }
  closeBtn.addEventListener('click', close);
  prevBtn.addEventListener('click', function () { go(cur - 1); });
  nextBtn.addEventListener('click', function () { go(cur + 1); });
  dialog.addEventListener('cancel', function (ev) { ev.preventDefault(); close(); });
  dialog.addEventListener('click', function (ev) { if (ev.target === dialog) close(); });
  window.addEventListener('popstate', function () { pushed = false; if (dialog.open) finish(); });
  dialog.addEventListener('keydown', function (e) {
    if (e.key === 'ArrowLeft') { e.preventDefault(); go(cur - 1); }
    else if (e.key === 'ArrowRight') { e.preventDefault(); go(cur + 1); }
  });
  // Swipe: one finger, mostly sideways. Two fingers are left to pinch zoom.
  var startX = null;
  dialog.addEventListener('touchstart', function (e) { startX = e.touches.length === 1 ? e.touches[0].clientX : null; }, { passive: true });
  dialog.addEventListener('touchend', function (e) {
    if (startX === null) return;
    var dx = (e.changedTouches[0] ? e.changedTouches[0].clientX : startX) - startX;
    startX = null;
    if (Math.abs(dx) < 40) return;
    go(dx > 0 ? cur - 1 : cur + 1);
  }, { passive: true });
  items.forEach(function (item) {
    item.el.addEventListener('click', function (ev) {
      if (ev.metaKey || ev.ctrlKey || ev.shiftKey || ev.altKey || ev.button !== 0) return; // a modified click keeps the browser's own behaviour
      ev.preventDefault();
      open(item, false);
    });
  });
  // Deep link: #<thumbnail id> opens the viewer at that screen.
  function openFromHash() {
    var wanted = location.hash.slice(1);
    if (!wanted || dialog.open) return;
    var hit = items.filter(function (x) { return x.el.id === wanted; })[0];
    if (!hit) return;
    hit.el.dispatchEvent(new CustomEvent('case-screens:reveal', { bubbles: true }));
    open(hit, true);
  }
  window.addEventListener('hashchange', openFromHash);
  openFromHash();
})();
