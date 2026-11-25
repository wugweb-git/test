<?php
/**
 * service-hero-metrics: Astro services/[slug].astro HERO + BODY + STATS sections.
 * Sanity/JSON data: wugweb_core_service_data(); subtitle/heroText/tags/body fall back to hero-copy.json
 * (extracted from src/lib/services-data.ts).
 */
defined('ABSPATH') || exit;
$p = sanitize_key($attributes['pillar'] ?? 'brand');
$d = wugweb_core_service_data($p);
if (!$d) { return; }
static $copy = null;
if ($copy === null) { $f = __DIR__ . '/hero-copy.json'; $copy = is_readable($f) ? json_decode(file_get_contents($f), true) : array(); }
$c = is_array($copy) && isset($copy[$p]) ? $copy[$p] : array();
$sub  = $d['subtitle'] ?? ($c['subtitle'] ?? ($d['tagline'] ?? ''));
$text = $d['heroText'] ?? ($c['heroText'] ?? '');
$tags = $d['tags'] ?? ($c['tags'] ?? array());
$body = $d['body'] ?? ($c['body'] ?? '');
$style = '--theme-base: var(--color-accent-primary); --theme-dark: var(--color-ash); --theme-light: var(--color-surface-inverse); --theme-gradient: linear-gradient(135deg, var(--theme-base), var(--theme-light));';
$wrap = get_block_wrapper_attributes(array('class' => 'wugweb-service-hero-metrics service-page-wrapper', 'style' => $style));
?>
<div <?php echo $wrap; ?>>
  <section class="detail-hero" id="detail-hero" aria-labelledby="detail-hero-title">
    <div class="hero-glow" data-reveal></div>
    <div class="wrap detail-hero-content">
      <h1 class="page-title" data-reveal id="detail-hero-title"><?php echo esc_html($d['h1']); ?></h1>
      <?php if ($sub !== '') : ?><p class="hero-sub" data-reveal><?php echo esc_html($sub); ?></p><?php endif; ?>
      <?php if ($text !== '') : ?><p class="hero-text" data-reveal><?php echo esc_html($text); ?></p><?php endif; ?>
      <?php if ($tags) : ?><ul class="service-tags" data-reveal><?php foreach ($tags as $t) : ?><li><?php echo esc_html($t); ?></li><?php endforeach; ?></ul><?php endif; ?>
      <a href="<?php echo esc_url(home_url('/contact-us/')); ?>" class="btn btn-solid" data-reveal>Get in Touch</a>
    </div>
  </section>
  <?php if ($body !== '') : ?>
  <section class="wrap detail-body" id="detail-body" aria-label="Detail body">
    <div class="glass-panel body-glass" data-reveal><p><?php echo esc_html($body); ?></p></div>
  </section>
  <?php endif; ?>
  <?php if (!empty($d['stats'])) : ?>
  <section class="wrap svc-stats" data-scroll="rise" id="svc-stats" aria-label="Svc stats">
    <ul>
      <?php foreach ($d['stats'] as $s) : ?>
      <li class="glass-panel stat-card" data-reveal>
        <span class="svc-stat-n"><?php echo esc_html($s['n']); ?></span>
        <p class="svc-stat-label"><?php echo esc_html($s['l'] ?? ($s['label'] ?? '')); ?></p>
      </li>
      <?php endforeach; ?>
    </ul>
  </section>
  <?php endif; ?>
</div>
