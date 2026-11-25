<?php
/** Block styles and content-block CSS (ported from the Wugweb Design System Layer plugin). */
defined('ABSPATH') || exit;
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('wugweb-content-blocks', get_theme_file_uri('assets/css/content-blocks.css'), array(), wp_get_theme()->get('Version'));
});
add_action('enqueue_block_editor_assets', function () {
    wp_enqueue_style('wugweb-content-blocks-editor', get_theme_file_uri('assets/css/content-blocks.css'), array(), wp_get_theme()->get('Version'));
});
add_action('init', function () {
    register_block_style('core/quote', array('name' => 'wugweb-ash-quote', 'label' => 'Wugweb Ash Quote'));
    register_block_style('core/details', array('name' => 'wugweb-details', 'label' => 'Wugweb Details'));
    register_block_style('core/table', array('name' => 'wugweb-table', 'label' => 'Wugweb Table'));
});

/** Skip-link target: add tabindex to the main landmark (cannot live in block markup without invalidating the group). */
add_filter('render_block_core/group', function ($html) {
    if (strpos($html, 'id="content-main"') !== false && strpos($html, 'tabindex=') === false) {
        $html = preg_replace('/<main\b/', '<main tabindex="-1"', $html, 1);
    }
    return $html;
});
