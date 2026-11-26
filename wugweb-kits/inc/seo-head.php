<?php
/** Head basics: description, Open Graph, Twitter card, canonical, favicon fallback. */
defined('ABSPATH') || exit;
add_action('wp_head', function () {
    if (is_admin()) return;
    if (!has_site_icon()) echo '<link rel="icon" href="' . esc_url(get_theme_file_uri('assets/brand/favicon.svg')) . '" type="image/svg+xml">' . "\n" . '<link rel="apple-touch-icon" href="' . esc_url(get_theme_file_uri('assets/brand/apple-touch-icon.png')) . '">' . "\n";
    // Rank Math owns description, canonical, robots, og:*, twitter:* and JSON-LD when active: nothing duplicated from here.
    if (function_exists('wugweb_rank_math_active') && wugweb_rank_math_active()) return;
    $home_desc = 'Wugweb is a digital experience agency crafting brand strategy, UX design, martech, AI, and web development for ambitious teams.';
    if (is_front_page()) {
        $desc = $home_desc;
        $title = 'Wugweb — Digital Experience Agency';
        $url = home_url('/');
    } elseif (is_singular()) {
        $desc = has_excerpt() ? get_the_excerpt() : wp_trim_words(wp_strip_all_tags(strip_shortcodes((string) get_post_field('post_content', get_the_ID()))), 30);
        $title = wp_get_document_title();
        $url = get_permalink();
    } else {
        $desc = get_bloginfo('description');
        $title = wp_get_document_title();
        $url = home_url(add_query_arg(array()));
    }
    if (is_page('blog')) { $desc = "Field notes from Wugweb's design, engineering, and strategy work: what we're learning, testing, and shipping."; }
    if (is_page('work') || is_page('our-work') || is_post_type_archive('case_study') || untrailingslashit((string) wp_parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH)) === '/work') { $desc = 'Case studies from Wugweb: UX, design, martech, AI, and web projects for ambitious teams.'; }
    $desc = trim(preg_replace('/\s+/', ' ', (string) $desc));
    if ($desc !== '') echo '<meta name="description" content="' . esc_attr($desc) . '">' . "\n";
    echo '<meta property="og:type" content="' . (is_front_page() ? 'website' : 'article') . '">' . "\n";
    echo '<meta property="og:site_name" content="Wugweb">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    if ($desc !== '') echo '<meta property="og:description" content="' . esc_attr($desc) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
    $og = (is_singular() && has_post_thumbnail()) ? get_the_post_thumbnail_url(null, 'large') : '';
    if ($og === '' && is_singular('case_study')) { $og = (string) get_post_meta(get_the_ID(), 'wugweb_cs_cover_image', true); }
    if ($og === '') { $og = get_theme_file_uri('assets/brand/og-default.png'); }
    echo '<meta property="og:image" content="' . esc_url($og) . '">' . "\n";
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    if (!has_action('wp_head', 'rel_canonical')) echo '<link rel="canonical" href="' . esc_url($url) . '">' . "\n";
}, 5);
