<?php
/** toolkit-grid: Astro .toolkit-section. Data: shared _toolkit {title, groups{category:[items]}}; optional icons{name:url}. */
defined('ABSPATH') || exit;
$p = sanitize_key($attributes['pillar'] ?? 'brand');
$tk = wugweb_core_service_toolkit();
if (!$tk || empty($tk['groups'])) { return; }
$icons = array();
foreach ((array) ($tk['icons'] ?? array()) as $k => $u) { $icons[strtolower((string) $k)] = $u; }
$style = '--theme-base: var(--color-accent-primary);';
$wrap = get_block_wrapper_attributes(array('class' => 'wrap toolkit-section wugweb-toolkit-grid', 'id' => 'toolkit-section', 'aria-labelledby' => 'toolkit-section-title', 'style' => $style));
?>
<section <?php echo $wrap; ?>>
  <h2 data-reveal id="toolkit-section-title"><?php echo esc_html($tk['title']); ?></h2>
  <div class="toolkit-grid">
    <?php foreach ($tk['groups'] as $cat => $items) : ?>
    <div class="glass-panel toolkit-category" data-reveal>
      <div class="toolkit-category-header"><?php echo esc_html($cat); ?></div>
      <ul class="toolkit-items">
        <?php foreach ($items as $item) : $ic = $icons[strtolower((string) $item)] ?? ''; ?>
        <li><?php if ($ic) : ?><img src="<?php echo esc_url($ic); ?>" alt="" width="16" height="16" loading="lazy"><?php endif; ?><span><?php echo esc_html($item); ?></span></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endforeach; ?>
  </div>
</section>
