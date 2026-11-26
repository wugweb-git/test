# test.wugweb.com — content structure master

Status: DRAFT for approval. Nothing here is implemented. The child session is on hold for menu and taxonomy work.
Rule: hide and detach only. No terms deleted, no URL or slug changes, no post type key changes.

## 1. Post types

| Type | Key | URL | Menu label | Owner of registration |
|---|---|---|---|---|
| Blog post | `post` | `/blog/<slug>/` | Posts | WordPress core |
| Case study (portfolio) | `case_study` | `/work/<slug>/` | Case Studies | theme `inc/core/includes/post-types.php` |
| Client | `client` | not public | inside the Reference group | theme `inc/core/includes/notion-schema.php` |

Case study menu labels become: Add Case Study, Edit Case Study, All Case Studies, and so on. The key `case_study` and the `/work/` slug stay.

## 2. Taxonomies: one home each

| Taxonomy | Slug | Attached to | Managed where |
|---|---|---|---|
| Categories (30) | `category` | Posts | Posts menu |
| Tags (157) | `post_tag` | Posts only | Posts menu |
| Discipline (36) | `discipline` | Case Studies | Case Studies menu |
| Industry (45) | `industry` | Posts, Case Studies, Clients | Reference group, once |
| Sectors (17) | `sector` | Case Studies, Clients | Reference group, once |
| Countries (11) | `country` | Case Studies, Clients | Reference group, once |
| Tool Stack (190) | `tool` | Case Studies | Reference group, once |
| Owners (2) | `owner` | Posts, Case Studies | Reference group, once |
| Service Sub-Groups (23) | `service_group` | Posts | Reference group, once |

Detached (the terms stay in the database, the box and menu entry go away):
- `post_tag` from Case Studies and Clients.
- `category`, `post_tag` and `discipline` from Clients.

Mapping between content and terms is unchanged. A taxonomy keeps its attachments and its editor sidebar box. Only the duplicate menu entries go.

## 3. Admin menu, target shape

- Posts: All Posts, Add Post, Categories, Tags.
- Case Studies: All Case Studies, Add Case Study, Discipline.
- Reference (renamed from "Notion Data", slug `wugweb-platforms` kept):
  - Clients, Platforms, Tools, Client - Engagement
  - Industry, Sectors, Countries, Tool Stack, Owners, Service Sub-Groups

Name "Reference" is a proposal. The alternatives were Taxonomy, Classification, Directory, and Notion Data (current name).

## 4. Not changing

- Core plugin extraction (`wugweb-core`) is a separate decision, not part of this change.
- SCF stays installed and unused (0 field groups, post types, taxonomies or options pages).
- Notion sync and admin-columns code must not read a detached taxonomy for the types it was removed from. The child checks this before pushing.

## 5. Icons and logos (R2), per term and per client

Checked on the live site 2026-11-26:
- Tool Stack (190), Platforms (98), Industry, Sectors, Countries, Service Sub-Groups: the only term meta is `wugweb_notion_data` (a JSON blob from Notion) and `wugweb_notion_url`. There is NO icon field, so the R2 icons from the old Sanity data (`iconPath`, 89 tool icons) did not come across.
- Clients (124): `wugweb_client_logo` is filled on 18 of 124. `wugweb_client_website` is filled on 1 of 124.

Target: one term meta key `wugweb_icon_path` (R2 key under `assets/`, same path map as the site: `assets/<path>` on the R2 base) on every taxonomy that has icons: tool, platform, industry, sector, country, service_group. Clients keep `wugweb_client_logo`. The term edit screen shows a small preview and the path field. Link an icon only where the R2 file exists, otherwise leave it empty (no broken images). Source: the iconPath and logoPath fields in the wugweb_site Sanity data.

## 6. Alt text for media

- The WordPress media library has only 3 attachments, and none has alt text. Media is served from R2 by path, not as attachments, so the usual Media alt field does not apply.
- Target: every image the theme prints from a path has an alt string stored beside the path (page meta for hero and gallery, case-study block data, term meta for icons, client logo alt). Purely decorative images get an empty alt on purpose. Image alt is never invented from the file name.
- Not checked yet: how many content images already carry an alt in the post and case-study markup.

## 7. SEO details (Rank Math)

Published items with a Rank Math title or description: pages 20 of 27, posts 0 of 103, case studies 0 of 44. Clients are not public, so they need none.
- Target: SEO title and description for every post and case study (Notion is the source for case studies, per the wugweb_site rule), focus keyword, canonical, and a social image. Taxonomy archives that are public get a description too.
- SEO build stays PARKED until the UI work is done (owner decision). This spec only reserves the fields so the data model does not need a second reorganisation.

## 8. Parent and child (services, sub-services, categories)

What exists today (checked live 2026-11-26):
- Pages: `services` (19) has six child pages, the pillars: strategy, brand, experience, engineering, growth, operations. So `/services/<pillar>/` is already parent and child.
- Service Sub-Groups (23 terms, e.g. brand-identity, user-flows, on-page-seo): flat, 0 content attached, and nothing records which pillar each belongs to (the term data holds only an id). So the sub-service to pillar link does not exist in WordPress yet.
- Categories: 30, with a messy hierarchy. "experience" (73) is parent of about a dozen blog topics, but there are duplicates and orphans: brand and brand-and-strategy, experience-design and experience-design-experience, strategy and strategy-topic, web-app-development and web-and-app-dev, martech and martech-and-ai, plus empty leftovers (ai-automation, digital-transformation, misc).
- Every Notion taxonomy (industry, sector, tool, platform, discipline, country) is flat.

Target:
1. Services stay pages: Services, then pillar, then sub-service (`/services/<pillar>/<sub-service>/`). Sub-service pages are children of their pillar page. Whether each of the 23 sub-groups becomes its own page is an open decision (section 9).
2. Service Sub-Groups become hierarchical, with each term's parent set to its pillar (a pillar term per page, or one term meta `wugweb_pillar` pointing at the pillar page ID). The mapping comes from Notion, never guessed from names.
3. Categories: merge the duplicates and orphans into the six pillars plus topics, one parent each. Merging terms moves content between categories, so it needs a backup and an explicit yes.
4. Tool Stack, Platforms: make them hierarchical only if Notion has a parent (capability area is a candidate for the tool parent). Industry and Sectors: same rule, parent only where Notion defines one.
5. Breadcrumbs and URLs follow the parent chain. Existing URLs do not change.

## 9. Open decisions

1. Group name: Reference, or another.
2. Do Categories and Tags stay under Posts (this draft) or also move into the group? The user said moving them would confuse.
3. Core plugin or keep registrations in the theme.
4. Does each of the 23 Service Sub-Groups become its own child page under its pillar, or stay a filter term only?
5. Where does the sub-service to pillar mapping come from: the Notion service tables? It must come from there, not from guesses.
6. Category cleanup: approve the merge list before any term is merged (backup first).
7. Icons: confirm `wugweb_icon_path` as the single key, and where to pull the icon paths from (Sanity iconPath data).
8. Alt text and SEO: keep parked until after UI, with only the fields reserved now.
