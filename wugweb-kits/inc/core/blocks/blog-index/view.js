(function(){

  // ?topic=<slug> selects a topic on load and is kept in the URL (replaceState),
  // so a filtered view can be linked and survives reload.
  const chips = [...document.querySelectorAll('.filter-chip')];
  function applyTopic(topic) {
    const chip = chips.find((c) => c.dataset.topic === topic) ?? chips[0];
    const active = chip?.dataset.topic ?? '';
    chips.forEach((c) => c.classList.toggle('active', c === chip));
    chips.forEach((c) => c.setAttribute('aria-pressed', String(c === chip)));
    // The featured post is outside the grid; hide it too when its topic doesn't match.
    document.querySelectorAll('.blog-grid li, .blog-featured').forEach((el) => {
      el.classList.toggle('hidden', !!active && el.dataset.topic !== active);
    });
    const url = new URL(location.href);
    if (active) url.searchParams.set('topic', active); else url.searchParams.delete('topic');
    history.replaceState(null, '', url);
  }
  chips.forEach((chip) => chip.addEventListener('click', () => applyTopic(chip.dataset.topic ?? '')));
  applyTopic(new URLSearchParams(location.search).get('topic') ?? '');

})();
