


function initFaq() {
  document.querySelectorAll('[data-faq]').forEach((list) => {
    if (list.dataset.faqBound === '1') return;
    list.dataset.faqBound = '1';

    const cards = Array.from(list.querySelectorAll('.faq-card'));
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const close = (card, animate) => {
      const panel = card.querySelector('.faq-panel');
      const btn = card.querySelector('.faq-trigger');
      if (!panel || !btn || !card.classList.contains('is-open')) return;
      card.classList.remove('is-open');
      btn.setAttribute('aria-expanded', 'false');
      if (!animate) { panel.hidden = true; panel.style.height = ''; return; }
      panel.style.height = `${panel.scrollHeight}px`;
      // Force a frame so the browser has a start value to animate from.
      void panel.offsetHeight;
      panel.style.height = '0px';
      panel.addEventListener('transitionend', function done() {
        panel.removeEventListener('transitionend', done);
        panel.hidden = true;
        panel.style.height = '';
      }, { once: true });
    };

    const open = (card, animate) => {
      const panel = card.querySelector('.faq-panel');
      const btn = card.querySelector('.faq-trigger');
      if (!panel || !btn) return;
      card.classList.add('is-open');
      btn.setAttribute('aria-expanded', 'true');
      panel.hidden = false;
      if (!animate) { panel.style.height = ''; return; }
      panel.style.height = '0px';
      void panel.offsetHeight;
      panel.style.height = `${panel.scrollHeight}px`;
      panel.addEventListener('transitionend', function done() {
        panel.removeEventListener('transitionend', done);
        // Release to auto so reflowing text stays correct.
        panel.style.height = '';
      }, { once: true });
    };

    cards.forEach((card) => {
      const panel = card.querySelector('.faq-panel');
      const btn = card.querySelector('.faq-trigger');
      if (!panel || !btn) return;

      if (!reduced) {
        panel.style.overflow = 'hidden';
        panel.style.transition = 'height 240ms cubic-bezier(0.16, 1, 0.3, 1)';
      }
      if (btn.getAttribute('aria-expanded') === 'true') card.classList.add('is-open');

      btn.addEventListener('click', () => {
        const isOpen = card.classList.contains('is-open');
        // Exclusive: opening one closes the rest.
        cards.forEach((c) => c !== card && close(c, !reduced));
        if (isOpen) close(card, !reduced);
        else open(card, !reduced);
      });
    });
  });
}

initFaq();
