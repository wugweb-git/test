<?php
/** Wugweb Kits theme bootstrap. */
add_action('after_setup_theme', function () {
    add_theme_support('wp-block-styles');
    add_theme_support('editor-styles');
});

/** Read tenant values from the SCF Design System options page. */
function wugweb_kits_tenant_tokens() {
    $get = static function ($name, $fallback = '') {
        if (function_exists('get_field')) {
            $value = get_field($name, 'option');
            if ($value !== null && $value !== '') return $value;
        }
        return $fallback;
    };
    $hex = static function ($value, $fallback) {
        $clean = sanitize_hex_color((string) $value);
        return $clean ?: $fallback;
    };
    $radius = (string) $get('corner_radius', '8px');
    if (!preg_match('/^[0-9.]+(px|rem|em|%)$/', $radius)) $radius = '16px';
    $tenant = sanitize_text_field((string) $get('tenant_name', 'wugweb'));
    return array(
        'tenant' => $tenant ?: 'wugweb',
        'primary' => $hex($get('primary_color', ''), '#ffbe1a'),
        'secondary' => $hex($get('secondary_color', ''), '#2167e9'),
        'accent' => $hex($get('accent_color', ''), '#09e2c0'),
        'body_font' => sanitize_text_field((string) $get('body_font', 'Inter Tight, system-ui, sans-serif')),
        'radius' => $radius,
    );
}

/** Make SCF tenant values override the shared theme token layer. */
function wugweb_kits_tenant_css() {
    $t = wugweb_kits_tenant_tokens();
    return ':root{' .
        '--wugweb-tenant:' . esc_attr($t['tenant']) . ';' .
        '--wugweb-tenant-primary:' . esc_attr($t['primary']) . ';' .
        '--wugweb-tenant-secondary:' . esc_attr($t['secondary']) . ';' .
        '--wugweb-tenant-accent:' . esc_attr($t['accent']) . ';' .
        '--wugweb-tenant-radius:' . esc_attr($t['radius']) . ';' .
        '--wp--preset--color--yellow:' . esc_attr($t['primary']) . ';' .
        '--wp--preset--color--blue:' . esc_attr($t['secondary']) . ';' .
        '--wp--preset--color--aqua:' . esc_attr($t['accent']) . ';' .
        '--wp--preset--font-family--body:' . esc_attr($t['body_font']) . ';' .
        '} .wp-block-button__link,.wugweb-scope-cta{border-radius:var(--wugweb-tenant-radius);}' .
        '.wugweb-tenant-name{content:"' . esc_attr($t['tenant']) . '";}';
}
add_action('wp_head', function () {
    if (!is_admin()) echo '<style id="wugweb-tenant-design-system">' . wugweb_kits_tenant_css() . '</style>';
}, 20);
add_action('enqueue_block_editor_assets', function () {
    wp_register_style('wugweb-tenant-editor', false, array(), wp_get_theme()->get('Version'));
    wp_enqueue_style('wugweb-tenant-editor');
    wp_add_inline_style('wugweb-tenant-editor', wugweb_kits_tenant_css());
});
add_filter('body_class', function ($classes) {
    $classes[] = 'wugweb-tenant-' . sanitize_html_class(strtolower(wugweb_kits_tenant_tokens()['tenant']));
    return $classes;
});

require_once __DIR__ . '/inc/motion-enqueue.php';

add_action('wp_enqueue_scripts', function () { wp_enqueue_style('wugweb-site-shell', get_theme_file_uri('assets/css/site-shell.css'), array('wugweb-layout-shell'), wp_get_theme()->get('Version')); wp_enqueue_script('wugweb-site-header', get_theme_file_uri('assets/js/site-header.js'), array(), wp_get_theme()->get('Version'), true); wp_enqueue_style('wugweb-blocks', get_theme_file_uri('assets/css/wugweb-blocks.css'), array('wugweb-site-shell'), wp_get_theme()->get('Version')); });
add_action('enqueue_block_editor_assets', function () { wp_enqueue_style('wugweb-site-shell-editor', get_theme_file_uri('assets/css/site-shell.css'), array(), wp_get_theme()->get('Version')); wp_enqueue_style('wugweb-blocks-editor', get_theme_file_uri('assets/css/wugweb-blocks.css'), array(), wp_get_theme()->get('Version')); });
add_action('wp_enqueue_scripts', function () { wp_enqueue_style('wugweb-layout-shell', get_theme_file_uri('assets/css/layout-shell.css'), array(), wp_get_theme()->get('Version')); });

add_action('init', function () {
    register_block_style('core/group', array('name' => 'stats-row', 'label' => 'Stats row'));
    register_block_style('core/group', array('name' => 'values-row', 'label' => 'Values row'));
    register_block_style('core/group', array('name' => 'team-cards', 'label' => 'Team cards grid'));
    register_block_style('core/button', array('name' => 'magnetic', 'label' => 'Magnetic Pill'));
    register_block_style('core/button', array('name' => 'ghost', 'label' => 'Ghost'));
    register_block_style('core/paragraph', array('name' => 'label', 'label' => 'Label'));
    register_block_style('core/paragraph', array('name' => 'caption', 'label' => 'Caption'));
    foreach (array('core/post-terms', 'core/tag-cloud', 'core/group') as $wb_blk) { register_block_style($wb_blk, array('name' => 'chip', 'label' => 'Chip')); register_block_style($wb_blk, array('name' => 'pill', 'label' => 'Pill')); }
    register_block_style('core/group', array('name' => 'label', 'label' => 'Label'));
    foreach (array('primary' => 'Primary (yellow)', 'secondary' => 'Secondary', 'small' => 'Small') as $wb_n => $wb_l) { register_block_style('core/button', array('name' => $wb_n, 'label' => $wb_l)); }
    register_block_style('core/group', array('name' => 'surface', 'label' => 'Surface Card'));
    register_block_style('core/group', array('name' => 'pill-nav', 'label' => 'Morphing Pill Header'));
    register_block_pattern_category('wugweb-kits', array('label' => 'Wugweb Kits'));
});

require_once __DIR__ . '/inc/design-system.php';

/** Wugweb Core (merged from the former plugin). Skipped while the old plugin is still active so nothing is declared twice. */
if (!in_array('wugweb-core/wugweb-core.php', (array) get_option('active_plugins', array()), true)) {
    require_once __DIR__ . '/inc/core/bootstrap.php';
}
require_once __DIR__ . '/inc/seo-engine.php';
require_once __DIR__ . '/inc/a11y-names.php';
require_once __DIR__ . '/inc/seo-head.php';

/* Motion: GSAP (self-hosted) + the site's scroll-animations / draggable-tags / lazy-video, bundled from wugweb_site. */
add_action('wp_head', function () {
    if (is_admin()) return;
    echo '<script>document.documentElement.classList.add("js-reveal");window.__revealWatchdog=setTimeout(function(){document.documentElement.classList.remove("js-reveal")},5000);</script>' . "\n";
}, 1);
add_action('wp_enqueue_scripts', function () {
    $v = wp_get_theme()->get('Version'); $u = get_theme_file_uri('assets/js/');
    wp_enqueue_script('gsap', $u . 'vendor/gsap.min.js', array(), '3.15.0', true);
    $deps = array('gsap');
    foreach (array('ScrollTrigger', 'Draggable', 'Flip', 'InertiaPlugin') as $p) { wp_enqueue_script('gsap-' . $p, $u . 'vendor/' . $p . '.min.js', array('gsap'), '3.15.0', true); $deps[] = 'gsap-' . $p; }
    wp_enqueue_script('wugweb-motion', $u . 'wugweb-motion.js', $deps, $v, true);
    wp_enqueue_script('wugweb-media', $u . 'wugweb-media.js', array(), $v, true);
});

add_action('init', function () {
    $v = wp_get_theme()->get('Version');
    wp_register_style('wugweb-post-card', get_theme_file_uri('assets/css/post-card.css'), array(), $v);
    wp_register_style('wugweb-work-card', get_theme_file_uri('assets/css/work-card.css'), array(), $v);
}, 5);
