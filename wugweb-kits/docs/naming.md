# Naming and identification

- **Block name:** `wugweb/<folder>`; the folder under `inc/core/blocks/` is the single source. Never rename a block name or folder (saved content refers to it).
- **Root class:** `wp-block-wugweb-<folder>`, printed on every block's root element (added in `inc/a11y-names.php` if the render did not print it). Inner classes keep their existing names; they are not renamed.
- **Block categories:** wugweb-layout, wugweb-content, wugweb-cards, wugweb-service, wugweb-blog-work, wugweb-charts, wugweb-overlays, wugweb-pages (registered in `inc/core/bootstrap.php`). Every block.json has `textdomain: wugweb-kits` and 3 to 5 plain keywords.
- **Patterns:** file `patterns/<name>.php`, slug `wugweb-kits/<name>` (lowercase, hyphens, equals the file name), a readable Title, one-sentence Description, comma-separated Keywords.
- **Numbers:** blocks `B-01…`, patterns `P-01…`, in `inc/core/registry-numbers.json`; shown under Appearance → Wugweb Registry. New items get the next number by editing that file; numbers are never reused.
- **Ids:** unique per page. Headings that name a landmark get `a11y-h-N` ids when they have none.
- **Accessible names:** see `inc/a11y-names.php`. Order: block's *Accessible label* → its own heading (`aria-labelledby`) → default per block type. Only section, aside, nav, form, article get names; never aria-label on plain div/span. Header and footer parts are "Site header" / "Site footer". Same-role landmarks on a page must have different names (repeats get " 2", " 3").
