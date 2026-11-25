<?php
/** Case Study Card: components/work/CaseRelated.astro item (card markup). */
$id = wugweb_block_post_id($block ?? null, $attributes, array('case_study'));
if (!$id) { return; }
$title = (string) get_post_meta($id, 'wugweb_cs_short_title', true);
if ($title === '') { $title = get_the_title($id); }
$cover = get_the_post_thumbnail_url($id, 'large') ?: (string) get_post_meta($id, 'wugweb_cs_cover_image', true);
$industry = (string) get_post_meta($id, 'wugweb_cs_industry', true);
if ($industry === '') {
    $terms = get_the_terms($id, 'industry');
    if ($terms && !is_wp_error($terms)) { $industry = implode(', ', wp_list_pluck($terms, 'name')); }
}
?><div <?php echo get_block_wrapper_attributes(array('class' => 'case-related-item')); ?>><a class="case-related-link" href="<?php echo esc_url(get_permalink($id)); ?>"><?php if ($cover): ?><img class="case-related-media" src="<?php echo esc_url($cover); ?>" alt="" loading="lazy" decoding="async"><?php endif; ?><div class="case-related-text"><h3 class="case-related-title"><?php echo esc_html($title); ?></h3><?php if ($industry !== ''): ?><p class="case-related-industry"><?php echo esc_html($industry); ?></p><?php endif; ?></div></a></div>
