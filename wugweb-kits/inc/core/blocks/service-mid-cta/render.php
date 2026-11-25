<?php
/** service-mid-cta: Astro .approach-cta aside (standing CTA), copy from $d['cta']. */
defined('ABSPATH') || exit;
$p = sanitize_key($attributes['pillar'] ?? 'brand');
$d = wugweb_core_service_data($p);
if (!$d || empty($d['cta'])) { return; }
$cta = $d['cta'];
$base = rtrim((string) apply_filters('wugweb_assets_base', 'https://pub-39ad1d03baec440faf48303be95c3782.r2.dev'), '/');
$img = $base . '/assets/site/services/reference/' . ($p === 'brand' ? 'brand-hero.jpg' : 'services-hero.jpg');
$logo = get_theme_file_uri('assets/brand/logo-large.svg');
$style = '--theme-base: var(--color-accent-primary);';
$wrap = get_block_wrapper_attributes(array('class' => 'approach-cta wugweb-service-mid-cta', 'style' => $style, 'data-reveal' => ''));
$btn = $cta['btn'] ?? array('Start a project', 'See the work first');
?>
<aside <?php echo $wrap; ?>>
  <img class="approach-cta-bg" src="<?php echo esc_url($img); ?>" alt="" loading="lazy" decoding="async">
  <div class="approach-cta-inner">
    <img class="approach-cta-mark" src="<?php echo esc_url($logo); ?>" alt="Wugweb" width="36" height="36">
    <h3><?php echo esc_html($cta['t'] ?? ''); ?></h3>
    <?php if (!empty($cta['p'])) : ?><p class="approach-cta-body"><?php echo esc_html($cta['p']); ?></p><?php endif; ?>
    <a href="<?php echo esc_url(home_url('/contact-us/')); ?>" class="btn btn-solid approach-cta-btn"><?php echo esc_html($btn[0] ?? 'Start a project'); ?></a>
    <a href="<?php echo esc_url(home_url('/work/')); ?>" class="approach-cta-link"><?php echo esc_html($btn[1] ?? 'See the work first'); ?></a>
  </div>
</aside>
