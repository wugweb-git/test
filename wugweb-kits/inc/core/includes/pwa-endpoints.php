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
});
add_filter('query_vars', function($vars){ $vars[]='wugweb_pwa'; return $vars; });
add_action('wp_head', function(){
    if (is_admin() || is_user_logged_in()) return;
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
    if($route==='manifest'){
        header_remove('Cache-Control'); header('Cache-Control: public, max-age=3600');
        status_header(200); header('Content-Type: application/manifest+json; charset=utf-8');
        echo wp_json_encode(array('name'=>'Wugweb','short_name'=>'Wugweb','description'=>'Digital experience systems.','start_url'=>'/','scope'=>'/','display'=>'standalone','background_color'=>'#191919','theme_color'=>'#191919','icons'=>wugweb_pwa_icons()), JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT); exit;
    }
    status_header(200); header('Content-Type: application/javascript; charset=utf-8'); header('Service-Worker-Allowed: /');
    header_remove('Cache-Control'); header('Cache-Control: no-cache'); header('X-LiteSpeed-Cache-Control: no-cache');
    $file=plugin_dir_path(__FILE__).'../assets/js/sw-template.js'; if(file_exists($file)) readfile($file); exit;
}
add_action('init', function(){
    $path=untrailingslashit((string) wp_parse_url(isset($_SERVER['REQUEST_URI'])?wp_unslash($_SERVER['REQUEST_URI']):'', PHP_URL_PATH));
    $base=untrailingslashit((string) wp_parse_url(home_url('/'), PHP_URL_PATH));
    if($path===$base.'/manifest.webmanifest') wugweb_pwa_serve('manifest');
    if($path===$base.'/sw.js' && !(defined('WUGWEB_DISABLE_PWA_SW') && WUGWEB_DISABLE_PWA_SW)) wugweb_pwa_serve('sw');
}, 1);
// Fallback if a rewrite match ever gets here first: never canonical-redirect these two.
add_filter('redirect_canonical', function($url){ return get_query_var('wugweb_pwa') ? false : $url; });
add_action('template_redirect', function(){ $r=get_query_var('wugweb_pwa'); if($r) wugweb_pwa_serve($r); }, 0);
// Logged-in renders are flagged so the worker never caches them.
add_action('send_headers', function(){ if(is_user_logged_in()) header('X-Wugweb-Auth: 1'); });
