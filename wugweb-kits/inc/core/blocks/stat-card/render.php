<?php
/** Stat Card: services .stat-card-wrap / about .stat-card (counter) / about .value-card. eyebrow = number, title = label, text = copy. metric = live count. */
$n = (string) ($attributes['eyebrow'] ?? ''); $label = (string) ($attributes['title'] ?? ''); $copy = (string) ($attributes['text'] ?? '');
$metric = (string) ($attributes['metric'] ?? '');
if ($metric !== '') {
  $cnt = function ($pt) { $c = wp_count_posts($pt); return isset($c->publish) ? (int) $c->publish : 0; };
  $v = 0;
  if ($metric === 'years') $v = (int) gmdate('Y') - (int) ($attributes['foundedYear'] ?? 2020);
  elseif ($metric === 'projects') $v = $cnt('case_study');
  elseif ($metric === 'clients') $v = wugweb_client_count();
  elseif ($metric === 'industries') { $t = get_terms(array('taxonomy' => 'industry', 'hide_empty' => true, 'fields' => 'ids')); $v = is_wp_error($t) ? 0 : count($t); }
  if ($v > 0) $n = $v . '+';
}
$cls = (string) ($attributes['className'] ?? '');
$is_value = strpos($cls, 'is-style-value') !== false;
if ($is_value) {
  echo '<div ' . get_block_wrapper_attributes(array('class' => 'value-card', 'data-reveal' => '')) . '><span class="value-n">' . esc_html($n) . '</span><h3>' . esc_html($label) . '</h3>' . ($copy !== '' ? '<p>' . esc_html($copy) . '</p>' : '') . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core 
  return;
}
$m = null; preg_match('/^(\d[\d,.]*)(.*)$/', $n, $m);
 // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core ?><div <?php echo get_block_wrapper_attributes(array('class' => 'stat-card-wrap', 'data-reveal' => '')); ?>><span class="stat-n"><?php if ($m): ?><span class="stat-num" data-count-to="<?php echo esc_attr(str_replace(',', '', $m[1])); ?>"><?php echo esc_html($m[1]); ?></span><span class="stat-suffix"><?php echo esc_html($m[2]); ?></span><?php else: ?><span class="stat-num is-blank"><?php echo esc_html($n); ?></span><?php endif; ?></span><p class="stat-label"><?php echo esc_html($label); ?></p><?php if ($copy !== ''): ?><p class="stat-copy"><?php echo esc_html($copy); ?></p><?php endif; ?></div>
