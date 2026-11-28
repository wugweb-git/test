# test.wugweb.com — pending

Updated 2026-11-28, theme 0.9.237 (deployed to test; the cache purge is the owner's).

## Open, needs the owner

- Purge the LiteSpeed cache after each deploy (term names, teaser, forms markup). Claude cannot purge it.
- Fluent Forms: send one test enquiry and one newsletter signup after the purge. Form 3 notification to hello@wugweb.com is enabled. Confirm the entry shows the visitor's real IP.
- Case studies: assign clients (14 names in the old data have no client term: create or map), 32 project types to create, tool source missing on most. Drafts: `taxonomy-move-list-4_case_studies.csv`, `taxonomy-move-list-2_platform.csv`.
- Posts: fill the missing taxonomy assignments from `taxonomy-move-list-3_posts.csv`.
- Sanity `platform` field (Website, Mobile App and so on) is not in the WordPress model. Skip it, or add a delivery-format taxonomy.
- Rank Math analyzer (78): focus keyword vs page title on 10 pages; Minify CSS/JS and 29 requests (cache layer); WWW canonicalization needs the `www.test` DNS record in hPanel; Links ratio. Noindex is on for staging and is lifted at launch.
- Blog index and related-posts grids still use the older one-column rule up to 1023px. Bound them like the home teaser, or leave.
- Mobile speed check: needs the PageSpeed quota back (Site Kit).

## Deferred by the owner

Services tree audit, offline page font (`system-ui`), admin bar font, contact form mail setup beyond Fluent Forms, work-block GSAP animation, social disc positions (code defaults), PWA offline page.

## Rules in force

- Global sections (blog teaser, industries/sectors, action block) are decided per page in `wugweb_global_sections_for_page()` in `inc/core/includes/global-sections.php`: no blog teaser on blog surfaces; no sectors on industry-led or utility pages; legal pages carry none.
- One taxonomy structure for every content type (see `docs/content-structure-master.md`).
- One font family: Inter Tight.
