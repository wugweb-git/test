# Blog Post Layout

**Status:** built, awaiting sign-off · **Last verified:** 2026-10-19
**Source:** `src/pages/blog/[slug].astro` · tokens `--article-*` in `src/styles/global.css` · related cards `src/components/PostCard.astro`

Reference (2026-11-25): the timeleft.com blog detail, with our tokens and
palette. Supersedes the earlier Fabrica split layout (sticky cover beside one
column, `--article-cols/areas/cover-*` tokens, no longer used by article-post).
Copy, order of content and the global sections are unchanged.

---

## 1. Anatomy

```
section.wrap.post-layout#article
├ header.post-hero#article-hero    breadcrumb (‹ Blog / category), h1, lede, byline (author · Updated date · N min read)
├ figure.post-cover#article-cover  rounded cover, 860px max, 20px radius
└ div.post-grid
  ├ aside.post-rail                contents ("In this article"), share (X, LinkedIn, Facebook, copy link), one promo card
  └ div.post-body                  .article-body, author block, newsletter, previous/next
section#related-posts              post-cross-sell-rail cards
```

## 2. Measures

| Part | Value |
|---|---|
| Hero | 860px max; h1 56px/60px weight 500, tracking -0.025em, 780px max; lede 20px grey |
| Rail (≥1200) | 230px, sticky under the header; contents links grey, active darker |
| Reading column | 680px, 616px text measure |
| Body | 17px/27px, p margin-bottom 12px, links underlined in text colour |
| h2 / h3 | 26/31 weight 500, -0.4px / 20/26 |
| Figures, quotes, summary | full-width rounded blocks (20px) |

## 3. Tiers

| Range | Layout |
|---|---|
| x < 1200 | Header, cover, contents as collapsed details, body, end-of-article share row, promo card |
| x ≥ 1200 | Header, cover, then rail (230) beside reading column (680); the end share row is hidden |

Authoring conventions (applied in `article-post/render.php`): a first h2
matching "short version / TL;DR / key takeaways / summary" becomes the tinted
summary box; an h2 matching "FAQ" turns its h3 + answers into native
`details` accordions; bare tables get a horizontal-scroll wrapper; h2/h3
without ids get slug ids for the contents links.

## 4. Related posts

The shared PostCard with `row`: compact row cards below 768, the normal card
from 768. Grid: 1 column, 2 at md, 3 at lg. Card states (hover lift and zoom,
focus, row press) come from PostCard; nothing is restyled here.
