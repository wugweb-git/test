<?php
/** Global sections (BaseLayout.astro): the sectors showcase and blog teaser sit above the final CTA on most pages.
 *  Blocks in the footer part carry className "is-global"; this decides per page whether each one renders. */
defined('ABSPATH') || exit;
function wugweb_global_sections_for_page() {
    $slug = is_page() ? (string) get_post_field('post_name', get_queried_object_id()) : '';
    $none = array('privacy-policy', 'terms-of-use', 'refund-policy', 'cookie-policy', 'industries'); // no sections, no CTA
    $teaser_only = array('contact-us', 'thank-you', 'offline', 'contact-error', 'page-not-found', 'page-not-found-alt');
    if (is_front_page() || is_home() && false) return array('sectors' => false, 'teaser' => false, 'cta' => true); // home carries its own
    if (in_array($slug, $none, true)) return array('sectors' => false, 'teaser' => false, 'cta' => false);
    if (is_404() || in_array($slug, $teaser_only, true)) return array('sectors' => false, 'teaser' => true, 'cta' => true);
    return array('sectors' => true, 'teaser' => true, 'cta' => true);
}
add_filter('render_block', function ($html, $block) {
    $name = $block['blockName'] ?? '';
    if (!in_array($name, array('wugweb/industries-served', 'wugweb/recent-thoughts', 'wugweb/cta-band'), true)) return $html;
    if (strpos((string) ($block['attrs']['className'] ?? ''), 'is-global') === false) return $html;
    $r = wugweb_global_sections_for_page();
    $key = $name === 'wugweb/industries-served' ? 'sectors' : ($name === 'wugweb/recent-thoughts' ? 'teaser' : 'cta');
    return $r[$key] ? $html : '';
}, 10, 2);

/** Primary nav: mark the current section (SiteHeader.astro isActive: exact or prefix match). */
add_filter('render_block_core/html', function ($html) {
    if (strpos($html, 'id="site-nav"') === false) return $html;
    $path = trailingslashit((string) wp_parse_url(add_query_arg(array()), PHP_URL_PATH));
    $extra = array('/work/' => array('/our-work/'));
    return preg_replace_callback('#<a href="([^"]+)" class="nav-link">#', function ($m) use ($path, $extra) {
        $href = trailingslashit((string) wp_parse_url($m[1], PHP_URL_PATH));
        $on = $path === $href || strpos($path, $href) === 0 && $href !== '/';
        foreach ($extra[$href] ?? array() as $alt) { if (strpos($path, $alt) === 0) $on = true; }
        return $on ? '<a href="' . $m[1] . '" class="nav-link is-active" aria-current="page">' : $m[0];
    }, $html);
});

/** Editable copy: every wugweb/* block gets a `copy` attribute { original: replacement } edited in the "Text & links" panel (assets/js/wugweb-copy-editor.js). */
add_filter('register_block_type_args', function ($args, $name) {
    if (strpos($name, 'wugweb/') !== 0) return $args;
    $args['attributes'] = isset($args['attributes']) ? $args['attributes'] : array();
    if (!isset($args['attributes']['copy'])) $args['attributes']['copy'] = array('type' => 'object', 'default' => new stdClass());
    return $args;
}, 10, 2);
add_filter('render_block', function ($html, $block) {
    $name = $block['blockName'] ?? '';
    $copy = $block['attrs']['copy'] ?? null;
    if (strpos($name, 'wugweb/') !== 0 || empty($copy) || !is_array($copy) || $html === '') return $html;
    $norm = function ($s) { return trim(preg_replace('/\s+/', ' ', html_entity_decode((string) $s, ENT_QUOTES))); };
    $html = preg_replace_callback('#>([^<>]+)<#', function ($m) use ($copy, $norm) {
        $t = $norm($m[1]); if ($t === '' || !array_key_exists($t, $copy)) return $m[0];
        return '>' . esc_html((string) $copy[$t]) . '<';
    }, $html);
    $html = preg_replace_callback('#href="([^"]*)"#', function ($m) use ($copy) {
        $h = html_entity_decode($m[1], ENT_QUOTES); return array_key_exists($h, $copy) ? 'href="' . esc_url((string) $copy[$h]) . '"' : $m[0];
    }, $html);
    $html = preg_replace_callback('#<img\b[^>]*>#i', function ($m) use ($copy) {
        $tag = $m[0];
        if (!preg_match('#\ssrc="([^"]*)"#i', $tag, $sm)) return $tag;
        $src = html_entity_decode($sm[1], ENT_QUOTES);
        if (isset($copy['src:' . $src]) && $copy['src:' . $src] !== '') {
            $tag = preg_replace('#\ssrc="[^"]*"#i', ' src="' . esc_url((string) $copy['src:' . $src]) . '"', $tag, 1);
            $tag = preg_replace('#\s(srcset|sizes)="[^"]*"#i', '', $tag);
        }
        if (array_key_exists('alt:' . $src, $copy)) {
            $alt = ' alt="' . esc_attr((string) $copy['alt:' . $src]) . '"';
            $tag = preg_match('#\salt="[^"]*"#i', $tag) ? preg_replace('#\salt="[^"]*"#i', $alt, $tag, 1) : preg_replace('#<img#i', '<img' . $alt, $tag, 1);
        }
        return $tag;
    }, $html);
    return $html;
}, 20, 2);
add_action('enqueue_block_editor_assets', function () {
    wp_enqueue_script('wugweb-copy-editor', get_theme_file_uri('assets/js/wugweb-copy-editor.js'), array('wp-hooks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-api-fetch', 'wp-data'), wp_get_theme()->get('Version'), true);
});

/** Site Identity logo: header/footer logo images follow the Site Identity logo when one is set (the DB header part keeps its theme-asset fallback). */
add_filter('render_block', function ($html, $block) {
    if (($block['blockName'] ?? '') !== 'core/html' || strpos($html, 'site-logo-img') === false) return $html;
    $id = (int) get_theme_mod('custom_logo');
    if (!$id) return $html;
    $src = wp_get_attachment_image_url($id, 'full');
    if (!$src) return $html;
    return preg_replace_callback('#<img\b[^>]*site-logo-img[^>]*>#', function ($m) use ($src) {
        return preg_replace('#\ssrc="[^"]*"#', ' src="' . esc_url($src) . '"', $m[0], 1);
    }, $html);
}, 25, 2);
