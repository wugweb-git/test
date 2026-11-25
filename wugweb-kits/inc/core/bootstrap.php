<?php
/** Wugweb Core, merged into the theme. Blocks, post types, schemas and DB tables. */
if (!defined('ABSPATH')) { exit; }
require_once __DIR__ . '/includes/service-data.php';
add_action('init', function () {
    wp_register_style('wugweb-core-service', get_theme_file_uri('inc/core/assets/service.css'), array(), '0.2.0');
    wp_register_style('wugweb-core-content', get_theme_file_uri('inc/core/assets/content.css'), array(), '0.2.0');
    $blocks = array(
        // interactive / runtime
        'pillar-scope', 'copyright-year', 'marquee', 'taxonomy-filter',
        // service pages (data from includes/service-data.json)
        'service-page', 'service-hero-metrics', 'capability-showcase', 'service-impact-bento',
        'process-timeline', 'service-mid-cta', 'framework-loop', 'toolkit-grid',
        // dynamic content
        'contact-form', 'editorial-card', 'case-study-card', 'article-meta-toc',
        'article-share-newsletter', 'case-study-brief-matrix', 'post-cross-sell-rail', 'faq-list',
        // page sections
        'hero-section', 'industries-served', 'clients-served', 'case-studies-grid', 'recent-thoughts', 'cta-band',
    );
    foreach ($blocks as $b) { register_block_type(__DIR__ . '/blocks/' . $b); }
    register_block_pattern_category('wugweb-sections', array('label' => 'Wugweb Sections'));
    register_block_pattern('wugweb-core/homepage-sections', array('title' => 'Wugweb Homepage Sections', 'categories' => array('wugweb-sections'), 'content' => '<!-- wp:wugweb/hero-section /--><!-- wp:wugweb/clients-served /--><!-- wp:wugweb/industries-served /--><!-- wp:wugweb/case-studies-grid /--><!-- wp:wugweb/recent-thoughts /--><!-- wp:wugweb/cta-band /-->'));
    register_block_pattern('wugweb-core/service-sections', array('title' => 'Wugweb Service Sections', 'categories' => array('wugweb-sections'), 'content' => '<!-- wp:wugweb/hero-section /--><!-- wp:wugweb/industries-served /--><!-- wp:wugweb/clients-served /--><!-- wp:wugweb/cta-band /-->'));
    register_block_pattern_category('wugweb-services', array('label' => 'Wugweb Services'));
    foreach (array('strategy', 'brand', 'experience', 'engineering', 'growth', 'operations') as $p) {
        register_block_pattern('wugweb-core/service-page-' . $p, array(
            'title' => 'Service Page — ' . ucfirst($p), 'categories' => array('wugweb-services'),
            'content' => '<!-- wp:wugweb/service-page {"pillar":"' . $p . '"} /-->',
        ));
    }
});
add_action('wp_footer', function () {
    echo '<script>document.addEventListener("click",function(e){var t=e.target.closest("[data-copy-current-link]");if(t&&navigator.clipboard){navigator.clipboard.writeText(location.href);}});</script>';
});
require_once __DIR__ . '/includes/pwa-endpoints.php';
require_once __DIR__ . '/includes/post-types.php';
require_once __DIR__ . '/includes/block-bindings.php';
require_once __DIR__ . '/includes/blog-schema.php';
require_once __DIR__ . '/includes/notion-schema.php';
require_once __DIR__ . '/includes/db-tables.php';
require_once __DIR__ . '/includes/author-avatar.php';
require_once __DIR__ . '/includes/admin-columns.php';
