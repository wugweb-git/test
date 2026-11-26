<?php
/** toolkit-grid: Astro .toolkit-section. Data: shared _toolkit {title, groups{category:[items]}}; optional icons{name:url}. */
defined('ABSPATH') || exit;
$p = sanitize_key($attributes['pillar'] ?? 'brand');
// Per-service tools (service-data.json `tools`: [{category, items[]}]). A service without its own list shows no toolkit section; the old shared engineering list is no longer used for other pillars.
$svc = wugweb_core_service_data($p);
$tk = null;
if (!empty($svc['tools']) && is_array($svc['tools'])) {
  $groups = array();
  foreach ($svc['tools'] as $g) { if (!empty($g['category']) && !empty($g['items'])) { $groups[(string) $g['category']] = (array) $g['items']; } }
  if ($groups) { $tk = array('title' => 'Toolkit of industry-leading technologies', 'groups' => $groups); }
}
if (!$tk) { return; }
$icons = array();
foreach ((array) ($tk['icons'] ?? array()) as $k => $u) { $icons[strtolower((string) $k)] = $u; }
$style = '--theme-base: var(--color-accent-primary);';
$wrap = get_block_wrapper_attributes(array('class' => 'wrap toolkit-section wugweb-toolkit-grid', 'id' => 'toolkit-section', 'aria-labelledby' => 'toolkit-section-title', 'style' => $style));
?>
<section <?php echo $wrap; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core ?>>
  <h2 data-reveal id="toolkit-section-title"><?php echo esc_html($tk['title']); ?></h2>
  <div class="toolkit-grid">
    <?php foreach ($tk['groups'] as $cat => $items) : ?>
    <div class="glass-panel toolkit-category" data-reveal>
      <div class="toolkit-category-header"><?php echo esc_html($cat); ?></div>
      <ul class="toolkit-items">
        <?php foreach ($items as $item) : $ic = wugweb_tool_icon_by_name($item) ?: ($icons[strtolower((string) $item)] ?? ''); ?>
        <li><?php if ($ic) : ?><img src="<?php echo esc_url($ic); ?>" alt="" width="16" height="16" loading="lazy"><?php endif; ?><span><?php echo esc_html($item); ?></span></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endforeach; ?>
  </div>
</section>
