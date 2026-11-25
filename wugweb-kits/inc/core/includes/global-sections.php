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
