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
    $extra = array();
    return preg_replace_callback('#<a href="([^"]+)" class="nav-link">#', function ($m) use ($path, $extra) {
        $href = str_replace('/our-work/', '/work/', trailingslashit((string) wp_parse_url($m[1], PHP_URL_PATH)));
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
    $html = preg_replace_callback('#<a\b[^>]*\shref="([^"]*)"[^>]*>#i', function ($m) use ($copy) {
        $h = html_entity_decode($m[1], ENT_QUOTES);
        if (!isset($copy['aria:' . $h]) || $copy['aria:' . $h] === '') return $m[0];
        $tag = preg_replace('#\saria-label="[^"]*"#i', '', $m[0]);
        return preg_replace('#^<a\b#i', '<a aria-label="' . esc_attr((string) $copy['aria:' . $h]) . '"', $tag, 1);
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
    if (strpos($html, 'site-logo-img') === false) return $html;
    $id = (int) get_option('site_logo') ?: (int) get_theme_mod('custom_logo');
    if (!$id) return $html;
    $src = wp_get_attachment_image_url($id, 'full');
    if (!$src) return $html;
    return preg_replace_callback('#<img\b[^>]*site-logo-img[^>]*>#', function ($m) use ($src) {
        return preg_replace('#\ssrc="[^"]*"#', ' src="' . esc_url($src) . '"', $m[0], 1);
    }, $html);
}, 25, 2);

/** Layout shell (docs/layout-shell.md): one skip link targeting #main; main landmark id/tabindex/aria-label; scroll flow + per-section index. */
add_action('init', function () {
    remove_action('wp_footer', 'the_block_template_skip_link');
    remove_action('wp_enqueue_scripts', 'wp_enqueue_block_template_skip_link');
}, 20);
/** Owner decision: no skip link at all (theme link stripped here; WP core's removed above). */
add_filter('render_block', function ($html) {
    if (strpos($html, 'class="skip-link"') === false) return $html;
    return preg_replace('#<a\b[^>]*class="skip-link"[^>]*>.*?</a>#s', '', $html);
}, 5);
add_filter('render_block_core/group', function ($html, $block) {
    if (($block['attrs']['tagName'] ?? '') !== 'main' || !preg_match('#^\s*<main\b[^>]*>#', $html, $om)) return $html;
    $title = is_singular() ? get_the_title() : (is_search() ? __('Search results', 'wugweb-kits') : (is_404() ? __('Page not found', 'wugweb-kits') : wp_strip_all_tags(get_the_archive_title())));
    if ($title === '') $title = get_bloginfo('name');
    $parts = preg_split('#(</?section\b[^>]*>)#i', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
    $depth = 0; $n = 0; $ids = array();
    foreach ($parts as $i => $p) {
        if (!preg_match('#^<(/?)section\b#i', $p, $sm)) continue;
        if ($sm[1] === '/') { $depth = max(0, $depth - 1); continue; }
        if ($depth === 0) {
            $n++;
            if (preg_match('#\sid="([^"]+)"#', $p, $im)) $ids[] = $im[1];
            else { $ids[] = 'section-' . $n; $p = preg_replace('#^<section\b#i', '<section id="section-' . $n . '"', $p); }
            $parts[$i] = preg_replace('#^<section\b#i', '<section data-scroll-index="' . $n . '"', $p);
        }
        $depth++;
    }
    $html = implode('', $parts);
    $open = ltrim($om[0]);
    $new = preg_replace('#\s(id|tabindex|aria-label|data-scroll-flow)="[^"]*"#', '', $open);
    $new = preg_replace('#^<main\b#', '<main id="main" tabindex="-1" aria-label="' . esc_attr($title) . '"' . ($ids ? ' data-scroll-flow="' . esc_attr(implode(' ', $ids)) . '"' : ''), $new);
    return preg_replace('#^\s*<main\b[^>]*>#', addcslashes($new, '\\$'), $html, 1);
}, 10, 2);

/** Cookie consent: render the banner + modal in the footer; hold analytics scripts (Site Kit / gtag) until consent (view.js releases them). */
add_action('wp_footer', function () {
    if (is_admin() || is_feed() || is_embed()) return;
    echo do_blocks('<!-- wp:wugweb/cookie-consent /-->');
}, 5);
add_action('template_redirect', function () {
    if (is_admin() || is_feed() || is_embed() || wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST) || is_customize_preview()) return;
    ob_start(function ($html) {
        if (stripos($html, '</html>') === false) return $html;
        return preg_replace_callback('#<script\b([^>]*)>(.*?)</script>#is', function ($m) {
            $attrs = $m[1]; $body = $m[2];
            $isGa = preg_match('#\bsrc=["\'][^"\']*(googletagmanager\.com|google-analytics\.com)#i', $attrs)
                || preg_match('#\bid=["\']google_gtagjs#i', $attrs)
                || (stripos($body, 'gtag(') !== false && stripos($body, 'dataLayer') !== false && stripos($body, 'wugweb_cookie') === false);
            if (!$isGa) return $m[0];
            $attrs = preg_replace('#\stype=["\'][^"\']*["\']#i', '', $attrs);
            return '<script type="text/plain" data-wugweb-consent="analytics"' . $attrs . '>' . $body . '</script>';
        }, $html);
    });
}, 1);

/** Cache busting: every theme-served CSS/JS (blocks' style/view/edit, assets/) is versioned by the file's mtime, not block.json's static version. */
$wugweb_bust = function ($src) {
    if (!is_string($src) || $src === '') return $src;
    $base = get_theme_file_uri('');
    $base = rtrim($base, '/') . '/';
    $clean = strtok($src, '?');
    if (strpos($clean, $base) !== 0) return $src;
    $file = get_theme_file_path(substr($clean, strlen($base)));
    if (!is_file($file)) return $src;
    return add_query_arg('ver', filemtime($file), $clean);
};
add_filter('style_loader_src', $wugweb_bust, 20);
add_filter('script_loader_src', $wugweb_bust, 20);


/** /our-work/ is retired: /work/ is the one case-study listing. 301 keeps path remainder and query string. */
add_action('template_redirect', function () {
    $path = (string) wp_parse_url(isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '', PHP_URL_PATH);
    if (!preg_match('#^/our-work(/.*)?$#', $path, $m)) return;
    $rest = isset($m[1]) ? $m[1] : '/';
    $q = (string) wp_parse_url(wp_unslash($_SERVER['REQUEST_URI']), PHP_URL_QUERY);
    wp_safe_redirect(home_url('/work' . ($rest === '' ? '/' : $rest)) . ($q !== '' ? '?' . $q : ''), 301);
    exit;
}, 1);
/** Any remaining /our-work/ link in stored content (header/footer parts, patterns, blocks) renders as /work/. */
add_filter('render_block', function ($html) {
    return is_string($html) && strpos($html, '/our-work') !== false ? preg_replace('#(href=["\'](?:https?://[^"\'/]+)?)/our-work(/|["\'?\#])#', '$1/work$2', $html) : $html;
}, 99);
/** Core sitemap: the retired our-work page out, the /work/ listing in. */
add_filter('wp_sitemaps_posts_query_args', function ($args, $type) {
    if ($type !== 'page') return $args;
    $pg = get_page_by_path('our-work');
    if ($pg) { $args['post__not_in'] = array_merge((array) ($args['post__not_in'] ?? array()), array((int) $pg->ID)); }
    return $args;
}, 10, 2);
add_filter('wp_sitemaps_posts_entry', function ($entry, $post) {
    if ($post && $post->post_type === 'page' && $post->post_name === 'our-work') { $entry['loc'] = home_url('/work/'); }
    return $entry;
}, 10, 2);
