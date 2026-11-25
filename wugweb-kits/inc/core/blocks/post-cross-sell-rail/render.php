<?php
/** Post Cross-Sell Rail: pages/blog/[slug].astro related sections ("More insights" PostCard row cards + "Featured case studies" related-card). */
$cur = (int) get_the_ID();
$posts = array();
if ($cur && function_exists('wugweb_post_card_data')) {
    $cats = wp_get_post_categories($cur);
    $ids = array();
    if ($cats) { $ids = get_posts(array('post_type' => 'post', 'post_status' => 'publish', 'numberposts' => 3, 'fields' => 'ids', 'post__not_in' => array($cur), 'category__in' => $cats)); }
    if (count($ids) < 3) {
        $ids = array_merge($ids, get_posts(array('post_type' => 'post', 'post_status' => 'publish', 'numberposts' => 3 - count($ids), 'fields' => 'ids', 'post__not_in' => array_merge(array($cur), $ids))));
    }
    $posts = $ids;
}
$cases = get_posts(array('post_type' => 'case_study', 'post_status' => 'publish', 'numberposts' => 2, 'post__not_in' => array($cur)));
if (!$posts && !$cases) { return; }
$work_url = get_post_type_archive_link('case_study') ?: home_url('/work/');
$blog_id = (int) get_option('page_for_posts');
$blog_url = $blog_id ? get_permalink($blog_id) : home_url('/blog/');
?><div <?php echo get_block_wrapper_attributes(array('class' => 'post-cross-sell-rail')); ?>><?php if ($posts): ?><section class="wrap article-related" id="related-posts" aria-labelledby="related-posts-title"><div class="related-head"><h2 id="related-posts-title">More insights</h2><a href="<?php echo esc_url($blog_url); ?>" class="btn btn-ghost">All insights</a></div><ul class="related-grid is-posts" role="list"><?php foreach ($posts as $pid): ?><li><?php echo wugweb_post_card_html(wugweb_post_card_data($pid), true); ?></li><?php endforeach; ?></ul></section><?php endif; ?><?php if ($cases): ?><section class="wrap article-related" id="related-cases" aria-labelledby="related-cases-title"><div class="related-head"><h2 id="related-cases-title">Featured case studies</h2><a href="<?php echo esc_url($work_url); ?>" class="btn btn-ghost">View all work</a></div><ul class="related-grid"><?php foreach ($cases as $cs):
    $cid = $cs->ID;
    $cover = get_the_post_thumbnail_url($cid, 'large') ?: (string) get_post_meta($cid, 'wugweb_cs_cover_image', true);
    $client = (string) get_post_meta($cid, 'wugweb_cs_client', true);
    $disc = array_filter(array_unique(array_map('trim', array_merge(explode(',', (string) get_post_meta($cid, 'wugweb_cs_primary_discipline', true)), explode(',', (string) get_post_meta($cid, 'wugweb_cs_additional_disciplines', true))))));
    $is_video = (bool) preg_match('/\.(mp4|webm)(\?|$)/i', $cover);
?><li><a href="<?php echo esc_url(get_permalink($cid)); ?>" class="related-card"><?php if ($cover): ?><div class="related-visual"><?php if ($is_video): ?><video aria-hidden="true" data-src="<?php echo esc_url($cover); ?>" muted loop playsinline preload="none"></video><?php else: ?><img src="<?php echo esc_url($cover); ?>" alt="" loading="lazy"><?php endif; ?></div><?php endif; ?><div class="related-content"><?php if ($client !== ''): ?><span class="related-kicker"><?php echo esc_html($client); ?></span><?php endif; ?><h3><?php echo esc_html(get_the_title($cid)); ?></h3><?php if ($disc): ?><span class="related-sub"><?php echo esc_html(implode(', ', $disc)); ?></span><?php endif; ?></div></a></li><?php endforeach; ?></ul></section><?php endif; ?></div>
