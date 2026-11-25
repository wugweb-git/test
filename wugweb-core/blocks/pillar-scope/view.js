import { store, getContext, getElement } from '@wordpress/interactivity';
const { state } = store('wugweb/pillar-scope', { state: {
 get activePillar() { const ctx=getContext(); return ctx.pillars.find((item)=>item.slug===ctx.activeSlug)||ctx.pillars[0]; },
 get tokenReadout() { const p=state.activePillar; return `--svc-base: ${p.base} · --svc-light: ${p.light}`; },
 get isActive() { const ctx=getContext(); const {ref}=getElement(); return ctx.activeSlug===ref.dataset.slug; },
 get isSelected() { const ctx=getContext(); const {ref}=getElement(); return ctx.selectedSlugs.includes(ref.dataset.slug); },
 get selectedCountLabel() { const count=getContext().selectedSlugs.length; return count===1?'1 Pillar Selected':`${count} Pillars Selected`; },
 get scopedCtaUrl() { const ctx=getContext(); if(!ctx.selectedSlugs.length)return ctx.baseCtaUrl; const sep=ctx.baseCtaUrl.includes('?')?'&':'?'; return `${ctx.baseCtaUrl}${sep}scope=${encodeURIComponent(ctx.selectedSlugs.join(','))}`; }
}, actions: {
 activatePillar() { const ctx=getContext(); const {ref}=getElement(); ctx.activeSlug=ref.dataset.slug; const root=ref.closest('.wugweb-pillar-scope'); if(root){root.style.setProperty('--svc-base',ref.dataset.base);root.style.setProperty('--svc-light',ref.dataset.light);} },
 toggleScope() { const ctx=getContext(); const {ref}=getElement(); const slug=ref.dataset.slug; const idx=ctx.selectedSlugs.indexOf(slug); if(idx>-1)ctx.selectedSlugs.splice(idx,1); else ctx.selectedSlugs.push(slug); }
} });