<?php
/**
 * capability-showcase: Astro .capabilities-section + ServiceSpotlightPanel.astro.
 * Items: $d['caps'][] {t, d, media?{video,poster}}; blurb = d. Media is optional (placeholder state when absent).
 */
defined('ABSPATH') || exit;
$p = sanitize_key($attributes['pillar'] ?? 'brand');
$d = wugweb_core_service_data($p);
if (!$d || empty($d['caps'])) { return; }
$items = array();
foreach ($d['caps'] as $i => $c) {
  $t = trim((string) ($c['t'] ?? ''));
  if ($t === '') { continue; }
  $m = (!empty($c['media']['video']) && !empty($c['media']['poster'])) ? $c['media'] : null;
  $items[] = array('title' => $t, 'n' => str_pad((string) (count($items) + 1), 2, '0', STR_PAD_LEFT), 'index' => count($items), 'media' => $m, 'blurb' => (string) ($c['d'] ?? ''));
}
if (!$items) { return; }
$dense = count($items) > 12;
$heading = $d['capsTitle'] ?? 'What we can do';
$theme = 'var(--color-accent-primary)';
$wrap = get_block_wrapper_attributes(array('class' => 'wrap capabilities-section wugweb-capability-showcase', 'id' => 'capabilities-section', 'aria-labelledby' => 'caps-heading'));
?>
<section <?php echo $wrap; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core ?>>
  <h2 class="eyebrow caps-heading" id="caps-heading" data-reveal><?php echo esc_html($heading); ?></h2>
  <div class="showcase" data-showcase style="--theme-base: <?php echo esc_attr($theme); ?>;">
    <div class="showcase-bg" aria-hidden="true">
      <?php foreach ($items as $it) : if (!$it['media']) { continue; } ?>
      <img class="showcase-bg-img" data-bg="<?php echo (int) $it['index']; ?>" src="<?php echo esc_url($it['media']['poster']); ?>" alt="" width="640" height="480" loading="lazy" decoding="async">
      <?php endforeach; ?>
      <span class="showcase-bg-scrim"></span>
    </div>
    <div class="showcase-panel glass-panel"<?php echo $dense ? ' data-dense="true"' : ''; ?>>
      <?php foreach ($items as $it) : $hid = 'cap-' . $p . '-' . $it['index']; ?>
      <div class="showcase-card" data-card="<?php echo (int) $it['index']; ?>">
        <span class="showcase-media">
          <?php if ($it['media']) : ?>
          <video class="showcase-video" data-video data-label="<?php echo esc_attr($it['title'] . ' clip'); ?>" src="<?php echo esc_url($it['media']['video']); ?>" poster="<?php echo esc_url($it['media']['poster']); ?>" preload="none" muted loop playsinline tabindex="-1" aria-hidden="true"></video>
          <button type="button" class="showcase-pause" data-pause data-label-pause="<?php echo esc_attr('Pause ' . $it['title'] . ' clip'); ?>" data-label-play="<?php echo esc_attr('Play ' . $it['title'] . ' clip'); ?>" aria-label="<?php echo esc_attr('Pause ' . $it['title'] . ' clip'); ?>" hidden>
            <svg class="showcase-pause-icon" data-icon="pause" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><rect x="4" y="3" width="3" height="10" rx="1"/><rect x="9" y="3" width="3" height="10" rx="1"/></svg>
            <svg class="showcase-pause-icon" data-icon="play" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path d="M5 3.5v9a.75.75 0 0 0 1.14.64l7.2-4.5a.75.75 0 0 0 0-1.28l-7.2-4.5A.75.75 0 0 0 5 3.5Z"/></svg>
          </button>
          <?php else : ?>
          <span class="showcase-placeholder" aria-hidden="true"><span class="showcase-placeholder-n"><?php echo esc_html($it['n']); ?></span></span>
          <?php endif; ?>
        </span>
        <span class="showcase-meta">
          <span class="showcase-n" aria-hidden="true"><?php echo esc_html($it['n']); ?></span>
          <h3 class="showcase-title" id="<?php echo esc_attr($hid); ?>"><?php echo esc_html($it['title']); ?></h3>
          <?php if ($it['blurb'] !== '') : ?><span class="showcase-blurb"><?php echo esc_html($it['blurb']); ?></span><?php endif; ?>
        </span>
        <button type="button" class="showcase-toggle" aria-expanded="false" aria-labelledby="<?php echo esc_attr($hid); ?>"></button>
        <span class="showcase-progress" aria-hidden="true"></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
