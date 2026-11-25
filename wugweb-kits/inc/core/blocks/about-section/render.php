<?php
/** About Section — #culture and #reviews bands from about.astro. */
defined('ABSPATH') || exit;
$section = (($attributes['section'] ?? 'culture') === 'reviews') ? 'reviews' : 'culture';
if ($section === 'reviews') : ?>
<section <?php echo get_block_wrapper_attributes(array('class' => 'wrap reviews', 'id' => 'reviews', 'aria-labelledby' => 'reviews-title')); ?>>
  <a href="<?php echo esc_url(home_url('/reviews/')); ?>" class="reviews-teaser" data-reveal>
    <div><h2 id="reviews-title">What clients and the team say.</h2><p>Verified review platforms Wugweb is listed on, and how to reach us if you're one of them.</p></div>
    <span class="btn btn-solid">See reviews</span>
  </a>
</section>
<?php else :
$culture = array('We Create Trust', 'We listen closely', 'We define your value', 'We pivot', 'We hustle', 'We form connections', 'We get cosy', 'We get our hands dirty'); ?>
<section <?php echo get_block_wrapper_attributes(array('class' => 'wrap culture', 'id' => 'culture', 'data-scroll' => 'rise', 'aria-labelledby' => 'culture-title')); ?>>
  <div class="culture-head">
    <div>
      <h2 data-reveal id="culture-title">Culture Reigns Supreme</h2>
      <p data-reveal>Establishing a robust foundational culture within our organisation is vital to Wugweb's long-term vision, growth, and success. We are driven by four core values: intimacy, transparency, urgency, and pivoting.</p>
    </div>
    <div class="culture-visual wugweb-ph" role="img" aria-label="Wugweb team culture"></div>
  </div>
  <ul class="culture-grid"><?php foreach ($culture as $c) : ?><li data-reveal><?php echo esc_html($c); ?></li><?php endforeach; ?></ul>
</section>
<?php endif;
