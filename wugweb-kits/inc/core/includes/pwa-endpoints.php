<?php

/** Manifest icons: the WP site icon (W mark) at 192/512, falling back to the theme's own brand files. purpose "any" only. */
if (!function_exists('wugweb_pwa_icons')) {
function wugweb_pwa_icons() {
    $out = array();
    foreach (array(192, 512) as $px) {
        $u = function_exists('get_site_icon_url') ? get_site_icon_url($px) : '';
        if ($u) { $out[] = array('src' => $u, 'sizes' => $px . 'x' . $px, 'type' => 'image/png', 'purpose' => 'any'); }
    }
    if (!$out) {
        $out[] = array('src' => get_theme_file_uri('assets/brand/pwa-512.png'), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any');
        $out[] = array('src' => get_theme_file_uri('assets/brand/favicon.svg'), 'sizes' => 'any', 'type' => 'image/svg+xml', 'purpose' => 'any');
    }
    return $out;
}
}
/** Dynamic manifest and service-worker endpoints for Wugweb Core. */
defined('ABSPATH') || exit;
function wugweb_pwa_disabled(){
    if (defined('WUGWEB_DISABLE_PWA_SW') && WUGWEB_DISABLE_PWA_SW) return true;
    $host = strtolower((string) wp_parse_url(home_url('/'), PHP_URL_HOST));
    return str_starts_with($host, 'test.');
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
add_action('template_redirect', function(){
    $route=get_query_var('wugweb_pwa'); if(!$route)return;
    $media_base=defined('WUGWEB_MEDIA_BASE_URL')?untrailingslashit(WUGWEB_MEDIA_BASE_URL):home_url('/brand');
    if($route==='manifest'){
        header('Content-Type: application/manifest+json; charset=utf-8'); header('Cache-Control: public, max-age=3600');
        echo wp_json_encode(array('name'=>'Wugweb','short_name'=>'Wugweb','description'=>'Digital experience systems.','start_url'=>'/','scope'=>'/','display'=>'standalone','background_color'=>'#191919','theme_color'=>'#191919','icons'=>wugweb_pwa_icons()), JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT); exit;
    }
    if($route==='sw'){
        header('Content-Type: application/javascript; charset=utf-8'); header('Service-Worker-Allowed: /'); header('Cache-Control: no-cache, no-store, must-revalidate');
        $file=plugin_dir_path(__FILE__).'../assets/js/sw-template.js'; if(file_exists($file)) readfile($file); exit;
    }
});
