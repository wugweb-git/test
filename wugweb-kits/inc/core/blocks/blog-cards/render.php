<?php
/** Blog Cards slot: renders one piece of the blog mosaic. Layout (columns per breakpoint) lives in the GenerateBlocks wrappers of patterns/blog-mosaic.php. */
defined('ABSPATH') || exit;
require_once __DIR__ . '/data.php';
$D = wugweb_blog_showcase(); if (!$D) { return; }
extract($D);
$slot = (string) ($attributes['slot'] ?? 'featured');
switch ($slot) {
case 'featured':
  if (!$hero) { return; }
  ?><a href="<?php echo esc_url($hero['url']); ?>" class="featured-card blog-featured" id="blog-featured" style="--card-accent: <?php echo esc_attr($hero['accent']); ?>">
    <div class="featured-visual"><?php if (!empty($hero['topic'])) : ?><span class="pc-chip"><?php echo esc_html($hero['topic']); ?></span><?php endif; ?><?php if (!empty($hero['cover'])) : ?><img src="<?php echo esc_url($hero['cover']); ?>" alt="<?php echo esc_attr($hero['alt']); ?>" loading="eager" decoding="async"><?php else : ?><div class="wugweb-ph" style="height:100%;aspect-ratio:auto;border-radius:0" aria-hidden="true"></div><?php endif; ?></div>
    <div class="featured-content">
      <h2><?php echo esc_html($hero['title']); ?></h2>
      <p class="featured-excerpt"><?php echo esc_html($hero['excerpt']); ?></p>
      <div class="post-meta"><span class="pc-date"><?php echo $cal; // phpcs:ignore ?><?php echo esc_html($hero['date']); ?></span></div>
    </div></a><?php
  break;
case 'list':
  if (!$side) { return; }
  ?><aside class="featured-list" aria-labelledby="featured-list-title"><h2 id="featured-list-title"><?php echo esc_html($side_label); ?></h2><ul>
  <?php foreach ($side as $sc) : ?><li><a href="<?php echo esc_url($sc['url']); ?>"><span class="fl-kicker"><?php echo esc_html($sc['topic']); ?></span><span class="fl-title"><?php echo esc_html($sc['title']); ?></span></a></li><?php endforeach; ?>
  </ul></aside><?php
  break;
case 'strip':
  if (!$strip) { return; }
  $sf = $strip['f'];
  echo do_blocks('<!-- wp:wugweb/media-card ' . wp_json_encode(array('className' => 'is-style-wide', 'title' => (string) $sf['title'], 'text' => trim($sf['client'] . ($sf['discipline'] !== '' ? ' · ' . $sf['discipline'] : '')), 'url' => (string) $sf['url'], 'imageUrl' => (string) $sf['image'], 'imageAlt' => (string) $sf['title'], 'cta' => 'See the case study')) . ' /-->'); // phpcs:ignore
  break;
case 'picks-rows':
  if (!$picks_rows) { return; }
  ?><div class="picks-rows"><ul class="is-style-row"><?php foreach ($picks_rows as $pr) : ?><li><?php echo wugweb_post_card_html($pr, true); // phpcs:ignore ?></li><?php endforeach; ?></ul></div><?php
  break;
case 'picks-big':
  if (!$picks_big) { return; }
  ?><div class="picks-big"><h2><?php echo esc_html($picks_label); ?></h2><ul><?php foreach ($picks_big as $pb) : ?><li><?php echo wugweb_post_card_html($pb, false); // phpcs:ignore ?></li><?php endforeach; ?></ul></div><?php
  break;
case 'newsletter':
  ?><div class="bn-card"><div class="bn-copy"><h2>Get insights like this in your inbox</h2><p>Design, product, and digital strategy. No spam, just thoughtful content.</p></div><form class="nl-form bn-form" method="POST" action="/api/subscribe"><input type="email" name="email" aria-label="Email address" placeholder="you@company.com" required autocomplete="email" class="nl-input"><button type="submit" class="btn btn-solid">Subscribe</button></form></div><?php
  break;
case 'cases':
  if (!$cs) { return; }
  ?><ul class="blog-work-grid"><?php foreach ($cs as $w) : $wf = $w['f']; ?><li><a href="<?php echo esc_url($wf['url']); ?>" class="related-card"><div class="related-visual"><?php if (!empty($wf['image'])) : ?><img src="<?php echo esc_url($wf['image']); ?>" alt="<?php echo esc_attr($wf['title']); ?>" loading="lazy" decoding="async"><?php else : ?><span class="related-visual-empty" aria-hidden="true"></span><?php endif; ?></div><div class="related-content"><span class="related-kicker"><?php echo esc_html($wf['client'] !== '' ? $wf['client'] : 'Case study'); ?></span><h3><?php echo esc_html($wf['title']); ?></h3><?php if ($wf['discipline'] !== '') : ?><span class="related-sub"><?php echo esc_html($wf['discipline']); ?></span><?php endif; ?></div></a></li><?php endforeach; ?></ul><?php
  break;
}
