<?php
/** Blog display filters: R2 cover fallback, reading time, uat author name. Fields live in the Content Web plugin; SEO is Rank Math and RankWeb. */
defined('ABSPATH') || exit;
/** Search titles, descriptions, canonicals, keyphrases and social tags are Rank Math's and RankWeb's: the Notion columns are copied into their fields (RankWeb import columns; prefixes are named at the end of inc/seo-engine.php). */

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

/** Article structured data now comes from inc/seo-engine.php (one @graph, merged into Rank Math's when active). */

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

