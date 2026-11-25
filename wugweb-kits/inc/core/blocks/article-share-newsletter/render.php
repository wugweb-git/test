<?php
/** Article Share & Newsletter: pages/blog/[slug].astro .article-share + .article-newsletter. */
$url = (string) get_permalink();
$title = rawurlencode(wp_strip_all_tags(get_the_title()));
$links = array(
    array('Share on X', 'https://twitter.com/intent/tweet?url=' . rawurlencode($url) . '&text=' . $title, 'X', false),
    array('Share on LinkedIn', 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode($url), 'Li', false),
    array('Share on WhatsApp', 'https://wa.me/?text=' . $title . '%20' . rawurlencode($url), 'Wa', false),
    array('Copy link', $url, '#', true),
);
$form_id = absint($attributes['formId'] ?? 0);
?><div <?php echo get_block_wrapper_attributes(array('class' => 'article-share-newsletter')); ?>><div class="article-share" role="group" aria-label="Share this article"><span class="share-label">Share</span><div class="share-links"><?php foreach ($links as $l): ?><a href="<?php echo esc_url($l[1]); ?>" target="_blank" rel="noopener noreferrer" class="share-link" aria-label="<?php echo esc_attr($l[0]); ?>"<?php if ($l[3]): ?> data-copy="<?php echo esc_url($url); ?>"<?php endif; ?>><?php echo esc_html($l[2]); ?></a><?php endforeach; ?></div></div><aside class="article-newsletter" aria-labelledby="newsletter-title"><h2 id="newsletter-title">Get insights like this in your inbox</h2><p>Design, product, and digital strategy. No spam, just thoughtful content.</p><?php if ($form_id && function_exists('fluentform')): ?><?php echo do_shortcode('[fluentform id="' . $form_id . '"]'); ?><?php else: ?><form class="nl-form" method="POST" action="/api/subscribe"><input type="email" name="email" aria-label="Email address" placeholder="you@company.com" required autocomplete="email" class="nl-input"><button type="submit" class="btn btn-solid">Subscribe</button></form><?php endif; ?></aside></div>
