<?php
$id = get_the_ID(); $client = $attributes['eyebrow'] ?? ''; $title = $attributes['title'] ?? ''; $url = $attributes['url'] ?? '#'; $media = $attributes['imageUrl'] ?? '';
if ($id && get_post_type($id) === 'case_study' && !$title) {
    $c = (string) get_post_meta($id, 'wugweb_cs_client', true); $d = (string) get_post_meta($id, 'wugweb_cs_additional_disciplines', true);
    if ($d === '') $d = (string) get_post_meta($id, 'wugweb_cs_primary_discipline', true);
    $client = $c . ' — ' . $d; $title = get_the_title($id); $url = get_permalink($id);
    $media = get_the_post_thumbnail_url($id, 'large') ?: (string) get_post_meta($id, 'wugweb_cs_cover_image', true);
}
$is_video = (bool) preg_match('/\.(mp4|webm)(\?|$)/i', $media);
?><ul <?php echo get_block_wrapper_attributes(array('class' => 'work-grid')); ?>><li data-reveal><a href="<?php echo esc_url($url); ?>"><?php if ($is_video): ?><video aria-hidden="true" muted loop playsinline preload="none" data-src="<?php echo esc_url($media); ?>"></video><?php elseif ($media): ?><img src="<?php echo esc_url($media); ?>" alt="" loading="lazy" decoding="async" style="width:100%;aspect-ratio:16/10;object-fit:cover;border-radius:var(--r-2)"><?php endif; ?><p class="work-client"><?php echo esc_html($client); ?></p><h3><?php echo esc_html($title); ?></h3></a></li></ul>
