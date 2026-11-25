<?php
/** Shared renderer for the Wugweb card blocks. */
defined('ABSPATH') || exit;

function wugweb_card_dynamic($type, array $a) {
    $id = get_the_ID();
    if (!$id) return $a;
    $pt = get_post_type($id);
    if ($type === 'blog' && $pt === 'post') {
        $cat = get_the_category($id);
        $a['eyebrow'] = $cat ? $cat[0]->name : '';
        $a['title'] = get_the_title($id);
        $a['text'] = wp_trim_words(get_the_excerpt($id), 24);
        $a['meta'] = max(1, (int) ceil(str_word_count(wp_strip_all_tags((string) get_post_field('post_content', $id))) / 200)) . ' min read';
        $a['url'] = get_permalink($id);
        $a['imageUrl'] = (string) get_the_post_thumbnail_url($id, 'large');
    } elseif ($type === 'portfolio' && $pt === 'case_study') {
        $client = get_post_meta($id, '_client', true);
        $terms = get_the_terms($id, 'discipline');
        $a['eyebrow'] = $client ? (is_numeric($client) ? get_the_title($client) : $client) : '';
        $a['title'] = get_the_title($id);
        $a['text'] = wp_trim_words(get_the_excerpt($id), 22);
        $a['meta'] = ($terms && !is_wp_error($terms)) ? implode(' · ', wp_list_pluck($terms, 'name')) : '';
        $a['url'] = get_permalink($id);
        $a['imageUrl'] = (string) get_the_post_thumbnail_url($id, 'large');
    } elseif ($type === 'client' && $pt === 'client') {
        $a['title'] = get_the_title($id);
        $a['url'] = get_permalink($id);
        $a['imageUrl'] = (string) get_the_post_thumbnail_url($id, 'medium');
    }
    return $a;
}

function wugweb_render_card($type, array $a, $wrapper) {
    $a = wugweb_card_dynamic($type, $a);
    $url = !empty($a['url']) ? $a['url'] : '';
    echo '<article ' . $wrapper . '>';
    if ($type === 'stat') echo '<p class="wugweb-card__value">' . esc_html($a['eyebrow']) . '</p>';
    if ($type === 'testimonial') echo '<blockquote class="wugweb-card__quote">' . esc_html($a['text']) . '</blockquote>';
    if (!empty($a['imageUrl']) && !in_array($type, array('stat', 'testimonial'), true)) {
        echo '<div class="wugweb-card__media"><img src="' . esc_url($a['imageUrl']) . '" alt="' . esc_attr($a['imageAlt']) . '" loading="lazy" decoding="async"></div>';
    }
    echo '<div class="wugweb-card__body">';
    if ($type !== 'stat' && !empty($a['eyebrow'])) echo '<p class="wugweb-card__eyebrow">' . esc_html($a['eyebrow']) . '</p>';
    $t = esc_html($a['title']);
    if ($url) $t = '<a href="' . esc_url($url) . '">' . $t . '</a>';
    echo '<h3 class="wugweb-card__title">' . $t . '</h3>';
    if ($type !== 'testimonial' && !empty($a['text'])) echo '<p class="wugweb-card__text">' . esc_html($a['text']) . '</p>';
    if (!empty($a['meta'])) echo '<p class="wugweb-card__meta">' . esc_html($a['meta']) . '</p>';
    echo '</div>';
    if ($url) echo '<span class="wugweb-card__arrow" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="19" x2="19" y2="5"/><polyline points="9 5 19 5 19 15"/></svg></span>';
    echo '</article>';
}
