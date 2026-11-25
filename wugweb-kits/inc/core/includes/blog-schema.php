<?php
/** Blog post schema: structured fields that mirror uat.wugweb.com articles. */
defined('ABSPATH') || exit;

/** Every column of the Notion "Blogs" master table, stored as post meta (type, description). */
function wugweb_core_blog_fields() {
    return array(
        'wugweb_post_id'             => array('string', 'Notion Post ID, e.g. POST-0001'),
        'wugweb_subtitle'            => array('string', 'Subtitle'),
        'wugweb_ai_summary'          => array('string', 'AI summary'),
        'wugweb_seo_score'           => array('string', 'SEO score'),
        'wugweb_seo_title'           => array('string', 'SEO title'),
        'wugweb_seo_description'     => array('string', 'SEO description'),
        'wugweb_focus_keyword'       => array('string', 'Focus keyword'),
        'wugweb_cover_url'           => array('string', 'Cover image URL (R2)'),
        'wugweb_og_image'            => array('string', 'Open Graph image URL'),
        'wugweb_canonical_url'       => array('string', 'Canonical path or URL'),
        'wugweb_schema_type'         => array('string', 'Schema.org type: BlogPosting or TechArticle'),
        'wugweb_featured'            => array('boolean', 'Featured post'),
        'wugweb_word_count'          => array('integer', 'Word count'),
        'wugweb_read_time'           => array('integer', 'Read time in minutes (merged with reading time label)'),
        'wugweb_publish_date'        => array('string', 'Publish date (YYYY-MM-DD)'),
        'wugweb_last_updated'        => array('string', 'Last updated (YYYY-MM-DD)'),
        'wugweb_primary_category' => array('string', 'Primary category name'),
        'wugweb_author_name'         => array('string', 'Author display name'),
        'wugweb_owner'               => array('string', 'Owner'),
        'wugweb_related_slugs'       => array('string', 'Related post slugs, comma separated'),
        'wugweb_related_case_studies'=> array('string', 'Related case study slugs, comma separated'),
        'wugweb_published_at'        => array('string', 'Syndicated on (Medium, LinkedIn, Dev.to, Hashnode), comma separated'),
        'wugweb_publications'        => array('string', 'Publication links, comma separated'),
        'wugweb_assets'              => array('string', 'Linked assets, comma separated'),
        'wugweb_filter_category'     => array('string', 'Filter Category (rollup from Category)'),
        'wugweb_published_on'        => array('string', 'Published On (rollup from Publications)'),
    );
}

add_action('init', function () {
    foreach (wugweb_core_blog_fields() as $key => $f) {
        $type = $f[0];
        $san = $type === 'integer' ? 'absint' : ($type === 'boolean' ? 'rest_sanitize_boolean' : (strpos($key, '_url') !== false || strpos($key, 'og_image') !== false ? 'esc_url_raw' : 'sanitize_text_field'));
        if (in_array($key, array('wugweb_canonical_url'), true)) { $san = 'sanitize_text_field'; }
        register_post_meta('post', $key, array(
            'type' => $type, 'single' => true, 'show_in_rest' => true,
            'description' => $f[1], 'sanitize_callback' => $san,
            'auth_callback' => function () { return current_user_can('edit_posts'); },
        ));
    }
    // Notion ids and descriptions for taxonomy terms (categories, tags).
    foreach (array('category', 'post_tag', 'industry', 'discipline') as $tax) {
        foreach (array('wugweb_term_id' => 'Notion id, e.g. CAT-001', 'wugweb_term_level' => 'CMS level (Standard or Filter)', 'wugweb_term_type' => 'Category type', 'wugweb_term_parent' => 'Parent term slug') as $k => $d) {
            register_term_meta($tax, $k, array('type' => 'string', 'single' => true, 'show_in_rest' => true, 'description' => $d, 'sanitize_callback' => 'sanitize_text_field', 'auth_callback' => function () { return current_user_can('manage_categories'); }));
        }
    }
    add_post_type_support('post', 'custom-fields');
    add_post_type_support('post', 'excerpt');
});

/** SEO head: title, description, canonical and social tags from the Notion columns (no SEO plugin is installed). */
add_filter('pre_get_document_title', function ($title) {
    if (is_singular('post')) {
        $t = get_post_meta(get_the_ID(), 'wugweb_seo_title', true);
        if ($t) { return $t; }
    }
    return $title;
});
add_action('wp_head', function () {
    if (!is_singular('post')) { return; }
    $id = get_the_ID();
    $m = function ($k) use ($id) { return get_post_meta($id, $k, true); };
    $title = $m('wugweb_seo_title') ?: get_the_title($id);
    $desc = $m('wugweb_seo_description') ?: wp_strip_all_tags(get_the_excerpt($id));
    $canon = $m('wugweb_canonical_url');
    $canon = $canon ? (strpos($canon, 'http') === 0 ? $canon : home_url($canon)) : get_permalink($id);
    $img = $m('wugweb_og_image') ?: $m('wugweb_cover_url');
    $out = array();
    if ($desc) { $out[] = '<meta name="description" content="' . esc_attr($desc) . '" />'; }
    if ($m('wugweb_focus_keyword')) { $out[] = '<meta name="keywords" content="' . esc_attr($m('wugweb_focus_keyword')) . '" />'; }
    $out[] = '<link rel="canonical" href="' . esc_url($canon) . '" />';
    $out[] = '<meta property="og:type" content="article" />';
    $out[] = '<meta property="og:title" content="' . esc_attr($title) . '" />';
    if ($desc) { $out[] = '<meta property="og:description" content="' . esc_attr($desc) . '" />'; }
    $out[] = '<meta property="og:url" content="' . esc_url($canon) . '" />';
    if ($img) { $out[] = '<meta property="og:image" content="' . esc_url($img) . '" />'; $out[] = '<meta name="twitter:card" content="summary_large_image" />'; }
    echo implode("\n", $out) . "\n";
}, 1);
remove_action('wp_head', 'rel_canonical');

/** Show the R2 cover where a post has no media-library featured image. */
add_filter('render_block_core/post-featured-image', function ($html, $block) {
    if (trim((string) $html) !== '') { return $html; }
    $id = $block->context['postId'] ?? get_the_ID();
    $url = $id ? get_post_meta($id, 'wugweb_cover_url', true) : '';
    if (!$url) { return $html; }
    $title = get_the_title($id);
    $img = '<img src="' . esc_url($url) . '" alt="' . esc_attr('Cover image for ' . $title) . '" loading="lazy" decoding="async" style="border-radius:16px;width:100%;height:auto" />';
    $link = !empty($block->attributes['isLink']);
    if ($link) { $img = '<a href="' . esc_url(get_permalink($id)) . '">' . $img . '</a>'; }
    return '<figure class="wp-block-post-featured-image">' . $img . '</figure>';
}, 10, 2);

/** Article structured data (JSON-LD) for single posts. */
add_action('wp_head', function () {
    if (!is_singular('post')) { return; }
    $id = get_the_ID();
    $cover = get_post_meta($id, 'wugweb_cover_url', true);
    if (!$cover && has_post_thumbnail($id)) { $cover = get_the_post_thumbnail_url($id, 'full'); }
    $author = get_post_meta($id, 'wugweb_author_name', true) ?: get_the_author_meta('display_name', get_post_field('post_author', $id));
    $data = array(
        '@context' => 'https://schema.org', '@type' => get_post_meta($id, 'wugweb_schema_type', true) ?: 'BlogPosting',
        'headline' => get_the_title($id), 'description' => get_post_meta($id, 'wugweb_seo_description', true) ?: wp_strip_all_tags(get_the_excerpt($id)),
        'alternativeHeadline' => get_post_meta($id, 'wugweb_subtitle', true) ?: null,
        'keywords' => get_post_meta($id, 'wugweb_focus_keyword', true) ?: null,
        'wordCount' => (int) get_post_meta($id, 'wugweb_word_count', true) ?: null,
        'inLanguage' => 'en',
        'datePublished' => get_the_date('c', $id), 'dateModified' => get_the_modified_date('c', $id),
        'author' => array('@type' => 'Person', 'name' => $author),
        'publisher' => array('@type' => 'Organization', 'name' => 'Wugweb', 'url' => home_url('/')),
        'mainEntityOfPage' => get_permalink($id),
    );
    $cover = get_post_meta($id, 'wugweb_og_image', true) ?: $cover;
    if ($cover) { $data['image'] = $cover; }
    $data = array_filter($data, function ($v) { return $v !== null; });
    echo '<script type="application/ld+json">' . wp_json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
});

/** Reading time fallback when no stored label exists (200 wpm). */
function wugweb_core_reading_time($post_id) {
    $stored = (int) get_post_meta($post_id, 'wugweb_read_time', true);
    if ($stored) { return $stored . ' min read'; }
    $words = str_word_count(wp_strip_all_tags(get_post_field('post_content', $post_id)));
    return max(1, (int) ceil($words / 200)) . ' min read';
}

/** Show the uat author (stored in meta) wherever WordPress prints the post author's name or avatar. */
add_filter('the_author', function ($name) {
    $id = get_the_ID();
    if ($id && get_post_type($id) === 'post') {
        $m = get_post_meta($id, 'wugweb_author_name', true);
        if ($m) { return $m; }
    }
    return $name;
});
add_filter('get_the_author_display_name', function ($name) {
    $id = get_the_ID();
    if ($id && get_post_type($id) === 'post') {
        $m = get_post_meta($id, 'wugweb_author_name', true);
        if ($m) { return $m; }
    }
    return $name;
});

