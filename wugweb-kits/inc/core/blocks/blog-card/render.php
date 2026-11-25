<?php
$id = get_the_ID();
if ($id && get_post_type($id) === 'post' && empty($attributes['title'])) { $c = wugweb_post_card_data($id); }
else {
    $c = array('url' => $attributes['url'] ?? '#', 'title' => $attributes['title'] ?? 'Blog post title', 'excerpt' => $attributes['text'] ?? '', 'topic' => $attributes['eyebrow'] ?? 'Strategy',
        'author' => '', 'minutes' => (int) filter_var($attributes['meta'] ?? '', FILTER_SANITIZE_NUMBER_INT), 'cover' => $attributes['imageUrl'] ?? '', 'alt' => $attributes['imageAlt'] ?? '',
        'accent' => 'var(--svc-strategy)', 'initials' => 'Ww');
}
echo '<div ' . get_block_wrapper_attributes() . '>' . wugweb_post_card_html($c) . '</div>';
