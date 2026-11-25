/* Behaviour for framework-loop — port of src/lib/process-map.ts.
   Hover/spring work without GSAP; the scroll phase-walk needs window.gsap + ScrollTrigger. */
(function () {
  function initProcessMap() {
    document.querySelectorAll('[data-process-map]').forEach(function (panel) {
      var fig = panel.querySelector('.pm-figure');
      if (!fig || fig.dataset.pmBound === '1') return;
      fig.dataset.pmBound = '1';

      var marked = Array.prototype.slice.call(fig.querySelectorAll('[data-pm-phase]'));
      var seen = {};
      marked.forEach(function (el) { seen[el.dataset.pmPhase] = 1; });
      var phaseCount = Object.keys(seen).length;
      if (phaseCount === 0) return;

      var scrollPhase = -1, hoverPhase = -1;

      function paint() {
        var active = hoverPhase >= 0 ? hoverPhase : scrollPhase;
        fig.classList.toggle('is-focused', active >= 0);
        marked.forEach(function (el) { el.classList.toggle('is-lit', Number(el.dataset.pmPhase) === active); });
        fig.querySelectorAll('[data-pm-edge]').forEach(function (el) {
          var i = Number(el.dataset.pmOrder);
          el.classList.toggle('is-lit', active >= 0 && (i === active || i + 1 === active));
        });
      }
      function setHover(p) { if (hoverPhase === p) return; hoverPhase = p; paint(); }

      marked.forEach(function (el) {
        var p = Number(el.dataset.pmPhase);
        el.addEventListener('pointerenter', function () { setHover(p); });
        el.addEventListener('focusin', function () { setHover(p); });
      });
      fig.addEventListener('pointerleave', function () { setHover(-1); });
      fig.addEventListener('focusout', function (e) { if (!fig.contains(e.relatedTarget)) setHover(-1); });

      var edgeEls = Array.prototype.slice.call(fig.querySelectorAll('[data-pm-edge]'));
      var sensitivity = Number(fig.dataset.pmSensitivity != null ? fig.dataset.pmSensitivity : 0.55);
      var k = Number(fig.dataset.pmReturn != null ? fig.dataset.pmReturn : 0.1);
      var critical = 2 * Math.sqrt(k);
      var c = critical * (1 - Math.min(0.97, Math.max(0, Number(fig.dataset.pmOscillation != null ? fig.dataset.pmOscillation : 0.78))) * 0.9);

      if (edgeEls.length > 0 && sensitivity > 0) {
        var svg = fig.querySelector('svg');
        var springs = edgeEls.map(function (el) {
          var g = function (n) { return Number(el.dataset[n]); };
          return { el: el, x1: g('x1'), y1: g('y1'), x2: g('x2'), y2: g('y2'), cx: g('cx'), cy: g('cy'), offset: 0, vel: 0, target: 0 };
        });
        var REACH = 260;
        var pointerIn = false, raf = 0, idleFrames = 0;

        function toField(e) {
          var r = svg.getBoundingClientRect();
          var vb = svg.viewBox.baseVal;
          return { x: ((e.clientX - r.left) / r.width) * vb.width, y: ((e.clientY - r.top) / r.height) * vb.height };
        }
        function tick() {
          var moving = false;
          springs.forEach(function (sp) {
            var a = (sp.target - sp.offset) * k - sp.vel * c;
            sp.vel += a;
            sp.offset += sp.vel;
            if (Math.abs(sp.vel) > 0.02 || Math.abs(sp.target - sp.offset) > 0.02) moving = true;
            sp.el.setAttribute('d', 'M ' + sp.x1 + ' ' + sp.y1 + ' Q ' + sp.cx + ' ' + (sp.cy + sp.offset).toFixed(2) + ', ' + sp.x2 + ' ' + sp.y2);
          });
          idleFrames = moving || pointerIn ? 0 : idleFrames + 1;
          if (idleFrames > 30) { raf = 0; return; }
          raf = requestAnimationFrame(tick);
        }
        function kick() { if (!raf) raf = requestAnimationFrame(tick); }

        fig.addEventListener('pointermove', function (e) {
          pointerIn = true;
          var p = toField(e);
          springs.forEach(function (sp) {
            var mx = (sp.x1 + sp.x2) / 2;
            var falloff = Math.max(0, 1 - Math.abs(p.x - mx) / REACH);
            sp.target = (p.y - sp.cy) * sensitivity * falloff * falloff;
          });
          kick();
        });
        fig.addEventListener('pointerleave', function () {
          pointerIn = false;
          springs.forEach(function (sp) { sp.target = 0; });
          kick();
        });
      }

      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
      if (!window.gsap || !window.ScrollTrigger) return;
      window.gsap.registerPlugin(window.ScrollTrigger);
      var st = window.ScrollTrigger.create({
        trigger: panel,
        start: 'top 60%',
        end: 'bottom 40%',
        onUpdate: function (self) {
          var p = Math.min(phaseCount - 1, Math.floor(self.progress * phaseCount));
          if (p !== scrollPhase) { scrollPhase = p; paint(); }
        },
        onLeave: function () { scrollPhase = -1; paint(); },
        onLeaveBack: function () { scrollPhase = -1; paint(); }
      });
      fig.addEventListener('pm:destroy', function () { st.kill(); });
    });
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initProcessMap);
  else initProcessMap();
})();
