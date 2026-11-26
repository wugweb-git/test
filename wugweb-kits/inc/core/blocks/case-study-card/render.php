<?php
/** Case Study Card: components/work/CaseRelated.astro (layout=related, optional heading / "Up next") and pages/work/index.astro work-grid card (layout=work). */
$id = wugweb_block_post_id($block ?? null, $attributes, array('case_study'));
if (!$id) { return; }
$layout = (string) ($attributes['layout'] ?? 'related');
$next = !empty($attributes['next']);
$heading = (string) ($attributes['heading'] ?? ''); if ($next && $heading === '') $heading = 'Up next';
$short = (string) get_post_meta($id, 'wugweb_cs_short_title', true);
$title = $short !== '' ? $short : get_the_title($id);
$cover = get_the_post_thumbnail_url($id, 'large') ?: (string) get_post_meta($id, 'wugweb_cs_cover_image', true);
$industry = (string) get_post_meta($id, 'wugweb_cs_industry', true);
if ($industry === '') { $t = get_the_terms($id, 'industry'); if ($t && !is_wp_error($t)) $industry = implode(', ', wp_list_pluck($t, 'name')); }
$is_video = (bool) preg_match('/\.(mp4|webm)(\?|$)/i', $cover);
if ($layout === 'work') {
  $client = (string) get_post_meta($id, 'wugweb_cs_client', true);
  $cat = (string) get_post_meta($id, 'wugweb_cs_primary_discipline', true);
 // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core ?><section <?php echo get_block_wrapper_attributes(array('class' => 'wrap work-grid-section')); ?>><ul class="work-grid"><li data-reveal data-categories="<?php echo esc_attr($cat); ?>"><a href="<?php echo esc_url(get_permalink($id)); ?>"><?php if ($is_video): ?><video aria-hidden="true" muted loop playsinline preload="none" data-src="<?php echo esc_url($cover); ?>"></video><?php elseif ($cover): ?><img src="<?php echo esc_url($cover); ?>" alt="" loading="lazy" decoding="async" style="width:100%;aspect-ratio:16/10;object-fit:cover;display:block"><?php else: ?><span class="wugweb-ph" style="--ph-ar:16/10" aria-hidden="true"></span><?php endif; ?><div class="work-content"><p class="work-client"><?php echo esc_html($client); ?></p><h3><?php echo esc_html($title); ?></h3><?php if ($cat !== ''): ?><span class="work-category"><?php echo esc_html($cat); ?></span><?php endif; ?></div></a></li></ul></section><?php
  return;
}
$hid = 'related-head-' . $id;
 // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core ?><section <?php echo get_block_wrapper_attributes(array('class' => 'case-related' . ($next ? ' case-related--next' : ''))); ?><?php if ($heading !== '') echo ' aria-labelledby="' . esc_attr($hid) . '"'; ?> data-reveal><?php if ($heading !== ''): ?><h2 class="case-related-head" id="<?php echo esc_attr($hid); ?>"><?php echo esc_html($heading); ?></h2><?php endif; ?><ul class="case-related-list"><li class="case-related-item"><a class="case-related-link" href="<?php echo esc_url(get_permalink($id)); ?>"><?php if ($cover && !$is_video): ?><img class="case-related-media" src="<?php echo esc_url($cover); ?>" alt="" loading="lazy" decoding="async"><?php else: ?><span class="wugweb-ph case-related-media" aria-hidden="true"></span><?php endif; ?><div class="case-related-text"><h3 class="case-related-title"><?php echo esc_html($title); ?></h3><?php if ($industry !== ''): ?><p class="case-related-industry"><?php echo esc_html($industry); ?></p><?php endif; ?></div></a></li></ul></section>
