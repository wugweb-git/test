<?php
/**
 * Accessible names for block landmarks, in one place.
 * Every wugweb/* block gets an `a11yLabel` attribute (sidebar via supports.autoRegister). On render, each landmark-capable element
 * (section, aside, nav, form, article) that has no name gets one: aria-labelledby its own heading (heading ids made unique),
 * else a plain-English aria-label (block-level a11yLabel first, then a per-type default). Plain div/span never get aria-label.
 * The header and footer template parts get "Site header" / "Site footer" and stay the page's single banner / contentinfo.
 */
defined('ABSPATH') || exit;

add_filter('register_block_type_args', function ($args, $name) {
    if (strpos($name, 'wugweb/') !== 0) return $args;
    $args['attributes'] = isset($args['attributes']) ? $args['attributes'] : array();
    if (!isset($args['attributes']['a11yLabel'])) $args['attributes']['a11yLabel'] = array('type' => 'string', 'default' => '', 'title' => 'Accessible label', 'description' => 'Overrides the automatic accessible name of this block (screen readers announce it as the region name).');
    return $args;
}, 11, 2);

/** Register a landmark name for this page: returns '' when it is the first use (keep as is), else a unique variant. */
function wugweb_a11y_seen($name) {
    static $seen = array();
    $k = strtolower(trim($name)); if ($k === '') return '';
    if (!isset($seen[$k])) { $seen[$k] = 1; return ''; }
    $seen[$k]++; return trim($name) . ' ' . $seen[$k];
}
/** Names already used by defaults on this page, so same-role landmarks stay unique (axe landmark-unique). */
function wugweb_a11y_unique($label) {
    static $used = array();
    $k = strtolower($label); $n = isset($used[$k]) ? $used[$k] + 1 : 1; $used[$k] = $n;
    return $n > 1 ? $label . ' ' . $n : $label;
}
function wugweb_a11y_block_title($name) {
    $t = WP_Block_Type_Registry::get_instance()->get_registered($name);
    $s = $t && !empty($t->title) ? $t->title : ucwords(str_replace('-', ' ', preg_replace('#^[a-z-]+/#', '', (string) $name)));
    return trim(preg_replace('/\s*\(.*\)\s*/', ' ', $s));
}

function wugweb_a11y_name_html($html, $block_name, $root_label) {
    if (!class_exists('WP_HTML_Processor') || trim($html) === '') return $html;
    $p = WP_HTML_Processor::create_fragment($html);
    if (!$p) return $html;
    $targets = array('SECTION', 'ASIDE', 'NAV', 'FORM', 'ARTICLE'); $heads = array('H1', 'H2', 'H3', 'H4', 'H5', 'H6');
    $root = true; $guard = 0;
    while ($p->next_tag() && ++$guard < 4000) {
        $is_root = $root; $root = false;
        $tag = $p->get_tag();
        if (!in_array($tag, $targets, true)) continue;
        $has_l = $p->get_attribute('aria-label'); $has_b = $p->get_attribute('aria-labelledby');
        if ($has_l !== null || $has_b !== null) {
            // Already named by its block: only make the name unique on the page (a block rendered twice would repeat it).
            $nm = is_string($has_l) ? $has_l : '';
            if ($nm === '' && is_string($has_b) && preg_match('#id=["\']' . preg_quote(trim(explode(' ', $has_b)[0]), '#') . '["\'][^>]*>(.*?)</h[1-6]>#is', $html, $mm)) { $nm = trim(wp_strip_all_tags($mm[1])); }
            $dup = $nm !== '' ? wugweb_a11y_seen($nm) : '';
            if ($dup !== '') { $p->remove_attribute('aria-labelledby'); $p->set_attribute('aria-label', $dup); }
            continue;
        }
        $cls = (string) $p->get_attribute('class');
        $action = (string) $p->get_attribute('action');
        if ($is_root && $root_label !== '') { $p->set_attribute('aria-label', $root_label); continue; }
        $p->set_bookmark('wa_t'); $depth = $p->get_current_depth(); $hid = null; $htxt = '';
        while ($p->next_tag()) {
            if ($p->get_current_depth() <= $depth) break;
            if (in_array($p->get_tag(), $heads, true)) {
                $hid = $p->get_attribute('id'); if (!is_string($hid) || $hid === '') { $hid = wp_unique_id('a11y-h-'); $p->set_attribute('id', $hid); }
                $htag = $p->get_tag();
                while ($p->next_token()) { if ($p->get_token_type() === '#text') { $htxt .= $p->get_modifiable_text(); } elseif ($p->is_tag_closer() && $p->get_tag() === $htag) break; }
                break;
            }
        }
        $p->seek('wa_t'); $p->release_bookmark('wa_t');
        if ($hid) {
            $dup = wugweb_a11y_seen(trim(preg_replace('/\s+/', ' ', $htxt)));
            if ($dup !== '') { $p->set_attribute('aria-label', $dup); } else { $p->set_attribute('aria-labelledby', $hid); }
            continue;
        }
        if ($tag === 'FORM') { $label = (strpos($cls, 'nl-form') !== false || strpos($cls, 'bn-form') !== false || strpos($action, 'subscribe') !== false) ? 'Newsletter signup' : (stripos($block_name . $cls . $action, 'contact') !== false ? 'Contact form' : wugweb_a11y_block_title($block_name)); }
        else { $label = wugweb_a11y_block_title($block_name); }
        $p->set_attribute('aria-label', wugweb_a11y_unique($label));
    }
    return $p->get_updated_html();
}
add_filter('render_block', function ($html, $block) {
    $n = $block['blockName'] ?? '';
    if (is_admin() || (defined('REST_REQUEST') && REST_REQUEST)) return $html;
    if (strpos($n, 'wugweb/') === 0) { return wugweb_a11y_name_html($html, $n, trim((string) ($block['attrs']['a11yLabel'] ?? ''))); }
    if ($n === 'core/template-part') {
        $slug = (string) ($block['attrs']['slug'] ?? '');
        $label = $slug === 'header' ? 'Site header' : ($slug === 'footer' ? 'Site footer' : '');
        if ($label !== '') {
            $p = new WP_HTML_Tag_Processor($html);
            if ($p->next_tag() && in_array($p->get_tag(), array('HEADER', 'FOOTER'), true) && $p->get_attribute('aria-label') === null && $p->get_attribute('aria-labelledby') === null) { $p->set_attribute('aria-label', $label); $html = $p->get_updated_html(); }
        }
    }
    return $html;
}, 20, 2);
