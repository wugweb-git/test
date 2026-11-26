<?php
/**
 * Case-study layout v2, media blocks: split, mosaic, wall, rail, scrollshot (tall figure), loop and the All screens viewer.
 * Port of the media half of resolveLayout()/classifyMedia()/screenSets() in src/lib/case-layout.ts (wugweb_site), driven by the measured
 * manifest in inc/core/media-manifest.json instead of the Astro body labels.
 *
 * SAFETY SWITCH: nothing here runs unless wugweb_cl3_active() (layout v2 is on AND option `wugweb_case_blocks_v2` is truthy or the URL has ?cl=3),
 * and even then only for a case study that is in the manifest. Every other case study keeps single figures, styles pairs and the closing grid.
 *
 * The manifest has no audience, step, set, caption or layoutHint. Fallbacks, all from the resolver's own rules or the layout grammar:
 *  - audience: always "other" (so a set is named by its flow, as screenSets() does for "other"); step: source order; set: none;
 *  - layoutHint: absent, so the grammar's shape rule picks the figure block (see wugweb_cl3_chapter_blocks);
 *  - kind: screen = phone shape (tall shape too when a chapter has 3+ phone/tall files); scrollshot = shape "scrollshot" or a label with "scroll"; loop = a video of 15 s or less with no audio track.
 */
defined('ABSPATH') || exit;

const WUGWEB_CL3_RAIL_MAX = 8;   // RAIL_MAX
const WUGWEB_CL3_MOSAIC_MIN = 3; // MOSAIC_MIN
const WUGWEB_CL3_MOSAIC_MAX = 6; // MOSAIC_MAX
const WUGWEB_CL3_WALL_MIN = 7;   // WALL_MIN
const WUGWEB_CL3_LOOP_MAX_S = 15; // a video over 15 s never loops (case-study.md section 4)

/** True when the media blocks should render. Default: false. Read-only: nothing is stored or changed. */
function wugweb_cl3_active() {
    static $on = null;
    if (null !== $on) { return $on; }
    if (!function_exists('wugweb_case_layout_v2_active') || !wugweb_case_layout_v2_active()) { return $on = false; }
    if (get_option('wugweb_case_blocks_v2')) { return $on = true; }
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    return $on = (isset($_GET['cl']) && '3' === sanitize_text_field(wp_unslash((string) $_GET['cl'])));
}

/** Bucket key for a URL, remap applied; falls back to the path after the host when the r2 helper is missing. */
function wugweb_cl3_key($url) {
    if (function_exists('wugweb_r2_key_from_url')) { $k = wugweb_r2_key_from_url($url); if ($k !== '') { return $k; } }
    return ltrim(rawurldecode((string) wp_parse_url(strtok((string) $url, '?#'), PHP_URL_PATH)), '/');
}

/** The measured manifest, cached for the request: slug => key => entry (type, w, h, shape, flow, poster, duration, audio, label). */
function wugweb_cl3_manifest() {
    static $m = null;
    if (null !== $m) { return $m; }
    $m = array();
    $f = dirname(__DIR__, 2) . '/media-manifest.json';
    $j = is_readable($f) ? json_decode((string) file_get_contents($f), true) : null;
    if (!is_array($j)) { return $m; }
    $rm = function_exists('wugweb_r2_remap') ? wugweb_r2_remap() : array();
    foreach ($j as $slug => $entries) {
        if (!is_array($entries)) { continue; }
        foreach ($entries as $e) {
            if (empty($e['url'])) { continue; }
            $k = wugweb_cl3_key($e['url']);
            if ($k === '') { continue; }
            $k_before = $k;
            if (isset($rm[$k])) { $k = $rm[$k]; }
            // Dead links and duplicates are a skip-list, never rendered (the manifest is a skip-list, not an allow-list).
            if ((isset($e['status']) && 200 !== (int) $e['status']) || !empty($e['dupOf'])) {
                // A dead URL that the R2 remap points at a live file was reorganised, not lost: the remap serves it, so it is not skipped.
                $raw = ltrim(rawurldecode((string) wp_parse_url(strtok((string) $e['url'], '?#'), PHP_URL_PATH)), '/');
                if (!isset($rm[$raw]) && !isset($rm[$k_before])) { $GLOBALS['wugweb_cl3_skip'][$slug][$k] = true; }
                continue;
            }
            $m[$slug][$k] = $e;
        }
    }
    return $m;
}

/** The manifest index for one case study, or array() when it is not in the manifest (then none of the media blocks apply). */
function wugweb_cl3_index($slug) {
    $m = wugweb_cl3_manifest();
    return isset($m[$slug]) ? $m[$slug] : array();
}

/** The manifest entry for a file, or null. */
function wugweb_cl3_entry(array $idx, $src) {
    $k = wugweb_cl3_key($src);
    return ($k !== '' && isset($idx[$k])) ? $idx[$k] : null;
}

/** [w, h] for a file: img-dims.json first, then the manifest's measured size (a lookup merged at read time; img-dims.json is not edited). null when neither knows it. */
function wugweb_cl3_dims($src) {
    static $flat = null;
    $k = wugweb_cl3_key($src);
    if ($k === '') { return null; }
    if (function_exists('wugweb_r2_img_dims')) { $dm = wugweb_r2_img_dims(); if (!empty($dm[$k][0]) && !empty($dm[$k][1])) { return array((int) $dm[$k][0], (int) $dm[$k][1]); } }
    if (null === $flat) {
        $flat = array();
        foreach (wugweb_cl3_manifest() as $ix) { foreach ($ix as $key => $e) { if (!empty($e['w']) && !empty($e['h'])) { $flat[$key] = array((int) $e['w'], (int) $e['h']); } } }
    }
    return isset($flat[$k]) ? $flat[$k] : null;
}

/** ' width=".." height=".."' from wugweb_cl3_dims(), or ''. */
function wugweb_cl3_dims_attr($src) {
    $d = wugweb_cl3_dims($src);
    return $d ? ' width="' . (int) $d[0] . '" height="' . (int) $d[1] . '"' : '';
}

/** isLoopLike() in case-layout.ts. */
function wugweb_cl3_loop_like($label) { return (bool) preg_match('/\b(loop|hover|anim|interaction|preview)\b/i', (string) $label); }
/** looksScrollshot() in case-layout.ts. */
function wugweb_cl3_scroll_like($label) { return (bool) preg_match('/\bscroll(shot)?\b/i', (string) $label); }

/** A video is a loop when it is 15 s or less (the grammar) and has no audio track, or its label says loop and it is not over 15 s. Unknown duration: a click-to-play video, as before. */
function wugweb_cl3_is_loop(array $md, $e) {
    if (!$e || ($e['type'] ?? '') !== 'video') { return false; }
    $dur = isset($e['duration']) ? (float) $e['duration'] : null;
    if (null === $dur || $dur > WUGWEB_CL3_LOOP_MAX_S) { return false; }
    if (!empty($e['audio'])) { return false; }
    return true;
}

/** Figma auto-names and hashes are not set names; the chip falls back to "Screens". */
function wugweb_cl3_flow_name($flow) {
    $f = trim((string) $flow);
    if ($f === '' || strtolower($f) === 'unsorted') { return ''; }
    if (preg_match('/^(group|frame|image|mask group|rectangle|vector|property \d.*|screenshot.*|untitled.*|scene.*|banner.*|f|da)\s*\d*$/i', $f)) { return ''; }
    if (preg_match('/^[0-9a-f ]{18,}$/i', $f) || !preg_match('/[a-z]{3}/i', $f)) { return ''; }
    return $f;
}

/** sentenceCase() in case-layout.ts. */
function wugweb_cl3_sentence($s) {
    $t = trim(preg_replace('/[-_]+/', ' ', (string) $s));
    return $t === '' ? $t : strtoupper(substr($t, 0, 1)) . substr($t, 1);
}

/** setSlug() in case-layout.ts. */
function wugweb_cl3_set_slug($s) {
    $x = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower((string) $s)), '-');
    return $x !== '' ? $x : 'all';
}

/** One media item as the blocks carry it: file, alt, size from the manifest, shape. */
function wugweb_cl3_item(array $md, $e, $alt) {
    return array('src' => $md['src'], 'alt' => $alt, 'w' => $e ? (int) ($e['w'] ?? 0) : 0, 'h' => $e ? (int) ($e['h'] ?? 0) : 0, 'shape' => $e['shape'] ?? '');
}

/** Poster for a loop: video-posters.json first, then the manifest's own poster when it is on our storage. */
function wugweb_cl3_poster($src, $e) {
    $p = wugweb_cl2_poster($src);
    if ($p !== '') { return $p; }
    $mp = (string) ($e['poster'] ?? '');
    if ($mp !== '' && preg_match('#^https?://#i', $mp) && !preg_match('/(framerusercontent|dribbble)\./i', (string) wp_parse_url($mp, PHP_URL_HOST))) { return $mp; }
    return '';
}

/**
 * One chapter's media -> blocks, in the resolver's order: loops, videos, scrollshots, figures (split / pair / mosaic / wall / single), rail.
 * The styles chapter keeps its pair logic in the caller. $overflow collects screens beyond the rail cap for All screens.
 */
function wugweb_cl3_chapter_blocks(array $s, $cid, $brand, array $idx, array &$overflow) {
    $out = array();
    $label = $brand . ' case study image';
    $loops = array(); $videos = array(); $scrolls = array(); $screens = array(); $talls = array(); $figs = array();
    foreach ($s['media'] as $md) {
        $e = wugweb_cl3_entry($idx, $md['src']);
        if ($md['video']) {
            if (wugweb_cl3_is_loop($md, $e)) { $loops[] = array($md, $e); } else { $videos[] = array($md, $e); }
            continue;
        }
        if ($s['kind'] === 'styles') { continue; } // the caller pairs these
        $shape = $e['shape'] ?? '';
        if ($shape === 'scrollshot' || wugweb_cl3_scroll_like($md['alt'])) { $scrolls[] = wugweb_cl3_item($md, $e, $label); }
        elseif ($shape === 'phone') { $screens[] = wugweb_cl3_item($md, $e, ''); }
        elseif ($shape === 'tall') { $talls[] = wugweb_cl3_item($md, $e, $label); }
        else { $figs[] = wugweb_cl3_item($md, $e, $label); }
    }
    // "Tall" (0.58 to 0.82) is a screen only when the chapter has a rail's worth (3+ phone/tall files, the grammar's 3 <= n); fewer are ordinary figures.
    if (count($screens) + count($talls) >= 3) { foreach ($talls as $t) { $t['alt'] = ''; $screens[] = $t; } }
    else { foreach ($talls as $t) { $figs[] = $t; } }
    foreach ($loops as $l) {
        $d = wugweb_cl3_item($l[0], $l[1], '');
        $out[] = array('type' => 'loop', 'src' => $l[0]['src'], 'poster' => wugweb_cl3_poster($l[0]['src'], $l[1]), 'w' => $d['w'], 'h' => $d['h'], 'chapterId' => $cid);
    }
    foreach ($videos as $v) { $out[] = array('type' => 'video', 'src' => $v[0]['src'], 'label' => $brand . ' case study video', 'poster' => wugweb_cl3_poster($v[0]['src'], $v[1]), 'chapterId' => $cid); }
    foreach ($scrolls as $it) { $out[] = array('type' => 'scrollshot', 'item' => $it, 'title' => $s['title'], 'chapterId' => $cid); }

    // No layoutHint in the data: the layout grammar's shape rule stands in. 7+ -> wall, 3 to 6 -> mosaic, 2 of one shape -> pair, 2 of different shapes -> split, 1 -> figure.
    $n = count($figs);
    if ($n >= WUGWEB_CL3_WALL_MIN) {
        $out[] = array('type' => 'wall', 'items' => $figs, 'title' => $s['title'], 'chapterId' => $cid);
    } elseif ($n >= WUGWEB_CL3_MOSAIC_MIN) {
        $out[] = array('type' => 'mosaic', 'items' => array_slice($figs, 0, WUGWEB_CL3_MOSAIC_MAX), 'chapterId' => $cid);
        foreach (array_slice($figs, WUGWEB_CL3_MOSAIC_MAX) as $f) { $out[] = array('type' => 'figure', 'src' => $f['src'], 'alt' => $f['alt'], 'chapterId' => $cid); }
    } elseif ($n === 2) {
        if ($figs[0]['shape'] === $figs[1]['shape']) { $out[] = array('type' => 'pair', 'left' => array('src' => $figs[0]['src'], 'alt' => $label), 'right' => array('src' => $figs[1]['src'], 'alt' => $label), 'chapterId' => $cid); }
        else { $out[] = array('type' => 'split', 'left' => $figs[0], 'right' => $figs[1], 'chapterId' => $cid); }
    } elseif ($n === 1) {
        $out[] = array('type' => 'figure', 'src' => $figs[0]['src'], 'alt' => $figs[0]['alt'], 'chapterId' => $cid);
    }

    // Screens in a chapter: the proof rail, at most RAIL_MAX; the rest go to All screens.
    if ($screens) {
        $rail = array_slice($screens, 0, WUGWEB_CL3_RAIL_MAX);
        $tot = count($rail);
        foreach ($rail as $i => $it) { $rail[$i]['alt'] = $brand . ' screen ' . ($i + 1) . ' of ' . $tot; }
        $out[] = array('type' => 'rail', 'items' => $rail, 'flow' => '', 'chapterId' => $cid);
        foreach (array_slice($screens, WUGWEB_CL3_RAIL_MAX) as $it) { $overflow[] = $it; }
    }
    return $out;
}

/** isWide() in case-layout.ts. */
function wugweb_cl3_is_wide(array $it) { return $it['w'] && $it['h'] ? $it['w'] > $it['h'] : false; }

/**
 * Closing media -> the All screens block (image files grouped into sets by flow) plus a plain closing block for any videos.
 * screensBucket()/screenSets(): audience is always "other", so a set is named by its flow; with no usable flow the kind fallback "Screens" is used.
 * A named set with a single file joins "Screens" so the chip row stays short. Sets keep the order in which the body first shows them.
 */
function wugweb_cl3_closing(array $closing, array $overflow, array $idx, $brand, $vlabel) {
    $out = array();
    $order = array(); $sets = array(); $videos = array();
    $rows = array(); $count = array();
    foreach ($overflow as $it) { $rows[] = array('Screens', $it); }
    foreach ($closing as $md) {
        if ($md['video']) { $videos[] = $md; continue; }
        $e = wugweb_cl3_entry($idx, $md['src']);
        $flow = wugweb_cl3_flow_name($e['flow'] ?? '');
        $name = $flow !== '' ? wugweb_cl3_sentence($flow) : 'Screens';
        $rows[] = array($name, wugweb_cl3_item($md, $e, ''));
    }
    foreach ($rows as $r) { $count[$r[0]] = ($count[$r[0]] ?? 0) + 1; }
    foreach ($rows as $r) {
        $name = ($count[$r[0]] >= 2) ? $r[0] : 'Screens'; // a named set with a single file joins "Screens"
        if (!isset($sets[$name])) { $sets[$name] = array(); $order[] = $name; }
        $sets[$name][] = $r[1];
    }
    $final = array(); $used = array();
    foreach ($order as $name) {
        if (empty($sets[$name])) { continue; }
        $items = $sets[$name]; $n = count($items);
        foreach ($items as $i => $it) { $items[$i]['alt'] = $brand . ' screen ' . ($i + 1) . ' of ' . $n; }
        $base = 'screens-other-' . wugweb_cl3_set_slug($name); $id = $base;
        for ($x = 2; isset($used[$id]); $x++) { $id = $base . '-' . $x; }
        $used[$id] = true;
        $wide = 0; foreach ($items as $it) { if (wugweb_cl3_is_wide($it)) { $wide++; } }
        $final[] = array('id' => $id, 'label' => $name, 'shape' => ($wide * 2 > $n) ? 'wide' : 'phone', 'items' => $items);
    }
    if ($final) { $out[] = array('type' => 'screens', 'sets' => $final, 'brand' => $brand); }
    if ($videos) { $out[] = array('type' => 'closing', 'items' => $videos, 'label' => $brand . ' case study image', 'vlabel' => $vlabel); }
    return $out;
}

/** Block types the media blocks add; layout-v2.php uses this to load the script only when one is on the page. */
function wugweb_cl3_has_script_blocks(array $blocks) {
    foreach ($blocks as $b) { if (in_array($b['type'], array('rail', 'screens', 'loop', 'wall', 'scrollshot'), true)) { return true; } }
    return false;
}

/** Load the small script (rail, chips, viewer, loops) for a page that has one of those blocks. */
function wugweb_cl3_enqueue_script() {
    $file = __DIR__ . '/layout-v2-blocks.js';
    $ver = (string) wp_get_theme()->get('Version');
    wp_enqueue_script('wugweb-case-layout-v2-blocks', get_theme_file_uri('inc/core/blocks/case-study-post/layout-v2-blocks.js'), array(), is_readable($file) ? $ver . '.' . filemtime($file) : $ver, true);
}


/** True when the manifest marks this file of this case study as dead (status not 200) or a duplicate. Only meaningful while the media blocks are on. */
function wugweb_cl3_skipped($slug, $url) {
    wugweb_cl3_manifest();
    $k = wugweb_cl3_key($url);
    return $k !== '' && !empty($GLOBALS['wugweb_cl3_skip'][$slug][$k]);
}
