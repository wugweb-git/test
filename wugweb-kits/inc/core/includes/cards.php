<?php
/** Shared renderer for the Wugweb card blocks. */
defined('ABSPATH') || exit;

function wugweb_card_dynamic($type, array $a) {
    $id = get_the_ID();
    if (!$id) return $a;
    $pt = get_post_type($id);
    if ($type === 'blog' && $pt === 'post') {
        $cat = get_the_category($id);
        $a['eyebrow'] = $cat ? $cat[0]->name : '';
        $a['title'] = get_the_title($id);
        $a['text'] = wp_trim_words(get_the_excerpt($id), 24);
        $a['meta'] = max(1, (int) ceil(str_word_count(wp_strip_all_tags((string) get_post_field('post_content', $id))) / 200)) . ' min read';
        $a['url'] = get_permalink($id);
        $a['imageUrl'] = (string) get_the_post_thumbnail_url($id, 'large');
    } elseif ($type === 'portfolio' && $pt === 'case_study') {
        $client = (string) get_post_meta($id, 'wugweb_cs_client', true);
        $disc = (string) get_post_meta($id, 'wugweb_cs_additional_disciplines', true);
        if ($disc === '') $disc = (string) get_post_meta($id, 'wugweb_cs_primary_discipline', true);
        $a['eyebrow'] = trim($client . ($disc !== '' ? ' — ' . $disc : ''));
        $a['title'] = get_the_title($id);
        $a['text'] = '';
        $a['meta'] = '';
        $a['url'] = get_permalink($id);
        $cover = (string) get_post_meta($id, 'wugweb_cs_cover_image', true);
        $a['imageUrl'] = get_the_post_thumbnail_url($id, 'large') ?: $cover;
    } elseif ($type === 'client' && $pt === 'client') {
        $a['title'] = get_the_title($id);
        $a['url'] = get_permalink($id);
        $a['imageUrl'] = (string) get_the_post_thumbnail_url($id, 'medium');
    }
    return $a;
}

function wugweb_render_card($type, array $a, $wrapper) {
    $a = wugweb_card_dynamic($type, $a);
    $url = !empty($a['url']) ? $a['url'] : '';
    echo '<article ' . $wrapper . '>';
    if ($type === 'stat') echo '<p class="wugweb-card__value">' . esc_html($a['eyebrow']) . '</p>';
    if ($type === 'testimonial') echo '<blockquote class="wugweb-card__quote">' . esc_html($a['text']) . '</blockquote>';
    if (empty($a['imageUrl']) && in_array($type, array('blog', 'portfolio'), true) && get_the_ID()) {
        echo '<div class="wugweb-card__media wugweb-card__media--empty" aria-hidden="true"></div>';
    } elseif (!empty($a['imageUrl']) && !in_array($type, array('stat', 'testimonial'), true)) {
        echo '<div class="wugweb-card__media"><img src="' . esc_url($a['imageUrl']) . '" alt="' . esc_attr($a['imageAlt']) . '" loading="lazy" decoding="async"></div>';
    }
    echo '<div class="wugweb-card__body">';
    if ($type !== 'stat' && !empty($a['eyebrow'])) echo '<p class="wugweb-card__eyebrow">' . esc_html($a['eyebrow']) . '</p>';
    $t = esc_html($a['title']);
    if ($url) $t = '<a href="' . esc_url($url) . '">' . $t . '</a>';
    echo '<h3 class="wugweb-card__title">' . $t . '</h3>';
    if ($type !== 'testimonial' && !empty($a['text'])) echo '<p class="wugweb-card__text">' . esc_html($a['text']) . '</p>';
    if (!empty($a['meta'])) echo '<p class="wugweb-card__meta">' . esc_html($a['meta']) . '</p>';
    echo '</div>';
    if ($url) echo '<span class="wugweb-card__arrow" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="19" x2="19" y2="5"/><polyline points="9 5 19 5 19 15"/></svg></span>';
    echo '</article>';
}

/** PostCard markup (components/PostCard.astro). $c: slug-less url, title, excerpt, topic, author, minutes, cover, alt, accent, initials. */
function wugweb_post_card_data($id) {
    $f = wugweb_fields_post($id);
    $pillars = array('Strategy', 'Brand', 'Experience', 'Engineering', 'Growth', 'Operations');
    $pillar = in_array($f['category'], $pillars, true) ? $f['category'] : 'Strategy';
    $w = preg_split('/\s+/', trim(preg_replace('/[^\w\s]/', '', $f['title'])), -1, PREG_SPLIT_NO_EMPTY);
    $ini = $w ? strtoupper(substr($w[0], 0, 1) . (isset($w[1]) ? substr($w[1], 0, 1) : '')) : 'Ww';
    return array('url' => $f['url'], 'title' => $f['title'], 'excerpt' => $f['excerpt'], 'topic' => $f['category'] ?: 'Strategy', 'author' => $f['author'], 'minutes' => $f['minutes'],
        'cover' => $f['image'], 'alt' => $f['title'], 'accent' => 'var(--svc-' . strtolower($pillar) . ')', 'initials' => $ini);
}
function wugweb_post_card_html(array $c, $row = false, $wrapper = '') {
    $cls = 'post-card' . ($row ? ' post-card--row' : '');
    ob_start(); ?>
<a href="<?php echo esc_url($c['url']); ?>" class="<?php echo esc_attr($cls); ?>" style="--card-accent: <?php echo esc_attr($c['accent']); ?>"><div class="pc-thumb-wrap"><?php if (!empty($c['cover'])): ?><img src="<?php echo esc_url($c['cover']); ?>" alt="<?php echo esc_attr($c['alt']); ?>" loading="lazy" decoding="async" class="pc-thumb"><?php else: ?><div class="pc-thumb wugweb-ph" aria-hidden="true"></div><?php endif; ?><span class="pc-dot" aria-hidden="true"></span><span class="pc-arrow" aria-hidden="true"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="19" x2="19" y2="5"></line><polyline points="9 5 19 5 19 15"></polyline></svg></span></div><div class="pc-content"><div class="pc-cat-row"><span class="pc-category"><?php echo esc_html($c['topic']); ?></span><?php if ($row): ?><span class="u-chip pc-launch" aria-hidden="true"><svg viewBox="0 0 16 16" focusable="false"><path d="M8 1.5v13M1.5 8h13" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" /></svg></span><?php endif; ?></div><h3><?php echo esc_html($c['title']); ?></h3><?php if (!empty($c['excerpt'])): ?><p class="pc-excerpt"><?php echo esc_html($c['excerpt']); ?></p><?php endif; ?><div class="pc-meta"><?php if (!empty($c['author'])): ?><span><?php echo esc_html($c['author']); ?></span><?php endif; ?><?php if (!empty($c['minutes'])): ?><span><?php echo (int) $c['minutes']; ?> min read</span><?php endif; ?></div></div></a>
<?php return ob_get_clean();
}
