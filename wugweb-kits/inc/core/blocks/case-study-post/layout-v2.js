(function () {
  // Case-study layout v2: video play/pause toggle (port of CaseVideo.astro) and removal of files that fail to load.
  // No autoplay, no sound. Loaded only when layout v2 is active.
  var root = document.querySelector('.case-page');
  if (!root) return;

  root.querySelectorAll('[data-case-video]').forEach(function (fig) {
    var video = fig.querySelector('video');
    var button = fig.querySelector('[data-video-toggle]');
    if (!video || !button || button.dataset.wired) return;
    button.dataset.wired = 'true';
    var icon = button.querySelector('.case-video-icon');
    var label = video.getAttribute('aria-label') || 'video';
    function sync() {
      var playing = !video.paused && !video.ended;
      button.setAttribute('aria-pressed', playing ? 'true' : 'false');
      button.setAttribute('aria-label', (playing ? 'Pause' : 'Play') + ' video: ' + label);
      if (icon) icon.textContent = playing ? '❚❚' : '▶';
    }
    video.muted = true;
    button.hidden = false;
    button.addEventListener('click', function () {
      video.muted = true;
      if (video.paused || video.ended) { var p = video.play(); if (p && p.catch) p.catch(function () {}); } else { video.pause(); }
    });
    video.addEventListener('click', function () { button.click(); });
    ['play', 'pause', 'ended'].forEach(function (ev) { video.addEventListener(ev, sync); });
    video.addEventListener('volumechange', function () { video.muted = true; });
    // The box is 16/9 until the file reports its own shape, then it takes it (no crop, no later jump).
    video.addEventListener('loadedmetadata', function () {
      if (video.videoWidth && video.videoHeight) video.style.aspectRatio = video.videoWidth + ' / ' + video.videoHeight;
    });
    video.addEventListener('error', function () { fig.hidden = true; });
    sync();
  });

  // Missing stays missing: an image that 404s drops out (its pair partner or figure box with it) instead of showing a broken icon.
  function drop(img) {
    var box = img.closest('.case-figure, .case-pair-item');
    if (box) box.hidden = true;
  }
  root.querySelectorAll('.case-figure-media, .case-pair-media').forEach(function (img) {
    if (img.complete && img.naturalWidth === 0 && img.getAttribute('src')) drop(img);
  });
  root.addEventListener('error', function (e) {
    if (e.target && e.target.tagName === 'IMG' && /case-(figure|pair)-media/.test(e.target.className)) drop(e.target);
  }, true);
})();
