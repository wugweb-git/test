<?php
/** service-page: thin wrapper composing the pillar's blocks in Astro page order. */
defined('ABSPATH') || exit;
$p = sanitize_key($attributes['pillar'] ?? 'brand');
$wrap = get_block_wrapper_attributes(array('class' => 'wugweb-service-page'));
$out = '';
foreach (array('service-hero-metrics', 'capability-showcase', 'service-impact-bento', 'process-timeline', 'service-mid-cta', 'framework-loop', 'toolkit-grid') as $b) {
  $out .= '<!-- wp:wugweb/' . $b . ' {"pillar":"' . esc_attr($p) . '"} /-->';
}
$d = wugweb_core_service_data($p);
$title = !empty($d['h1']) ? $d['h1'] : ucfirst($p);
$faq = array('heading' => 'Frequently asked questions', 'lede' => 'Everything worth knowing before we start on ' . strtolower($title) . '.', 'items' => array_map(function ($f) { return array('q' => $f['q'], 'a' => $f['a']); }, (array) ($d['faqs'] ?? array())));
if ($faq['items']) { $out .= '<!-- wp:wugweb/faq-list ' . wp_json_encode($faq, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ' /-->'; }
$out .= '<!-- wp:wugweb/service-related-work {"pillar":"' . esc_attr($p) . '"} /-->';
$out .= '<!-- wp:wugweb/recent-thoughts ' . wp_json_encode(array('heading' => $title . ' thinking from the team', 'buttonLabel' => 'Visit our Blog', 'count' => 3, 'category' => $p)) . ' /-->';
echo '<div ' . $wrap . '>' . do_blocks($out) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core 
