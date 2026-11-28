# test.wugweb.com — content structure master

Status: IMPLEMENTED (theme 0.9.237, 2026-11-28). Replaces the earlier draft. Source of truth in code: `inc/core/includes/master-menu.php` and `acf-json/`.
Rule: one taxonomy structure for the whole site, shared by every content type.

## 1. Post types

| Type | Key | URL | Menu label |
|---|---|---|---|
| Blog post | `post` | `/blog/<slug>/` | Posts |
| Case study | `case_study` | `/work/<slug>/` | Case Studies |

Comments are unused: the menu and admin-bar bubble are hidden. Enquiries are not a post type any more; they live only in Fluent Forms (form 3 enquiry, form 2 newsletter).

## 2. Taxonomies: one structure, every content type

Every taxonomy below is attached to every public content type (posts, case studies and any later type), including Categories and Tags. They are managed in one sidebar group, **Taxonomy**, and not under Posts or Case Studies.

| Taxonomy | Slug | Means |
|---|---|---|
| Categories | `category` | Editorial grouping |
| Tags | `post_tag` | Free topics only |
| Clients | `client` | Who the work was for (logo, wall order) |
| Industry | `industry` | Industry the work serves |
| Sectors | `sector` | The seven sector cards |
| Countries | `country` | Where |
| Discipline | `discipline` | What kind of design or strategy work |
| Tool Stack | `tool` | What a project used (Figma, Shopify, WordPress, Webflow...) |
| Service Sub-Groups | `service_group` | Service pages |
| Platforms | `platform` | Where an article appears (Medium, Substack, X, LinkedIn, the Wugweb blog) |
| Project Types | `project_type` | Kind of project |

Platform is about where an article appears. Tools are never platforms. The word "published" is not used as a label.

## 3. Admin menu

One top-level **Taxonomy** group (slug `wugweb-taxonomy`) lists every taxonomy once. Individual taxonomies have no entry under Posts or Case Studies.

## 4. Data decisions already applied (2026-11-28)

- Nine tool-named terms removed from Platform (Figma, Framer, GitHub, Hugging Face, Mailchimp, Shopify, Webflow, Wix, WordPress). Each already existed in Tool Stack. Nothing was tagged with them. Rollback: `platform-cleanup-rollback.json`.
- 38 tags moved to the taxonomy they belong to (industry, discipline, sector, tool, service group, country) or replaced by the same-name category, with posts reassigned and the tag deleted. 119 tags stay as tags. Rollback: `tag-moves-rollback.json`.
- Five term names that held raw markdown links were fixed (Dev.to, Sur.ly, Any.do, ASP.NET Core, Stockimg.ai).
- Notion Tool Stack gained WooCommerce, WordPress, Xano, Zapier, Zeroheight and Zoom, which existed only in WordPress.

## 5. Not done yet

See `docs/pending.md`.
