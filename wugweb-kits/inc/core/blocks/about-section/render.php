<?php
/** About Section — #culture and #reviews bands from about.astro. */
defined('ABSPATH') || exit;
$section = in_array(($attributes['section'] ?? 'culture'), array('reviews', 'stats', 'values'), true) ? $attributes['section'] : 'culture';
if ($section === 'stats') :
  $cases = (int) wp_count_posts('case_study')->publish; $inds = (int) wp_count_terms(array('taxonomy' => 'industry', 'hide_empty' => false)); $clients = wugweb_client_count(); $years = max(1, (int) gmdate('Y') - 2020);
  $stats = array(array($years . '+', 'Years of experience', 'Expertise that drives meaningful results.'), array(($cases ?: '—') . ($cases ? '+' : ''), 'Projects delivered', 'Creative solutions built with purpose.'), array(($inds ?: '—') . ($inds ? '+' : ''), 'Industries served', 'Sector fluency built case by case.'), array(($clients ?: '—') . ($clients ? '+' : ''), 'Brands transformed', 'From strategy to standout visuals.')); ?>
<section <?php echo get_block_wrapper_attributes(array('class' => 'wrap stats', 'id' => 'stats', 'data-scroll' => 'rise', 'aria-label' => 'Stats')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core ?>>
  <ul data-counters><?php foreach ($stats as $s) : preg_match('/^(\d[\d,.]*)(.*)$/', $s[0], $m); ?>
    <li data-reveal><div class="stat-card"><span class="stat-n"><?php if ($m) : ?><span class="stat-num" data-count-to="<?php echo esc_attr(str_replace(',', '', $m[1])); ?>"><?php echo esc_html($m[1]); ?></span><span class="stat-suffix"><?php echo esc_html($m[2]); ?></span><?php else : ?><span class="stat-num is-blank"><?php echo esc_html($s[0]); ?></span><?php endif; ?></span><p class="stat-label"><?php echo esc_html($s[1]); ?></p><p class="stat-copy"><?php echo esc_html($s[2]); ?></p></div></li>
  <?php endforeach; ?></ul>
</section>
<?php elseif ($section === 'values') :
  $values = array(array('01', 'Bold', 'We balance distinctive expression with measurable results.'), array('02', 'Adaptive', 'We build flexible solutions without sacrificing creative vision.'), array('03', 'Distinct', 'We reject sameness, crafting experiences with unique identities.'), array('04', 'Start Smart, Start Small', 'We start focused, prove value early, and scale what works.')); ?>
<section <?php echo get_block_wrapper_attributes(array('class' => 'wrap values', 'id' => 'values', 'data-scroll' => 'rise', 'aria-labelledby' => 'values-title')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core ?>>
  <h2 class="sr-only" id="values-title">Our values</h2>
  <p class="values-lede" data-reveal>Creating projects at the nexus of design and technology, bringing the future into the present. We develop comprehensive strategies, looking from a hybrid and more coherent perspective. Then we give it a life of its own by equipping it with cutting-edge systems that allow it to adapt to and live in the wild, ever-evolving environment.</p>
  <ol class="values-list"><?php foreach ($values as $v) : ?><li data-reveal><div class="value-card"><span class="value-n"><?php echo esc_html($v[0]); ?></span><h3><?php echo esc_html($v[1]); ?></h3><p><?php echo esc_html($v[2]); ?></p></div></li><?php endforeach; ?></ol>
</section>
<?php elseif ($section === 'reviews') : ?>
<section <?php echo get_block_wrapper_attributes(array('class' => 'wrap reviews', 'id' => 'reviews', 'aria-labelledby' => 'reviews-title')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core ?>>
  <a href="<?php echo esc_url(home_url('/reviews/')); ?>" class="reviews-teaser" data-reveal>
    <div><h2 id="reviews-title">What clients and the team say.</h2><p>Verified review platforms Wugweb is listed on, and how to reach us if you're one of them.</p></div>
    <span class="btn btn-solid">See reviews</span>
  </a>
</section>
<?php else :
$culture = array('We Create Trust', 'We listen closely', 'We define your value', 'We pivot', 'We hustle', 'We form connections', 'We get cosy', 'We get our hands dirty'); ?>
<section <?php echo get_block_wrapper_attributes(array('class' => 'wrap culture', 'id' => 'culture', 'data-scroll' => 'rise', 'aria-labelledby' => 'culture-title')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core ?>>
  <div class="culture-head">
    <div>
      <h2 data-reveal id="culture-title">Culture Reigns Supreme</h2>
      <p data-reveal>Establishing a robust foundational culture within our organisation is vital to Wugweb's long-term vision, growth, and success. We are driven by four core values: intimacy, transparency, urgency, and pivoting.</p>
    </div>
    <img class="culture-visual" src="<?php echo esc_url(wugweb_r2_url('assets/site/about/about-section-2.jpg')); ?>" alt="Wugweb team culture" loading="lazy" decoding="async">
  </div>
  <ul class="culture-grid"><?php foreach ($culture as $c) : ?><li data-reveal><?php echo esc_html($c); ?></li><?php endforeach; ?></ul>
</section>
<?php endif;
