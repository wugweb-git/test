<?php
/** Legal Hub — LegalLayout.astro chrome around the policy text ($content = the page's own blocks). */
defined('ABSPATH') || exit;
$active = (string) ($attributes['active'] ?? 'privacy-policy');
$title = (string) ($attributes['title'] ?? '');
if ($title === '') { $title = get_the_title(); }
$updated = (string) ($attributes['updated'] ?? '');
$nav = array('privacy-policy' => array('Privacy Policy', '/privacy-policy/'), 'terms-of-use' => array('Terms of Use', '/terms-of-use/'), 'refund-policy' => array('Refund Policy', '/refund-policy/'), 'cookie-policy' => array('Cookie Policy', '/cookie-policy/'));
?>
<section <?php echo get_block_wrapper_attributes(array('class' => 'wrap legal-hub', 'id' => 'legal-hub', 'aria-labelledby' => 'legal-hub-title')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core ?>>
  <header class="legal-hub-head">
    <a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>" class="legal-hub-crumb" data-reveal>Legal &amp; Policies</a>
    <h1 class="page-title" id="legal-hub-title" data-reveal><?php echo esc_html($title); ?></h1>
    <?php if ($updated !== '') : ?><p class="legal-hub-updated meta-row" data-reveal><span class="meta-item"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="meta-icon" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg> Updated <?php echo esc_html($updated); ?></span></p><?php endif; ?>
  </header>
  <div class="legal-hub-grid">
    <nav class="legal-hub-nav" aria-label="Legal pages"><ul>
      <?php foreach ($nav as $slug => $n) : ?><li><a href="<?php echo esc_url(home_url($n[1])); ?>"<?php echo $slug === $active ? ' aria-current="page"' : ''; ?>><?php echo esc_html($n[0]); ?></a></li><?php endforeach; ?>
      <li><button type="button" id="legal-cookie-settings-btn" class="legal-hub-action">Cookie Settings</button></li>
    </ul></nav>
    <div class="legal-hub-content"><div class="legal legal-body"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput ?></div></div>
  </div>
</section>
