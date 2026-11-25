"use strict";
window.__bf = (() => {
  var __defProp = Object.defineProperty;
  var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
  var __getOwnPropNames = Object.getOwnPropertyNames;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
  var __export = (target, all) => {
    for (var name in all)
      __defProp(target, name, { get: all[name], enumerable: true });
  };
  var __copyProps = (to, from, except, desc) => {
    if (from && typeof from === "object" || typeof from === "function") {
      for (let key of __getOwnPropNames(from))
        if (!__hasOwnProp.call(to, key) && key !== except)
          __defProp(to, key, { get: () => from[key], enumerable: !(desc = __getOwnPropDesc(from, key)) || desc.enumerable });
    }
    return to;
  };
  var __toCommonJS = (mod) => __copyProps(__defProp({}, "__esModule", { value: true }), mod);
  var ball_field_exports = {};
  __export(ball_field_exports, {
    initBallField: () => initBallField
  });
  const DRIFT = 0.34;
  const WALL = 0.86;
  const DAMP = 0.992;
  const FLOOR_SPEED = 0.18;
  const MAX_SPEED = 26;
  const TILT_GAIN = 0.012;
  const SHAKE_GAIN = 0.11;
  function initBallField() {
    if (typeof window === "undefined") return () => {
    };
    const fields = Array.from(document.querySelectorAll("[data-ball-field]"));
    if (fields.length === 0) return () => {
    };
    const reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    const cleanups = [];
    fields.forEach((field) => {
      if (field.dataset.ballsBound === "1") return;
      field.dataset.ballsBound = "1";
      const els = Array.from(field.querySelectorAll("[data-ball]"));
      if (els.length === 0) return;
      let W = 0;
      let H = 0;
      let discs = [];
      let raf = 0;
      let held = null;
      let heldId = -1;
      let last = { x: 0, y: 0 };
      let dragged = false;
      const rand = (n) => {
        const s = Math.sin(n * 12.9898) * 43758.5453;
        return s - Math.floor(s);
      };
      const layout = () => {
        const box = field.getBoundingClientRect();
        W = box.width;
        H = box.height;
        const r = els[0].offsetWidth / 2;
        const step2 = W / (els.length + 1);
        discs = els.map((el, i) => {
          const a = rand(i + 1) * Math.PI * 2;
          return {
            el,
            r,
            x: Math.min(W - r, Math.max(r, step2 * (i + 1))),
            y: Math.min(H - r, Math.max(r, H * (0.55 + rand(i * 3 + 5) * 0.32))),
            vx: Math.cos(a) * DRIFT,
            vy: Math.sin(a) * DRIFT * 0.7
          };
        });
      };
      const draw = () => {
        for (const d of discs) {
          d.el.style.transform = `translate(${(d.x - d.r).toFixed(1)}px, ${(d.y - d.r).toFixed(1)}px)`;
        }
      };
      const step = () => {
        for (const d of discs) {
          if (d === held) continue;
          d.vx += tiltX;
          d.vy += tiltY;
          d.x += d.vx;
          d.y += d.vy;
          d.vx *= DAMP;
          d.vy *= DAMP;
          const sp = Math.hypot(d.vx, d.vy);
          if (sp < FLOOR_SPEED && sp > 1e-4) {
            d.vx = d.vx / sp * FLOOR_SPEED;
            d.vy = d.vy / sp * FLOOR_SPEED;
          }
          let hitX = false;
          let hitY = false;
          if (d.x - d.r <= 0) {
            d.x = d.r;
            d.vx = Math.abs(d.vx) * WALL;
            hitX = true;
          } else if (d.x + d.r >= W) {
            d.x = W - d.r;
            d.vx = -Math.abs(d.vx) * WALL;
            hitX = true;
          }
          if (d.y - d.r <= 0) {
            d.y = d.r;
            d.vy = Math.abs(d.vy) * WALL;
            hitY = true;
          } else if (d.y + d.r >= H) {
            d.y = H - d.r;
            d.vy = -Math.abs(d.vy) * WALL;
            hitY = true;
          }
          if ((hitX || hitY) && Math.hypot(d.vx, d.vy) > 1.6) squash(d, hitX ? "x" : "y");
        }
        for (let i = 0; i < discs.length; i++) {
          for (let j = i + 1; j < discs.length; j++) {
            const a = discs[i];
            const b = discs[j];
            const dx = b.x - a.x;
            const dy = b.y - a.y;
            const dist = Math.hypot(dx, dy) || 1;
            const overlap = a.r + b.r - dist;
            if (overlap <= 0) continue;
            const nx = dx / dist;
            const ny = dy / dist;
            const shift = overlap / (a === held || b === held ? 1 : 2);
            if (a !== held) {
              a.x -= nx * shift;
              a.y -= ny * shift;
            }
            if (b !== held) {
              b.x += nx * shift;
              b.y += ny * shift;
            }
            const rel = (b.vx - a.vx) * nx + (b.vy - a.vy) * ny;
            if (rel > 0) continue;
            const imp = -rel;
            if (a !== held) {
              a.vx -= imp * nx;
              a.vy -= imp * ny;
            }
            if (b !== held) {
              b.vx += imp * nx;
              b.vy += imp * ny;
            }
          }
        }
        draw();
        raf = requestAnimationFrame(step);
      };
      const squash = (d, axis) => {
        const cls = axis === "x" ? "is-squash-x" : "is-squash-y";
        if (d.el.classList.contains(cls)) return;
        d.el.classList.add(cls);
        d.el.addEventListener("animationend", () => d.el.classList.remove(cls), { once: true });
      };
      const toLocal = (e) => {
        const box = field.getBoundingClientRect();
        return { x: e.clientX - box.left, y: e.clientY - box.top };
      };
      const onDown = (e) => {
        const p = toLocal(e);
        const hit = discs.find((d) => Math.hypot(d.x - p.x, d.y - p.y) <= d.r);
        if (!hit) return;
        held = hit;
        heldId = e.pointerId;
        dragged = false;
        last = p;
        hit.el.classList.add("is-held");
        field.setPointerCapture(e.pointerId);
      };
      const onMove = (e) => {
        if (!held || e.pointerId !== heldId) return;
        const p = toLocal(e);
        if (Math.hypot(p.x - last.x, p.y - last.y) > 2) dragged = true;
        held.vx = p.x - last.x;
        held.vy = p.y - last.y;
        held.x = Math.min(W - held.r, Math.max(held.r, p.x));
        held.y = Math.min(H - held.r, Math.max(held.r, p.y));
        last = p;
      };
      const release = () => {
        if (!held) return;
        const sp = Math.hypot(held.vx, held.vy);
        if (sp > MAX_SPEED) {
          held.vx = held.vx / sp * MAX_SPEED;
          held.vy = held.vy / sp * MAX_SPEED;
        }
        held.el.classList.remove("is-held");
        held = null;
        heldId = -1;
      };
      const onClick = (e) => {
        if (dragged) {
          e.preventDefault();
          dragged = false;
        }
      };
      let tiltX = 0;
      let tiltY = 0;
      const onTilt = (e) => {
        const g = Math.max(-45, Math.min(45, e.gamma ?? 0));
        const b = Math.max(-45, Math.min(45, (e.beta ?? 0) - 35));
        tiltX = g * TILT_GAIN;
        tiltY = b * TILT_GAIN;
      };
      const onShake = (e) => {
        const a = e.acceleration;
        if (!a) return;
        const ax = a.x ?? 0;
        const ay = a.y ?? 0;
        if (Math.hypot(ax, ay) < 2.5) return;
        for (const d of discs) {
          if (d === held) continue;
          d.vx += -ax * SHAKE_GAIN;
          d.vy += ay * SHAKE_GAIN;
        }
      };
      let motionBound = false;
      const bindMotion = () => {
        if (motionBound) return;
        motionBound = true;
        window.addEventListener("deviceorientation", onTilt);
        window.addEventListener("devicemotion", onShake);
        cleanups.push(() => {
          window.removeEventListener("deviceorientation", onTilt);
          window.removeEventListener("devicemotion", onShake);
        });
      };
      const askMotion = () => {
        const DOE = window.DeviceOrientationEvent;
        const DME = window.DeviceMotionEvent;
        const needsAsk = DOE && typeof DOE.requestPermission === "function";
        if (!needsAsk) {
          bindMotion();
          return;
        }
        Promise.all([
          DOE.requestPermission().catch(() => "denied"),
          DME && typeof DME.requestPermission === "function" ? DME.requestPermission().catch(() => "denied") : Promise.resolve("granted")
        ]).then(([o, m]) => {
          if (o === "granted" || m === "granted") bindMotion();
        });
      };
      layout();
      draw();
      if (reduced) return;
      field.addEventListener("pointerdown", askMotion, { once: true });
      field.addEventListener("pointerdown", onDown);
      field.addEventListener("pointermove", onMove);
      field.addEventListener("pointerup", release);
      field.addEventListener("pointercancel", release);
      field.addEventListener("click", onClick, true);
      const io = new IntersectionObserver(
        (entries) => {
          entries.forEach((en) => {
            if (en.isIntersecting && !raf) raf = requestAnimationFrame(step);
            else if (!en.isIntersecting && raf) {
              cancelAnimationFrame(raf);
              raf = 0;
            }
          });
        },
        { threshold: 0 }
      );
      io.observe(field);
      const onResize = () => {
        layout();
        draw();
      };
      window.addEventListener("resize", onResize);
      cleanups.push(() => {
        io.disconnect();
        cancelAnimationFrame(raf);
        window.removeEventListener("resize", onResize);
        field.removeEventListener("pointerdown", askMotion);
        field.removeEventListener("pointerdown", onDown);
        field.removeEventListener("pointermove", onMove);
        field.removeEventListener("pointerup", release);
        field.removeEventListener("pointercancel", release);
        field.removeEventListener("click", onClick, true);
      });
    });
    return () => cleanups.forEach((fn) => fn());
  }
  return __toCommonJS(ball_field_exports);
})();

;(function(){var go=function(){window.__bf&&window.__bf.initBallField&&window.__bf.initBallField()};document.readyState==='loading'?document.addEventListener('DOMContentLoaded',go):go();})();
