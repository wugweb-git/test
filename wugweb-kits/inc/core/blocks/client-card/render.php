<?php
$id = wugweb_block_post_id($block ?? null, $attributes, array('client'));
$img = $attributes['imageUrl'] ?? ''; $name = $attributes['title'] ?? ''; $link = $attributes['url'] ?? '';
if ($id) { $f = wugweb_fields_client($id); $img = $f['logo']; $name = $f['name']; $link = $f['website']; }
$tag = ($link && $link !== '#') ? 'a' : 'div';
 // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core ?><div <?php echo get_block_wrapper_attributes(); ?>><<?php echo $tag; ?> class="logo-tile"<?php if ($tag === 'a') echo ' href="' . esc_url($link) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr($name) . '"'; ?>><?php if ($img): ?><img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($name ? $name . ' logo' : ''); ?>" loading="lazy" decoding="async" /><?php else: ?><span class="wugweb-ph" style="--ph-ar:16/9;border-radius:inherit" aria-hidden="true"></span><?php endif; ?></<?php echo $tag; ?>></div>
