# Layout shell — page · section · content

Authoritative spec, supplied 2026-10-03 from the Framer source that built wugweb.com (as carried in the wugweb_site repo). It is the skeleton every page is built on. Theme code that disagrees with this file is wrong, not the file. Copied here on 2026-11-25 for the test.wugweb.com theme (`wugweb-kits`).

## The rule

**The section owns the gutter. The content owns the max-width.**

- A section is always full-bleed and relative (`width: 100%; position: relative`). It carries the page gutter plus the vertical rhythm.
- Inside it sits one content box, also `width: 100%`, capped at `max-width: 1200px` and centred.
- Section padding is for sections only. Content and blocks may add their own padding inside (a card's inset, a reading column's measure). That is not a gutter and never replaces the section's.
- Blocks live inside content. Nothing below the section sets a page gutter.
- Filters are part of the CMS body, never a section of their own. A filter chip row lives inside the content of the grid it filters.

Layer tree: `page -> section -> content -> blocks`.

```
canvas   bg #050505, padding 0, gap 0
  section   width 100%, padding = rhythm + gutter
    content   width 100%, max-width 1200, padding 0
      blocks ...
```

## Breakpoints

| | small · mobile | medium · tablet | large · desktop |
|---|---|---|---|
| canvas width | 375 | 768 | 1200 |
| section padding (T R B L) | 48 16 48 16 | 70 32 70 32 | 96 48 96 48 |
| content max-width | 1200 | 1200 | 1200 |
| layout grid columns | 4 | 8 | 12 |
| column gap | 16 | 24 | 32 |
| grid margin (= gutter) | 16 | 32 | 48 |
| header padding | 16 | 20 | 20 |
| header height (derived) | 100 | 108 | 108 |
| header position | sticky, top 0 | sticky, top 0 | sticky, top 0 |
| nav bar height | 68 | 68 | 68 |
| nav bar inset (all sides) | 12 | 12 | 12 |
| button height (links, CTA, menu) | 44 | 44 | 44 |
| button text / padding / gap | 16 / 10 12 / 12 | same | same |
| logo height | 40 | 40 | 40 |

Header formula: 24px line height + 2 x 10px button padding = 44px button; 44 + 2 x 12 = 68px bar; 68 + 2 x 20 = 108px header (mobile outer spacing 16, so 100). Desktop tier is tested at 1200, not 1440.

## Exact declarations

Canvas: `display:flex; flex-direction:column; align-items:center; background:#050505; padding:0; gap:0` (sections stack on padding only, never margin).

Section: `box-sizing:border-box; width:100%; display:flex; flex-direction:column; justify-content:center; position:relative; gap:0;` padding 48px 16px / 70px 32px / 96px 48px. align-items center / flex-start / center; overflow hidden / hidden / visible.

Content: `width:100%; max-width:1200px; display:flex; flex-direction:column; justify-content:center; align-items:center; padding:0; position:relative; gap:16px | 24px | 32px; z-index:1 (mobile, tablet)`.

Top nav: `width:100%; height:100px (mobile) | 108px; padding:16px | 20px | 20px; position:sticky; top:0; z-index:10; cursor:grab` (links and buttons inside keep `cursor:pointer`).

Spring transition (nav and hover): 400ms, bounce 0.2, delay 0 -> `--duration-spring: 400ms; --ease-spring: linear(...)` (sampled damped spring, ~1.5% overshoot). GSAP stays the engine for scroll animation.

## Every page is indexed (canvas)

- `<html lang>`. Skip link: removed by owner decision 2026-11-25 (the spec wanted one as the first focusable element, targeting `#main`).
- `<main id="main" tabindex="-1" aria-label={page title}>`; `<header>` (sticky nav); `<footer id="site-footer" aria-label="Site footer">`.
- Exactly one `h1` per page, inside main. Heading levels never skip.
- `main[data-scroll-flow]` lists section ids in order; each top-level section gets `data-scroll-index` 1..n.
- `scroll-margin-top: var(--header-height)` on sections.

## Every section is labelled

Each section has a scroll anchor (kebab-case id, unique per page) and an accessible name.

```html
<section class="wrap services" id="services" aria-labelledby="services-title">
  <h2 id="services-title">...</h2>
```

- `aria-labelledby` only points at a heading that always renders; otherwise use `aria-label`.
- Never label a section with a heading inside a loop (duplicate ids); use `aria-label`, and keep looped headings id-free.
- Sections in layouts (industries strip, legal hub) count.
- Filters are `role="toolbar"` divs with an `aria-label`, inside the grid section they filter.
- A section with no visible heading gets an `.sr-only` h2 and `aria-labelledby`.

Content: lists of cards use `ul/ol + li`; images always have `alt` (empty only when decorative); decorative muted video loops `aria-hidden="true"`, controlled videos get `aria-label`; links and buttons have visible text or aria-label; form fields have `<label for>` or aria-label; media + caption use `figure + figcaption`.

## Width rule

Nav inner container max 1280. Section content max 1200. No non-full-bleed block may be wider than the nav. `.global-final-cta` is a band with a rounded card inside; the card must not opt out of the shell with its own width or gutter.

## Mobile menu

Covers the screen (100dvh with 100svh fallback, minus safe areas); scrolls internally, `overscroll-behavior: contain`; scroll lock via `position: fixed` on body with scroll position restored; toggle keeps the same coordinates open and closed; one left edge for logo, links, contact and legal; links at top, contact and legal pinned to the bottom; open/close uses the nav spring, links fade and rise with ~40ms stagger, toggle morphs by transform. WhatsApp / phone: +91 92059 96219.

## Motion

Load/scroll-in: opacity + translateY via GSAP, first group 0.4s, second 0.9s. Transform-based only; everything behind `prefers-reduced-motion`.

## PWA / safe areas

`viewport-fit=cover`; `env(safe-area-inset-*)` on the nav; maskable icon; dvh with svh fallback. Hero: on a 390 x 844 phone the headline, subhead and primary action sit on the first screen.

## How it is implemented (Astro source)

- Section = `.wrap`; gutter 16/32/48 = `--shell-gutter`; content max 1200 centred: `.wrap { width:100%; padding-inline: max(var(--shell-gutter), calc((100% - 1200px) / 2)) }`.
- Rhythm 48/70/96 = `--section-y`; column gap = `--grid-gap`; columns 4/8/12 switch at 768 and 1200; `--breakpoint-xl: 1200px`.
- One global rule enforces the rhythm: every top-level section gets `padding-block: var(--section-y); margin-block: 0` (narrow `!important`); an inner `.wrap` gets no vertical padding. Exempt: `.global-final-cta` only.
- The section is full width; the 1200 box is carved by padding, so the section background spans the viewport. A `max-width` on the section would stop its background short.
- Grid gaps never borrow a section token.

## Measuring note

The measuring iframe needs `border: 0`. A 768px iframe with the default 2px border has a 764px viewport and misses `min-width: 768px`.

## Not carried (superseded)

Old audit items on 1024 breakpoint, flat spacing, nav card values, type scale and component counts are superseded by this file and `breakpoints.md` / `typography.md` in the source repo.

## Layout tokens (from `kits_design_system/docs/tokens/layout.md`)

- `.wrap` = 1200px content plus gutters; it is the section and owns the only page gutter. `.page` (90rem) is legacy; nothing may be wider than the nav (1280).
- `.grid`: 4 columns mobile, 8 at 768, 12 at 1200; gap `--grid-gap` 16 / 24 / 32.
- Page gutter `--space-page-xs/sm/md/lg/xl` = 16 / 20 / 32 / 40 / 48.
- Section spacing `--space-section-xs` to `--space-section-xl` all = 48 mobile, 70 at 768, 96 at 1200. The old 32/40/48/64/80 ladder is retired.

## Global sections per page

The footer part holds three global blocks: industries-served, recent-thoughts (blog teaser) and cta-band (the action block, the yellow box before the footer). `wugweb_global_sections_for_page()` decides which render:

| Page | Sectors | Blog teaser | Action block |
|---|---|---|---|
| Home, About, Services, FAQ, Impact, Reviews, Subscription | yes | yes | yes |
| Blog index, single post, category, tag, author, date, search, Sitemap | no | no | yes |
| Case study archive, single case study, industry/sector/discipline archives | no | yes | yes |
| Contact | no | yes | yes |
| Thank-you and other utility pages | no | yes | yes |
| Legal pages, Industries page | no | no | no |

The function is the source of truth; this table follows it.
