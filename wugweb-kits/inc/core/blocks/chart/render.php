<?php
/** wugweb/chart: dependency-free inline SVG/CSS charts. Final state is fully rendered server-side; view.js only animates it in. */
defined('ABSPATH') || exit;
$type = in_array($attributes['type'] ?? 'bar-h', array('count', 'bar-h', 'bar-v', 'progress', 'line', 'area', 'sparkline', 'donut', 'ring', 'compare'), true) ? $attributes['type'] : 'bar-h';
$rows = array();
foreach ((array) ($attributes['rows'] ?? array()) as $r) {
    if (!is_array($r) || trim((string) ($r['label'] ?? '')) === '') continue;
    $rows[] = array('label' => (string) $r['label'], 'value' => (float) ($r['value'] ?? 0), 'unit' => (string) ($r['unit'] ?? ''), 'color' => (string) ($r['color'] ?? ''));
}
if (!$rows) return;
$unit = (string) ($attributes['unit'] ?? '');
$title = (string) ($attributes['title'] ?? '');
$caption = (string) ($attributes['caption'] ?? '');
$legend = !empty($attributes['showLegend']);
$fmt = function ($v) { return rtrim(rtrim(number_format($v, 2, '.', ','), '0'), '.'); };
$col = function ($c, $i) {
    $map = array('fg' => 'var(--fg)', 'accent' => 'var(--color-accent-primary)', 'neutral-3' => 'var(--neutral-3)', 'neutral-5' => 'var(--neutral-5)', 'neutral-6' => 'var(--neutral-6)');
    if (isset($map[$c])) return $map[$c];
    $pal = array('var(--neutral-1)', 'var(--neutral-4)', 'var(--neutral-6)', 'var(--neutral-2)', 'var(--neutral-5)', 'var(--neutral-7)');
    return $pal[$i % count($pal)];
};
$maxAttr = (float) ($attributes['max'] ?? 0);
$maxV = $maxAttr > 0 ? $maxAttr : max(array_map(function ($r) { return $r['value']; }, $rows));
if ($maxV <= 0) $maxV = 1;
$uOf = function ($r) use ($unit) { return $r['unit'] !== '' ? $r['unit'] : $unit; };
$summary = trim((string) ($attributes['summary'] ?? ''));
if ($summary === '') {
    $parts = array_map(function ($r) use ($fmt, $uOf) { return $r['label'] . ' ' . $fmt($r['value']) . $uOf($r); }, $rows);
    $summary = ($title !== '' ? $title . ': ' : '') . ucfirst(str_replace('-', ' ', $type)) . ' chart. ' . implode(', ', $parts) . '.';
}
$wrap = get_block_wrapper_attributes(array('class' => 'wugweb-chart is-' . $type, 'data-chart' => $type));
$sr = '<table class="sr-only"><caption>' . esc_html($title !== '' ? $title : 'Chart data') . '</caption><thead><tr><th scope="col">Label</th><th scope="col">Value</th></tr></thead><tbody>';
foreach ($rows as $r) $sr .= '<tr><th scope="row">' . esc_html($r['label']) . '</th><td>' . esc_html($fmt($r['value']) . $uOf($r)) . '</td></tr>';
$sr .= '</tbody></table>';
ob_start();
$n = count($rows);
if ($type === 'count') : ?>
  <ul class="chart-counts"><?php foreach ($rows as $i => $r) : ?><li><span class="chart-count-n"><span data-count-to="<?php echo esc_attr($fmt($r['value'])); ?>"><?php echo esc_html($fmt($r['value'])); ?></span><?php echo esc_html($uOf($r)); ?></span><span class="chart-count-l"><?php echo esc_html($r['label']); ?></span></li><?php endforeach; ?></ul>
<?php elseif ($type === 'bar-h' || $type === 'progress') : ?>
  <ul class="chart-bars"><?php foreach ($rows as $i => $r) : $pct = $type === 'progress' ? max(0, min(100, $r['value'] * 100 / ($maxAttr > 0 ? $maxAttr : 100))) : max(0, min(100, $r['value'] * 100 / $maxV)); ?>
    <li><span class="chart-bar-label"><?php echo esc_html($r['label']); ?></span><span class="chart-track"><span class="chart-fill" data-fill style="width:<?php echo esc_attr(round($pct, 2)); ?>%;background:<?php echo esc_attr($col($r['color'], $i)); ?>"></span></span><span class="chart-bar-val"><?php echo esc_html($fmt($r['value']) . $uOf($r)); ?></span></li><?php endforeach; ?></ul>
<?php elseif ($type === 'bar-v') : ?>
  <ul class="chart-cols"><?php foreach ($rows as $i => $r) : $pct = max(0, min(100, $r['value'] * 100 / $maxV)); ?>
    <li><span class="chart-bar-val"><?php echo esc_html($fmt($r['value']) . $uOf($r)); ?></span><span class="chart-col-track"><span class="chart-fill chart-fill-v" data-fill-v style="height:<?php echo esc_attr(round($pct, 2)); ?>%;background:<?php echo esc_attr($col($r['color'], $i)); ?>"></span></span><span class="chart-bar-label"><?php echo esc_html($r['label']); ?></span></li><?php endforeach; ?></ul>
<?php elseif ($type === 'compare') : $a = $rows[0]; $b = $rows[1] ?? $rows[0]; $mx = max($a['value'], $b['value'], 1); $delta = $a['value'] != 0 ? round(($b['value'] - $a['value']) * 100 / abs($a['value'])) : 0; ?>
  <ul class="chart-bars chart-compare"><?php foreach (array($a, $b) as $i => $r) : ?>
    <li><span class="chart-bar-label"><?php echo esc_html($r['label']); ?></span><span class="chart-track"><span class="chart-fill" data-fill style="width:<?php echo esc_attr(round($r['value'] * 100 / $mx, 2)); ?>%;background:<?php echo esc_attr($i ? 'var(--fg)' : 'var(--neutral-5)'); ?>"></span></span><span class="chart-bar-val"><?php echo esc_html($fmt($r['value']) . $uOf($r)); ?></span></li><?php endforeach; ?></ul>
  <p class="chart-delta"><?php echo esc_html(($delta > 0 ? '+' : '') . $delta . '%'); ?></p>
<?php elseif ($type === 'line' || $type === 'area' || $type === 'sparkline') :
  $W = $type === 'sparkline' ? 120 : 600; $H = $type === 'sparkline' ? 36 : 240; $pad = $type === 'sparkline' ? 2 : 16;
  $min = $type === 'sparkline' ? min(array_column($rows, 'value')) : 0; $span = max($maxV - $min, 1);
  $pts = array(); foreach ($rows as $i => $r) { $x = $n > 1 ? $pad + ($W - 2 * $pad) * $i / ($n - 1) : $W / 2; $y = $H - $pad - ($H - 2 * $pad) * (($r['value'] - $min) / $span); $pts[] = array(round($x, 2), round($y, 2)); }
  $d = 'M' . implode(' L', array_map(function ($p) { return $p[0] . ' ' . $p[1]; }, $pts));
  $area = $d . ' L' . $pts[$n - 1][0] . ' ' . ($H - $pad) . ' L' . $pts[0][0] . ' ' . ($H - $pad) . ' Z'; ?>
  <svg class="chart-svg" viewBox="0 0 <?php echo (int) $W; ?> <?php echo (int) $H; ?>" preserveAspectRatio="none" aria-hidden="true" focusable="false">
    <?php if ($type !== 'sparkline') : for ($g = 0; $g <= 3; $g++) : $gy = $pad + ($H - 2 * $pad) * $g / 3; ?><line class="chart-grid" x1="<?php echo (int) $pad; ?>" x2="<?php echo (int) ($W - $pad); ?>" y1="<?php echo esc_attr(round($gy, 2)); ?>" y2="<?php echo esc_attr(round($gy, 2)); ?>"/><?php endfor; endif; ?>
    <?php if ($type === 'area') : ?><path class="chart-area" data-area d="<?php echo esc_attr($area); ?>"/><?php endif; ?>
    <path class="chart-line" data-line pathLength="1" d="<?php echo esc_attr($d); ?>" vector-effect="non-scaling-stroke"/>
    <?php if ($type !== 'sparkline') foreach ($pts as $p) : ?><circle class="chart-pt" cx="<?php echo esc_attr($p[0]); ?>" cy="<?php echo esc_attr($p[1]); ?>" r="3.5"/><?php endforeach; ?>
  </svg>
  <?php if ($type !== 'sparkline') : ?><ul class="chart-xlabels"><?php foreach ($rows as $r) : ?><li><?php echo esc_html($r['label']); ?></li><?php endforeach; ?></ul><?php endif; ?>
<?php elseif ($type === 'donut' || $type === 'ring') :
  $R = 42; $C = 2 * M_PI * $R; $total = array_sum(array_column($rows, 'value')) ?: 1;
  if ($type === 'ring') { $v = $rows[0]['value']; $den = $maxAttr > 0 ? $maxAttr : 100; $segs = array(array(max(0, min($v / $den, 1)), 0)); }
  else { $segs = array(); $acc = 0; foreach ($rows as $i => $r) { $f = $r['value'] / $total; $segs[] = array($f, $acc); $acc += $f; } } ?>
  <div class="chart-ring-wrap"><svg class="chart-svg chart-ring" viewBox="0 0 100 100" aria-hidden="true" focusable="false">
    <circle class="chart-ring-track" cx="50" cy="50" r="<?php echo (int) $R; ?>"/>
    <?php foreach ($segs as $i => $s) : $len = $s[0] * $C; $gap = $type === 'donut' && $n > 1 ? min(2, $len / 4) : 0; ?>
    <circle class="chart-seg" data-seg cx="50" cy="50" r="<?php echo (int) $R; ?>" stroke="<?php echo esc_attr($col($rows[$i]['color'] ?? '', $i)); ?>" stroke-dasharray="<?php echo esc_attr(round(max($len - $gap, 0.01), 3) . ' ' . round($C, 3)); ?>" stroke-dashoffset="<?php echo esc_attr(round(-$s[1] * $C, 3)); ?>" transform="rotate(-90 50 50)"/>
    <?php endforeach; ?>
  </svg><span class="chart-ring-c"><?php if ($type === 'ring') : ?><span data-count-to="<?php echo esc_attr($fmt($rows[0]['value'])); ?>"><?php echo esc_html($fmt($rows[0]['value'])); ?></span><?php echo esc_html($uOf($rows[0])); ?><?php else : ?><span data-count-to="<?php echo esc_attr($fmt($total)); ?>"><?php echo esc_html($fmt($total)); ?></span><?php echo esc_html($unit); ?><?php endif; ?></span></div>
<?php endif;
$vis = ob_get_clean();
?>
<figure <?php echo $wrap; ?>>
  <?php if ($title !== '') : ?><figcaption class="chart-title"><?php echo esc_html($title); ?></figcaption><?php endif; ?>
  <div class="chart-body" role="img" aria-label="<?php echo esc_attr($summary); ?>"><?php echo $vis; ?></div>
  <?php if ($legend && in_array($type, array('donut'), true)) : ?><ul class="chart-legend"><?php foreach ($rows as $i => $r) : ?><li><span class="chart-sw" style="background:<?php echo esc_attr($col($r['color'], $i)); ?>"></span><?php echo esc_html($r['label']); ?> <b><?php echo esc_html($fmt($r['value']) . $uOf($r)); ?></b></li><?php endforeach; ?></ul><?php endif; ?>
  <?php if ($caption !== '') : ?><p class="chart-caption"><?php echo esc_html($caption); ?></p><?php endif; ?>
  <?php echo $sr; ?>
</figure>
