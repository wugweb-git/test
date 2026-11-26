<?php
/** Testimonial Card: components/PortableTextRenderer.astro quoteBlock (.pt-quote-block). text = quote, title = author, eyebrow = role, meta = company, imageUrl = avatar. */
$quote = (string) ($attributes['text'] ?? '');
$author = (string) ($attributes['title'] ?? '');
$role = (string) ($attributes['eyebrow'] ?? '');
$company = (string) ($attributes['meta'] ?? '');
$avatar = (string) ($attributes['imageUrl'] ?? '');
$role_line = $role . ($role !== '' && $company !== '' ? ' @ ' : '') . $company;
 // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core ?><figure <?php echo get_block_wrapper_attributes(array('class' => 'pt-quote-block', 'data-reveal' => '')); ?>><blockquote class="pt-quote-text"><p>"<?php echo esc_html($quote); ?>"</p></blockquote><?php if ($author !== '' || $role_line !== '' || true): ?><figcaption class="pt-quote-author"><?php if ($avatar): ?><img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr($attributes['imageAlt'] ?? '' ?: $author); ?>" class="pt-quote-avatar" loading="lazy"><?php else: ?><span class="wugweb-ph pt-quote-avatar" aria-hidden="true"></span><?php endif; ?><div class="pt-quote-meta"><?php if ($author !== ''): ?><strong class="pt-quote-name"><?php echo esc_html($author); ?></strong><?php endif; ?><?php if ($role_line !== ''): ?><span class="pt-quote-role"><?php echo esc_html($role_line); ?></span><?php endif; ?></div></figcaption><?php endif; ?></figure>
