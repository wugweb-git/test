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
    // One read pass, then one write pass, once per frame; panels whose progress has not moved are skipped.
    const last = inners.map(() => -1);
    let queued = false;
    const frame = () => {
      queued = false;
      const vh = window.innerHeight;
      const ps = [];
      for (let i = 0; i < panels.length - 1; i++) {
        ps[i] = Math.min(1, Math.max(0, (vh - panels[i + 1].getBoundingClientRect().top) / vh));
      }
      for (let i = 0; i < ps.length; i++) {
        const inner = inners[i]; if (!inner) continue;
        const q = Math.round(ps[i] * 400);
        if (q === last[i]) continue;
        last[i] = q;
        inner.style.transform = 'scale(' + (1 - 0.06 * ps[i]).toFixed(4) + ')';
        inner.style.opacity = (1 - 0.7 * ps[i]).toFixed(3);
      }
    };
    const update = () => { if (!queued) { queued = true; requestAnimationFrame(frame); } };
    frame();
    window.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update);
  }

})();
