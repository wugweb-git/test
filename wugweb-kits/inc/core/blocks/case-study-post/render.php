<?php
/** Case Study Post — port of work/[slug].astro (hero, cover, bento body, more cases). Body comes from post_content blocks. */
defined('ABSPATH') || exit;
$id = wugweb_block_post_id($block ?? null, $attributes, array('case_study'));
if (!$id) { return; }
$m = function ($k) use ($id) { return trim((string) get_post_meta($id, 'wugweb_cs_' . $k, true)); };
// Media is linked only where the file exists on our own storage; Framer / Dribbble CDN links stay grey boxes.
$ok = function ($u) { $u = (string) $u; if (!preg_match('#^https?://#i', $u)) return false; $h = strtolower((string) wp_parse_url($u, PHP_URL_HOST)); return !preg_match('/(framerusercontent|dribbble)\./', $h); };
$title = get_the_title($id);
$brand = $m('short_title') ?: $m('client');
$subtitle = $m('subtitle');
$terms = function ($tax) use ($id) { $t = get_the_terms($id, $tax); return ($t && !is_wp_error($t)) ? implode(', ', array_map(function ($x) { return html_entity_decode($x->name, ENT_QUOTES); }, $t)) : ''; };
$industry = html_entity_decode($m('industry') ?: $terms('industry'), ENT_QUOTES);
$disc = array_values(array_filter(array_unique(array_map('trim', array_merge(explode(',', $m('primary_discipline')), explode(',', $m('additional_disciplines')))))));
$client = $m('client');
$logo = '';
if ($client !== '') { $cp = get_page_by_title($client, OBJECT, 'client'); if ($cp) { $l = (string) get_post_meta($cp->ID, 'wugweb_client_logo', true); if ($ok($l)) $logo = $l; } }
$meta = array();
$meta[] = array('Client', $client !== '' ? $client : '—', $logo);
if ($m('partner') !== '' && strtolower($m('partner')) !== 'none') $meta[] = array('Partner', $m('partner'), '');
foreach (array('Industry' => $industry, 'Disciplines' => implode(', ', $disc), 'Team' => $m('team'), 'Timeline' => $m('timeline'), 'Platform' => $m('platform'), 'Year' => $m('year'), 'Country' => $m('country')) as $k => $v) { if (trim((string) $v) !== '') $meta[] = array($k, $v, ''); }
$cover = $m('cover_image'); $cover_ok = $ok($cover);

// Parse the stored body into sections (short copy before "Media", or the long copy after the site-copy marker).
$short = array(); $long = array(); $media = array(); $mode = 'short'; $cur = null;
$push = function () use (&$cur, &$short, &$long, &$mode) { if ($cur && ($cur['paras'] || $cur['tiles'])) { if ($mode === 'long') $long[] = $cur; elseif ($mode === 'short') $short[] = $cur; } $cur = null; };
$txt = function ($html) { return trim(preg_replace('/\s+/', ' ', wp_strip_all_tags(html_entity_decode((string) $html, ENT_QUOTES)))); };
foreach (parse_blocks((string) get_post_field('post_content', $id)) as $b) {
  $n = $b['blockName'] ?? '';
  if ($n === 'core/heading') {
    $t = $txt($b['innerHTML']);
    $push();
    if (preg_match('/^media$/i', $t)) { $mode = 'media'; continue; }
    if (preg_match('/site copy$/i', $t)) { $mode = 'long'; continue; }
    if (preg_match('/^gallery/i', $t)) { $mode = 'gallery'; continue; }
    if ($mode === 'short' || $mode === 'long') { $cur = array('title' => trim(preg_replace('/\s*\(context\)\s*/i', '', $t)), 'paras' => array(), 'tiles' => array(), 'media' => 0); }
    continue;
  }
  if ($mode === 'media' || $mode === 'gallery') {
    if ($n === 'core/image' && preg_match('/<img[^>]*>/', $b['innerHTML'], $im)) { $alt = preg_match('/alt="([^"]*)"/', $im[0], $a) ? html_entity_decode($a[1]) : ''; $src = preg_match('/src="([^"]*)"/', $im[0], $s) ? $s[1] : ''; $media[] = array('alt' => $alt, 'src' => $src, 'video' => false); }
    elseif ($n === 'core/video' && preg_match('/src="([^"]*)"/', $b['innerHTML'], $s)) { $media[] = array('alt' => '', 'src' => $s[1], 'video' => true); }
    continue;
  }
  if (!$cur) continue;
  if ($n === 'core/paragraph') { $t = $txt($b['innerHTML']); if ($t !== '' && !preg_match('/^The long-form copy as published/i', $t)) $cur['paras'][] = $t; }
  elseif ($n === 'core/list') { foreach ($b['innerBlocks'] as $li) { $t = $txt($li['innerHTML']); if ($t === '') continue; if (preg_match('/^([^:]{2,70}):\s+(.+)$/s', $t, $mm)) $cur['tiles'][] = array($mm[1], $mm[2]); else $cur['tiles'][] = array('', $t); } }
}
$push();
$secs = $long ?: $short;
// Route media to sections by label words; client logo and cover are handled elsewhere.
$key = function ($s) { $s = strtolower($s); if (preg_match('/style/', $s)) return 'styles'; if (preg_match('/challenge|problem/', $s)) return 'challenges'; if (preg_match('/solution|approach|what we built/', $s)) return 'solutions'; if (preg_match('/philosoph|insight|thesis/', $s)) return 'philosophy'; if (preg_match('/conclusion|impact|outcome|result|filler/', $s)) return 'outcome'; if (preg_match('/about|context/', $s)) return 'about'; return $s; };
$by = array(); $closing = array();
foreach ($media as $md) {
  if (preg_match('/logo/i', $md['alt']) || preg_match('/^cover/i', $md['alt'])) continue;
  if ($md['video'] && !$ok($md['src'])) continue;
  $k = $key($md['alt']);
  $hit = -1; foreach ($secs as $i => $sec) { if ($key($sec['title']) === $k) { $hit = $i; break; } }
  if ($hit < 0 && $k === 'outcome' && $secs) $hit = count($secs) - 1;
  if ($hit < 0) { $closing[] = $md; continue; }
  $by[$hit][] = $md;
}
$ph = function ($ar = '16/10') { return '<span class="wugweb-ph" style="--ph-ar:' . esc_attr($ar) . '" aria-hidden="true"></span>'; };
$fig = function ($md, $label) use ($ok, $ph) {
  if ($md['video']) return '<figure class="b-media b-video"><video aria-hidden="true" data-src="' . esc_url($md['src']) . '" muted loop playsinline preload="none"></video></figure>';
  if ($ok($md['src'])) return '<figure class="b-media" data-shape="landscape"><img src="' . esc_url($md['src']) . '" alt="' . esc_attr($label) . '" loading="lazy" decoding="async"></figure>';
  return '<figure class="b-media" data-shape="landscape">' . $ph() . '</figure>';
};
// Other case studies (two, distinct titles).
$others = array(); $seen = array(strtolower($title));
foreach (get_posts(array('post_type' => 'case_study', 'post_status' => 'publish', 'numberposts' => 12, 'post__not_in' => array($id), 'orderby' => 'date', 'order' => 'DESC')) as $p) {
  $t = strtolower(get_the_title($p)); if (isset($seen[$t]) || in_array($t, $seen, true)) continue; $seen[] = $t;
  $f = wugweb_fields_case_study($p->ID);
  $others[] = array('url' => $f['url'], 'client' => $f['client'], 'title' => $f['title'], 'disc' => $f['discipline'], 'img' => $ok($f['image']) ? $f['image'] : '');
  if (count($others) >= 2) break;
}
?>
<div <?php echo get_block_wrapper_attributes(array('class' => 'wugweb-case-study')); ?>>
<section class="work-detail-hero" id="work-detail-hero" aria-labelledby="work-detail-hero-title"><div class="wrap">
  <?php if ($brand !== '') : ?><p class="eyebrow"><?php echo esc_html($brand); ?></p><?php endif; ?>
  <h1 id="work-detail-hero-title"><?php echo esc_html($title); ?></h1>
  <?php if ($subtitle !== '') : ?><p class="work-subtitle"><?php echo esc_html($subtitle); ?></p><?php endif; ?>
  <dl class="work-meta"><?php foreach ($meta as $row) : ?><div><dt class="meta-label"><?php echo esc_html($row[0]); ?></dt><dd<?php echo $row[0] === 'Client' ? ' class="work-meta-client"' : ''; ?>><?php if ($row[2]) : ?><img src="<?php echo esc_url($row[2]); ?>" alt="" class="work-client-logo" loading="eager"><?php endif; ?><?php echo esc_html($row[1]); ?></dd></div><?php endforeach; ?></dl>
</div></section>
<section class="wrap section work-cover" data-reveal id="work-cover" aria-label="Work cover">
  <?php if ($cover_ok) : ?><img src="<?php echo esc_url($cover); ?>" alt="<?php echo esc_attr(($brand ?: $title) . ' case study cover'); ?>" loading="eager" decoding="async" class="work-cover-media"><?php else : echo $ph('16/9'); endif; ?>
</section>
<?php if ($secs) : ?>
<div class="bento-body wrap" id="case-study">
<?php foreach ($secs as $si => $s) :
  $items = array(); $mm2 = $by[$si] ?? array(); $imgs = array_values(array_filter($mm2, function ($x) { return !$x['video']; })); $vids = array_values(array_filter($mm2, function ($x) { return $x['video']; }));
  $stylesRow = $key($s['title']) === 'styles' ? $imgs : array();
  if ($stylesRow) $imgs = array();
  $mi = 0;
  foreach ($s['tiles'] as $ti => $t) { if ($ti % 2 === 0 && $mi < count($imgs)) $items[] = array('media', $imgs[$mi++]); $items[] = array('point', $t); }
  while ($mi < count($imgs)) $items[] = array('media', $imgs[$mi++]);
  $lead = $s['paras'][0] ?? ''; $rest = array_slice($s['paras'], 1);
?>
<section class="bento" id="b-s-<?php echo (int) $si; ?>" aria-labelledby="b-h-<?php echo (int) $si; ?>">
  <div class="b-head<?php echo $items ? '' : ' b-head--solo'; ?>"><h2 id="b-h-<?php echo (int) $si; ?>"><?php echo esc_html($s['title']); ?></h2><?php if ($lead !== '') : ?><p><?php echo esc_html($lead); ?></p><?php endif; foreach ($rest as $p) : ?><p><?php echo esc_html($p); ?></p><?php endforeach; ?></div>
  <?php if ($stylesRow) : ?><div class="b-styles"><?php foreach ($stylesRow as $md) : ?><figure><?php echo $ok($md['src']) ? '<img src="' . esc_url($md['src']) . '" alt="' . esc_attr($brand . ' style') . '" loading="lazy" decoding="async">' : $ph('4/3'); ?></figure><?php endforeach; ?></div><?php endif; ?>
  <?php foreach ($items as $it) : if ($it[0] === 'media') { echo $fig($it[1], $brand . ' case study image'); } else { $t = $it[1]; ?>
  <div class="b-point<?php echo strlen($t[1]) > 170 ? ' b-point--long' : ''; ?>"><?php if ($t[0] !== '') : ?><h3><?php echo esc_html($t[0]); ?></h3><?php endif; ?><p><?php echo esc_html($t[1]); ?></p></div>
  <?php } endforeach; foreach ($vids as $v) { echo $fig($v, ''); } ?>
</section>
<?php endforeach;
if ($closing) : ?><section class="bento" id="b-closing" aria-label="More from the project"><?php foreach ($closing as $md) { echo $fig($md, $brand . ' case study image'); } ?></section><?php endif; ?>
</div>
<?php endif; ?>
<?php if ($others) : ?>
<section class="wrap section more-cases-section" id="more-cases-section" aria-labelledby="more-cases-section-title">
  <div class="more-cases-head"><h2 id="more-cases-section-title">More Case Studies</h2><a href="<?php echo esc_url(home_url('/our-work/')); ?>" class="btn btn-ghost">View all work</a></div>
  <div class="more-grid"><?php foreach ($others as $c) : ?>
    <a href="<?php echo esc_url($c['url']); ?>" class="more-card"><div class="more-card-visual"><?php echo $c['img'] ? '<img src="' . esc_url($c['img']) . '" alt="" loading="lazy">' : $ph('16/10'); ?></div><div class="more-card-content"><span class="more-card-client"><?php echo esc_html($c['client']); ?></span><h3><?php echo esc_html($c['title']); ?></h3><?php if ($c['disc']) : ?><span class="more-card-discipline"><?php echo esc_html($c['disc']); ?></span><?php endif; ?></div></a>
  <?php endforeach; ?></div>
</section>
<?php endif; ?>
</div>
