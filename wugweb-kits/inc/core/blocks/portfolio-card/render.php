<?php
$id = wugweb_block_post_id($block ?? null, $attributes, array('case_study'));
$client = $attributes['eyebrow'] ?? ''; $title = $attributes['title'] ?? ''; $url = $attributes['url'] ?? '#'; $media = $attributes['imageUrl'] ?? '';
if ($id) { $f = wugweb_fields_case_study($id); $client = $f['client'] . ' — ' . $f['discipline']; $title = $f['title']; $url = $f['url']; $media = $f['image']; }
$is_video = (bool) preg_match('/\.(mp4|webm)(\?|$)/i', $media);
?><ul <?php echo get_block_wrapper_attributes(array('class' => 'work-grid')); ?>><li data-reveal><a href="<?php echo esc_url($url); ?>"><?php if ($is_video): ?><video aria-hidden="true" muted loop playsinline preload="none" data-src="<?php echo esc_url($media); ?>"></video><?php elseif ($media): ?><img src="<?php echo esc_url($media); ?>" alt="" loading="lazy" decoding="async" style="width:100%;aspect-ratio:16/10;object-fit:cover;border-radius:var(--r-2)"><?php else: ?><span class="wugweb-ph" style="--ph-ar:16/10" aria-hidden="true"></span><?php endif; ?><p class="work-client"><?php echo esc_html($client); ?></p><h3><?php echo esc_html($title); ?></h3></a></li></ul>
