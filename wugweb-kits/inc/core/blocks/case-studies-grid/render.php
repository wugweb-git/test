<?php
$order = json_decode((string) file_get_contents(__DIR__ . '/../../includes/home-work-order.json'), true) ?: array();
$count = max(1, min(12, (int) ($attributes['count'] ?? 8)));
$ids = array();
foreach ($order as $t) {
    $f = get_posts(array('post_type' => 'case_study', 'post_status' => 'publish', 'title' => $t, 'numberposts' => 1, 'fields' => 'ids'));
    if ($f) $ids[] = $f[0];
}
if (count($ids) < $count) {
    $more = get_posts(array('post_type' => 'case_study', 'post_status' => 'publish', 'numberposts' => $count, 'fields' => 'ids', 'post__not_in' => $ids ?: array(0), 'meta_key' => 'wugweb_cs_featured', 'meta_value' => '1'));
    $ids = array_merge($ids, $more);
}
$ids = array_slice($ids, 0, $count);
$url = !empty($attributes['buttonUrl']) ? $attributes['buttonUrl'] : (get_post_type_archive_link('case_study') ?: '/work/');
?><section <?php echo get_block_wrapper_attributes(array('class' => 'wrap work', 'id' => 'work', 'data-scroll' => 'rise', 'aria-labelledby' => 'work-title')); ?>>
<div class="work-head"><h2 data-reveal id="work-title"><?php echo esc_html($attributes['heading'] ?? 'Work that Leads with Clarity and Delivers with Impact'); ?></h2><a href="<?php echo esc_url($url); ?>" class="btn btn-ghost"><?php echo esc_html($attributes['buttonLabel'] ?? 'View case studies'); ?></a></div>
<ul class="work-grid"><?php foreach ($ids as $id):
    $client = (string) get_post_meta($id, 'wugweb_cs_client', true);
    $disc = (string) get_post_meta($id, 'wugweb_cs_additional_disciplines', true);
    if ($disc === '') $disc = (string) get_post_meta($id, 'wugweb_cs_primary_discipline', true);
    $media = get_the_post_thumbnail_url($id, 'large') ?: (string) get_post_meta($id, 'wugweb_cs_cover_image', true);
    $is_video = (bool) preg_match('/\.(mp4|webm)(\?|$)/i', $media); ?>
<li data-reveal><a href="<?php echo esc_url(get_permalink($id)); ?>"><?php if ($is_video): ?><video aria-hidden="true" muted loop playsinline preload="none" data-src="<?php echo esc_url($media); ?>"></video><?php elseif ($media): ?><img src="<?php echo esc_url($media); ?>" alt="" loading="lazy" decoding="async" style="width:100%;aspect-ratio:16/10;object-fit:cover;border-radius:var(--r-2)"><?php endif; ?><p class="work-client"><?php echo esc_html($client . ' — ' . $disc); ?></p><h3><?php echo esc_html(get_the_title($id)); ?></h3></a></li>
<?php endforeach; ?></ul></section>
