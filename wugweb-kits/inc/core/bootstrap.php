<?php
/** Wugweb Core, merged into the theme. Blocks, post types, schemas and DB tables. */
if (!defined('ABSPATH')) { exit; }
require_once __DIR__ . '/includes/service-data.php';
require_once __DIR__ . '/includes/cards.php';
require_once __DIR__ . '/includes/card-data.php';
add_filter('block_categories_all', function ($cats) {
    $mine = array(
        array('slug' => 'wugweb-layout', 'title' => 'Wugweb: Layout'),
        array('slug' => 'wugweb-content', 'title' => 'Wugweb: Content'),
        array('slug' => 'wugweb-cards', 'title' => 'Wugweb: Cards'),
        array('slug' => 'wugweb-service', 'title' => 'Wugweb: Services'),
        array('slug' => 'wugweb-blog-work', 'title' => 'Wugweb: Blog and Work'),
        array('slug' => 'wugweb-charts', 'title' => 'Wugweb: Charts'),
        array('slug' => 'wugweb-overlays', 'title' => 'Wugweb: Overlays'),
        array('slug' => 'wugweb-pages', 'title' => 'Wugweb: Pages'),
        array('slug' => 'wugweb', 'title' => 'Wugweb (other)'),
    );
    return array_merge($mine, $cats);
});
add_action('init', function () {
    wp_register_style('wugweb-core-service', get_theme_file_uri('inc/core/assets/service.css'), array(), '0.3.0');
    wp_register_style('wugweb-core-content', get_theme_file_uri('inc/core/assets/content.css'), array(), '0.3.0');
    wp_register_style('wugweb-core-cards', get_theme_file_uri('inc/core/assets/cards.css'), array(), '0.3.0');
    $blocks = array(
        // cards
        'blog-card', 'portfolio-card', 'industry-card', 'client-card', 'team-card', 'stat-card', 'media-card', 'blog-cards', 'testimonial-card',
        // interactive / runtime
        'pillar-scope', 'copyright-year', 'marquee', 'taxonomy-filter',
        // service pages (data from includes/service-data.json)
        'service-page', 'service-hero-metrics', 'capability-showcase', 'service-impact-bento',
        'process-timeline', 'service-mid-cta', 'service-stack', 'subscription-page', 'impact-page', 'legal-hub', 'sitemap-index', 'reviews-page', 'about-section', 'work-index', 'industries-directory', 'article-post', 'case-study-post', 'blog-index', 'service-related-work', 'section-intro', 'framework-loop', 'toolkit-grid',
        // dynamic content
        'cookie-consent', 'overlay', 'chart', 'contact-form', 'editorial-card', 'case-study-card', 'article-meta-toc',
        'article-share-newsletter', 'case-study-brief-matrix', 'post-cross-sell-rail', 'faq-list',
        // page sections
        'service-tags', 'intro-statement', 'hero-section', 'industries-served', 'clients-served', 'case-studies-grid', 'recent-thoughts', 'cta-band', 'page-hero', 
    );
    foreach ($blocks as $b) { register_block_type(__DIR__ . '/blocks/' . $b); }
    register_block_pattern_category('wugweb-cards', array('label' => 'Wugweb Cards'));
    foreach (array('blog','portfolio','industry','client','team','stat','testimonial') as $c) {
        register_block_pattern('wugweb-core/' . $c . '-card', array('title' => ucfirst($c) . ' Card', 'categories' => array('wugweb-cards'), 'content' => '<!-- wp:wugweb/' . $c . '-card /-->'));
    }
    register_block_pattern_category('wugweb-sections', array('label' => 'Wugweb Sections'));
    register_block_pattern('wugweb-core/homepage-sections', array('title' => 'Wugweb Homepage Sections', 'categories' => array('wugweb-sections'), 'content' => '<!-- wp:wugweb/hero-section /--><!-- wp:wugweb/intro-statement /--><!-- wp:wugweb/service-tags /--><!-- wp:wugweb/case-studies-grid /--><!-- wp:wugweb/clients-served /--><!-- wp:wugweb/industries-served /--><!-- wp:wugweb/recent-thoughts /-->'));
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
require_once __DIR__ . '/includes/global-sections.php';
require_once __DIR__ . '/includes/notion-schema.php';
require_once __DIR__ . '/includes/db-tables.php';
require_once __DIR__ . '/includes/author-avatar.php';
require_once __DIR__ . '/includes/admin-columns.php';
require_once __DIR__ . '/includes/master-menu.php';
require_once __DIR__ . '/includes/admin-icons.php';
require_once __DIR__ . '/includes/page-schema.php';
require_once __DIR__ . '/includes/r2-media.php';
require_once __DIR__ . '/blocks/case-study-post/layout-v2-helpers.php';
