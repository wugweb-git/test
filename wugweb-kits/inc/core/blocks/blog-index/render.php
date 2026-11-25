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
  $c['date'] = wugweb_post_date_label($id);
  $c['author'] = $f['author'];
  $c['is_featured'] = in_array($id, $feat, true);
  $c['pid'] = $id;
  $c['tags'] = array_slice(array_values(array_filter((array) $f['tags'])), 0, 3);
  $cards[] = $c;
  if ($c['slug_topic'] !== '') { if (!isset($topics[$c['slug_topic']])) { $topics[$c['slug_topic']] = array('name' => $c['topic'], 'count' => 0); } $topics[$c['slug_topic']]['count']++; }
}
uasort($topics, function ($a, $b) { return $b['count'] <=> $a['count'] ?: strcasecmp($a['name'], $b['name']); });
$hero = array_shift($cards);
// Allocation (each post appears once in the showcase blocks; the main grid lists everything and hides the repeats until a filter/search is active).
$side = array_slice($cards, 0, 4);                      // "Featured articles" ruled list
$picks_rows = array_slice($cards, 4, 4);                // ruled rows with square thumbs
$picks_big = array_slice($cards, 8, 2);                 // two big cards
$used = array();
foreach (array_merge(array($hero), $side, $picks_rows, $picks_big) as $u) { if ($u) { $used[$u['pid']] = true; } }
$side_label = (!empty($side) && !empty($side[0]['is_featured'])) ? 'Featured articles' : 'Latest articles';
$picks_label = (!empty($picks_rows) && !empty($picks_rows[0]['is_featured'])) ? "Editor's picks" : 'More recent articles';
// Real case studies (newest 3): wide strip + case-study cards.
$cs_ids = get_posts(array('post_type' => 'case_study', 'post_status' => 'publish', 'numberposts' => 3, 'orderby' => 'date', 'order' => 'DESC', 'fields' => 'ids'));
$cs = array(); foreach ($cs_ids as $cid) { $cs[] = array('id' => $cid, 'f' => wugweb_fields_case_study($cid)); }
$strip = $cs ? array_shift($cs) : null;
$cal = '<svg class="date-icon" viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>';
?><div <?php echo get_block_wrapper_attributes(array('class' => 'wugweb-blog-index')); ?>>
<section class="wrap blog-main" id="blog-main" aria-label="Articles">
  <div class="blog-toolbar">
    <div class="blog-filters" role="toolbar" aria-label="Filter articles by topic">
      <button class="filter-chip" data-topic="">All</button>
      <?php foreach ($topics as $slug => $t) : ?><button class="filter-chip" data-topic="<?php echo esc_attr($slug); ?>"><?php echo esc_html($t['name']); ?></button><?php endforeach; ?>
    </div>
    <label class="blog-search"><span class="sr-only">Search articles</span><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg><input type="search" id="blog-search" placeholder="What would you like to read about?" autocomplete="off"></label>
  </div>

  <div class="blog-extra">
<?php if ($hero) : ?>
    <div class="blog-featured featured-grid" id="blog-featured" data-topic="<?php echo esc_attr($hero['slug_topic']); ?>">
      <a href="<?php echo esc_url($hero['url']); ?>" class="featured-card" style="--card-accent: <?php echo esc_attr($hero['accent']); ?>">
        <div class="featured-visual"><?php if (!empty($hero['cover'])) : ?><img src="<?php echo esc_url($hero['cover']); ?>" alt="<?php echo esc_attr($hero['alt']); ?>" loading="eager" decoding="async"><?php else : ?><div class="wugweb-ph" style="height:100%;aspect-ratio:auto;border-radius:0" aria-hidden="true"></div><?php endif; ?></div>
        <div class="featured-content">
          <span class="pillar-tag"><i></i><?php echo esc_html($hero['topic']); ?></span>
          <h2 id="blog-featured-title"><?php echo esc_html($hero['title']); ?></h2>
          <p class="featured-excerpt"><?php echo esc_html($hero['excerpt']); ?></p>
          <div class="post-meta"><?php if ($hero['author']) : ?><span>By <?php echo esc_html($hero['author']); ?></span><?php endif; ?><span class="pc-date"><?php echo $cal; // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html($hero['date']); ?></span><?php if (!empty($hero['minutes'])) : ?><span><?php echo (int) $hero['minutes']; ?> min read</span><?php endif; ?></div>
        </div>
      </a>
      <?php if ($side) : ?>
      <aside class="featured-list" aria-labelledby="featured-list-title">
        <h2 id="featured-list-title"><?php echo esc_html($side_label); ?></h2>
        <ul>
          <?php foreach ($side as $sc) : ?><li><a href="<?php echo esc_url($sc['url']); ?>"><span class="fl-kicker"><?php echo esc_html($sc['topic']); ?></span><span class="fl-title"><?php echo esc_html($sc['title']); ?></span></a></li><?php endforeach; ?>
        </ul>
      </aside>
      <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ($strip) : $sf = $strip['f']; ?>
    <div class="blog-strip">
      <?php echo do_blocks('<!-- wp:wugweb/media-card ' . wp_json_encode(array('className' => 'is-style-wide', 'title' => (string) $sf['title'], 'text' => trim($sf['client'] . ($sf['discipline'] !== '' ? ' · ' . $sf['discipline'] : '')), 'url' => (string) $sf['url'], 'imageUrl' => (string) $sf['image'], 'imageAlt' => (string) $sf['title'], 'cta' => 'See the case study')) . ' /-->'); ?>
    </div>
<?php endif; ?>

<?php if ($picks_rows || $picks_big) : ?>
    <div class="picks-grid">
      <?php if ($picks_rows) : ?>
      <div class="picks-rows">
        <h2><?php echo esc_html($picks_label); ?></h2>
        <ul class="is-style-row">
          <?php foreach ($picks_rows as $pr) : ?><li><?php echo wugweb_post_card_html($pr, true); ?></li><?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>
      <?php if ($picks_big) : ?>
      <div class="picks-big">
        <ul>
          <?php foreach ($picks_big as $pb) : ?><li><?php echo wugweb_post_card_html($pb, false); ?></li><?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ($cs) : ?>
    <div class="blog-work">
      <ul class="blog-work-grid">
        <?php foreach ($cs as $w) : $wf = $w['f']; ?>
        <li><a href="<?php echo esc_url($wf['url']); ?>" class="related-card"><div class="related-visual"><?php if (!empty($wf['image'])) : ?><img src="<?php echo esc_url($wf['image']); ?>" alt="<?php echo esc_attr($wf['title']); ?>" loading="lazy" decoding="async"><?php else : ?><span class="related-visual-empty" aria-hidden="true"></span><?php endif; ?></div><div class="related-content"><span class="related-kicker"><?php echo esc_html($wf['client'] !== '' ? $wf['client'] : 'Case study'); ?></span><h3><?php echo esc_html($wf['title']); ?></h3><?php if ($wf['discipline'] !== '') : ?><span class="related-sub"><?php echo esc_html($wf['discipline']); ?></span><?php endif; ?></div></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
<?php endif; ?>
  </div>

  <ul class="blog-grid">
    <?php foreach ($cards as $i => $c) : ?><li data-reveal data-reveal-delay="<?php echo esc_attr(number_format(min($i % 6, 5) * 0.05, 2)); ?>" data-topic="<?php echo esc_attr($c['slug_topic']); ?>"<?php echo isset($used[$c['pid']]) ? ' data-dup="1"' : ''; ?>><?php echo wugweb_post_card_html($c, false); ?></li><?php endforeach; ?>
    <?php if ($hero) : ?><li data-topic="<?php echo esc_attr($hero['slug_topic']); ?>" data-dup="1"><?php echo wugweb_post_card_html($hero, false); ?></li><?php endif; ?>
  </ul>
  <div class="blog-more" hidden><button type="button" class="btn btn-ghost blog-more-btn">Load more articles</button><p class="blog-count" role="status" aria-live="polite"></p></div>
</section>
</div>
