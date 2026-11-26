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
    // Rank Math drops the canonical on noindex pages (the whole test host): emit the production one so each page has exactly one.
    if (!preg_match('#<link[^>]+rel=["\']canonical["\']#i', $h)) {
        $u = '';
        if (is_singular()) { $u = (string) get_permalink(get_queried_object_id()); }
        elseif (is_front_page()) { $u = home_url('/'); }
        elseif (is_post_type_archive()) { $u = (string) get_post_type_archive_link(get_query_var('post_type')); }
        elseif (is_tax() || is_category() || is_tag()) { $t = get_term_link(get_queried_object()); $u = is_wp_error($t) ? '' : (string) $t; }
        if ($u !== '') { $h .= '<link rel="canonical" href="' . esc_url($u) . '" />' . "\n"; }
    }
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

/* ── 3. JSON-LD by template ────────────────────────────────────────── */
function wugweb_seo_faq_items($post) {
    $o = array();
    $walk = function ($blocks) use (&$walk, &$o) {
        foreach ($blocks as $b) {
            if (($b['blockName'] ?? '') === 'wugweb/service-page' && function_exists('wugweb_core_service_data')) {
                $d = wugweb_core_service_data(sanitize_key($b['attrs']['pillar'] ?? 'brand'));
                foreach ((array) ($d['faqs'] ?? array()) as $f) { $q = trim(wp_strip_all_tags((string) ($f['q'] ?? ''))); $a = trim(wp_strip_all_tags((string) ($f['a'] ?? ''))); if ($q !== '' && $a !== '') $o[] = array($q, $a); }
            }
            if (($b['blockName'] ?? '') === 'wugweb/faq-list') { foreach ((array) ($b['attrs']['items'] ?? array()) as $it) { $q = trim(wp_strip_all_tags((string) ($it['q'] ?? ''))); $a = trim(wp_strip_all_tags((string) ($it['a'] ?? ''))); if ($q !== '' && $a !== '') $o[] = array($q, $a); } }
            if (!empty($b['innerBlocks'])) $walk($b['innerBlocks']);
        }
    };
    $walk(parse_blocks((string) $post->post_content));
    return $o;
}
/** Main image as ImageObject with Wugweb credit (no license URLs). */
function wugweb_seo_image($url) { return array('@type' => 'ImageObject', 'url' => $url, 'creator' => array('@type' => 'Organization', 'name' => 'Wugweb'), 'creditText' => 'Wugweb', 'copyrightNotice' => '© Wugweb'); }
/** R2 mp4 videos in a case study body. VideoObject needs a thumbnail: only videos with a real poster file (post meta wugweb_cs_video_poster, one per video in order) qualify. */
function wugweb_seo_videos($post) {
    $out = array(); $posters = array_values(array_filter(array_map('trim', explode(',', (string) get_post_meta($post->ID, 'wugweb_cs_video_poster', true)))));
    $walk = function ($blocks) use (&$walk, &$out) { foreach ($blocks as $b) { if (($b['blockName'] ?? '') === 'core/video' && preg_match('/src="([^"]+\.mp4[^"]*)"/i', (string) $b['innerHTML'], $m) && strpos($m[1], 'r2.dev') !== false) { $out[] = $m[1]; } if (!empty($b['innerBlocks'])) $walk($b['innerBlocks']); } };
    $walk(parse_blocks((string) $post->post_content));
    $res = array(); foreach ($out as $i => $src) { if (!empty($posters[$i])) { $res[] = array($src, $posters[$i]); } }
    return $res;
}
function wugweb_seo_entities() {
    $home = home_url('/'); $org_id = $home . '#organization'; $site_id = $home . '#website';
    $ent = array();
    $is_front = is_front_page();
    $logo = array('@type' => 'ImageObject', 'url' => 'https://pub-39ad1d03baec440faf48303be95c3782.r2.dev/assets/brand/logo.png', 'width' => 1014, 'height' => 352);
    $is_contact = is_page('contact-us');
    $ent['Organization'] = array('@type' => ($is_front || $is_contact) ? array('Organization', 'ProfessionalService') : 'Organization', '@id' => $org_id, 'name' => 'Wugweb', 'legalName' => 'Wug Web Services Private Limited', 'url' => $home, 'logo' => $logo,
        'email' => 'hello@wugweb.com', 'telephone' => '+919205996219', 'foundingDate' => '2020-12-04',
        'address' => array('@type' => 'PostalAddress', 'streetAddress' => 'Floor 19, WeWork Berger Delhi One, C-001/A2, Sector 16B', 'addressLocality' => 'Noida', 'addressRegion' => 'Uttar Pradesh', 'postalCode' => '201301', 'addressCountry' => 'IN'),
        'sameAs' => array('https://www.linkedin.com/company/wugweb', 'https://x.com/wugweb', 'https://www.facebook.com/wugweb', 'https://www.instagram.com/wugweb', 'https://www.youtube.com/channel/UC7qGSNvbvZqxZI8cA-Fijlg'));
    if ($is_front || $is_contact) { $ent['Organization']['image'] = $logo['url']; }
    $ent['WebSite'] = array('@type' => 'WebSite', '@id' => $site_id, 'url' => $home, 'name' => 'Wugweb', 'inLanguage' => 'en', 'publisher' => array('@id' => $org_id));
    if (is_front_page()) { $ent['WebSite']['potentialAction'] = array('@type' => 'SearchAction', 'target' => array('@type' => 'EntryPoint', 'urlTemplate' => $home . '?s={search_term_string}'), 'query-input' => 'required name=search_term_string'); }
    // Breadcrumbs: Home › section › page.
    $crumbs = array(array('Home', $home));
    $q = get_queried_object();
    if (is_singular()) {
        $id = get_the_ID(); $pt = get_post_type($id);
        if ($pt === 'post') $crumbs[] = array('Blog', home_url('/blog/'));
        elseif ($pt === 'case_study') $crumbs[] = array('Work', home_url('/work/'));
        elseif ($pt === 'page' && in_array($q->post_name, array('brand', 'strategy', 'experience', 'engineering', 'growth', 'operations'), true)) $crumbs[] = array('Services', home_url('/services/'));
        if (!is_front_page()) $crumbs[] = array(wp_strip_all_tags(get_the_title($id)), get_permalink($id));
    } elseif (is_post_type_archive('case_study')) { $crumbs[] = array('Work', home_url('/work/')); }
    if (count($crumbs) > 1) {
        $items = array(); foreach ($crumbs as $i => $c) { $items[] = array('@type' => 'ListItem', 'position' => $i + 1, 'name' => $c[0], 'item' => $c[1]); }
        $ent['BreadcrumbList'] = array('@type' => 'BreadcrumbList', '@id' => (is_singular() ? get_permalink() : $home) . '#breadcrumb', 'itemListElement' => $items);
    }
    if (is_singular()) {
        $id = get_the_ID(); $url = get_permalink($id); $pt = get_post_type($id);
        $desc = trim(wp_strip_all_tags((string) (get_post_meta($id, 'wugweb_seo_description', true) ?: get_post_meta($id, 'wugweb_page_seo_description', true) ?: get_the_excerpt($id))));
        if ($pt === 'post') {
            $cover = get_post_meta($id, 'wugweb_og_image', true) ?: get_post_meta($id, 'wugweb_cover_url', true) ?: (has_post_thumbnail($id) ? get_the_post_thumbnail_url($id, 'full') : '');
            $author = get_post_meta($id, 'wugweb_author_name', true) ?: get_the_author_meta('display_name', get_post_field('post_author', $id));
            $a = array('@type' => get_post_meta($id, 'wugweb_schema_type', true) ?: 'BlogPosting', '@id' => $url . '#article', 'headline' => wp_strip_all_tags(get_the_title($id)), 'url' => $url, 'mainEntityOfPage' => array('@id' => $url . '#webpage'),
                'datePublished' => get_the_date('c', $id), 'dateModified' => get_the_modified_date('c', $id), 'inLanguage' => 'en', 'publisher' => array('@type' => 'Organization', 'name' => 'Wugweb', 'url' => $home, 'logo' => $logo));
            if ($desc !== '') $a['description'] = $desc;
            if ($author) $a['author'] = array('@type' => 'Person', 'name' => $author, 'url' => home_url('/about/'));
            if ($cover) $a['image'] = array(wugweb_seo_image($cover));
            if (get_post_meta($id, 'wugweb_subtitle', true)) $a['alternativeHeadline'] = get_post_meta($id, 'wugweb_subtitle', true);
            if (get_post_meta($id, 'wugweb_focus_keyword', true)) $a['keywords'] = get_post_meta($id, 'wugweb_focus_keyword', true);
            if ((int) get_post_meta($id, 'wugweb_word_count', true)) $a['wordCount'] = (int) get_post_meta($id, 'wugweb_word_count', true);
            $ent['Article'] = $a;
        } elseif ($pt === 'case_study') {
            $m = function ($k) use ($id) { return trim((string) get_post_meta($id, 'wugweb_cs_' . $k, true)); };
            $cw = array('@type' => 'CreativeWork', '@id' => $url . '#case-study', 'name' => wp_strip_all_tags(get_the_title($id)), 'url' => $url, 'mainEntityOfPage' => array('@id' => $url . '#webpage'),
                'datePublished' => get_the_date('c', $id), 'dateModified' => get_the_modified_date('c', $id), 'inLanguage' => 'en', 'creator' => array('@id' => $org_id), 'publisher' => array('@id' => $org_id));
            if ($m('subtitle') !== '') $cw['alternativeHeadline'] = $m('subtitle');
            if ($desc !== '') $cw['description'] = $desc; elseif ($m('subtitle') !== '') $cw['description'] = $m('subtitle');
            if ($m('client') !== '') $cw['about'] = array('@type' => 'Organization', 'name' => $m('client'));
            $ind = $m('industry'); if ($ind === '') { $t = get_the_terms($id, 'industry'); if ($t && !is_wp_error($t)) $ind = html_entity_decode($t[0]->name, ENT_QUOTES); }
            if ($ind !== '') $cw['genre'] = $ind;
            $disc = array_values(array_filter(array_unique(array_map('trim', array_merge(explode(',', $m('primary_discipline')), explode(',', $m('additional_disciplines')))))));
            if ($disc) $cw['keywords'] = implode(', ', $disc);
            $cv = $m('cover_image'); if ($cv === '' && has_post_thumbnail($id)) $cv = get_the_post_thumbnail_url($id, 'full');
            if ($cv !== '' && !preg_match('/(framerusercontent|dribbble)\./', $cv)) $cw['image'] = array(wugweb_seo_image($cv));
            $ent['CreativeWork'] = $cw;
            foreach (wugweb_seo_videos($q) as $vi => $v) { $ent['VideoObject' . $vi] = array('@type' => 'VideoObject', '@id' => $url . '#video-' . ($vi + 1), 'name' => wp_strip_all_tags(get_the_title($id)) . ' (video ' . ($vi + 1) . ')', 'description' => $desc !== '' ? $desc : wp_strip_all_tags(get_the_title($id)), 'thumbnailUrl' => $v[1], 'uploadDate' => get_the_date('c', $id), 'contentUrl' => $v[0]); }
        } elseif ($pt === 'page' && in_array($q->post_name, array('brand', 'strategy', 'experience', 'engineering', 'growth', 'operations'), true)) {
            $s = array('@type' => 'Service', '@id' => $url . '#service', 'name' => wp_strip_all_tags(get_the_title($id)), 'url' => $url, 'provider' => array('@id' => $org_id), 'serviceType' => ucfirst($q->post_name));
            if ($desc !== '') $s['description'] = $desc;
            $ent['Service'] = $s;
        }
        $faq = wugweb_seo_faq_items($q);
        if ($faq) {
            $qs = array(); foreach ($faq as $f) { $qs[] = array('@type' => 'Question', 'name' => $f[0], 'acceptedAnswer' => array('@type' => 'Answer', 'text' => $f[1])); }
            $ent['FAQPage'] = array('@type' => 'FAQPage', '@id' => $url . '#faq', 'mainEntity' => $qs);
        }
    }
    return $ent;
}
/** Merge our entities into a graph: same @type present = merge into it (keeps its @id); otherwise add. */
/** Strip placeholder / unsupported business fields Rank Math may add (hours, price range, geo). */
function wugweb_seo_clean(array $graph) {
    foreach ($graph as $k => $g) {
        if (!is_array($g)) continue;
        $t = (array) ($g['@type'] ?? array());
        if (array_intersect($t, array('Organization', 'LocalBusiness', 'ProfessionalService', 'Corporation'))) { unset($graph[$k]['openingHoursSpecification'], $graph[$k]['openingHours'], $graph[$k]['priceRange'], $graph[$k]['geo'], $graph[$k]['hasMap'], $graph[$k]['aggregateRating'], $graph[$k]['review']); if (isset($graph[$k]['location']) && is_array($graph[$k]['location']) && isset($graph[$k]['location']['openingHoursSpecification'])) unset($graph[$k]['location']['openingHoursSpecification']); }
    }
    return $graph;
}
function wugweb_seo_merge(array $graph, array $ours) {
    $alias = array('Article' => array('Article', 'BlogPosting', 'TechArticle', 'NewsArticle'));
    foreach ($ours as $key => $e) {
        $types = $alias[$key] ?? array($e['@type']);
        $hit = null;
        foreach ($graph as $k => $g) { if (!is_array($g)) continue; $t = (array) ($g['@type'] ?? array()); if (array_intersect($t, $types)) { $hit = $k; break; } }
        if ($hit === null) { $graph[$key] = $e; continue; }
        if ($key === 'FAQPage') { continue; }   // Rank Math already emits one for this page: leave it alone
        if ($key === 'BreadcrumbList') { $graph[$hit] = $e; continue; }   // ours is the complete trail
        $keep_id = $graph[$hit]['@id'] ?? null;
        $graph[$hit] = array_replace($graph[$hit], $e);
        if ($keep_id) $graph[$hit]['@id'] = $keep_id;
    }
    return $graph;
}
add_filter('rank_math/json_ld', function ($data) {
    if (is_admin() || !is_array($data)) return $data;
    $data = wugweb_seo_clean(wugweb_seo_merge($data, wugweb_seo_entities()));
    return wugweb_seo_origin() !== '' ? wugweb_seo_deep($data) : $data;
}, 99);
// Rank Math inactive: the same entities as one @graph (never alongside Rank Math's).
add_action('wp_head', function () {
    if (is_admin() || wugweb_rank_math_active()) return;
    $g = array_values(wugweb_seo_entities());
    $g = wugweb_seo_origin() !== '' ? wugweb_seo_deep($g) : $g;
    echo '<script type="application/ld+json">' . wp_json_encode(array('@context' => 'https://schema.org', '@graph' => $g), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n"; // phpcs:ignore
}, 6);
