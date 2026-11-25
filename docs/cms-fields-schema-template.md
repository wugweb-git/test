# Schema template: section and component-level fields

Source: wugweb_site `docs/schema-template-section-component-level.md`, pasted by the owner on 2026-11-25. Saved here as reference for the WordPress build. It is SEO and schema guidance, so it is parked while the UI work is the priority.

## Status (checked 2026-11-19 in the Astro/Sanity project)

Live in the Sanity schema: `ogImagePath`, `ogTitle`, `ogDescription`, `noIndex` on caseStudy and post, plus `focusKeyword` on caseStudy. The other section 1 fields are deliberately not added (see the comments in `sanity/schemaTypes/caseStudy.ts` and `post.ts`). The section 2 fields are not added. Field rulings now belong to wugweb-strategy and wugweb-seo. Anything below that says "nothing is deployed" is out of date.

For the WordPress theme: the blog schema was already trimmed by the owner (no notion_url, sanity_id, status, etc.), so do not re-add fields from this file without asking.

## 1. SEO / AI-answers group for caseStudy and post (as proposed)

| Field | Type | Group | Label | Purpose |
|---|---|---|---|---|
| ogImagePath | string | seo | OG Image (R2 path) | Path-field convention like coverPath/thumbnailPath |
| ogTitle | string | seo | OG Title override | Falls back to seoTitle / title |
| ogDescription | text | seo | OG Description override | Falls back to seoDescription / excerpt |
| noIndex | boolean | seo | Hide from search engines | Default false |
| seoCanonicalUrl | url | seo | Canonical URL override | Distinct name; caseStudy.canonicalUrl already means stub-redirect |
| focusKeyword | string | seo | Focus keyword | Primary keyword |
| secondaryKeywords | string[] | seo | Secondary keywords | Supporting keywords |
| searchIntent | list | seo | Search intent | informational / commercial / navigational / transactional |
| breadcrumbLabel | string | seo | Breadcrumb label | Falls back to title |
| aiSummary | text | ai-answers | AI answer summary | 40-60 words, rendered visibly |
| keyTakeaways | string[] | ai-answers | Key takeaways | 3-5 bullets |
| faqs | {question, answer}[] | ai-answers | FAQs | Feeds FAQPage JSON-LD |
| schemaType | list | ai-answers | Schema.org type | caseStudy: CreativeWork / Article; post: Article / BlogPosting |
| lastReviewedAt | datetime | ai-answers | Last reviewed | Feeds dateModified |

## 2. Component-level fields (small set, only if worth the complexity)

| Field | On object | Purpose |
|---|---|---|
| ariaLabel (string, optional) | link / button | Only when the visible label does not describe the action (icon-only buttons, repeated "Learn more") |
| relNofollow (boolean, default false) | link / button with external href | Only once outbound or sponsored links exist |
| imageContext (string, optional) | image | Editor-facing note on the copy an image supports; never rendered |

No new field for alt text: where image objects already have `alt`, the fix is validation (required), not a new field.

## 3. Checklist items that are code patterns, not fields

Real landmark tags (section/article/nav), heading level flexibility (Portable Text H2-H4), no skipped heading levels, section label / scroll anchor (hard-coded per template), native href, descriptive anchor text, native lazy loading, image/asset optimisation (Asset Registry / R2), asymmetric loading and DOM order, answer-first copy, key-takeaway blocks and entity density (copywriting guidance for filling aiSummary / keyTakeaways / body). The layout rules for landmarks and section labels are in `layout-shell.md`.

## Next step (original plan)

Add the SEO/AI-answers group to caseStudy.ts and post.ts, add the section 2 fields only where confirmed, run `npx tsc --noEmit` and deploy the schema. Not started here.
