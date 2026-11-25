<?php
/** service-page: thin wrapper composing the pillar's blocks in Astro page order. */
defined('ABSPATH') || exit;
$p = sanitize_key($attributes['pillar'] ?? 'brand');
$wrap = get_block_wrapper_attributes(array('class' => 'wugweb-service-page'));
$out = '';
foreach (array('service-hero-metrics', 'capability-showcase', 'service-impact-bento', 'process-timeline', 'service-mid-cta', 'framework-loop', 'toolkit-grid') as $b) {
  $out .= '<!-- wp:wugweb/' . $b . ' {"pillar":"' . esc_attr($p) . '"} /-->';
}
echo '<div ' . $wrap . '>' . do_blocks($out) . '</div>';
