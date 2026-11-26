<?php
/** Page Hero: heroes of wugweb_site/src/pages/*.astro, one variant per source hero. */
$a = $attributes; $v = sanitize_key((string) ($a['variant'] ?? 'standard'));
$pid = wugweb_block_post_id($block ?? null, $a, array());
$title = (string) ($a['title'] ?? ''); $lede = (string) ($a['lede'] ?? ''); $sub = (string) ($a['sub'] ?? ''); $eyebrow = (string) ($a['eyebrow'] ?? '');
$tags = array_filter(array_map('trim', explode(',', (string) ($a['tags'] ?? ''))));
if ($title === '' && $pid) { $title = get_the_title($pid); }
if ($lede === '' && $pid && in_array($v, array('article', 'case'), true)) { $lede = has_excerpt($pid) ? wp_strip_all_tags(get_the_excerpt($pid)) : ''; }
if (!$tags && $pid && $v === 'article') { $t = get_the_tags($pid); if ($t) $tags = wp_list_pluck($t, 'name'); }
if (!$tags && $pid && $v === 'case') { $tags = array_merge(wugweb_term_names($pid, 'industry'), wugweb_term_names($pid, 'sector')); }
if ($eyebrow === '' && $pid && $v === 'case') { $eyebrow = (string) get_post_meta($pid, 'wugweb_cs_client', true); }
$btn = function ($l, $u, $cls) { return $l !== '' ? '<a href="' . esc_url($u) . '" class="btn ' . $cls . '">' . esc_html($l) . '</a>' : ''; };
$b1 = $btn((string) ($a['primaryLabel'] ?? ''), (string) ($a['primaryUrl'] ?? '#'), 'btn-solid');
$b2 = $btn((string) ($a['secondaryLabel'] ?? ''), (string) ($a['secondaryUrl'] ?? '#'), 'btn-ghost');
$media = (string) ($a['mediaUrl'] ?? ''); $poster = (string) ($a['posterUrl'] ?? '');
if ($v === 'about') { if ($media === '') { $hp = $pid ? (string) get_post_meta($pid, 'wugweb_page_hero_path', true) : ''; $media = $hp !== '' ? (preg_match('#^https?://#', $hp) ? $hp : wugweb_r2_url($hp)) : wugweb_r2_url('assets/site/about/about__about-message.mp4'); } if ($poster === '') { $poster = wugweb_r2_url('assets/site/about/about-section.jpg'); } }
$is_vid = (bool) preg_match('/\.(mp4|webm)(\?|$)/i', $media);
$h1 = '<h1 class="page-title" data-reveal id="page-hero-title">' . esc_html($title) . '</h1>';
$eb = $eyebrow !== '' ? '<p class="eyebrow">' . esc_html($eyebrow) . '</p>' : '';
echo '<div ' . get_block_wrapper_attributes(array('class' => 'page-hero--' . $v)) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core 
switch ($v) {
case 'about':
  echo '<section class="about-hero" id="about-hero" aria-labelledby="page-hero-title"><div class="wrap about-hero-content">' . $h1 . ($lede !== '' ? '<p class="about-hero-lede" data-reveal>' . esc_html($lede) . '</p>' : '') . '</div><div class="wrap about-hero-media" data-reveal><div class="about-hero-frame">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_html()/esc_url() or a sanitized variant key above 
  if ($is_vid) echo '<video aria-hidden="true" autoplay muted loop playsinline src="' . esc_url($media) . '"' . ($poster ? ' poster="' . esc_url($poster) . '"' : '') . '></video>';
  elseif ($media) echo '<img src="' . esc_url($media) . '" alt="" style="display:block;width:100%;aspect-ratio:2/1;object-fit:cover">';
  else echo '<span class="wugweb-ph" style="--ph-ar:2/1" aria-hidden="true"></span>';
  if (!empty($a['statement'])) echo '<p class="about-hero-statement is-lead">' . esc_html($a['statement']) . '</p>';
  if (!empty($a['statementSub'])) echo '<p class="about-hero-statement is-sub">' . esc_html($a['statementSub']) . '</p>';
  echo '</div></div></section>'; break;
case 'services': case 'work': case 'blog':
  echo '<section class="' . $v . '-hero" id="' . $v . '-hero" aria-labelledby="page-hero-title"><div class="wrap ' . $v . '-hero-content">' . $h1 . ($lede !== '' ? '<p data-reveal>' . esc_html($lede) . '</p>' : '') . ($sub !== '' ? '<p class="services-hero-sub" data-reveal>' . esc_html($sub) . '</p>' : '') . '</div></section>'; break; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_html()/esc_url() or a sanitized variant key above 
case 'faq':
  echo '<section class="wrap faq" id="faq" aria-labelledby="page-hero-title">' . $h1 . ($lede !== '' ? '<p class="faq-lede" data-reveal>' . esc_html($lede) . '</p>' : '') . ($sub !== '' ? '<p class="faq-updated" data-reveal>' . esc_html($sub) . '</p>' : '') . '</section>'; break; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_html()/esc_url() or a sanitized variant key above 
case 'contact':
  echo '<div class="contact-v2-container wrap"><header class="contact-v2-hero">' . (!empty($a['badge']) ? '<div class="hero-badge"><span class="pulse-dot"></span><span>' . esc_html($a['badge']) . '</span></div>' : '') . $h1 . ($lede !== '' ? '<p class="hero-statement">' . esc_html($lede) . '</p>' : '') . ($sub !== '' ? '<p class="hero-subtext">' . esc_html($sub) . '</p>' : '') . '<div class="hero-quick-actions">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_html()/esc_url() or a sanitized variant key above 
  foreach ((array) ($a['chips'] ?? array()) as $c) { if (empty($c['label'])) continue; $ext = !empty($c['href']) && strpos($c['href'], 'http') === 0; echo !empty($c['href']) ? '<a href="' . esc_url($c['href']) . '" class="quick-chip"' . ($ext ? ' target="_blank" rel="noopener noreferrer"' : '') . '>' . esc_html($c['label']) . '</a>' : '<span class="quick-sla">' . esc_html($c['label']) . '</span>'; }
  echo '</div></header></div>'; break;
case 'notfound':
  echo '<section class="wrap not-found" id="not-found" aria-labelledby="page-hero-title"><h1 id="page-hero-title">' . esc_html($title) . '</h1>' . ($lede !== '' ? '<p>' . esc_html($lede) . '</p>' : '') . (!empty($a['showGame']) ? '<div class="game" id="game"><span class="cell" data-cell></span><span class="cell" data-cell></span><span class="cell" data-cell></span><span class="cell" data-cell></span><span class="cell" data-cell></span><span class="cell" data-cell></span><span class="cell" data-cell></span><span class="cell" data-cell></span><span class="cell" data-cell></span></div>' : '') . $b1 . '</section>'; break; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_html()/esc_url() or a sanitized variant key above 
case 'thanks':
  echo '<section class="wrap thank-you" id="thank-you" aria-labelledby="page-hero-title"><h1 id="page-hero-title">' . esc_html($title) . '</h1>' . ($lede !== '' ? '<p>' . esc_html($lede) . '</p>' : '') . '<div class="thank-you-actions">' . $b1 . $b2 . '</div></section>'; break; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_html()/esc_url() or a sanitized variant key above 
case 'split':
  echo '<section class="hero-section is-split wrap" id="hero-section" aria-labelledby="page-hero-title"><div class="hero-content">' . $eb . $h1 . ($lede !== '' ? '<p class="hero-lead" data-reveal>' . esc_html($lede) . '</p>' : '') . ($sub !== '' ? '<p class="hero-body" data-reveal>' . esc_html($sub) . '</p>' : ''); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_html()/esc_url() or a sanitized variant key above 
  if (!empty($a['priceValue'])) echo '<div class="hero-price-box" data-reveal><div class="price-row"><span class="price-val">' . esc_html($a['priceValue']) . '</span><span class="price-period">' . esc_html($a['pricePeriod'] ?? '') . '</span></div><p class="price-support">' . esc_html($a['priceNote'] ?? '') . '</p></div>';
  echo '<div class="hero-actions" data-reveal>' . $b1 . $b2 . '</div></div><div class="hero-visual-slot" data-reveal><div class="visual-container ratio-16-9">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_html()/esc_url() or a sanitized variant key above 
  if ($is_vid) echo '<video aria-hidden="true" autoplay muted loop playsinline src="' . esc_url($media) . '"></video>'; elseif ($media) echo '<img src="' . esc_url($media) . '" alt="" loading="lazy">'; else echo '<span class="wugweb-ph" style="--ph-ar:16/9" aria-hidden="true"></span>';
  echo '</div></div></section>'; break;
case 'narrow':
  echo '<section class="hero-section is-narrow wrap" id="hero-section" aria-labelledby="page-hero-title">' . $h1 . ($lede !== '' ? '<p class="hero-lead" data-reveal>' . esc_html($lede) . '</p>' : '') . '</section>'; break; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_html()/esc_url() or a sanitized variant key above 
case 'service':
  echo '<section class="detail-hero" id="detail-hero" aria-labelledby="page-hero-title"><div class="hero-glow" data-reveal></div><div class="wrap detail-hero-content">' . $eb . $h1 . ($sub !== '' ? '<p class="hero-sub" data-reveal>' . esc_html($sub) . '</p>' : '') . ($lede !== '' ? '<p class="hero-text" data-reveal>' . esc_html($lede) . '</p>' : ''); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_html()/esc_url() or a sanitized variant key above 
  if ($tags) { echo '<ul class="service-tags" data-reveal>'; foreach ($tags as $t) echo '<li>' . esc_html($t) . '</li>'; echo '</ul>'; }
  echo ($b1 ? str_replace('class="btn', 'data-reveal class="btn', $b1) : '') . '</div></section>'; break; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_html()/esc_url() or a sanitized variant key above 
case 'article':
  echo '<header class="post-hero" id="article-hero"><h1 id="page-hero-title" data-reveal>' . esc_html($title) . '</h1>' . ($lede !== '' ? '<p class="article-intro" data-reveal>' . esc_html($lede) . '</p>' : '');
  if ($tags) { echo '<ul class="article-tags" aria-label="Tags" data-reveal>'; foreach ($tags as $t) echo '<li class="u-tag">' . esc_html($t) . '</li>'; echo '</ul>'; }
  echo '</header>'; break;
case 'case':
  echo '<section class="work-detail-hero" id="work-detail-hero" aria-labelledby="page-hero-title"><div class="wrap">' . $eb . '<h1 id="page-hero-title">' . esc_html($title) . '</h1>' . ($lede !== '' ? '<p class="work-subtitle">' . esc_html($lede) . '</p>' : ''); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_html()/esc_url() or a sanitized variant key above 
  if ($tags) { echo '<div class="work-tags">'; foreach ($tags as $t) echo '<span class="u-tag">' . esc_html($t) . '</span>'; echo '</div>'; }
  echo '</div></section>'; break;
default:
  echo '<section class="wrap" aria-labelledby="page-hero-title">' . $eb . $h1 . ($lede !== '' ? '<p data-reveal>' . esc_html($lede) . '</p>' : '') . '</section>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_html()/esc_url() or a sanitized variant key above 
}
echo '</div>';
