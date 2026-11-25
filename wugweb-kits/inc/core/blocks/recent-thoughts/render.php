<?php
$q = new WP_Query(array('post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => max(1, min(12, (int) ($attributes['count'] ?? 4))), 'ignore_sticky_posts' => true));
if (!$q->have_posts()) { return; }
$heading = $attributes['heading'] ?? 'Thoughts on design, product, and strategy';
?><section <?php echo get_block_wrapper_attributes(array('class' => 'wrap blog-teaser', 'id' => 'blog-teaser', 'aria-labelledby' => 'blog-teaser-title')); ?>>
<div class="blog-teaser-head"><div class="btc-head-text"><h2 data-reveal id="blog-teaser-title"><?php echo esc_html($heading); ?></h2></div><a href="<?php echo esc_url($attributes['buttonUrl'] ?? '/blog/'); ?>" class="btn btn-block-mobile btc-cta"><?php echo esc_html($attributes['buttonLabel'] ?? 'View all articles'); ?></a></div>
<ul class="blog-teaser-grid"><?php $i = 0; while ($q->have_posts()): $q->the_post(); ?><li data-reveal data-reveal-delay="<?php echo esc_attr((string) ($i * 0.12)); ?>"><?php echo wugweb_post_card_html(wugweb_post_card_data(get_the_ID()), $i > 0); ?></li><?php $i++; endwhile; wp_reset_postdata(); ?></ul></section>
