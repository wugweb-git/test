<?php
/**
 * Service Impact Bento — port of BentoBlock.astro (+ the six per-pillar layouts
 * from services/[slug].astro). Data: service-data.json `bentoTitle` + `bento`.
 */
defined('ABSPATH') || exit;
$pillar = sanitize_key($attributes['pillar'] ?? 'strategy');
$d = wugweb_core_service_data($pillar);
if (!$d) { return; }

$why = array_slice(is_array($d['bento'] ?? null) ? $d['bento'] : array(), 0, 4);
if (!$why) { return; }
$heading = !empty($d['bentoTitle']) ? $d['bentoTitle'] : 'Built to deliver lasting impact.';
$media_image = isset($attributes['mediaImage']) ? esc_url($attributes['mediaImage']) : '';
$service_title = !empty($d['h1']) ? $d['h1'] : ucfirst($pillar);

// [column, weight, role] per slot. One layout per pillar, as in the page.
$layouts = array(
  array(array(0,'tall','0'),array(0,'strip','cta'),array(1,'short','1'),array(1,'mid','2'),array(2,'slim','media'),array(2,'tall','3')),
  array(array(0,'short','0'),array(0,'mid','1'),array(1,'tall','2'),array(1,'strip','cta'),array(2,'mid','3'),array(2,'slim','media')),
  array(array(0,'slim','media'),array(0,'tall','0'),array(1,'mid','1'),array(1,'short','2'),array(2,'tall','3'),array(2,'strip','cta')),
  array(array(0,'mid','0'),array(0,'short','1'),array(1,'strip','cta'),array(1,'tall','2'),array(2,'tall','3'),array(2,'slim','media')),
  array(array(0,'tall','0'),array(0,'slim','media'),array(1,'strip','cta'),array(1,'mid','1'),array(2,'short','2'),array(2,'tall','3')),
  array(array(0,'short','0'),array(0,'tall','1'),array(1,'mid','2'),array(1,'slim','media'),array(2,'tall','3'),array(2,'strip','cta')),
);
$order = array('strategy','brand','experience','engineering','growth','operations');
$idx = array_search($pillar, $order, true);
$layout = $layouts[($idx === false ? 0 : $idx) % count($layouts)];

$hero_index = -1;
foreach ($layout as $i => $slot) { if ($slot[1] === 'tall') { $hero_index = $i; break; } }

$tiles = array();
foreach ($layout as $i => $slot) {
  list($col, $span, $role) = $slot;
  if ($role === 'cta') {
    $tiles[] = array('col'=>$col,'span'=>$span,'kind'=>'cta','href'=>'/contact-us','linkLabel'=>'Talk ' . $service_title);
  } elseif ($role === 'media') {
    $t = array('col'=>$col,'span'=>$span,'kind'=>'media','fit'=>'cover');
    if ($media_image) { $t['image'] = $media_image; }
    $tiles[] = $t;
  } else {
    $r = $why[(int) $role] ?? null;
    if (!$r || empty($r['t'])) { continue; }
    $t = array('col'=>$col,'span'=>$span,'title'=>$r['t'],'body'=>$r['p'] ?? '');
    if ($i === $hero_index) { $t['tone'] = 'light'; }
    $tiles[] = $t;
  }
}
if (!$tiles) { return; }

$columns = 3;
$show_numbers = false;
$anchor = !empty($attributes['anchor']);
$wrap_args = array('class' => 'wrap bento-block', 'aria-labelledby' => 'bento-block-title');
if (!$anchor) { $wrap_args['id'] = 'bento-block'; }
$wrap = get_block_wrapper_attributes($wrap_args);
?>
<section <?php echo $wrap; ?>>
  <h2 data-reveal id="bento-block-title"><?php echo esc_html($heading); ?></h2>

  <span class="bento-cursor" data-bento-cursor aria-hidden="true"></span>
  <div class="bento" data-bento data-scroll="bento">
    <?php for ($col = 0; $col < $columns; $col++) : ?>
    <div class="bento-col">
      <?php foreach ($tiles as $ti => $t) :
        if ($t['col'] !== $col) { continue; }
        $num   = str_pad((string) ($ti + 1), 2, '0', STR_PAD_LEFT);
        $kind  = $t['kind'] ?? 'reason';
        $tag   = !empty($t['href']) ? 'a' : 'article';
        $media = !empty($t['video']) || !empty($t['image']);
        $fit   = $t['fit'] ?? 'cover';
        $tone  = $t['tone'] ?? '';
        $cls   = array('bento-tile', 'is-' . $t['span'], 'kind-' . $kind);
        if ($tone === 'light') { $cls[] = 'is-light'; }
        if ($tone === 'accent') { $cls[] = 'is-accent'; }
        if (!empty($t['href'])) { $cls[] = 'is-link'; }
        if (($t['align'] ?? '') === 'start') { $cls[] = 'align-start'; }
        if (!empty($t['headline'])) { $cls[] = 'has-headline'; }
        if ($media) { $cls[] = 'media-' . $fit; }
      ?>
      <<?php echo $tag; ?><?php echo !empty($t['href']) ? ' href="' . esc_url($t['href']) . '"' : ''; ?> class="<?php echo esc_attr(implode(' ', $cls)); ?>">
        <?php if (!empty($t['eyebrow'])) : ?><span class="bento-eyebrow"><?php echo esc_html($t['eyebrow']); ?></span><?php endif; ?>
        <?php if (!empty($t['mark'])) : ?>
          <span class="bento-mark" aria-hidden="true">
            <svg viewBox="0 0 16 16" focusable="false"><path d="M8 1.5v13M1.5 8h13" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" /></svg>
          </span>
        <?php endif; ?>
        <?php if ($media) : ?>
          <span class="bento-media is-<?php echo esc_attr($fit); ?>">
            <?php if (!empty($t['video'])) : ?>
              <video data-bento-video src="<?php echo esc_url($t['video']); ?>"<?php echo !empty($t['poster']) ? ' poster="' . esc_url($t['poster']) . '"' : ''; ?> preload="none" muted loop playsinline tabindex="-1" aria-hidden="true"></video>
            <?php else : ?>
              <img src="<?php echo esc_url($t['image']); ?>" alt="" loading="lazy" decoding="async" />
            <?php endif; ?>
            <?php if ($fit === 'cover' && !empty($t['chips'])) : ?>
              <span class="bento-chips">
                <?php foreach ($t['chips'] as $c) : ?>
                  <span class="bento-chip"><strong><?php echo esc_html($c['value']); ?></strong><?php if (!empty($c['label'])) : ?><em><?php echo esc_html($c['label']); ?></em><?php endif; ?></span>
                <?php endforeach; ?>
              </span>
            <?php endif; ?>
          </span>
        <?php endif; ?>

        <div class="bento-body">
          <?php if ($kind === 'stat') : ?>
            <span class="bento-stat"><?php echo esc_html($t['value'] ?? ''); ?></span>
            <?php if (!empty($t['label'])) : ?><span class="bento-stat-label"><?php echo esc_html($t['label']); ?></span><?php endif; ?>
          <?php elseif ($kind === 'stack') : ?>
            <ul class="bento-stack">
              <?php foreach (($t['items'] ?? array()) as $s) : ?>
                <li><?php if (!empty($s['iconPath'])) : ?><img src="<?php echo esc_url($s['iconPath']); ?>" alt="" width="18" height="18" loading="lazy" /><?php endif; ?><?php echo esc_html($s['name']); ?></li>
              <?php endforeach; ?>
            </ul>
          <?php elseif ($kind === 'cta') : ?>
            <span class="bento-cta">
              <img src="<?php echo esc_url(get_theme_file_uri('assets/brand/logo-large.svg')); ?>" alt="wugweb" width="149" height="54" />
              <span><?php echo esc_html(!empty($t['linkLabel']) ? $t['linkLabel'] : "Let's talk"); ?></span>
            </span>
          <?php elseif ($kind === 'checklist') : ?>
            <?php if (!empty($t['title'])) : ?><h3><?php echo esc_html($t['title']); ?></h3><?php endif; ?>
            <?php if (!empty($t['body'])) : ?><p><?php echo esc_html($t['body']); ?></p><?php endif; ?>
            <ul class="bento-list">
              <?php foreach (($t['list'] ?? array()) as $li) : ?>
                <li><svg viewBox="0 0 14 14" aria-hidden="true" focusable="false"><path d="M2 7.5l3.2 3.2L12 4" stroke="currentColor" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round" /></svg><?php echo esc_html($li); ?></li>
              <?php endforeach; ?>
            </ul>
          <?php else : ?>
            <?php if ($show_numbers && !empty($t['title'])) : ?><span class="bento-n"><?php echo esc_html($num); ?></span><?php endif; ?>
            <?php if (!empty($t['title'])) : ?><h3><?php echo esc_html($t['title']); ?></h3><?php endif; ?>
            <?php if (!empty($t['body'])) : ?><p><?php echo esc_html($t['body']); ?></p><?php endif; ?>
            <?php if (!empty($t['caption'])) : ?><span class="bento-caption"><?php echo esc_html($t['caption']); ?></span><?php endif; ?>
          <?php endif; ?>
        </div>
      </<?php echo $tag; ?>>
      <?php endforeach; ?>
    </div>
    <?php endfor; ?>
  </div>
</section>
