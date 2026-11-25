/* service-stack — verbatim behaviour from services/index.astro <script> (tabs + stacked panel scale). */
(function () {
  var panels = Array.from(document.querySelectorAll('.svc-panel'));
  if (!panels.length) return;

  
  panels.forEach((panel) => {
    const btns = Array.from(panel.querySelectorAll('[data-tier-btn]'));
    const tiers = Array.from(panel.querySelectorAll('[data-tier-panel]'));
    btns.forEach((btn) => {
      btn.addEventListener('click', () => {
        const tier = btn.dataset.tierBtn;
        btns.forEach((b) => { 
          b.classList.toggle('active', b === btn); 
          b.setAttribute('aria-selected', String(b === btn)); 
        });
        
        tiers.forEach((t) => { 
          if (t.dataset.tierPanel === tier) {
            t.removeAttribute('hidden');
            t.style.opacity = '0';
            t.style.transform = 'translateY(6px)';
            // force browser layout calculation to restart transition
            t.offsetHeight;
            t.style.opacity = '1';
            t.style.transform = 'translateY(0)';
          } else {
            t.setAttribute('hidden', '');
          }
        });
      });
    });
  });

  if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    const inners = panels.map((p) => p.querySelector('.svc-inner'));
    const update = () => {
      const vh = window.innerHeight;
      for (let i = 0; i < panels.length - 1; i++) {
        const inner = inners[i]; if (!inner) continue;
        const nextTop = panels[i + 1].getBoundingClientRect().top;
        const p = Math.min(1, Math.max(0, (vh - nextTop) / vh));
        inner.style.transform = 'scale(' + (1 - 0.06 * p).toFixed(4) + ')';
        inner.style.opacity = (1 - 0.7 * p).toFixed(3);
      }
    };
    update();
    window.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update);
  }

})();
