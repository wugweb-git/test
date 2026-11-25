<?php
/** Frontend-only motion assets. */
defined('ABSPATH') || exit;
add_action('wp_enqueue_scripts', function () {
    if (is_admin() || is_customize_preview() || (function_exists('wp_is_json_request') && wp_is_json_request())) return;
    $version = wp_get_theme()->get('Version');
    wp_enqueue_style('wugweb-motion', get_theme_file_uri('assets/css/motion.css'), array(), $version);
    wp_enqueue_script('wugweb-motion-init', get_theme_file_uri('assets/js/motion-init.js'), array(), $version, true);
});
