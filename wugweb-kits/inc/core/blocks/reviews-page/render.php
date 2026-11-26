<?php
/** Reviews Page — agency platforms, employee reviews, other recognition (footer/reviews.astro). Platform logos arrive later: grey placeholder. */
defined('ABSPATH') || exit;
$platforms = array(array('Clutch', 'https://clutch.co/profile/wugweb'), array('DesignRush', 'https://www.designrush.com/agency/profile/wugweb'), array('GoodFirms', 'https://www.goodfirms.co/company/wugweb'));
?>
<div <?php echo get_block_wrapper_attributes(array('class' => 'reviews-page')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core ?>>
<section class="wrap reviews-section" id="reviews-section" aria-labelledby="reviews-section-title">
  <div class="section-header">
    <h2 data-reveal id="reviews-section-title">Agency reviews</h2>
    <p class="section-lead" data-reveal>Verified review platforms Wugweb is listed on. Ratings and counts live on each platform directly — visit the profile for the current number.</p>
  </div>
  <div class="platform-grid">
    <?php foreach ($platforms as $p) : ?>
    <a href="<?php echo esc_url($p[1]); ?>" target="_blank" rel="noopener noreferrer" class="platform-card glass-panel" data-reveal>
      <span class="wugweb-ph platform-logo-ph" style="--ph-ar:120/26" aria-hidden="true"></span>
      <span class="platform-name"><?php echo esc_html($p[0]); ?></span>
      <span class="platform-cta">View profile</span>
    </a>
    <?php endforeach; ?>
  </div>
</section>
<section class="wrap reviews-section" id="reviews-section-3" aria-labelledby="reviews-section-3-title">
  <div class="section-header">
    <h2 data-reveal id="reviews-section-3-title">Employee reviews</h2>
    <p class="section-lead" data-reveal>No verified employer-review platform profile (Glassdoor, AmbitionBox, etc.) is on record for Wugweb yet — this section is ready to go the moment there's a real, confirmed profile link to point at. Nothing is linked here to avoid guessing at a URL for a page like this.</p>
  </div>
</section>
<section class="wrap reviews-section" id="reviews-section-4" aria-labelledby="reviews-section-4-title">
  <div class="section-header"><h2 data-reveal id="reviews-section-4-title">Other recognition</h2></div>
  <div class="platform-grid">
    <a href="https://www.startupindia.gov.in/" target="_blank" rel="noopener noreferrer" class="platform-card glass-panel" data-reveal>
      <span class="platform-name">DPIIT Recognised Startup</span>
      <span class="platform-sub">Department for Promotion of Industry and Internal Trade, Government of India</span>
      <span class="platform-cta">View registry</span>
    </a>
  </div>
</section>
</div>
