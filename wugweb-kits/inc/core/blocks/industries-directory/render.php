<?php
/** Industries Directory — port of pages/footer/industries.astro + IndustriesHero + SectorRow. Data: sector/industry terms (Notion-synced meta). */
defined('ABSPATH') || exit;
$icons = json_decode((string) file_get_contents(__DIR__ . '/../../includes/lucide-sector-icons.json'), true) ?: array();
$sectors = array();
foreach ((array) get_terms(array('taxonomy' => 'sector', 'hide_empty' => false)) as $t) {
  if (is_wp_error($t)) { continue; }
  $d = json_decode((string) get_term_meta($t->term_id, 'wugweb_notion_data', true), true) ?: array();
  $sectors[] = array('name' => html_entity_decode($t->name, ENT_QUOTES), 'slug' => $t->slug, 'desc' => (string) $t->description, 'icon' => $d['icon'] ?? '', 'show' => !empty($d['show']), 'order' => (int) ($d['order'] ?? 99), 'inds' => array());
}
usort($sectors, function ($a, $b) { return $a['order'] <=> $b['order']; });
$by = array(); foreach ($sectors as $i => $s) { $by[$s['slug']] = $i; }
foreach ((array) get_terms(array('taxonomy' => 'industry', 'hide_empty' => false, 'orderby' => 'name')) as $in) {
  if (is_wp_error($in)) { continue; }
  $d = json_decode((string) get_term_meta($in->term_id, 'wugweb_notion_data', true), true) ?: array();
  foreach ((array) ($d['sector'] ?? array()) as $sl) { if (isset($by[$sl])) { $sectors[$by[$sl]]['inds'][] = html_entity_decode($in->name, ENT_QUOTES); } }
}
$sectors = array_values(array_filter($sectors, function ($s) { return $s['inds'] || $s['show']; }));
if (!$sectors) { return; }
$hero = array_slice(array_values(array_filter($sectors, function ($s) { return $s['show']; })), 0, 8);
$cells = array('n', 'w', 'e', 's', 'nw', 'ne', 'sw', 'se');
$pts = array('nw' => array(50, 50), 'n' => array(150, 50), 'ne' => array(250, 50), 'w' => array(50, 150), 'e' => array(250, 150), 'sw' => array(50, 250), 's' => array(150, 250), 'se' => array(250, 250));
$title = (string) ($attributes['title'] ?? '');
$lead = (string) ($attributes['lead'] ?? '');
$cross = array_slice($hero, 0, 3);
$arrows = array(1 => array('|'), 2 => array('\\', '/'), 3 => array('\\', '|', '/'));
$cross_arrows = $arrows[count($cross)] ?? array();
?>
<div <?php echo get_block_wrapper_attributes(array('class' => 'industries-page')); ?>>
<section class="ind-hero wrap" id="hero-section" aria-labelledby="hero-section-title">
  <div class="ind-hero-copy">
    <h1 id="hero-section-title" data-reveal><?php echo esc_html($title); ?></h1>
    <p class="ind-hero-lead" data-reveal><?php echo esc_html($lead); ?></p>
  </div>
  <?php if ($hero) : ?>
  <div class="ind-hero-visual" aria-hidden="true" data-reveal>
    <div class="ind-diagram">
      <svg class="ind-diagram-lines" viewBox="0 0 300 300" preserveAspectRatio="none" focusable="false">
        <?php foreach ($hero as $i => $s) : $c = $cells[$i]; ?><line class="ind-line<?php echo $i >= 4 ? ' is-extra' : ''; ?>" x1="150" y1="150" x2="<?php echo (int) $pts[$c][0]; ?>" y2="<?php echo (int) $pts[$c][1]; ?>" vector-effect="non-scaling-stroke"></line><?php endforeach; ?>
      </svg>
      <div class="ind-node ind-node-centre" style="grid-area: c">Wugweb</div>
      <?php foreach ($hero as $i => $s) : ?>
      <div class="ind-node<?php echo $i >= 4 ? ' is-extra' : ''; ?>" style="grid-area: <?php echo esc_attr($cells[$i]); ?>">
        <?php if (!empty($icons[$s['icon']])) : ?><span class="ind-node-tile"><?php echo $icons[$s['icon']]; // phpcs:ignore ?></span><?php endif; ?>
        <span class="ind-node-name"><?php echo esc_html($s['name']); ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</section>

<section class="directory-section wrap" id="industries" aria-labelledby="directory-section-title">
  <div class="section-header">
    <h2 data-reveal id="directory-section-title">Explore our industries.</h2>
    <p class="section-lede" data-reveal>From highly regulated sectors to fast-moving consumer businesses, we adapt our approach to the context we're working in.</p>
  </div>
  <div class="directory-list">
    <?php foreach ($sectors as $i => $s) : ?>
    <article class="dir-row <?php echo $i % 2 === 1 ? 'is-tint' : 'is-plain'; ?>" id="<?php echo esc_attr($s['slug']); ?>" aria-labelledby="sector-<?php echo esc_attr($s['slug']); ?>-title">
      <div class="dir-row-meta">
        <span class="dir-row-num" aria-hidden="true"><?php echo esc_html(str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)); ?></span>
        <h3 class="dir-row-name" id="sector-<?php echo esc_attr($s['slug']); ?>-title"><?php echo esc_html($s['name']); ?></h3>
        <?php if ($s['desc'] !== '') : ?><p class="dir-row-line"><?php echo esc_html($s['desc']); ?></p><?php endif; ?>
      </div>
      <?php if ($s['inds']) : ?>
      <ul class="dir-row-labels" role="list" aria-label="<?php echo esc_attr('Industries in ' . $s['name']); ?>"><?php foreach ($s['inds'] as $n) : ?><li class="dir-row-label"><?php echo esc_html($n); ?></li><?php endforeach; ?></ul>
      <?php endif; ?>
    </article>
    <?php endforeach; ?>
  </div>
</section>

<section class="cross-experience-section wrap" id="cross-experience-section" aria-labelledby="cross-experience-section-title">
  <div class="cross-grid">
    <div class="cross-copy" data-reveal>
      <h2 id="cross-experience-section-title">Different industries. Shared problems.</h2>
      <p>Customers still expect clarity. Teams still need better systems. Businesses still need products that work.</p>
      <p>Working across industries allows us to recognise patterns, transfer useful ideas and challenge assumptions, without ignoring the context that makes each business different.</p>
    </div>
    <div class="cross-visual" data-reveal>
      <div class="transfer-diagram" aria-hidden="true">
        <?php if ($cross) : ?>
        <div class="diagram-inputs"><?php foreach ($cross as $s) : ?><div class="diag-node"><?php echo esc_html($s['name']); ?></div><?php endforeach; ?></div>
        <div class="diagram-arrows"><?php foreach ($cross_arrows as $a) : ?><span><?php echo esc_html($a); ?></span><?php endforeach; ?></div>
        <?php endif; ?>
        <div class="diagram-center"><span>WUGWEB</span></div>
        <div class="diagram-arrows"><span>|</span></div>
        <div class="diagram-outputs">
          <div class="diag-node outline">Shared principles</div>
          <div class="diagram-arrows"><span>|</span></div>
          <div class="diag-node active">Better digital systems</div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="missing-industry-section wrap" data-reveal id="no-industry" aria-labelledby="missing-industry-section-title">
  <div class="missing-inner">
    <h2 id="missing-industry-section-title">Don't see your industry?</h2>
    <p>Our experience spans different sectors, business models and operating environments. If your industry isn't listed, tell us what you're building and we'll explore where we can help.</p>
    <div class="missing-actions">
      <a href="<?php echo esc_url(home_url('/contact-us/')); ?>" class="btn btn-solid btn-light">Talk to us</a>
      <a href="<?php echo esc_url(home_url('/services/')); ?>" class="btn btn-ghost">View our services</a>
    </div>
  </div>
</section>

<section class="final-cta wrap" data-reveal id="final-cta" aria-labelledby="final-cta-title">
  <div class="final-inner">
    <h2 id="final-cta-title">Bring us the problem, not just the brief.</h2>
    <p>Tell us what you're trying to achieve. We'll bring the industry context, strategy, design and technology needed to move it forward.</p>
    <a href="<?php echo esc_url(home_url('/contact-us/')); ?>" class="btn btn-solid">Start a conversation</a>
  </div>
</section>
</div>
