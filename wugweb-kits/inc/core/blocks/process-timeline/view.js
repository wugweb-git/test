/* Behaviour for process-timeline: approach card toggle (from services/[slug].astro)
   + the 'cardstack' deal-out (from scroll-animations.ts). */
(function () {
  function initApproachCards() {
    var cards = Array.prototype.slice.call(document.querySelectorAll('[data-approach-card]'));
    if (!cards.length) return;
    function set(card, open) {
      card.classList.toggle('is-open', open);
      var t = card.querySelector('[data-approach-toggle]');
      if (t) t.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
    // Exclusive: opening one closes the rest; one card is always expanded.
    function open(card) { cards.forEach(function (c) { set(c, c === card); }); }
    cards.forEach(function (card) {
      if (card.dataset.bound === '1') return;
      card.dataset.bound = '1';
      card.addEventListener('click', function (e) {
        if (e.target.closest('[data-approach-toggle]')) return;
        open(card);
      });
      var t = card.querySelector('[data-approach-toggle]');
      if (t) t.addEventListener('click', function () { open(card); });
    });
  }

  function initCardStack() {
    if (!window.gsap || !window.ScrollTrigger) return;
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    window.gsap.registerPlugin(window.ScrollTrigger);
    var gsap = window.gsap;
    document.querySelectorAll('.approach-cards[data-scroll="cardstack"]').forEach(function (el) {
      if (el.dataset.cardstackBound === '1') return;
      var cards = Array.prototype.slice.call(el.querySelectorAll('[data-stack-card]'));
      if (cards.length < 2) return;
      el.dataset.cardstackBound = '1';
      var base = cards[0].offsetTop;
      var tl = gsap.timeline({
        scrollTrigger: { trigger: el, start: 'top 85%', end: 'bottom 65%', scrub: 0.5 }
      });
      var n = cards.length - 1;
      var SETTLE = 0.55;
      var STEP = n > 1 ? (1 - SETTLE) / (n - 1) : 0;
      cards.forEach(function (card, i) {
        if (i === 0) return;
        var collapse = base - card.offsetTop - i * 12;
        tl.fromTo(
          card,
          { y: collapse, opacity: Math.max(0.22, 1 - 0.26 * i) },
          { y: 0, opacity: 1, ease: 'power1.out', duration: SETTLE },
          (i - 1) * STEP
        );
      });
    });
  }

  function init() { initApproachCards(); initCardStack(); }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();
