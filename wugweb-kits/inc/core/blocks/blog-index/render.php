<?php
/** Blog Index — toolbar (chips + search), the GenerateBlocks mosaic of card variants, then the main grid with Load more. */
defined('ABSPATH') || exit;
require_once dirname(__DIR__) . '/blog-cards/data.php';
$D = wugweb_blog_showcase(); if (!$D) { return; }
extract($D);
 // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core ?><div <?php echo get_block_wrapper_attributes(array('class' => 'wugweb-blog-index')); ?>>
<section class="wrap blog-main" id="blog-main" aria-label="Articles">
  <div class="blog-toolbar">
    <div class="blog-filters" role="toolbar" aria-label="Filter articles by topic">
      <button class="filter-chip" data-topic="">All</button>
      <?php foreach ($topics as $slug => $t) : ?><button class="filter-chip" data-topic="<?php echo esc_attr($slug); ?>"><?php echo esc_html($t['name']); ?></button><?php endforeach; ?>
    </div>
    <label class="blog-search"><span class="sr-only">Search articles</span><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg><input type="search" id="blog-search" placeholder="What would you like to read about?" autocomplete="off"></label>
  </div>

  <div class="blog-extra"><?php ob_start(); include get_theme_file_path('patterns/blog-mosaic.php'); echo do_blocks(ob_get_clean()); // phpcs:ignore ?></div>

  <ul class="blog-grid">
    <?php foreach ($cards as $i => $c) : // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- card helper escapes every value it prints ?><li data-reveal data-reveal-delay="<?php echo esc_attr(number_format(min($i % 6, 5) * 0.05, 2)); ?>" data-topic="<?php echo esc_attr($c['slug_topic']); ?>"<?php echo isset($used[$c['pid']]) ? ' data-dup="1"' : ''; ?>><?php echo wugweb_post_card_html($c, false); ?></li><?php endforeach; ?>
    <?php if ($hero) : // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- card helper escapes every value it prints ?><li data-topic="<?php echo esc_attr($hero['slug_topic']); ?>" data-dup="1"><?php echo wugweb_post_card_html($hero, false); ?></li><?php endif; ?>
  </ul>
  <div class="blog-more" hidden><button type="button" class="btn btn-ghost blog-more-btn">Load more articles</button><p class="blog-count" role="status" aria-live="polite"></p></div>
</section>
</div>
