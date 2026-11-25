<?php
/** Stats / counters: pages/about.astro (.stats, data-counters) and pages/services/index.astro. live=1 reads years/projects/industries/clients from WP. */
$items = is_array($attributes['items'] ?? null) ? $attributes['items'] : array();
if (!empty($attributes['live'])) {
  $cnt = function ($pt) { $c = wp_count_posts($pt); return isset($c->publish) ? (int) $c->publish : 0; };
  $ind = get_terms(array('taxonomy' => 'industry', 'hide_empty' => true, 'fields' => 'ids')); $ind = is_wp_error($ind) ? 0 : count($ind);
  $live = array((int) gmdate('Y') - (int) ($attributes['foundedYear'] ?? 2020), $cnt('case_study'), $ind, $cnt('client'));
  foreach ($live as $i => $n) if (isset($items[$i]) && $n > 0) $items[$i]['n'] = $n . '+';
}
$light = ($attributes['variant'] ?? 'dark') === 'light';
?><section <?php echo get_block_wrapper_attributes(array('class' => 'wrap stats' . ($light ? ' is-light' : ''), 'id' => 'stats', 'data-scroll' => 'rise', 'aria-label' => 'Stats')); ?>><ul data-counters><?php foreach ($items as $s):
  $n = (string) ($s['n'] ?? ''); $m = null; preg_match('/^(\d[\d,.]*)(.*)$/', $n, $m); ?><li data-reveal><div class="stat-card"><span class="stat-n"><?php if ($m): ?><span class="stat-num" data-count-to="<?php echo esc_attr(str_replace(',', '', $m[1])); ?>"><?php echo esc_html($m[1]); ?></span><span class="stat-suffix"><?php echo esc_html($m[2]); ?></span><?php else: ?><span class="stat-num is-blank"><?php echo esc_html($n); ?></span><?php endif; ?></span><p class="stat-label"><?php echo esc_html($s['label'] ?? ''); ?></p><?php if (!empty($s['copy'])): ?><p class="stat-copy"><?php echo esc_html($s['copy']); ?></p><?php endif; ?></div></li><?php endforeach; ?></ul></section>
