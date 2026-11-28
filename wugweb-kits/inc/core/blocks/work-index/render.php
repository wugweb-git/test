<?php
/** Work Index — filter chips + case study grid, port of work/index.astro (.work-grid-section). */
defined('ABSPATH') || exit;
$pillars = array('Strategy', 'Brand', 'Experience', 'Engineering', 'Growth', 'Operations');
$kw = array(
  'Strategy' => array('strategy', 'consulting', 'research', 'branding-strategy'),
  'Brand' => array('brand', 'identity', 'naming'),
  'Experience' => array('ux', 'ui', 'design', 'experience', 'product'),
  'Engineering' => array('development', 'engineering', 'web', 'app', 'tech', 'saas', 'platform'),
  'Growth' => array('marketing', 'growth', 'seo', 'martech', 'lead'),
  'Operations' => array('operations', 'qa', 'automation', 'devops', 'support'),
);
$posts = get_posts(array('post_type' => 'case_study', 'post_status' => 'publish', 'numberposts' => max(1, min(300, (int) ($attributes['limit'] ?? 200))), 'orderby' => 'date', 'order' => 'DESC'));
$items = array();
foreach ($posts as $p) {
  $f = wugweb_fields_case_study($p->ID);
  $disc = array_values(array_filter(array_unique(array_map('trim', array_merge(explode(',', (string) get_post_meta($p->ID, 'wugweb_cs_primary_discipline', true)), explode(',', (string) get_post_meta($p->ID, 'wugweb_cs_additional_disciplines', true)))))));
  $hay = strtolower(implode(' ', array_merge($disc, (array) $f['industry'], array($f['client']))));
  $pillar = 'Experience';
  foreach ($pillars as $pl) { foreach ($kw[$pl] as $k) { if (strpos($hay, $k) !== false) { $pillar = $pl; break 2; } } }
  $items[] = array('client' => $f['client'], 'category' => $disc ? implode(', ', $disc) : 'UX Design', 'pillar' => $pillar, 'title' => $f['title'], 'url' => $f['url'], 'media' => $f['image']);
}
if (!$items) { return; }
 // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core ?><section <?php echo get_block_wrapper_attributes(array('class' => 'wrap work-grid-section', 'id' => 'work-grid-section', 'aria-labelledby' => 'work-grid-title')); ?>>
  <h2 class="sr-only" id="work-grid-title">Case studies</h2>
  <div class="work-filters" role="toolbar" aria-label="Filter case studies" data-reveal>
    <?php foreach (array_merge(array('All'), $pillars) as $c) : ?><button type="button" class="filter-chip" data-category="<?php echo esc_attr($c); ?>" aria-pressed="false"><?php echo esc_html($c); ?></button><?php endforeach; ?>
  </div>
  <ul class="work-grid">
    <?php foreach ($items as $w) : $video = (bool) preg_match('/\.(mp4|webm)(\?|$)/i', $w['media']); ?>
    <li data-reveal data-categories="<?php echo esc_attr($w['pillar']); ?>"><a href="<?php echo esc_url($w['url']); ?>">
      <?php if ($video) : ?><video aria-hidden="true" muted loop playsinline preload="none" data-src="<?php echo esc_url($w['media']); ?>"></video>
      <?php elseif ($w['media']) : ?><img src="<?php echo esc_url($w['media']); ?>" alt="<?php echo esc_attr($w['title']); ?>" loading="lazy" decoding="async">
      <?php else : ?><span class="wugweb-ph" style="--ph-ar:16/10;border-radius:0" aria-hidden="true"></span><?php endif; ?>
      <div class="work-content"><p class="work-client"><?php echo esc_html($w['client']); ?></p><h3><?php echo esc_html($w['title']); ?></h3><span class="work-category"><?php echo esc_html($w['category']); ?></span></div>
    </a></li>
    <?php endforeach; ?>
  </ul>
</section>
