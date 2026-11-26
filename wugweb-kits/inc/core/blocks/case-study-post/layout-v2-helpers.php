<?php
/**
 * Case-study layout v2: the switch and the asset hook. Loaded on every request, so it stays tiny.
 * v2 is OFF unless the option `wugweb_case_layout_v2` is truthy or the URL has ?cl=2 or ?cl=3 (preview).
 * The media blocks (split, mosaic, wall, rail, scrollshot, loop, All screens) additionally need the option `wugweb_case_blocks_v2` or ?cl=3.
 * Everything else lives in layout-v2.php / layout-v2-resolver.php, which only load when v2 renders.
 */
defined('ABSPATH') || exit;

/** True when the new case-study layout should render. Default: false (legacy output, unchanged). */
function wugweb_case_layout_v2_active() {
    if (get_option('wugweb_case_layout_v2')) { return true; }
    // Read-only display switch for previews; nothing is stored or changed.
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    // cl=3 is v2 plus the media blocks (layout-v2-media.php), so it switches v2 on too.
    return isset($_GET['cl']) && in_array(sanitize_text_field(wp_unslash((string) $_GET['cl'])), array('2', '3'), true);
}

/** CSS + the small video-toggle script, only on a case-study page that renders v2. */
function wugweb_case_layout_v2_enqueue() {
    $dir = __DIR__;
    $uri = get_theme_file_uri('inc/core/blocks/case-study-post');
    $ver = (string) wp_get_theme()->get('Version');
    $css = $dir . '/layout-v2.css'; $js = $dir . '/layout-v2.js';
    wp_enqueue_style('wugweb-case-layout-v2', $uri . '/layout-v2.css', array(), is_readable($css) ? $ver . '.' . filemtime($css) : $ver);
    wp_enqueue_script('wugweb-case-layout-v2', $uri . '/layout-v2.js', array(), is_readable($js) ? $ver . '.' . filemtime($js) : $ver, true);
}

add_action('wp_enqueue_scripts', function () {
    if (is_singular('case_study') && wugweb_case_layout_v2_active()) { wugweb_case_layout_v2_enqueue(); }
}, 20);
