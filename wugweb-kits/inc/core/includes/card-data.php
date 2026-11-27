<?php
if (!function_exists('wugweb_r2_url')) {
  /** Public media base (R2) + path; base is filterable (wugweb_assets_base). */
  function wugweb_r2_url($path) { $b = rtrim((string) apply_filters('wugweb_assets_base', 'https://pub-39ad1d03baec440faf48303be95c3782.r2.dev'), '/'); return $b . '/' . ltrim((string) $path, '/'); }
  /** Team photos on R2, exact files as published on uat (assets/site/about/team). */
  function wugweb_team_photo($name) { static $m = array('vedanshu-srivastava'=>'jpg','praneet-verma'=>'jpg','vaibhav-shukla'=>'jpg','shreya-khot'=>'jpg','satish-kumar'=>'jpg','kartik-chaturvedi'=>'jpg','swarndeep-singh'=>'jpg','sunil-thapa'=>'jpg','divyanshu-pundir'=>'jpg','ojaswa-verma'=>'png'); $k = sanitize_title((string) $name); return isset($m[$k]) ? wugweb_r2_url('assets/site/about/team/team-' . $k . '.' . $m[$k]) : ''; }
}
/** Site-wide post date: published date only, "DD Month YY" (17 July 25). Never the modified date. */
if (!function_exists('wugweb_post_date_label')) { function wugweb_post_date_label($id) { return get_the_date('j F y', $id); } }
/** Data schema -> card fields. One place that maps WordPress records (post, case_study, client, industry term, user) to what the card blocks show. */
defined('ABSPATH') || exit;

function wugweb_img_first(array $cands) { foreach ($cands as $c) { if (is_string($c) && $c !== '') return $c; } return ''; }

/** Record id for a card: manual attribute postId wins, else Query Loop / current post context. */
function wugweb_block_post_id($block, array $attributes, array $types = array()) {
    $id = (int) ($attributes['postId'] ?? 0);
    if (!$id && is_object($block) && !empty($block->context['postId'])) $id = (int) $block->context['postId'];
    if (!$id && in_the_loop()) $id = (int) get_the_ID();
    if (!$id) $id = (int) get_the_ID();
    if ($id && $types && !in_array(get_post_type($id), $types, true)) return 0;
    return $id;
}

function wugweb_term_names($id, $tax) { $t = get_the_terms($id, $tax); return ($t && !is_wp_error($t)) ? array_map(function ($x) { return html_entity_decode($x->name, ENT_QUOTES); }, $t) : array(); }

function wugweb_fields_post($id) {
    $cat = (string) get_post_meta($id, 'wugweb_primary_category', true);
    if ($cat === '') { $c = get_the_category($id); $cat = $c ? $c[0]->name : ''; }
    $mins = (int) get_post_meta($id, 'wugweb_read_time', true);
    if ($mins < 1) $mins = max(1, (int) ceil(str_word_count(wp_strip_all_tags((string) get_post_field('post_content', $id))) / 200));
    $author = (string) get_post_meta($id, 'wugweb_author_name', true);
    if ($author === '') $author = get_the_author_meta('display_name', get_post_field('post_author', $id));
    return array('title' => get_the_title($id), 'url' => get_permalink($id),
        'image' => wugweb_img_first(array(get_the_post_thumbnail_url($id, 'large'), (string) get_post_meta($id, 'wugweb_cover_url', true))),
        'excerpt' => wp_trim_words(get_the_excerpt($id), 24), 'date' => wugweb_post_date_label($id), 'category' => $cat, 'minutes' => $mins, 'author' => $author,
        'tags' => wugweb_term_names($id, 'post_tag'));
}

function wugweb_fields_case_study($id) {
    $m = function ($k) use ($id) { return (string) get_post_meta($id, 'wugweb_cs_' . $k, true); };
    $disc = $m('additional_disciplines') ?: $m('primary_discipline');
    return array('title' => get_the_title($id), 'short_title' => wugweb_cs_client($id), 'url' => get_permalink($id),
        'image' => wugweb_img_first(array(get_the_post_thumbnail_url($id, 'large'), $m('cover_image'))),
        'client' => wugweb_cs_client($id), 'discipline' => $disc, 'industry' => wugweb_term_names($id, 'industry'), 'sector' => wugweb_term_names($id, 'sector'),
        'services' => $m('services'), 'results' => $m('results'), 'year' => $m('year'), 'live_url' => $m('live_url'));
}

function wugweb_fields_industry($term_id) {
    $t = get_term((int) $term_id, 'industry'); if (!$t || is_wp_error($t)) return array();
    $sector = ''; $icon = '';
    $sid = (int) get_term_meta($t->term_id, 'wugweb_sector', true);
    if ($sid) {
        $s = get_term($sid, 'sector');
        if ($s && !is_wp_error($s)) { $sector = html_entity_decode($s->name, ENT_QUOTES); $icons = json_decode((string) file_get_contents(__DIR__ . '/lucide-sector-icons.json'), true) ?: array(); $icon = $icons[(string) get_term_meta($s->term_id, 'wugweb_sector_icon', true)] ?? ''; }
    }
    return array('name' => html_entity_decode($t->name, ENT_QUOTES), 'description' => $t->description, 'url' => get_term_link($t), 'sector' => $sector, 'icon_svg' => $icon, 'count' => (int) $t->count, 'show_on_sector_card' => (bool) get_term_meta($t->term_id, 'wugweb_on_sector_card', true));
}

function wugweb_fields_user($uid) {
    $u = get_userdata((int) $uid); if (!$u) return array();
    $img = (string) get_user_meta($u->ID, 'wugweb_avatar_url', true) ?: get_avatar_url($u->ID, array('size' => 400));
    return array('name' => $u->display_name, 'role' => (string) strtok((string) $u->description, "\n"), 'image' => $img, 'url' => get_author_posts_url($u->ID));
}

/** Block Bindings sources: pick a field of the current record. */
add_action('init', function () {
    if (!function_exists('register_block_bindings_source')) return;
    $get = function ($source_args, $block) {
        $key = sanitize_key($source_args['key'] ?? ''); $id = (int) ($block->context['postId'] ?? get_the_ID());
        $pt = get_post_type($id);
        $f = $pt === 'case_study' ? wugweb_fields_case_study($id) : wugweb_fields_post($id);
        $v = $f[$key] ?? null; return is_array($v) ? implode(', ', $v) : $v;
    };
    register_block_bindings_source('wugweb/record-field', array('label' => 'Wugweb record field', 'get_value_callback' => $get, 'uses_context' => array('postId', 'postType')));
    register_block_bindings_source('wugweb/term-field', array('label' => 'Wugweb industry term field', 'uses_context' => array('termId', 'taxonomy'), 'get_value_callback' => function ($a, $block) { $f = wugweb_fields_industry((int) ($block->context['termId'] ?? 0)); $v = $f[sanitize_key($a['key'] ?? '')] ?? null; return is_scalar($v) ? (string) $v : null; }));
});
/** Let the wugweb card blocks' text/url/image attributes be bound in the editor. */
add_filter('block_bindings_supported_attributes', function ($attrs, $block_type) {
    if (strpos($block_type, 'wugweb/') === 0) $attrs = array_values(array_unique(array_merge((array) $attrs, array('eyebrow', 'title', 'text', 'meta', 'url', 'imageUrl', 'imageAlt', 'heading'))));
    return $attrs;
}, 10, 2);
