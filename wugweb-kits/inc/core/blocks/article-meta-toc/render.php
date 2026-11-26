<?php
/** Article Meta & Contents: pages/blog/[slug].astro .article-meta + .article-toc. */
$id = wugweb_block_post_id($block ?? null, $attributes, array('post'));
static $wugweb_toc_busy = false;
if (!$id || $wugweb_toc_busy) { return; }
$wugweb_toc_busy = true;
$content = (string) get_post_field('post_content', $id);
$author_id = (int) get_post_field('post_author', $id);
$author = get_the_author_meta('display_name', $author_id);
$avatar = $author_id ? get_avatar_url($author_id, array('size' => 48)) : '';
$cats = get_the_category($id);
$category = $cats ? $cats[0]->name : '';
$minutes = max(1, (int) ceil(str_word_count(wp_strip_all_tags($content)) / 200));
$slugify = function ($t) { return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($t)), '-'); };
$toc = array();
if (preg_match_all('/<h([23])([^>]*)>(.*?)<\/h\1>/is', do_blocks(preg_replace('/<!-- wp:wugweb\/article-meta-toc[^>]*-->/', '', $content)), $mm, PREG_SET_ORDER)) {
    foreach ($mm as $h) {
        $text = trim(wp_strip_all_tags($h[3]));
        if ($text === '') { continue; }
        $anchor = preg_match('/\sid=["\']([^"\']+)["\']/', $h[2], $im) ? $im[1] : $slugify($text);
        $toc[] = array('level' => (int) $h[1], 'text' => $text, 'id' => $anchor);
    }
}
$wugweb_toc_busy = false;
 // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core ?><div <?php echo get_block_wrapper_attributes(array('class' => 'article-meta-toc')); ?>><dl class="article-meta" data-reveal><?php if ($author): ?><div><dt class="meta-label">Author</dt><dd class="meta-author"><?php if ($avatar): ?><img src="<?php echo esc_url($avatar); ?>" alt="" class="meta-avatar"><?php else: ?><span class="wugweb-ph meta-avatar" aria-hidden="true"></span><?php endif; ?><?php echo esc_html($author); ?></dd></div><?php endif; ?><div><dt class="meta-label">Date</dt><dd><time datetime="<?php echo esc_attr(get_the_date('c', $id)); ?>"><?php echo esc_html(wugweb_post_date_label($id)); ?></time></dd></div><div><dt class="meta-label">Reading time</dt><dd><?php echo esc_html($minutes . ' min read'); ?></dd></div><?php if ($category): ?><div><dt class="meta-label">Category</dt><dd><?php echo esc_html($category); ?></dd></div><?php endif; ?></dl><?php if ($toc): ?><nav class="article-toc" aria-label="Table of contents"><p class="toc-title">Contents</p><ol><?php foreach ($toc as $t): ?><li class="toc-item<?php echo $t['level'] === 3 ? ' toc-h3' : ''; ?>"><a href="#<?php echo esc_attr($t['id']); ?>"><?php echo esc_html($t['text']); ?></a></li><?php endforeach; ?></ol></nav><?php endif; ?></div>
