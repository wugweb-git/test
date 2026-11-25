(function(){

  // ?topic=<slug> selects a topic on load and is kept in the URL (replaceState),
  // so a filtered view can be linked and survives reload.
  const PAGE = 12; let shown = PAGE;
  const more = document.querySelector('.blog-more');
  const moreBtn = document.querySelector('.blog-more-btn');
  const count = document.querySelector('.blog-count');
  function paginate() {
    const root = document.querySelector('.wugweb-blog-index');
    const filtered = !!root?.classList.contains('is-filtered');
    // Posts already shown in the showcase blocks are listed in the grid too, but hidden until a filter/search is active.
    const items = [...document.querySelectorAll('.blog-grid > li')].filter((li) => !li.classList.contains('hidden') && (filtered || !li.dataset.dup));
    items.forEach((li, i) => li.classList.toggle('is-later', i >= shown));
    if (!more) return;
    more.hidden = items.length <= shown;
    if (count) count.textContent = 'Showing ' + Math.min(shown, items.length) + ' of ' + items.length;
  }
  if (moreBtn) moreBtn.addEventListener('click', () => { shown += PAGE; paginate(); });
  const chips = [...document.querySelectorAll('.filter-chip')];
  function applyTopic(topic) {
    const chip = chips.find((c) => c.dataset.topic === topic) ?? chips[0];
    const active = chip?.dataset.topic ?? '';
    chips.forEach((c) => c.classList.toggle('active', c === chip));
    chips.forEach((c) => c.setAttribute('aria-pressed', String(c === chip)));
    // The featured post is outside the grid; hide it too when its topic doesn't match.
    const q = (document.getElementById('blog-search')?.value || '').trim().toLowerCase();
    document.querySelectorAll('.blog-grid > li').forEach((el) => {
      const topicOk = !active || el.dataset.topic === active;
      const textOk = !q || el.textContent.toLowerCase().includes(q);
      el.classList.toggle('hidden', !(topicOk && textOk));
    });
    // While a topic or search is active the showcase blocks (featured, strip, picks, case studies) step aside; the grid then lists every match.
    document.querySelector('.wugweb-blog-index')?.classList.toggle('is-filtered', !!active || !!q);
    shown = PAGE; paginate();
    const url = new URL(location.href);
    if (active) url.searchParams.set('topic', active); else url.searchParams.delete('topic');
    history.replaceState(null, '', url);
  }
  document.getElementById('blog-search')?.addEventListener('input', () => applyTopic(new URLSearchParams(location.search).get('topic') ?? ''));
  chips.forEach((chip) => chip.addEventListener('click', () => applyTopic(chip.dataset.topic ?? '')));
  applyTopic(new URLSearchParams(location.search).get('topic') ?? '');

})();
