<?php
$img = $attributes['imageUrl'] ?? '';
if (!$img && get_post_type() === 'client') { $img = (string) get_the_post_thumbnail_url(get_the_ID(), 'medium'); }
$name = $attributes['title'] ?? ''; ?><div <?php echo get_block_wrapper_attributes(); ?>><div class="logo-tile"><?php if ($img): ?><img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($name ? $name . ' logo' : ''); ?>" loading="lazy" decoding="async" /><?php endif; ?></div></div>
