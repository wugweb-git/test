<?php
/**
 * SEO engine (theme side). Rank Math stays the SEO engine; this file only
 *  1. gives it a "production origin" while the site is served from a test host (host rewrite in canonical / og / JSON-LD / sitemaps / robots.txt),
 *  2. keeps the test host out of search (noindex via wp_robots + X-Robots-Tag; never a robots.txt Disallow),
 *  3. adds real-data JSON-LD by template through the rank_math/json_ld filter (or prints one @graph itself when Rank Math is inactive).
 * Everything keys off wugweb_seo_origin(): empty on the real host, so it does nothing after cutover.
 */
defined('ABSPATH') || exit;

function wugweb_rank_math_active() { return defined('RANK_MATH_VERSION') || class_exists('RankMath', false); }

/** Production origin to present, or '' when the site is served from its real host. WUGWEB_SEO_ORIGIN overrides (empty string = off). */
function wugweb_seo_origin() {
    if (defined('WUGWEB_SEO_ORIGIN')) { return untrailingslashit((string) WUGWEB_SEO_ORIGIN); }
    return wugweb_is_test_host() ? 'https://www.wugweb.com' : '';
}
function wugweb_is_test_host() {
    $h = strtolower((string) wp_parse_url(home_url('/'), PHP_URL_HOST));
    return strpos($h, 'test.') === 0;
}
/** Site host bases that get rewritten (https/http of the current home host). */
function wugweb_seo_bases() {
    $h = (string) wp_parse_url(home_url('/'), PHP_URL_HOST);
    return $h === '' ? array() : array('https://' . $h, 'http://' . $h);
}
/** Swap the test host for the production origin in a URL or any string. Only the host part; R2 and other hosts are untouched. */
function wugweb_seo_url($s) {
    $o = wugweb_seo_origin(); if ($o === '' || !is_string($s) || $s === '') { return $s; }
    $out = str_replace(wugweb_seo_bases(), $o, $s);
    // JSON-escaped slashes
    foreach (wugweb_seo_bases() as $b) { $out = str_replace(str_replace('/', '\\/', $b), str_replace('/', '\\/', $o), $out); }
    return $out;
}
function wugweb_seo_deep($v) {
    if (is_string($v)) return wugweb_seo_url($v);
    if (is_array($v)) { foreach ($v as $k => $x) { $v[$k] = wugweb_seo_deep($x); } }
    return $v;
}

/* ── 1. Origin rewrite ─────────────────────────────────────────────── */
add_filter('rank_math/frontend/canonical', 'wugweb_seo_url');
add_filter('rank_math/opengraph/url', 'wugweb_seo_url');
// Version-independent: rewrite the finished <head> for canonical, og:url, og:image and JSON-LD bodies.
add_action('wp_head', function () { if (wugweb_seo_origin() !== '') ob_start(); }, 0);
add_action('wp_head', function () {
    if (wugweb_seo_origin() === '') return;
    $h = (string) ob_get_clean();
    $h = preg_replace_callback('#<link[^>]+rel=["\']canonical["\'][^>]*>#i', function ($m) { return wugweb_seo_url($m[0]); }, $h);
    $h = preg_replace_callback('#<meta[^>]+(?:property|name)=["\'](?:og:url|og:image|og:image:secure_url|twitter:image|twitter:url)["\'][^>]*>#i', function ($m) { return wugweb_seo_url($m[0]); }, $h);
    $h = preg_replace_callback('#<script[^>]+application/ld\+json[^>]*>.*?</script>#is', function ($m) { return wugweb_seo_url($m[0]); }, $h);
    echo $h; // phpcs:ignore
}, PHP_INT_MAX);
// Sitemaps (Rank Math sitemap_index.xml / *-sitemap.xml, core wp-sitemap*.xml): rewrite the whole response.
add_action('init', function () {
    if (wugweb_seo_origin() === '') return;
    $p = (string) wp_parse_url(isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '', PHP_URL_PATH);
    if (preg_match('#/(sitemap_index\.xml|sitemap\.xml|[a-z0-9_-]*-sitemap[0-9]*\.xml|wp-sitemap[a-z0-9_-]*\.(?:xml|xsl))$#i', $p)) { ob_start('wugweb_seo_url'); }
}, 0);
// robots.txt: sitemap at the production origin; no Disallow added (a disallow would hide the noindex from crawlers).
add_filter('robots_txt', function ($out) {
    $o = wugweb_seo_origin(); if ($o === '') return $out;
    $out = preg_replace('/^Sitemap:.*$\R?/mi', '', (string) $out);
    return rtrim($out) . "\n\nSitemap: " . $o . (wugweb_rank_math_active() ? '/sitemap_index.xml' : '/wp-sitemap.xml') . "\n";
}, 99);

/* ── 2. Test host stays out of search ──────────────────────────────── */
add_filter('wp_robots', function ($r) {
    if (!wugweb_is_test_host()) return $r;
    unset($r['index'], $r['follow'], $r['max-image-preview']);
    $r['noindex'] = true; $r['nofollow'] = true; return $r;
}, 99);
add_filter('rank_math/frontend/robots', function ($r) {
    if (!wugweb_is_test_host()) return $r;
    $r['index'] = 'noindex'; $r['follow'] = 'nofollow'; unset($r['max-snippet'], $r['max-video-preview'], $r['max-image-preview']); return $r;
}, 99);
add_action('init', function () { if (wugweb_is_test_host() && !headers_sent()) header('X-Robots-Tag: noindex, nofollow'); }, 0);

/* ── 3. JSON-LD: Rank Math's standard schema is used as is; the only thing added is FAQPage from authored wugweb/faq-list items ── */
function wugweb_seo_faq_items($post) {
    $o = array();
    $walk = function ($blocks) use (&$walk, &$o) {
        foreach ($blocks as $b) {
            if (($b['blockName'] ?? '') === 'wugweb/faq-list') { foreach ((array) ($b['attrs']['items'] ?? array()) as $it) { $q = trim(wp_strip_all_tags((string) ($it['q'] ?? ''))); $a = trim(wp_strip_all_tags((string) ($it['a'] ?? ''))); if ($q !== '' && $a !== '') $o[] = array($q, $a); } }
            if (!empty($b['innerBlocks'])) $walk($b['innerBlocks']);
        }
    };
    $walk(parse_blocks((string) $post->post_content));
    return $o;
}
add_filter('rank_math/json_ld', function ($data) {
    if (is_admin() || !is_array($data)) return $data;
    $q = get_queried_object();
    if ($q instanceof WP_Post) {
        $faq = wugweb_seo_faq_items($q);
        $has = false; foreach ($data as $g) { if (is_array($g) && in_array('FAQPage', (array) ($g['@type'] ?? array()), true)) { $has = true; } }
        if ($faq && !$has) {
            $qs = array(); foreach ($faq as $f) { $qs[] = array('@type' => 'Question', 'name' => $f[0], 'acceptedAnswer' => array('@type' => 'Answer', 'text' => $f[1])); }
            $data['wugweb_faq'] = array('@type' => 'FAQPage', '@id' => get_permalink($q) . '#faq', 'mainEntity' => $qs);
        }
    }
    return wugweb_seo_origin() !== '' ? wugweb_seo_deep($data) : $data;
}, 99);
