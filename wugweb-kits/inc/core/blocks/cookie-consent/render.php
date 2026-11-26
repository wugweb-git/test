<?php
/** cookie-consent: port of BaseLayout.astro consent banner + preferences modal. Behaviour in view.js. */
defined('ABSPATH') || exit;
$a = $attributes;
$cats = (isset($a['cats']) && is_array($a['cats']) && count($a['cats']) === 3) ? $a['cats'] : array(
    array('title' => 'Strictly Necessary', 'text' => ''), array('title' => 'Functional', 'text' => ''), array('title' => 'Analytics', 'text' => ''));
$e = function ($k) use ($a) { return esc_html((string) ($a[$k] ?? '')); };
$policy = !empty($a['policyUrl']) ? '<a class="cookie-policy-link" href="' . esc_url($a['policyUrl']) . '">' . esc_html($a['policyLabel'] ?? 'Cookie Policy') . '</a>' : '';
?>
<div class="wugweb-cookie-consent">
<div id="wugweb-cookie-banner" class="cookie-banner-wrap" role="region" aria-label="<?php echo esc_attr($a['title'] ?? 'Cookies'); ?>" hidden>
  <div class="cookie-banner-inner">
    <div class="cookie-banner-text">
      <div class="cookie-head"><p class="cookie-title"><?php echo $e('title'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- closure returns esc_html() ?></p><?php if (!empty($a['byLabel'])) : ?><span class="cookie-by"><?php echo $e('byLabel'); ?></span><?php endif; ?></div>
      <p class="cookie-body"><?php echo $e('subtitle'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- closure returns esc_html() ?> <button type="button" id="cookie-btn-manage" class="cookie-more"><?php echo $e('manage'); ?></button></p>
    </div>
    <div class="cookie-banner-actions">
      <button type="button" id="cookie-btn-accept-all" class="btn btn-solid btn-sm-cookie cookie-accept"><?php echo $e('accept'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- closure returns esc_html() ?></button>
      <button type="button" id="cookie-btn-reject-optional" class="btn btn-ghost btn-sm-cookie cookie-reject"><?php echo $e('reject'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- closure returns esc_html() ?></button>
    </div>
  </div>
</div>
<div id="wugweb-cookie-modal" class="cookie-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="cookie-modal-title" hidden>
  <div class="cookie-modal-box">
    <button type="button" id="cookie-modal-close" class="cookie-modal-close-btn" aria-label="<?php echo esc_attr($a['close'] ?? 'Close preferences'); ?>">&times;</button>
    <h2 id="cookie-modal-title"><?php echo $e('modalTitle'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- closure returns esc_html() ?></h2>
    <p class="cookie-modal-intro"><?php echo $e('modalIntro'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- closure returns esc_html() ?> <?php echo $policy; ?></p>
    <div class="cookie-options-list">
      <div class="cookie-opt-row"><div class="opt-desc"><h3><?php echo esc_html($cats[0]['title']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- closure returns esc_html() ?></h3><p><?php echo esc_html($cats[0]['text']); ?></p></div><div class="opt-control"><span class="active-badge"><?php echo $e('alwaysActive'); ?></span></div></div>
      <div class="cookie-opt-row"><div class="opt-desc"><h3 id="cookie-functional-h"><?php echo esc_html($cats[1]['title']); ?></h3><p><?php echo esc_html($cats[1]['text']); ?></p></div><div class="opt-control"><label class="toggle-switch"><input type="checkbox" id="cookie-toggle-functional" aria-labelledby="cookie-functional-h" checked><span class="toggle-slider"></span></label></div></div>
      <div class="cookie-opt-row"><div class="opt-desc"><h3 id="cookie-analytics-h"><?php echo esc_html($cats[2]['title']); ?></h3><p><?php echo esc_html($cats[2]['text']); ?></p></div><div class="opt-control"><label class="toggle-switch"><input type="checkbox" id="cookie-toggle-analytics" aria-labelledby="cookie-analytics-h" checked><span class="toggle-slider"></span></label></div></div>
    </div>
    <div class="cookie-modal-actions">
      <button type="button" id="cookie-modal-reject" class="btn btn-ghost"><?php echo $e('reject'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- closure returns esc_html() ?></button>
      <button type="button" id="cookie-modal-save" class="btn btn-solid"><?php echo $e('save'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- closure returns esc_html() ?></button>
      <button type="button" id="cookie-modal-accept" class="btn btn-solid"><?php echo $e('accept'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- closure returns esc_html() ?></button>
    </div>
  </div>
</div>
</div>
