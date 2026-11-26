<?php

/** Manifest icons: the W mark at 192 and 512 ("any") plus a 512 "maskable" with safe-zone padding, all from the theme's brand files. */
if (!function_exists('wugweb_pwa_icons')) {
function wugweb_pwa_icons() {
    $u = function ($f) { return get_theme_file_uri('assets/brand/' . $f); };
    return array(
        array('src' => $u('pwa-192.png'), 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any'),
        array('src' => $u('pwa-512.png'), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any'),
        array('src' => $u('pwa-maskable-512.png'), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'maskable'),
    );
}
}
/** Dynamic manifest and service-worker endpoints for Wugweb Core. */
defined('ABSPATH') || exit;
function wugweb_pwa_disabled(){
    return defined('WUGWEB_DISABLE_PWA_SW') && WUGWEB_DISABLE_PWA_SW;
}
add_action('init', function(){
    add_rewrite_rule('^manifest\.webmanifest$', 'index.php?wugweb_pwa=manifest', 'top');
    add_rewrite_rule('^sw\.js$', 'index.php?wugweb_pwa=sw', 'top');
    add_rewrite_rule('^offline/?$', 'index.php?wugweb_pwa=offline', 'top');
});
add_filter('query_vars', function($vars){ $vars[]='wugweb_pwa'; return $vars; });
// viewport-fit=cover so env(safe-area-inset-*) is real in an installed (standalone) app: replace core's viewport tag with ours.
add_action('init', function () { remove_action('wp_head', '_block_template_viewport_meta_tag', 0); });
add_action('wp_head', function () {
    if (is_admin()) return;
    echo '<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">' . "\n";
}, 0);
// iOS: the home-screen icon is the same-origin 180x180 theme file, not the 512 Media Library site icon.
add_filter('site_icon_meta_tags', function ($tags) {
    $own = '<link rel="apple-touch-icon" href="' . esc_url(get_theme_file_uri('assets/brand/apple-touch-icon.png')) . '" sizes="180x180">';
    $out = array(); $done = false;
    foreach ((array) $tags as $t) {
        if (strpos($t, 'rel="apple-touch-icon"') !== false) { if (!$done) { $out[] = $own; $done = true; } continue; }
        $out[] = $t;
    }
    if (!$done) { $out[] = $own; }
    return $out;
});
add_action('wp_head', function(){
    if (is_admin() || is_user_logged_in()) return;
    echo '<meta name="apple-mobile-web-app-capable" content="yes">'."\n".'<meta name="mobile-web-app-capable" content="yes">'."\n".'<meta name="apple-mobile-web-app-title" content="Wugweb">'."\n".'<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">'."\n";
    echo '<link rel="manifest" href="'.esc_url(home_url('/manifest.webmanifest')).'">'."\n";
    echo '<meta name="theme-color" content="#191919">'."\n";
}, 5);
add_action('wp_footer', function(){
    if (is_admin() || is_user_logged_in() || is_preview() || wugweb_pwa_disabled()) return;
    $sw_url=esc_url(home_url('/sw.js'));
    echo '<script>if("serviceWorker" in navigator){window.addEventListener("load",function(){navigator.serviceWorker.register("'.esc_js($sw_url).'",{scope:"/"});});}</script>';
}, 99);
/** Answer /manifest.webmanifest and /sw.js directly with 200: no trailing-slash redirect (a redirecting worker script cannot register). */
function wugweb_pwa_serve($route){
    nocache_headers();
    header_remove('Expires'); header_remove('Pragma'); // nocache_headers() adds an Expires in 1984 that contradicts the Cache-Control below
    $ver = wp_get_theme()->get('Version');
    if($route==='manifest'){
        header_remove('Cache-Control'); header('Cache-Control: public, max-age=3600');
        status_header(200); header('Content-Type: application/manifest+json; charset=utf-8');
        $icon192 = get_theme_file_uri('assets/brand/pwa-192.png');
        $m = array(
            'id' => '/', 'name' => 'Wugweb', 'short_name' => 'Wugweb', 'description' => 'Digital experience systems.',
            'lang' => 'en', 'dir' => 'ltr', 'start_url' => '/', 'scope' => '/',
            'display' => 'standalone', 'display_override' => array('standalone', 'minimal-ui'), 'orientation' => 'any',
            'categories' => array('business', 'design'),
            // The icons are drawn on pure black, so the splash background matches them (a #191919 splash showed a black box around the icon).
            'background_color' => '#000000', 'theme_color' => '#191919',
            'icons' => wugweb_pwa_icons(),
            'shortcuts' => array(
                array('name' => 'Work', 'url' => '/work/', 'icons' => array(array('src' => $icon192, 'sizes' => '192x192', 'type' => 'image/png'))),
                array('name' => 'Services', 'url' => '/services/', 'icons' => array(array('src' => $icon192, 'sizes' => '192x192', 'type' => 'image/png'))),
                array('name' => 'Contact', 'url' => '/contact-us/', 'icons' => array(array('src' => $icon192, 'sizes' => '192x192', 'type' => 'image/png'))),
            ),
        );
        echo wp_json_encode($m, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE); exit;
    }
    if($route==='offline'){
        status_header(200); header('Content-Type: text/html; charset=utf-8'); header('X-Robots-Tag: noindex, nofollow');
        header_remove('Cache-Control'); header('Cache-Control: no-cache');
        echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"><meta name="robots" content="noindex,nofollow"><meta name="theme-color" content="#191919"><title>You are offline | Wugweb</title><style>html{background:#191919;color:#f4f4f4;font-family:system-ui,-apple-system,"Inter Tight",sans-serif}body{margin:0;min-height:100vh;min-height:100dvh;display:grid;place-items:center;padding:calc(env(safe-area-inset-top,0px) + 24px) 24px 24px;text-align:center}main{max-width:34rem}h1{font-size:clamp(2rem,6vw,3rem);margin:0 0 .75rem}p{color:#bababa;line-height:1.6;margin:0 0 1.5rem}a{display:inline-block;min-height:44px;padding:12px 24px;border-radius:8px;background:#f4f4f4;color:#191919;font-weight:600;text-decoration:none}a:focus-visible{outline:2px solid #f4f4f4;outline-offset:3px}</style></head><body><main><h1>You are offline</h1><p>This page is not saved on your device yet. Check your connection and try again; pages you have already visited stay available.</p><a href="/">Try again</a></main></body></html>'; exit;
    }
    status_header(200); header('Content-Type: application/javascript; charset=utf-8'); header('Service-Worker-Allowed: /');
    header_remove('Cache-Control'); header('Cache-Control: no-cache'); header('X-LiteSpeed-Cache-Control: no-cache');
    $file=plugin_dir_path(__FILE__).'../assets/js/sw-template.js';
    if(file_exists($file)) { echo str_replace('__CACHE_VERSION__', 'wugweb-' . preg_replace('/[^0-9a-z.]/i', '', (string) $ver), (string) file_get_contents($file)); } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static worker script with the version substituted
    exit;
}
add_action('init', function(){
    $path=untrailingslashit((string) wp_parse_url(isset($_SERVER['REQUEST_URI'])?wp_unslash($_SERVER['REQUEST_URI']):'', PHP_URL_PATH));
    $base=untrailingslashit((string) wp_parse_url(home_url('/'), PHP_URL_PATH));
    if($path===$base.'/manifest.webmanifest') wugweb_pwa_serve('manifest');
    if($path===$base.'/offline') wugweb_pwa_serve('offline');
    if($path===$base.'/sw.js' && !(defined('WUGWEB_DISABLE_PWA_SW') && WUGWEB_DISABLE_PWA_SW)) wugweb_pwa_serve('sw');
}, 1);
// Fallback if a rewrite match ever gets here first: never canonical-redirect these two.
add_filter('redirect_canonical', function($url){ return get_query_var('wugweb_pwa') ? false : $url; });
add_action('template_redirect', function(){ $r=get_query_var('wugweb_pwa'); if($r) wugweb_pwa_serve($r); }, 0);
// Logged-in renders are flagged so the worker never caches them.
add_action('send_headers', function(){ if(is_user_logged_in()) header('X-Wugweb-Auth: 1'); });
