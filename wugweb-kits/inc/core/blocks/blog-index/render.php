<?php
/** Blog Index — featured card + topic chips + grid, port of blog/index.astro. Featured-first then newest; first post is the large card. */
defined('ABSPATH') || exit;
$ids = get_posts(array('post_type' => 'post', 'post_status' => 'publish', 'numberposts' => max(1, min(300, (int) ($attributes['limit'] ?? 200))), 'orderby' => 'date', 'order' => 'DESC', 'fields' => 'ids', 'ignore_sticky_posts' => true));
if (!$ids) { return; }
$feat = array(); $rest = array();
foreach ($ids as $id) { if (in_array(strtolower((string) get_post_meta($id, 'wugweb_featured', true)), array('1', 'true', 'yes'), true)) { $feat[] = $id; } else { $rest[] = $id; } }
$ids = array_merge($feat, $rest);
$cards = array(); $topics = array();
foreach ($ids as $id) {
  $c = wugweb_post_card_data($id);
  $f = wugweb_fields_post($id);
  $c['slug_topic'] = $c['topic'] !== '' ? sanitize_title($c['topic']) : '';
  $c['date'] = get_the_date('j M Y', $id);
  $c['author'] = $f['author'];
  $c['tags'] = array_slice(array_values(array_filter((array) $f['tags'])), 0, 3);
  $cards[] = $c;
  if ($c['slug_topic'] !== '') { if (!isset($topics[$c['slug_topic']])) { $topics[$c['slug_topic']] = array('name' => $c['topic'], 'count' => 0); } $topics[$c['slug_topic']]['count']++; }
}
uasort($topics, function ($a, $b) { return $b['count'] <=> $a['count'] ?: strcasecmp($a['name'], $b['name']); });
$hero = array_shift($cards);
$arrow = '';
?><div <?php echo get_block_wrapper_attributes(array('class' => 'wugweb-blog-index')); ?>>
<?php if ($hero) : ?>
<section class="wrap blog-featured" data-reveal id="blog-featured" aria-labelledby="blog-featured-title" data-topic="<?php echo esc_attr($hero['slug_topic']); ?>">
  <a href="<?php echo esc_url($hero['url']); ?>" class="featured-card" style="--card-accent: <?php echo esc_attr($hero['accent']); ?>">
    <div class="featured-visual"><?php if (!empty($hero['cover'])) : ?><img src="<?php echo esc_url($hero['cover']); ?>" alt="<?php echo esc_attr($hero['alt']); ?>" loading="eager" decoding="async"><?php else : ?><div class="wugweb-ph" style="height:100%;aspect-ratio:auto;border-radius:0" aria-hidden="true"></div><?php endif; ?></div>
    <div class="featured-content">
      <span class="pillar-tag"><i></i><?php echo esc_html($hero['topic']); ?></span>
      <h2 id="blog-featured-title"><?php echo esc_html($hero['title']); ?></h2>
      <p class="featured-excerpt"><?php echo esc_html($hero['excerpt']); ?></p>
      <div class="post-meta"><?php if ($hero['author']) : ?><span><?php echo esc_html($hero['author']); ?></span><?php endif; ?><span><?php echo esc_html($hero['date']); ?></span><?php if (!empty($hero['minutes'])) : ?><span><?php echo (int) $hero['minutes']; ?> min read</span><?php endif; ?></div>
    </div>
  </a>
</section>
<?php endif; ?>
<section class="wrap blog-grid-section" id="blog-grid-section" aria-label="All articles">
  <div class="blog-filters" role="toolbar" aria-label="Filter articles by topic">
    <button class="filter-chip" data-topic="">All</button>
    <?php foreach ($topics as $slug => $t) : ?><button class="filter-chip" data-topic="<?php echo esc_attr($slug); ?>"><?php echo esc_html($t['name']); ?></button><?php endforeach; ?>
  </div>
  <ul class="blog-grid">
    <?php foreach ($cards as $i => $c) : ?><li data-reveal data-reveal-delay="<?php echo esc_attr(number_format(min($i % 6, 5) * 0.05, 2)); ?>" data-topic="<?php echo esc_attr($c['slug_topic']); ?>"><?php echo wugweb_post_card_html($c, false); ?></li><?php endforeach; ?>
  </ul>
  <div class="blog-more" hidden><button type="button" class="btn btn-ghost blog-more-btn">Load more articles</button><p class="blog-count" role="status" aria-live="polite"></p></div>
</section>
</div>
