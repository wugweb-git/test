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
<section class="wrap post-layout<?php echo $cover ? ' has-cover' : ''; ?>" id="article" style="--accent: <?php echo esc_attr($accent); ?>" aria-labelledby="article-hero-title">
  <header class="post-hero" id="article-hero">
    <h1 id="article-hero-title" data-reveal><?php echo esc_html(get_the_title($id)); ?></h1>
    <?php if ($excerpt) : ?><p class="article-intro" data-reveal><?php echo esc_html(wp_strip_all_tags($excerpt)); ?></p><?php endif; ?>
    <?php if ($tags) : ?><ul class="article-tags" aria-label="Tags" data-reveal><?php foreach ($tags as $t) : ?><li class="u-tag"><?php echo esc_html($t); ?></li><?php endforeach; ?></ul><?php endif; ?>
    <dl class="article-meta" data-reveal>
      <?php if ($author) : ?><div><dt class="meta-label">Author</dt><dd class="meta-author"><?php if ($photo) : ?><img src="<?php echo esc_url($photo); ?>" alt="" class="meta-avatar"><?php else : ?><span class="wugweb-ph meta-avatar" aria-hidden="true"></span><?php endif; ?><?php echo esc_html($author); ?></dd></div><?php endif; ?>
      <div><dt class="meta-label">Published</dt><dd><time datetime="<?php echo esc_attr(get_the_date('c', $id)); ?>"><?php echo esc_html(get_the_date('j M Y', $id)); ?></time></dd></div>
      <div><dt class="meta-label">Reading time</dt><dd><?php echo esc_html((int) $f['minutes'] . ' min read'); ?></dd></div>
      <?php if ($f['category']) : ?><div><dt class="meta-label">Category</dt><dd><?php echo esc_html($f['category']); ?></dd></div><?php endif; ?>
    </dl>
  </header>
  <?php if ($cover) : ?>
  <figure class="post-cover" id="article-cover" data-reveal><img src="<?php echo esc_url($cover); ?>" alt="<?php echo esc_attr(get_the_title($id)); ?>" loading="eager" fetchpriority="high" decoding="async"></figure>
  <?php endif; ?>
  <div class="post-body">
    <?php if ($toc) : ?>
    <nav class="article-toc" aria-label="Table of contents"><p class="toc-title">Contents</p><ol>
      <?php foreach ($toc as $t) : ?><li class="toc-item<?php echo $t['level'] === 3 ? ' toc-h3' : ''; ?>"><a href="#<?php echo esc_attr($t['id']); ?>"><?php echo esc_html($t['text']); ?></a></li><?php endforeach; ?>
    </ol></nav>
    <?php endif; ?>
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
</section>
</article>
</div>
