<?php
/** Article Post — port of blog/[slug].astro #article (grid: hero, sticky cover, body) + prev/next + reading progress. */
defined('ABSPATH') || exit;
$id = wugweb_block_post_id($block ?? null, $attributes, array('post'));
if (!$id) { return; }
static $wugweb_article_busy = false;
if ($wugweb_article_busy) { return; }
$wugweb_article_busy = true;
$f = wugweb_fields_post($id);
$card = wugweb_post_card_data($id);
$accent = $card['accent'];
$uid = (int) get_post_field('post_author', $id);
$author = $f['author'];
$author_data = $uid ? wugweb_fields_user($uid) : array();
$photo = $author_data['image'] ?? '';
$desc = $uid ? trim((string) get_the_author_meta('description', $uid)) : '';
$parts = preg_split('/\R+/', $desc, 2);
$role = $parts[0] ?? '';
$bio = isset($parts[1]) ? trim($parts[1]) : '';
$body = apply_filters('the_content', (string) get_post_field('post_content', $id));
$wugweb_article_busy = false;
$slugify = function ($t) { return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($t)), '-'); };
$toc = array();
if (preg_match_all('/<h([23])([^>]*)>(.*?)<\/h\1>/is', $body, $mm, PREG_SET_ORDER)) {
  foreach ($mm as $h) {
    $text = trim(wp_strip_all_tags($h[3]));
    if ($text === '') { continue; }
    $anchor = preg_match('/\sid=["\']([^"\']+)["\']/', $h[2], $im) ? $im[1] : $slugify($text);
    $toc[] = array('level' => (int) $h[1], 'text' => $text, 'id' => $anchor);
  }
}

// Anchors for h2/h3, summary box, FAQ accordions, scroll-safe tables.
$body = preg_replace_callback('/<h([23])([^>]*)>(.*?)<\/h\1>/is', function ($h) use ($slugify) {
  if (preg_match('/\sid=["\']/', $h[2])) { return $h[0]; }
  $t = trim(wp_strip_all_tags($h[3]));
  return $t === '' ? $h[0] : '<h' . $h[1] . $h[2] . ' id="' . esc_attr($slugify($t)) . '">' . $h[3] . '</h' . $h[1] . '>';
}, $body);
$chunks = preg_split('/(?=<h2[\s>])/i', $body, -1, PREG_SPLIT_NO_EMPTY);
$first_h2 = true;
foreach ($chunks as $i => $c) {
  if (!preg_match('/^<h2[^>]*>(.*?)<\/h2>/is', $c, $hm)) { continue; }
  $ht = strtolower(wp_strip_all_tags($hm[1]));
  if ($first_h2 && preg_match('/short version|tl;?dr|key takeaways|in short|summary/', $ht)) {
    $chunks[$i] = '<div class="article-summary">' . $c . '</div>';
  } elseif (preg_match('/\bfaqs?\b|frequently asked/', $ht)) {
    $parts = preg_split('/(?=<h3[\s>])/i', $c, -1, PREG_SPLIT_NO_EMPTY);
    $out = array_shift($parts);
    $items = '';
    foreach ($parts as $p) {
      if (preg_match('/^<h3[^>]*>(.*?)<\/h3>(.*)$/is', $p, $qm)) {
        $items .= '<details class="faq-item"><summary>' . wp_strip_all_tags($qm[1]) . '</summary><div class="faq-answer">' . $qm[2] . '</div></details>';
      } else { $items .= $p; }
    }
    $chunks[$i] = '<div class="article-faq">' . $out . $items . '</div>';
  }
  $first_h2 = false;
}
$body = implode('', $chunks);
$body = preg_replace('/(<table\b.*?<\/table>)/is', '<div class="table-scroll" tabindex="0" role="region" aria-label="Table, scrolls horizontally">$1</div>', $body);
$site_logo = (int) get_option('site_logo') ?: (int) get_theme_mod('custom_logo');
$logo_url = $site_logo ? wp_get_attachment_image_url($site_logo, 'medium') : '';
$permalink = get_permalink($id);
$title_enc = rawurlencode(wp_strip_all_tags(get_the_title($id)));
$icons = array(
  'X' => '<path d="M17.5 3h3.1l-6.8 7.7L21.8 21h-6.3l-4.9-6.4L5 21H1.9l7.2-8.3L1.5 3h6.4l4.4 5.9L17.5 3Zm-1.1 16.2h1.7L6.9 4.7H5.1l11.3 14.5Z"/>',
  'LinkedIn' => '<path d="M4.98 3.5a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5ZM3 9.5h4V21H3V9.5Zm6.5 0h3.8v1.6h.1c.5-1 1.8-1.9 3.7-1.9 4 0 4.7 2.6 4.7 6V21h-4v-5c0-1.2 0-2.8-1.7-2.8s-2 1.3-2 2.7V21h-4V9.5Z"/>',
  'Facebook' => '<path d="M13.5 21v-8h2.7l.4-3.2h-3.1V7.8c0-.9.3-1.5 1.6-1.5h1.7V3.4c-.3 0-1.3-.1-2.4-.1-2.4 0-4 1.5-4 4.1v2.4H7.7V13h2.7v8h3.1Z"/>',
);
$share = array(
  array('Share on X', 'https://twitter.com/intent/tweet?url=' . rawurlencode($permalink) . '&text=' . $title_enc, 'X'),
  array('Share on LinkedIn', 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode($permalink), 'LinkedIn'),
  array('Share on Facebook', 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($permalink), 'Facebook'),
);
$tags = $f['tags'];
$cat_pills = array();
foreach ((array) get_the_category($id) as $c) { if (strtolower($c->name) !== 'uncategorized') { $cat_pills[] = array($c->name, get_category_link($c)); } }
if (!$cat_pills && $f['category']) { $cat_pills[] = array($f['category'], ''); }
$tag_terms = get_the_tags($id);
if (is_array($tag_terms)) { foreach ($tag_terms as $tt) { if (count($cat_pills) < 3) { $cat_pills[] = array($tt->name, get_tag_link($tt)); } } }
elseif ($tags) { foreach ((array) $tags as $tg) { if (count($cat_pills) < 3 && $tg !== '') { $cat_pills[] = array($tg, ''); } } }
$cat_pills = array_slice($cat_pills, 0, 3);
$cover = $f['image'];
$tags = $f['tags'];
$excerpt = get_the_excerpt($id);
$prev = get_adjacent_post(false, '', true);
$next = get_adjacent_post(false, '', false);
$wrap = get_block_wrapper_attributes(array('class' => 'wugweb-article-post'));
?>
<div <?php echo $wrap; ?>>
<div id="reading-progress" class="reading-progress-bar" style="--bar-accent: <?php echo esc_attr($accent); ?>" aria-hidden="true"></div>
<article class="post-article">
<section class="wrap post-layout" id="article" style="--accent: <?php echo esc_attr($accent); ?>" aria-labelledby="article-hero-title">
  <header class="post-hero" id="article-hero">
    <nav class="article-crumbs" aria-label="Breadcrumb"><a href="<?php echo esc_url(home_url('/blog/')); ?>"><span aria-hidden="true">&lsaquo;</span> Blog</a><?php if ($f['category']) : ?><span class="crumb-sep" aria-hidden="true">/</span><span class="crumb-current"><?php echo esc_html($f['category']); ?></span><?php endif; ?></nav>
    <h1 id="article-hero-title" data-reveal><?php echo esc_html(get_the_title($id)); ?></h1>
    <?php if ($excerpt) : ?><p class="article-intro" data-reveal><?php echo esc_html(wp_strip_all_tags($excerpt)); ?></p><?php endif; ?>
    <div class="article-byline" data-reveal>
      <?php if ($author) : ?><<?php echo $uid ? 'a href="' . esc_url(get_author_posts_url($uid)) . '"' : 'div'; ?> class="byline-card byline-author"><?php if ($photo) : ?><img src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr($author); ?>" class="meta-avatar" width="94" height="94"><?php else : ?><span class="wugweb-ph meta-avatar" aria-hidden="true"></span><?php endif; ?><div class="byline-text"><p class="byline-main"><?php echo esc_html($author); ?></p><?php if ($role !== '') : ?><p class="byline-sub"><?php echo esc_html($role); ?></p><?php endif; ?></div></<?php echo $uid ? 'a' : 'div'; ?>><?php endif; ?>
      <div class="byline-card byline-date"><svg class="byline-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"/></svg><time datetime="<?php echo esc_attr(get_the_date('c', $id)); ?>"><?php echo esc_html(wugweb_post_date_label($id)); ?></time></div>
      <div class="byline-card byline-read"><svg class="byline-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg><?php echo esc_html((int) $f['minutes'] . ' min read'); ?></div>
      <?php foreach ($cat_pills as $cp) : ?><?php if ($cp[1]) : ?><a class="byline-card byline-tag" href="<?php echo esc_url($cp[1]); ?>"><svg class="byline-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20.6 13.4 13.4 20.6a2 2 0 0 1-2.8 0L3 13V3h10l7.6 7.6a2 2 0 0 1 0 2.8Z"/><circle cx="7.5" cy="7.5" r="1" fill="currentColor"/></svg><?php echo esc_html($cp[0]); ?></a><?php else : ?><span class="byline-card byline-tag"><svg class="byline-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20.6 13.4 13.4 20.6a2 2 0 0 1-2.8 0L3 13V3h10l7.6 7.6a2 2 0 0 1 0 2.8Z"/><circle cx="7.5" cy="7.5" r="1" fill="currentColor"/></svg><?php echo esc_html($cp[0]); ?></span><?php endif; ?><?php endforeach; ?>
    </div>
  </header>
  <?php if ($cover) : ?>
  <figure class="post-cover" id="article-cover" data-reveal><img src="<?php echo esc_url($cover); ?>" alt="<?php echo esc_attr(get_the_title($id)); ?>" loading="eager" fetchpriority="high" decoding="async"></figure>
  <?php endif; ?>
  <div class="post-grid">
    <aside class="post-rail" aria-label="Article tools">
      <?php if ($toc) : ?>
      <nav class="article-toc" aria-label="Table of contents"><details class="toc-details" open><summary class="toc-title">In this article</summary><ol>
        <?php foreach ($toc as $t) : if ($t['level'] !== 2) { continue; } ?><li class="toc-item"><a href="#<?php echo esc_attr($t['id']); ?>"><?php echo esc_html($t['text']); ?></a></li><?php endforeach; ?>
      </ol></details></nav>
      <?php endif; ?>
      <div class="rail-share" role="group" aria-label="Share this article"><span class="share-label">Share</span><div class="share-links">
        <?php foreach ($share as $l) : ?><a href="<?php echo esc_url($l[1]); ?>" target="_blank" rel="noopener noreferrer" class="share-link" aria-label="<?php echo esc_attr($l[0]); ?>"><svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true" focusable="false"><?php echo $icons[$l[2]]; // phpcs:ignore WordPress.Security.EscapeOutput ?></svg></a><?php endforeach; ?>
        <button type="button" class="share-link" data-copy-link="<?php echo esc_url($permalink); ?>" aria-label="Copy link"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M10 13a5 5 0 0 0 7.1 0l3-3a5 5 0 0 0-7.1-7.1l-1.7 1.7"/><path d="M14 11a5 5 0 0 0-7.1 0l-3 3A5 5 0 0 0 11 21.1l1.7-1.7"/></svg></button>
      </div></div>
      <aside class="rail-promo" aria-labelledby="rail-promo-title">
        <?php if ($logo_url) : ?><img class="promo-logo" src="<?php echo esc_url($logo_url); ?>" alt="Wugweb" width="96" height="auto" loading="lazy"><?php endif; ?>
        <p class="promo-title" id="rail-promo-title">Have a project in mind?</p>
        <p class="promo-text">Tell us what you are building and what it needs to achieve. Our team will help you map the next step.</p>
        <a class="btn btn-solid promo-cta" href="<?php echo esc_url(home_url('/contact/')); ?>">Start a project</a>
      </aside>
    </aside>
    <div class="post-body">
      <?php if (trim(wp_strip_all_tags($body)) !== '') : ?>
      <div class="article-body"><?php echo $body; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
      <?php else : ?>
      <div class="article-empty"><p>Full article coming soon. Browse more <a href="<?php echo esc_url(home_url('/blog/')); ?>">insights</a>.</p></div>
      <?php endif; ?>
    <?php if ($bio !== '') : ?>
    <aside class="article-author" aria-label="About the author">
      <?php if ($photo) : ?><img src="<?php echo esc_url($photo); ?>" alt="<?php echo esc_attr($author); ?>" class="author-photo"><?php else : ?><span class="wugweb-ph author-photo" aria-hidden="true"></span><?php endif; ?>
      <div class="author-info"><p class="author-name"><?php echo esc_html($author); ?></p><?php if ($role !== '') : ?><p class="author-role"><?php echo esc_html($role); ?></p><?php endif; ?><p class="author-bio"><?php echo esc_html($bio); ?></p></div>
    </aside>
    <?php endif; ?>
    <?php echo do_blocks('<!-- wp:wugweb/article-share-newsletter /-->'); ?>
    <?php if ($prev || $next) : ?>
    <nav class="article-prevnext" aria-label="Previous and next articles">
      <?php if ($prev) : ?><a href="<?php echo esc_url(get_permalink($prev)); ?>" class="pn-link pn-prev"><span class="pn-dir">Previous</span><span class="pn-title"><?php echo esc_html(get_the_title($prev)); ?></span></a><?php else : ?><span class="pn-link pn-placeholder"></span><?php endif; ?>
      <?php if ($next) : ?><a href="<?php echo esc_url(get_permalink($next)); ?>" class="pn-link pn-next"><span class="pn-dir">Next</span><span class="pn-title"><?php echo esc_html(get_the_title($next)); ?></span></a><?php else : ?><span class="pn-link pn-placeholder"></span><?php endif; ?>
    </nav>
    <?php endif; ?>
  </div>
  </div>
</section>
</article>
</div>
