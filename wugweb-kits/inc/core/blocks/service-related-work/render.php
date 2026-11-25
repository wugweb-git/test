<?php
/** Service Related Work — port of #svc-work + lib/work-mapping.ts (disciplines → pillar, de-duplicated on title, featured first). */
defined('ABSPATH') || exit;
$pillar = sanitize_key($attributes['pillar'] ?? 'strategy');
$limit = max(1, min(8, (int) ($attributes['limit'] ?? 4)));
$map = array(
  'strategy'=>array('strategy'),'consulting'=>array('strategy'),'leadership'=>array('strategy'),'content strategy'=>array('strategy','brand'),
  'branding'=>array('brand'),'visual identity'=>array('brand'),'creative direction'=>array('brand'),'visual design'=>array('brand','experience'),
  'ux design'=>array('experience'),'ui design'=>array('experience'),'ux/ui design'=>array('experience'),'ui/ux design'=>array('experience'),'product ui/ux'=>array('experience'),'product design'=>array('experience'),'interaction design'=>array('experience'),'dashboard design'=>array('experience'),'mobility ux'=>array('experience'),'erp design'=>array('experience','operations'),'data visualization'=>array('experience','growth'),
  'web development'=>array('engineering'),'website development'=>array('engineering'),'frontend development'=>array('engineering'),'frontend engineering'=>array('engineering'),'backend development'=>array('engineering'),'full-stack development'=>array('engineering'),'e-commerce development'=>array('engineering','growth'),'creative technology'=>array('engineering','brand'),'cms architecture'=>array('engineering'),'content architecture'=>array('engineering','brand'),
  'systems integration'=>array('operations','engineering'),'systems architecture'=>array('operations','engineering'),'system architecture'=>array('operations','engineering'),'systems design'=>array('operations','engineering'),
);
$posts = get_posts(array('post_type' => 'case_study', 'post_status' => 'publish', 'numberposts' => 200, 'orderby' => 'date', 'order' => 'DESC'));
$seen = array(); $rows = array();
foreach ($posts as $p) {
  $m = function ($k) use ($p) { return (string) get_post_meta($p->ID, 'wugweb_cs_' . $k, true); };
  $disc = array_values(array_filter(array_unique(array_map('trim', array_merge(explode(',', $m('primary_discipline')), explode(',', $m('additional_disciplines')))))));
  $hit = false;
  foreach ($disc as $d) { if (in_array($pillar, $map[strtolower($d)] ?? array(), true)) { $hit = true; break; } }
  if (!$hit) { continue; }
  $key = strtolower(trim(get_the_title($p))); if ($key === '' || isset($seen[$key])) { continue; } $seen[$key] = 1;
  $rows[] = array('title' => get_the_title($p), 'client' => $m('client'), 'disc' => $disc, 'url' => get_permalink($p), 'featured' => in_array(strtolower($m('featured')), array('1', 'true', 'yes'), true));
}
usort($rows, function ($a, $b) { return (int) $b['featured'] - (int) $a['featured']; });
$rows = array_slice($rows, 0, $limit);
if (!$rows) { return; }
?><section <?php echo get_block_wrapper_attributes(array('class' => 'wrap svc-work', 'id' => 'svc-work', 'aria-labelledby' => 'svc-work-title')); ?>>
  <div class="svc-work-head">
    <h2 data-reveal id="svc-work-title">Work that leads with clarity and delivers with impact</h2>
    <a href="<?php echo esc_url(home_url('/work/')); ?>" class="btn btn-ghost" data-reveal>View all case studies</a>
  </div>
  <ul class="svc-work-grid">
    <?php foreach ($rows as $cs) : ?>
    <li data-reveal><a href="<?php echo esc_url($cs['url']); ?>" class="glass-panel svc-work-card"><div class="card-glow"></div><h3><?php echo esc_html($cs['title']); ?></h3><p><?php echo esc_html($cs['client'] . ($cs['disc'] ? ' — ' . implode(', ', $cs['disc']) : '')); ?></p></a></li>
    <?php endforeach; ?>
  </ul>
</section>
