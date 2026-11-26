<?php
/**
 * Framework Loop — full port of ProcessMap.astro (orbital process map).
 * Data: service-data.json `fwTitle`, `phases[{t,items[]}]`. Shape/loop per pillar
 * are the page's MAP_BY_PILLAR table (not in the JSON).
 */
defined('ABSPATH') || exit;
$pillar = sanitize_key($attributes['pillar'] ?? 'strategy');
$d = wugweb_core_service_data($pillar);
if (!$d) { return; }

$phases = array();
foreach ((is_array($d['phases'] ?? null) ? $d['phases'] : array()) as $p) {
  $phases[] = array('name' => $p['t'] ?? '', 'steps' => array_values(is_array($p['items'] ?? null) ? $p['items'] : array()));
}
if (!$phases) { return; }

$map_by_pillar = array(
  'strategy'    => array('descending', 1, 'Re-test'),
  'brand'       => array('arc',        1, 'Refine'),
  'experience'  => array('wave',       2, 'Iterate'),
  'engineering' => array('line',       2, 'Ship again'),
  'growth'      => array('zigzag',     1, 'Compound'),
  'operations'  => array('ascending',  2, 'Improve'),
);
$conf = $map_by_pillar[$pillar] ?? array('wave', 1, 'Iterate');
$shape = $conf[0];
$n = count($phases);
$loop_to = ($conf[1] < $n - 1) ? $conf[1] : null;
$loop_label = $conf[2];
$heading = !empty($d['fwTitle']) ? $d['fwTitle'] : 'Our Framework';

$line_w = 1.25; $sensitivity = 0.55; $return_speed = 0.1; $oscillation = 0.78;

$W = 1280; $H = 720; $PAD = 190; $MID = 356; $HUB_R = 30; $ORBIT = array(112, 152); $FAN = 110; $LABEL_CAP = 3;

$f = function ($v) { $s = rtrim(rtrim(number_format((float) $v, 4, '.', ''), '0'), '.'); return ($s === '' || $s === '-0') ? '0' : $s; };

$profile = function ($i, $n) use ($shape) {
  $d = max(1, $n - 1);
  switch ($shape) {
    case 'arc': return -sin(($i / $d) * M_PI) * 52;
    case 'ascending': return 44 - ($i / $d) * 88;
    case 'descending': return -44 + ($i / $d) * 88;
    case 'line': return 0;
    case 'zigzag': return ($i % 2 === 0) ? -34 : 34;
    default: return -sin(($i / $d) * M_PI * 1.5) * 46;
  }
};
$usable = $W - $PAD * 2;
$hubs = array();
foreach ($phases as $i => $p) {
  $hubs[] = $p + array('i' => $i, 'x' => $n === 1 ? $W / 2 : $PAD + ($usable / ($n - 1)) * $i, 'y' => $MID + $profile($i, $n), 'up' => $i % 2 === 0);
}

if (!function_exists('wugweb_fl_icon_for')) {
  function wugweb_fl_icon_for($s) {
    $icons = array(
      array('~kick.?off|handover|handoff|meeting|workshop|stakeholder|collabor~i', 'M3 13a4 4 0 0 1 8 0M7 5.5a2 2 0 1 1 0 4 2 2 0 0 1 0-4M11.5 13a3.2 3.2 0 0 1 4.5-2.9'),
      array('~interview|conversation|voice|survey|feedback~i', 'M2.5 4.5h11v7h-6l-3 2.5v-2.5h-2z'),
      array('~competitor|benchmark|market|audit|analys|analyz|result|measur|metric|report|analytic|data~i', 'M2.5 13.5h11M4.5 11V7M8 11V3.5M11.5 11V8.5'),
      array('~brain|storm|ideat|concept|idea|hypoth|principl~i', 'M8 2.2a4 4 0 0 1 2.4 7.2v1.6h-4.8V9.4A4 4 0 0 1 8 2.2ZM6.4 13.2h3.2'),
      array('~journey|flow|route|roadmap|path|mapping~i', 'M3 12.5c3 0 2-8 5-8s2 5 5 5M3 12.5h.01M13 9.5h.01'),
      array('~sitemap|architect|structure|taxonom|hierarch|design system|system~i', 'M8 2.5v3M4 13.5v-3M12 13.5v-3M4 10.5h8v-2H4zM6.5 5.5h3v3h-3z'),
      array('~wireframe|prototyp|layout|frame|draft|sketch~i', 'M2.5 3.5h11v9h-11zM2.5 6.5h11M6 6.5v6'),
      array('~usability|user test|testing|validat|qa|quality|review~i', 'M6.5 2.5h3v2h-3zM4 4.5h8v9H4zM6.2 9l1.3 1.3 2.4-2.6'),
      array('~accessib|inclusive|wcag~i', 'M8 2.6a1.1 1.1 0 1 1 0 2.2 1.1 1.1 0 0 1 0-2.2M4 6.4h8M8 6.4v3.2M8 9.6l-1.8 3.8M8 9.6l1.8 3.8'),
      array('~look and feel|visual|ui design|palette|identity|craft|art direction~i', 'M8 2.5a5.5 5.5 0 1 0 0 11c.9 0 1.2-.7.7-1.3-.5-.7-.2-1.7.8-1.7h1a3 3 0 0 0 3-3c0-2.8-2.5-5-5.5-5M5.5 7.5h.01M8 5.5h.01M10.5 7.5h.01'),
      array('~research|discover|explore|insight|persona|study~i', 'M7.2 2.6a4.6 4.6 0 1 1 0 9.2 4.6 4.6 0 0 1 0-9.2M10.6 10.6 13.5 13.5'),
      array('~build|develop|engineer|code|implement|integrat|automat~i', 'M6 5.5 3 8l3 2.5M10 5.5 13 8l-3 2.5'),
      array('~launch|ship|deploy|go.?live|scale|optimi|growth|iterat~i', 'M8 2.5c2.4 1.6 3.4 4 3.4 6.4L8 12l-3.4-3.1c0-2.4 1-4.8 3.4-6.4ZM6.6 12.2 5.4 14M9.4 12.2l1.2 1.8'),
      array('~content|copy|narrative|messag|document|spec|guideline~i', 'M4 2.5h8v11H4zM6 5.5h4M6 8h4M6 10.5h2.5'),
      array('~govern|operat|process|training|enable|support|maintain~i', 'M8 5.9a2.1 2.1 0 1 1 0 4.2 2.1 2.1 0 0 1 0-4.2M8 2.5v1.6M8 11.9v1.6M13.5 8h-1.6M4.1 8H2.5M11.9 4.1l-1.1 1.1M5.2 10.8l-1.1 1.1M11.9 11.9l-1.1-1.1M5.2 5.2 4.1 4.1'),
    );
    foreach ($icons as $row) { if (preg_match($row[0], (string) $s)) { return $row[1]; } }
    return 'M8 4.5a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7';
  }
}

// satellites
$seq = -1; $sats = array();
foreach ($hubs as $h) {
  $m = count($h['steps']);
  $base = $h['up'] ? -90 : 90;
  foreach ($h['steps'] as $j => $label) {
    $labelled = $j < $LABEL_CAP;
    if ($labelled) { $seq++; }
    $slots = min($m, $LABEL_CAP);
    $spread = $labelled ? ($slots === 1 ? 0 : $FAN * ($j / ($slots - 1) - 0.5)) : (($FAN / 2 + 16) * ($j % 2 === 0 ? 1 : -1));
    $a = (($base + $spread) * M_PI) / 180;
    $r = $labelled ? $ORBIT[$j % 2] : $ORBIT[0];
    $x = $h['x'] + cos($a) * $r; $y = $h['y'] + sin($a) * $r;
    $sats[] = array(
      'label' => $label, 'labelled' => $labelled, 'icon' => wugweb_fl_icon_for($label), 'phaseIndex' => $h['i'],
      'order' => $labelled ? $seq : -1, 'up' => $h['up'],
      'sx' => $h['x'] + cos($a) * ($HUB_R + 4), 'sy' => $h['y'] + sin($a) * ($HUB_R + 4),
      'x' => $x, 'y' => $y, 'xPct' => ($x / $W) * 100, 'yPct' => ($y / $H) * 100,
    );
  }
}
$chip_sats = array_values(array_filter($sats, function ($s) { return $s['labelled']; }));

// edges
$edges = array();
for ($i = 0; $i < $n - 1; $i++) {
  $h = $hubs[$i]; $b = $hubs[$i + 1];
  $bow = $h['up'] ? 34 : -34;
  $x1 = $h['x'] + $HUB_R; $y1 = $h['y']; $x2 = $b['x'] - $HUB_R; $y2 = $b['y'];
  $cx = ($h['x'] + $b['x']) / 2; $cy = ($h['y'] + $b['y']) / 2 + $bow;
  $edges[] = compact('i', 'x1', 'y1', 'x2', 'y2', 'cx', 'cy') + array('d' => 'M ' . $f($x1) . ' ' . $f($y1) . ' Q ' . $f($cx) . ' ' . $f($cy) . ', ' . $f($x2) . ' ' . $f($y2));
}
$has_loop = $loop_to !== null && $loop_to >= 0 && $loop_to < $n - 1;
$last = $hubs[$n - 1];
$back = $hubs[$loop_to ?? 0];
$loop_y = $H - 42;
$loop_path = $has_loop ? 'M ' . $f($last['x']) . ' ' . $f($last['y'] + $HUB_R) . ' C ' . $f($last['x'] + 70) . ' ' . $f($loop_y) . ', ' . $f($back['x'] - 70) . ' ' . $f($loop_y) . ', ' . $f($back['x']) . ' ' . $f($back['y'] + $HUB_R) : '';

$uid = wp_unique_id('pm-');
$summary = $heading . ' — ' . implode('; ', array_map(function ($p) { return $p['name'] . ': ' . implode(', ', $p['steps']); }, $phases));

$wrap_args = array('class' => 'process-map-section', 'data-scroll' => 'processmap', 'aria-labelledby' => 'process-map-section-title');
if (empty($attributes['anchor'])) { $wrap_args['id'] = 'process-map-section'; }
$wrap = get_block_wrapper_attributes($wrap_args);
?>
<section <?php echo $wrap; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core ?>>
  <figure class="pm-panel" data-process-map>
    <header class="pm-title">
      <h2 id="process-map-section-title"><?php echo esc_html($heading); ?></h2>
    </header>

    <div class="pm-scroll">
     <div class="pm-figure" data-pm-sensitivity="<?php echo esc_attr($f($sensitivity)); ?>" data-pm-return="<?php echo esc_attr($f($return_speed)); ?>" data-pm-oscillation="<?php echo esc_attr($f($oscillation)); ?>" style="--pm-line-w:<?php echo esc_attr($f($line_w)); ?>">
      <svg viewBox="0 0 <?php echo (int) $W; ?> <?php echo (int) $H; ?>" class="pm-svg" role="group" aria-labelledby="<?php echo esc_attr($uid); ?>-t">
        <title id="<?php echo esc_attr($uid); ?>-t"><?php echo esc_html($summary); ?></title>
        <g aria-hidden="true">
          <?php foreach ($hubs as $h) : ?>
            <circle cx="<?php echo $f($h['x']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- numeric SVG geometry (number_format / integer constants) ?>" cy="<?php echo $f($h['y']); ?>" r="<?php echo $ORBIT[0]; ?>" class="pm-ring" data-pm-phase="<?php echo (int) $h['i']; ?>" />
            <circle cx="<?php echo $f($h['x']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- numeric SVG geometry (number_format / integer constants) ?>" cy="<?php echo $f($h['y']); ?>" r="<?php echo $ORBIT[1]; ?>" class="pm-ring is-outer" data-pm-phase="<?php echo (int) $h['i']; ?>" />
          <?php endforeach; ?>

          <?php foreach ($edges as $e) : ?>
            <path d="<?php echo esc_attr($e['d']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- numeric SVG geometry (number_format / integer constants) ?>" class="pm-edge" data-pm-edge data-pm-order="<?php echo (int) $e['i']; ?>" data-x1="<?php echo $f($e['x1']); ?>" data-y1="<?php echo $f($e['y1']); ?>" data-x2="<?php echo $f($e['x2']); ?>" data-y2="<?php echo $f($e['y2']); ?>" data-cx="<?php echo $f($e['cx']); ?>" data-cy="<?php echo $f($e['cy']); ?>" />
          <?php endforeach; ?>

          <?php foreach ($chip_sats as $s) : ?>
            <line x1="<?php echo $f($s['sx']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- numeric SVG geometry (number_format / integer constants) ?>" y1="<?php echo $f($s['sy']); ?>" x2="<?php echo $f($s['x']); ?>" y2="<?php echo $f($s['y']); ?>" class="pm-spoke" data-pm-spoke data-pm-order="<?php echo (int) $s['order']; ?>" data-pm-phase="<?php echo (int) $s['phaseIndex']; ?>" />
          <?php endforeach; ?>

        </g>
        <?php /* The focusable hubs sit outside the aria-hidden decoration (aria-hidden must not contain focusable elements). */ ?>
          <?php foreach ($hubs as $h) : ?>
            <g class="pm-hub" data-pm-hub data-pm-order="<?php echo (int) $h['i']; ?>" data-pm-phase="<?php echo (int) $h['i']; ?>" tabindex="0" role="button" aria-label="<?php echo esc_attr($h['name'] . ' — ' . implode(', ', $h['steps'])); ?>">
              <circle cx="<?php echo $f($h['x']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- numeric SVG geometry (number_format / integer constants) ?>" cy="<?php echo $f($h['y']); ?>" r="<?php echo $HUB_R + 7; ?>" class="pm-hub-halo" />
              <circle cx="<?php echo $f($h['x']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- numeric SVG geometry (number_format / integer constants) ?>" cy="<?php echo $f($h['y']); ?>" r="<?php echo $HUB_R; ?>" class="pm-hub-disc" />
              <text x="<?php echo $f($h['x']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- numeric SVG geometry (number_format / integer constants) ?>" y="<?php echo $f($h['y']); ?>" class="pm-hub-n" text-anchor="middle" dominant-baseline="central"><?php echo esc_html(str_pad((string) ($h['i'] + 1), 2, '0', STR_PAD_LEFT)); ?></text>
              <text x="<?php echo $f($h['x']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- numeric SVG geometry (number_format / integer constants) ?>" y="<?php echo $f($h['up'] ? $h['y'] + $HUB_R + 26 : $h['y'] - $HUB_R - 15); ?>" class="pm-hub-name" text-anchor="middle"><?php echo esc_html($h['name']); ?></text>
            </g>
          <?php endforeach; ?>
        <g aria-hidden="true">

          <?php foreach ($sats as $s) : ?>
            <circle cx="<?php echo $f($s['x']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- numeric SVG geometry (number_format / integer constants) ?>" cy="<?php echo $f($s['y']); ?>" r="<?php echo $s['labelled'] ? '3.5' : '2.5'; ?>" class="pm-dot<?php echo $s['labelled'] ? '' : ' is-quiet'; ?>"<?php if ($s['labelled']) : ?> data-pm-dot data-pm-order="<?php echo (int) $s['order']; ?>"<?php else : ?> data-pm-order="-1"<?php endif; ?> data-pm-phase="<?php echo (int) $s['phaseIndex']; ?>"><?php if (!$s['labelled']) : ?><title><?php echo esc_html($s['label']); ?></title><?php endif; ?></circle>
          <?php endforeach; ?>

          <?php if ($has_loop) : ?>
            <defs>
              <marker id="<?php echo esc_attr($uid); ?>-arrow" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="5" markerHeight="5" orient="auto-start-reverse"><path d="M 0 0 L 10 5 L 0 10 z" class="pm-arrow" /></marker>
            </defs>
            <path d="<?php echo esc_attr($loop_path); ?>" class="pm-loop-path" data-pm-loop marker-end="url(#<?php echo esc_attr($uid); ?>-arrow)" />
            <text x="<?php echo $f(($last['x'] + $back['x']) / 2); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- numeric SVG geometry (number_format / integer constants) ?>" y="<?php echo $f($loop_y - 12); ?>" class="pm-loop-label" text-anchor="middle"><?php echo esc_html($loop_label); ?></text>
          <?php endif; ?>
        </g>
      </svg>

      <ul class="pm-sats">
        <?php foreach ($chip_sats as $s) : ?>
          <li class="pm-sat <?php echo $s['up'] ? 'is-up' : 'is-down'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- numeric SVG geometry (number_format / integer constants) ?>" data-pm-sat data-pm-order="<?php echo (int) $s['order']; ?>" data-pm-phase="<?php echo (int) $s['phaseIndex']; ?>" style="left:<?php echo $f($s['xPct']); ?>%; top:<?php echo $f($s['yPct']); ?>%">
            <span class="pm-chip">
              <svg viewBox="0 0 16 16" class="pm-icon" aria-hidden="true"><path d="<?php echo esc_attr($s['icon']); ?>" /></svg>
              <span class="pm-chip-text"><?php echo esc_html($s['label']); ?></span>
            </span>
          </li>
        <?php endforeach; ?>
      </ul>
     </div>
    </div>

    <ol class="pm-run">
      <?php foreach ($hubs as $h) : ?>
        <li class="pm-run-phase">
          <span class="pm-run-node" aria-hidden="true"><?php echo esc_html(str_pad((string) ($h['i'] + 1), 2, '0', STR_PAD_LEFT)); ?></span>
          <div class="pm-run-body">
            <h3><?php echo esc_html($h['name']); ?></h3>
            <ul class="pm-run-steps">
              <?php foreach ($h['steps'] as $st) : ?>
                <li><svg viewBox="0 0 16 16" class="pm-icon" aria-hidden="true"><path d="<?php echo esc_attr(wugweb_fl_icon_for($st)); ?>" /></svg><span><?php echo esc_html($st); ?></span></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </li>
      <?php endforeach; ?>
      <?php if ($has_loop) : ?>
        <li class="pm-run-loop"><span class="pm-run-loop-mark" aria-hidden="true"></span><p><?php echo esc_html($loop_label . ' — ' . $last['name'] . ' feeds back into ' . $back['name'] . '.'); ?></p></li>
      <?php endif; ?>
    </ol>
  </figure>
</section>
