<?php
/**
 * Case-study layout v2: data + resolver.
 * wugweb_cl2_collect() reads the same post meta and parses the same post_content blocks as the legacy renderer (render.php, left untouched).
 * wugweb_cl2_resolve() is the WordPress subset of src/lib/case-layout.ts in the Astro site: it turns that data into an ordered block plan.
 * The resolver never invents fact or media: a field or file that is missing stays missing.
 * Media blocks (split, mosaic, wall, rail, scrollshot, loop, All screens) live in layout-v2-media.php and run only behind the switch (option wugweb_case_blocks_v2 or ?cl=3).
 * Not ported (no WP data to feed them): layoutHint, step/posterAt/audience/set, quote, table, timeline.
 */
defined('ABSPATH') || exit;

/** Chapter id from its title (slugifyChapter in case-layout.ts). */
function wugweb_cl2_slug($title, $i) {
    $s = strtolower(remove_accents((string) $title));
    $s = preg_replace('/[^a-z0-9\s-]/', '', $s);
    $s = preg_replace('/\s+/', '-', trim($s));
    $s = trim(preg_replace('/-+/', '-', $s), '-');
    return $s !== '' ? $s : 'chapter-' . ($i + 1);
}

/** Unique chapter id; never collides with the ids the page already uses (uniqueId in case-layout.ts). */
function wugweb_cl2_unique_id($base, array &$used) {
    $taken = function ($x) use (&$used) { return isset($used[$x]) || $x === 'case-study' || $x === 'work-cover'; };
    $id = $base; $n = 2;
    while ($taken($id)) { $id = $base . '-' . $n; $n++; }
    $used[$id] = true;
    return $id;
}

/** inline() from case-body.ts: escape, then **bold** and [text](https://url). Returns safe HTML. */
function wugweb_cl2_inline($text) {
    $s = esc_html((string) $text);
    $s = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $s);
    return preg_replace_callback('/\[([^\]]+)\]\((https?:\/\/[^)\s]+)\)/', function ($m) {
        return '<a href="' . esc_url(html_entity_decode($m[2], ENT_QUOTES)) . '" rel="noopener" target="_blank">' . $m[1] . '</a>';
    }, $s);
}

/** Result lines "value | what | source | as of" (optionally bulleted). Lines without a value and a label are skipped. */
function wugweb_cl2_parse_results($lines) {
    $out = array();
    foreach ((array) $lines as $line) {
        $line = trim(preg_replace('/^[-*\x{2022}]\s+/u', '', trim((string) $line)));
        if ($line === '' || strpos($line, '|') === false) { continue; }
        $c = array_map('trim', explode('|', $line));
        if (($c[0] ?? '') === '' || ($c[1] ?? '') === '') { continue; }
        $out[] = array('value' => $c[0], 'what' => $c[1], 'source' => $c[2] ?? '', 'asOf' => $c[3] ?? '');
    }
    return $out;
}

/** Poster for a video file (inc/core/video-posters.json: video key -> poster key), or ''. */
function wugweb_cl2_poster($src) {
    static $map = null;
    if (null === $map) { $f = dirname(__DIR__, 2) . '/video-posters.json'; $map = is_readable($f) ? (array) json_decode((string) file_get_contents($f), true) : array(); }
    if (!$map || !function_exists('wugweb_r2_key_from_url')) { return ''; }
    $k = wugweb_r2_key_from_url($src);
    return ($k !== '' && !empty($map[$k])) ? wugweb_r2_key_url($map[$k]) : '';
}

/**
 * Everything the page needs from the record: meta, parsed body sections, routed media, related.
 * The body parsing mirrors render.php so v2 sees exactly the sections the legacy page shows.
 */
function wugweb_cl2_collect($id) {
    $m = function ($k) use ($id) { return trim((string) get_post_meta($id, 'wugweb_cs_' . $k, true)); };
    // Media is used only where the file is on our own storage; Framer / Dribbble CDN links are not shown.
    $ok = function ($u) { $u = (string) $u; if (!preg_match('#^https?://#i', $u)) return false; $h = strtolower((string) wp_parse_url($u, PHP_URL_HOST)); return !preg_match('/(framerusercontent|dribbble)\./', $h); };
    $title = get_the_title($id);
    $client = $m('client');
    $brand = $m('short_title') ?: $client;
    $terms = function ($tax) use ($id) { $t = get_the_terms($id, $tax); return ($t && !is_wp_error($t)) ? implode(', ', array_map(function ($x) { return html_entity_decode($x->name, ENT_QUOTES); }, $t)) : ''; };
    $industry = html_entity_decode($m('industry') ?: $terms('industry'), ENT_QUOTES);
    $disc = array_values(array_filter(array_unique(array_map('trim', array_merge(explode(',', $m('primary_discipline')), explode(',', $m('additional_disciplines')))))));

    // Hero meta, in the Astro order. Empty values are left out.
    $meta = array();
    foreach (array('Client' => $client, 'Partner' => (strtolower($m('partner')) === 'none' ? '' : $m('partner')), 'Industry' => $industry, 'Disciplines' => implode(', ', $disc), 'Team' => $m('team'), 'Timeline' => $m('timeline'), 'Platform' => $m('platform'), 'Year' => $m('year'), 'Country' => $m('country'), 'Special Thanks' => $m('special_thanks')) as $k => $v) {
        if (trim((string) $v) !== '') { $meta[] = array($k, trim((string) $v)); }
    }
    $tags = array_values(array_filter(array_map('trim', explode(',', $m('categories')))));
    if (!$tags) { $t = get_the_terms($id, 'post_tag'); $tags = ($t && !is_wp_error($t)) ? array_map(function ($x) { return html_entity_decode($x->name, ENT_QUOTES); }, $t) : array(); }
    $cover = $m('cover_image');

    // Parse the stored body (same rules as render.php).
    $short = array(); $long = array(); $media = array(); $mode = 'short'; $cur = null;
    $push = function () use (&$cur, &$short, &$long, &$mode) { if ($cur && ($cur['paras'] || $cur['tiles'] || $cur['raw'])) { if ($mode === 'long') $long[] = $cur; elseif ($mode === 'short') $short[] = $cur; } $cur = null; };
    $txt = function ($html) { return trim(preg_replace('/\s+/', ' ', wp_strip_all_tags(html_entity_decode((string) $html, ENT_QUOTES)))); };
    foreach (parse_blocks((string) get_post_field('post_content', $id)) as $b) {
        $n = $b['blockName'] ?? '';
        if ($n === 'core/heading') {
            $t = $txt($b['innerHTML']);
            $push();
            if (preg_match('/^media$/i', $t)) { $mode = 'media'; continue; }
            if (preg_match('/site copy$/i', $t)) { $mode = 'long'; continue; }
            if (preg_match('/^gallery/i', $t)) { $mode = 'gallery'; continue; }
            if ($mode === 'short' || $mode === 'long') { $cur = array('title' => trim(preg_replace('/\s*\(context\)\s*/i', '', $t)), 'paras' => array(), 'tiles' => array(), 'raw' => array()); }
            continue;
        }
        if ($mode === 'media' || $mode === 'gallery') {
            if ($n === 'core/image' && preg_match('/<img[^>]*>/', $b['innerHTML'], $im)) { $alt = preg_match('/alt="([^"]*)"/', $im[0], $a) ? html_entity_decode($a[1]) : ''; $src = preg_match('/src="([^"]*)"/', $im[0], $s) ? $s[1] : ''; $media[] = array('alt' => $alt, 'src' => $src, 'video' => false); }
            elseif ($n === 'core/video' && preg_match('/src="([^"]*)"/', $b['innerHTML'], $s)) { $media[] = array('alt' => '', 'src' => $s[1], 'video' => true); }
            continue;
        }
        if (!$cur) continue;
        if ($n === 'core/paragraph') { $t = $txt($b['innerHTML']); if ($t !== '' && !preg_match('/^The long-form copy as published/i', $t)) { $cur['paras'][] = $t; $cur['raw'][] = $t; } }
        elseif ($n === 'core/list') { foreach ($b['innerBlocks'] as $li) { $t = $txt($li['innerHTML']); if ($t === '') continue; $cur['raw'][] = $t; if (preg_match('/^([^:]{2,70}):\s+(.+)$/s', $t, $mm)) $cur['tiles'][] = array($mm[1], $mm[2]); else $cur['tiles'][] = array('', $t); } }
    }
    $push();
    $secs = $long ?: $short;
    // Long copy: a short unpunctuated line followed by a longer one is a titled point (case-body.ts).
    foreach ($secs as $i => $sec) { $paras = array(); $n = count($sec['paras']); for ($k = 0; $k < $n; $k++) { $l = $sec['paras'][$k]; $nx = $sec['paras'][$k + 1] ?? null; if ($nx !== null && strlen($l) <= 70 && !preg_match('/[.:!?]$/', $l) && strlen($nx) > strlen($l)) { $secs[$i]['tiles'][] = array($l, $nx); $k++; } else { $paras[] = $l; } } $secs[$i]['paras'] = $paras; }

    // Route media to sections by label words (same words as the legacy page).
    $key = function ($s) { $s = strtolower($s); if (preg_match('/style/', $s)) return 'styles'; if (preg_match('/challenge|problem/', $s)) return 'challenges'; if (preg_match('/solution|approach|what we built/', $s)) return 'solutions'; if (preg_match('/philosoph|insight|thesis/', $s)) return 'philosophy'; if (preg_match('/conclusion|impact|outcome|result|filler/', $s)) return 'outcome'; if (preg_match('/about|context/', $s)) return 'about'; return $s; };
    $by = array(); $closing = array();
    foreach ($media as $md) {
        if (preg_match('/logo/i', $md['alt']) || preg_match('/^cover/i', $md['alt'])) continue;
        if (!$ok($md['src'])) continue; // missing stays missing: no grey box in v2
        if (function_exists('wugweb_cl3_active') && wugweb_cl3_active() && wugweb_cl3_skipped((string) get_post_field('post_name', $id), $md['src'])) continue; // manifest: dead link or duplicate
        $k = $key($md['alt']);
        $hit = -1; foreach ($secs as $i => $sec) { if ($key($sec['title']) === $k) { $hit = $i; break; } }
        if ($hit < 0 && $k === 'outcome' && $secs) $hit = count($secs) - 1;
        if ($hit < 0) { $closing[] = $md; continue; }
        $by[$hit][] = $md;
    }
    foreach ($secs as $i => $sec) { $secs[$i]['kind'] = $key($sec['title']); $secs[$i]['media'] = $by[$i] ?? array(); }

    // Results: the record's results field, one "value | what | source | as of" line each.
    $results = wugweb_cl2_parse_results(preg_split('~\R|<br\s*/?>~i', (string) get_post_meta($id, 'wugweb_cs_results', true)));

    // Credits from the record (same keys as credits() in case-layout.ts).
    $credits = array();
    foreach (array('Team' => $m('team'), 'Special thanks' => $m('special_thanks'), 'Engagement' => $m('engagement_type'), 'Project type' => $m('project_type'), 'Timeline' => $m('timeline'), 'Year' => $m('year'), 'Country' => $m('country')) as $k => $v) { if ($v !== '') { $credits[$k] = $v; } }

    // Where it lives: only links the record has.
    $links = array();
    foreach (array('Live' => $m('live_url'), 'Prototype' => $m('prototype_url')) as $k => $u) {
        if ($u !== '' && preg_match('#^https?://#i', $u)) { $links[] = array('value' => $k, 'text' => preg_replace('#^www\.#i', '', (string) wp_parse_url($u, PHP_URL_HOST)), 'href' => $u); }
    }

    // Related: the studies the record names (wugweb_cs_related_cases, comma separated slugs, in that order), then the most recent in the same industry,
    // then the most recent overall. Distinct titles, never this page, at most 3.
    $related = array(); $seen = array(strtolower($title)); $used = array($id);
    $add = function ($p) use (&$related, &$seen, &$used, $ok) {
        if (count($related) >= 3 || in_array($p->ID, $used, true)) return;
        $t = strtolower(get_the_title($p)); if (in_array($t, $seen, true)) return;
        $seen[] = $t; $used[] = $p->ID;
        $f = wugweb_fields_case_study($p->ID);
        $related[] = array('url' => $f['url'], 'title' => $f['short_title'] ?: $f['title'], 'industry' => $f['industry'] ? implode(', ', $f['industry']) : '', 'img' => $ok($f['image']) ? $f['image'] : '');
    };
    foreach (array_filter(array_map('trim', explode(',', (string) get_post_meta($id, 'wugweb_cs_related_cases', true)))) as $slug) {
        $p = get_page_by_path(sanitize_title($slug), OBJECT, 'case_study');
        if ($p && $p->post_status === 'publish') $add($p);
    }
    if (count($related) < 3) {
        $q = array('post_type' => 'case_study', 'post_status' => 'publish', 'numberposts' => 12, 'post__not_in' => $used, 'orderby' => 'date', 'order' => 'DESC');
        $tt = get_the_terms($id, 'industry');
        if ($tt && !is_wp_error($tt)) { $q['tax_query'] = array(array('taxonomy' => 'industry', 'field' => 'term_id', 'terms' => wp_list_pluck($tt, 'term_id'))); foreach (get_posts($q) as $p) $add($p); }
    }
    if (count($related) < 3) {
        foreach (get_posts(array('post_type' => 'case_study', 'post_status' => 'publish', 'numberposts' => 12, 'post__not_in' => $used, 'orderby' => 'date', 'order' => 'DESC')) as $p) $add($p);
    }
    // Up next: the case study published just before this one (the newest when this is the oldest). Not one already shown as related.
    $next = null;
    $prev = get_posts(array('post_type' => 'case_study', 'post_status' => 'publish', 'numberposts' => 1, 'post__not_in' => array($id), 'orderby' => 'date', 'order' => 'DESC', 'date_query' => array(array('before' => get_post_field('post_date', $id), 'inclusive' => false))));
    if (!$prev) $prev = get_posts(array('post_type' => 'case_study', 'post_status' => 'publish', 'numberposts' => 1, 'post__not_in' => array($id), 'orderby' => 'date', 'order' => 'DESC'));
    if ($prev) {
        $f = wugweb_fields_case_study($prev[0]->ID);
        $next = array('url' => $f['url'], 'title' => $f['short_title'] ?: $f['title'], 'industry' => $f['industry'] ? implode(', ', $f['industry']) : '', 'img' => $ok($f['image']) ? $f['image'] : '');
    }

    return array('id' => $id, 'title' => $title, 'brand' => $brand, 'subtitle' => $m('subtitle'), 'summary' => $m('ai_summary') ?: $m('seo_description'),
        'tags' => $tags, 'meta' => $meta, 'cover' => $ok($cover) ? $cover : '', 'secs' => $secs, 'closing' => $closing, 'results' => $results,
        'credits' => $credits, 'links' => $links, 'related' => $related, 'next' => $next);
}

/** Resolver: data -> ordered blocks (port of resolveLayout, WordPress subset). */
function wugweb_cl2_resolve(array $d) {
    $blocks = array();
    $brand = $d['brand'] !== '' ? $d['brand'] : $d['title'];
    $blocks[] = array('type' => 'hero', 'title' => $d['title'], 'subtitle' => $d['subtitle'], 'clientName' => $d['brand'], 'cover' => $d['cover'], 'coverAlt' => $brand . ' case study cover', 'meta' => $d['meta'], 'tags' => $d['tags']);
    if ($d['summary'] !== '') { $blocks[] = array('type' => 'summary', 'text' => $d['summary']); }
    $results = $d['results'];
    $secs = array(); $used = array();
    // Media blocks (layout-v2-media.php): only with the switch on AND the case study in the measured manifest; otherwise $idx stays empty and nothing below changes.
    $idx = array(); $overflow = array();
    if (function_exists('wugweb_cl3_active') && wugweb_cl3_active()) { $idx = wugweb_cl3_index((string) get_post_field('post_name', $d['id'])); }
    foreach ($d['secs'] as $s) {
        // A "Results" section made of value | what lines is the results list, not a chapter.
        if (preg_match('/^results$/i', trim($s['title']))) { $r = wugweb_cl2_parse_results($s['raw']); if ($r) { $results = $r; continue; } }
        $secs[] = $s;
    }
    if (count($results) >= 3) { $blocks[] = array('type' => 'stats', 'items' => array_slice($results, 0, 3)); }
    foreach ($secs as $i => $s) {
        $cid = wugweb_cl2_unique_id(wugweb_cl2_slug($s['title'], $i), $used);
        $blocks[] = array('type' => 'chapter', 'id' => $cid, 'title' => $s['title'], 'lead' => $s['paras'][0] ?? '', 'paras' => array_slice($s['paras'], 1));
        if ($s['tiles']) { $blocks[] = array('type' => 'points', 'items' => $s['tiles'], 'chapterId' => $cid); }
        $imgs = array(); $vids = array();
        foreach ($s['media'] as $md) { if ($md['video']) { $vids[] = $md; } else { $imgs[] = $md; } }
        // Videos first, then images (as in the resolver), then the styles pair.
        if ($idx) {
            // Measured manifest: loops, videos, scrollshots, figures (split / pair / mosaic / wall), rail.
            foreach (wugweb_cl3_chapter_blocks($s, $cid, $brand, $idx, $overflow) as $nb) { $blocks[] = $nb; }
        } else {
            foreach ($vids as $v) { $blocks[] = array('type' => 'video', 'src' => $v['src'], 'label' => $brand . ' case study video', 'poster' => wugweb_cl2_poster($v['src']), 'chapterId' => $cid); }
        }
        $label = $brand . ' case study image';
        if ($s['kind'] === 'styles') {
            // The styles images are the colour and type pair: two by two, a last odd one stands alone.
            for ($k = 0; $k < count($imgs); $k += 2) {
                if (isset($imgs[$k + 1])) { $blocks[] = array('type' => 'pair', 'left' => array('src' => $imgs[$k]['src'], 'alt' => $brand . ' style'), 'right' => array('src' => $imgs[$k + 1]['src'], 'alt' => $brand . ' style'), 'chapterId' => $cid); }
                else { $blocks[] = array('type' => 'figure', 'src' => $imgs[$k]['src'], 'alt' => $brand . ' style', 'chapterId' => $cid); }
            }
        } elseif (!$idx) {
            foreach ($imgs as $im) { $blocks[] = array('type' => 'figure', 'src' => $im['src'], 'alt' => $label, 'chapterId' => $cid); }
        }
    }
    if ($idx && ($d['closing'] || $overflow)) {
        // All screens (image files grouped into sets) + a plain closing grid for any videos.
        foreach (wugweb_cl3_closing($d['closing'], $overflow, $idx, $brand, $brand . ' case study video') as $nb) { $blocks[] = $nb; }
    } elseif ($d['closing']) { $blocks[] = array('type' => 'closing', 'items' => $d['closing'], 'label' => $brand . ' case study image', 'vlabel' => $brand . ' case study video'); }
    if ($results) { $blocks[] = array('type' => 'results', 'items' => $results); }
    if ($d['credits']) { $blocks[] = array('type' => 'credits', 'items' => $d['credits']); }
    if ($d['links']) { $blocks[] = array('type' => 'presence', 'links' => $d['links']); }
    if ($d['related']) { $blocks[] = array('type' => 'related', 'items' => $d['related']); }
    if (!empty($d['next'])) { $blocks[] = array('type' => 'next', 'item' => $d['next']); }
    return $blocks;
}
