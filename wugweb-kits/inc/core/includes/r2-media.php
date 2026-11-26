<?php
/**
 * Media Library records that only POINT at files already in R2 (no upload, no copy).
 * An attachment with post meta `_wugweb_r2_key` (e.g. assets/work/plufo/cover-image-8z0d.jpg) is served from the R2 public base.
 * Deleting such a record never touches the bucket: the R2 plugin's delete handler is kept away from it, and its `_r2cs_offloaded` meta is never set or read here.
 */
defined('ABSPATH') || exit;

if (!defined('WUGWEB_R2_BASE_URL')) { define('WUGWEB_R2_BASE_URL', 'https://pub-39ad1d03baec440faf48303be95c3782.r2.dev'); }

/** Bucket key for an attachment, or '' when it is an ordinary upload. */
function wugweb_r2_key($id) { return trim((string) get_post_meta((int) $id, '_wugweb_r2_key', true)); }

/** Old key -> new key for files that were reorganised in R2 (inc/core/r2-remap.json). */
function wugweb_r2_remap() {
    static $m = null;
    if ($m === null) {
        $f = dirname(__DIR__) . '/r2-remap.json';
        $j = is_readable($f) ? json_decode((string) file_get_contents($f), true) : array();
        $m = is_array($j['map'] ?? null) ? $j['map'] : array();
    }
    return $m;
}

/** Public URL for a bucket key: base + key, each path segment rawurlencoded, slashes kept. */
function wugweb_r2_key_url($key) {
    $base = rtrim((string) apply_filters('wugweb_r2_base_url', WUGWEB_R2_BASE_URL), '/');
    $rm = wugweb_r2_remap();
    if (isset($rm[ltrim($key, '/')])) { $key = $rm[ltrim($key, '/')]; }
    return $base . '/' . implode('/', array_map('rawurlencode', explode('/', ltrim($key, '/'))));
}

add_filter('wp_get_attachment_url', function ($url, $id) {
    $k = wugweb_r2_key($id);
    return $k !== '' ? wugweb_r2_key_url($k) : $url;
}, 20, 2);

// Without generated sizes core cannot resize; hand back the original for any requested size.
add_filter('image_downsize', function ($out, $id) {
    $k = wugweb_r2_key($id);
    return $k !== '' ? array(wugweb_r2_key_url($k), 0, 0, false) : $out;
}, 10, 2);

/** Media grid / list / modal: full size and thumbnail from R2, with the right type for video and svg. */
add_filter('wp_prepare_attachment_for_js', function ($r, $att) {
    $k = wugweb_r2_key($att->ID);
    if ($k === '') return $r;
    $url = wugweb_r2_key_url($k);
    $ext = strtolower(pathinfo($k, PATHINFO_EXTENSION));
    $r['url'] = $url;
    if (empty($r['filename'])) $r['filename'] = basename($k);
    $map = array('mp4' => array('video', 'mp4'), 'webm' => array('video', 'webm'), 'mov' => array('video', 'quicktime'), 'svg' => array('image', 'svg+xml'), 'png' => array('image', 'png'), 'jpg' => array('image', 'jpeg'), 'jpeg' => array('image', 'jpeg'), 'webp' => array('image', 'webp'), 'gif' => array('image', 'gif'), 'avif' => array('image', 'avif'));
    if (isset($map[$ext])) { $r['type'] = $map[$ext][0]; $r['subtype'] = $map[$ext][1]; $r['mime'] = $map[$ext][0] . '/' . $map[$ext][1]; }
    if (($r['type'] ?? '') === 'image') {
        $w = (int) ($r['width'] ?? 0); $h = (int) ($r['height'] ?? 0);
        $full = array('url' => $url, 'width' => $w, 'height' => $h, 'orientation' => $h > $w ? 'portrait' : 'landscape');
        $r['sizes'] = array('full' => $full, 'thumbnail' => $full);
        $r['icon'] = $url;
    }
    return $r;
}, 20, 2);

/** Deletion guard: the record goes, the bucket object stays. */
$GLOBALS['wugweb_r2_stash'] = array();
add_action('delete_attachment', function ($id) {
    if (wugweb_r2_key($id) === '') return;
    global $wp_filter;
    if (empty($wp_filter['delete_attachment'])) return;
    foreach ($wp_filter['delete_attachment']->callbacks as $prio => $cbs) {
        foreach ($cbs as $cb) {
            $f = $cb['function']; $name = '';
            if (is_array($f)) { $name = (is_object($f[0]) ? get_class($f[0]) : (string) $f[0]) . '::' . $f[1]; }
            elseif (is_string($f)) { $name = $f; }
            if ($name !== '' && preg_match('/r2/i', $name)) {
                remove_action('delete_attachment', $f, $prio);
                $GLOBALS['wugweb_r2_stash'][] = array($f, $prio, $cb['accepted_args']);
            }
        }
    }
}, 0);
add_action('delete_attachment', function () {
    foreach ($GLOBALS['wugweb_r2_stash'] as $s) { add_action('delete_attachment', $s[0], $s[1], $s[2]); }
    $GLOBALS['wugweb_r2_stash'] = array();
}, 9999);
add_filter('pre_delete_attachment', function ($check, $post) {
    if ($post && wugweb_r2_key($post->ID) !== '') {
        // Stop the offload plugin's own state from being consulted during this delete.
        add_filter('get_post_metadata', 'wugweb_r2_hide_offload_meta', 10, 3);
    }
    return $check;
}, 10, 2);
function wugweb_r2_hide_offload_meta($v, $id, $key) { return $key === '_r2cs_offloaded' && wugweb_r2_key($id) !== '' ? '' : $v; }
add_action('deleted_post', function () { remove_filter('get_post_metadata', 'wugweb_r2_hide_offload_meta', 10); });

/** Registry fields, read-only, on the attachment edit screen. */
add_filter('attachment_fields_to_edit', function ($fields, $post) {
    if (wugweb_r2_key($post->ID) === '') return $fields;
    $rows = array('_wugweb_r2_key' => 'R2 key', '_wugweb_asset_folder' => 'Folder', '_wugweb_asset_usage' => 'Usage', '_wugweb_asset_status' => 'Status', '_wugweb_asset_owner' => 'Owner');
    foreach ($rows as $meta => $label) {
        $v = (string) get_post_meta($post->ID, $meta, true);
        if ($v === '') continue;
        $fields['wugweb' . $meta] = array('label' => $label, 'input' => 'html', 'html' => '<input type="text" readonly value="' . esc_attr($v) . '" class="widefat">');
    }
    return $fields;
}, 10, 2);

/**
 * Every Media Library image gets a non-empty alt unless it was deliberately marked decorative.
 * "Deliberate" = the alt meta exists and is empty; when no alt was ever set, fall back to the attachment title.
 */
add_filter('wp_get_attachment_image_attributes', function ($attr, $att) {
    if (isset($attr['alt']) && trim((string) $attr['alt']) !== '') { return $attr; }
    if (!$att || metadata_exists('post', (int) $att->ID, '_wp_attachment_image_alt')) { return $attr; }
    $t = trim(preg_replace('/[-_]+/', ' ', pathinfo((string) $att->post_title, PATHINFO_FILENAME)));
    if ($t !== '') { $attr['alt'] = $t; }
    return $attr;
}, 10, 2);

/**
 * Resized WebP variants of cover/card images (generated outside WordPress, uploaded to R2 next to the originals as
 * <key without extension>-640.webp / -960.webp). OFF until the option wugweb_r2_variants_enabled is 1: with it off nothing changes.
 * inc/core/r2-variants.json maps each source R2 key to the variant widths that exist.
 */
function wugweb_r2_variants_enabled() { return (bool) get_option('wugweb_r2_variants_enabled', 0); }

function wugweb_r2_variant_map() {
    static $m = null;
    if (null === $m) {
        $f = dirname(__DIR__) . '/r2-variants.json';
        $m = is_readable($f) ? json_decode((string) file_get_contents($f), true) : array();
        if (!is_array($m)) { $m = array(); }
    }
    return $m;
}

/** R2 key for a public URL (either R2 base used by the theme), or ''. */
function wugweb_r2_key_from_url($url) {
    $url = strtok((string) $url, '?#');
    foreach (array((string) apply_filters('wugweb_r2_base_url', WUGWEB_R2_BASE_URL), (string) apply_filters('wugweb_assets_base', 'https://pub-39ad1d03baec440faf48303be95c3782.r2.dev')) as $b) {
        $b = rtrim($b, '/') . '/';
        if ($b !== '/' && strpos($url, $b) === 0) { $k = rawurldecode(substr($url, strlen($b))); $rm = wugweb_r2_remap(); return isset($rm[$k]) ? $rm[$k] : $k; }
    }
    return '';
}

/**
 * srcset + sizes for an R2 image URL or key, or null (feature off, not in the manifest, not R2).
 * The original is listed last with a nominal 1920w (its real width is not in the manifest), so a wide or high-density slot still gets the full image.
 */
function wugweb_r2_srcset($src_or_key, $sizes = '(max-width:767px) 100vw, (max-width:1199px) 50vw, 33vw') {
    if (!wugweb_r2_variants_enabled()) { return null; }
    $src = (string) $src_or_key;
    $key = preg_match('#^https?://#i', $src) ? wugweb_r2_key_from_url($src) : ltrim($src, '/');
    $map = wugweb_r2_variant_map();
    if ($key === '' || empty($map[$key])) { return null; }
    $base = preg_replace('/\.[A-Za-z0-9]+$/', '', $key);
    $parts = array();
    foreach ((array) $map[$key] as $w) { $w = (int) $w; if ($w > 0) { $parts[] = wugweb_r2_key_url($base . '-' . $w . '.webp') . ' ' . $w . 'w'; } }
    if (!$parts) { return null; }
    $parts[] = wugweb_r2_key_url($key) . ' 1920w';
    return array('srcset' => implode(', ', $parts), 'sizes' => $sizes);
}

/** Adds srcset/sizes to every <img> in rendered block HTML whose src is a mapped R2 image (og/twitter meta are not <img> tags and keep the original). */
add_filter('render_block', function ($html) {
    if (!is_string($html) || $html === '' || strpos($html, '<img') === false || !wugweb_r2_variants_enabled()) { return $html; }
    return preg_replace_callback('#<img\b[^>]*>#i', function ($m) {
        $tag = $m[0];
        if (stripos($tag, ' srcset=') !== false || !preg_match('#\ssrc="([^"]+)"#i', $tag, $s)) { return $tag; }
        $sizes = (stripos($tag, 'fetchpriority="high"') !== false || stripos($tag, 'loading="eager"') !== false)
            ? '(max-width:1199px) 100vw, 50vw' : '(max-width:767px) 100vw, (max-width:1199px) 50vw, 33vw';
        $v = wugweb_r2_srcset(html_entity_decode($s[1], ENT_QUOTES), $sizes);
        if (!$v) { return $tag; }
        return preg_replace('#<img\b#i', '<img srcset="' . esc_attr($v['srcset']) . '" sizes="' . esc_attr($v['sizes']) . '"', $tag, 1);
    }, $html);
}, 20);

/** Rendered block HTML that still names a reorganised R2 file gets the new URL. Plain str_replace on the few known old URLs, so it costs nothing on pages that do not use them. */
add_filter('render_block', function ($html) {
    if (!is_string($html) || $html === '' || strpos($html, 'assets/work/') === false) { return $html; }
    foreach (wugweb_r2_remap() as $old => $new) {
        if (strpos($html, $old) === false && strpos($html, str_replace(' ', '%20', $old)) === false) { continue; }
        foreach (array($old, str_replace(' ', '%20', $old)) as $o) { $html = str_replace($o, $new, $html); }
    }
    return $html;
}, 5);

/**
 * Intrinsic width/height on R2 <img> tags that have neither, so the browser reserves the space before the file loads (no layout shift).
 * inc/core/img-dims.json maps R2 key => [width, height], read from the files themselves. Images not in it, or that already
 * carry width or height, are left alone. CSS keeps height:auto, so the attributes only supply the aspect ratio.
 */
function wugweb_r2_img_dims() {
    static $m = null;
    if (null === $m) {
        $f = dirname(__DIR__) . '/img-dims.json';
        $m = is_readable($f) ? json_decode((string) file_get_contents($f), true) : array();
        if (!is_array($m)) { $m = array(); }
    }
    return $m;
}
add_filter('render_block', function ($html) {
    if (!is_string($html) || $html === '' || strpos($html, '<img') === false) { return $html; }
    $d = wugweb_r2_img_dims();
    if (!$d) { return $html; }
    return preg_replace_callback('#<img\b[^>]*>#i', function ($m) use ($d) {
        $tag = $m[0];
        if (preg_match('#\s(?:width|height)\s*=#i', $tag) || !preg_match('#\ssrc="([^"]+)"#i', $tag, $s)) { return $tag; }
        $key = wugweb_r2_key_from_url(html_entity_decode($s[1], ENT_QUOTES));
        if ($key === '' || empty($d[$key][0]) || empty($d[$key][1])) {
            // Media Library sizes carry their real size in the file name (name-1024x575.webp): use it for files in wp-content/uploads.
            if (preg_match('#/wp-content/uploads/[^"\s]*-(\d{2,5})x(\d{2,5})\.(?:webp|jpe?g|png|gif)(?:[?\#][^"\s]*)?$#i', html_entity_decode($s[1], ENT_QUOTES), $wh)) {
                return preg_replace('#<img\b#i', '<img width="' . (int) $wh[1] . '" height="' . (int) $wh[2] . '"', $tag, 1);
            }
            return $tag;
        }
        return preg_replace('#<img\b#i', '<img width="' . (int) $d[$key][0] . '" height="' . (int) $d[$key][1] . '"', $tag, 1);
    }, $html);
}, 19);

/**
 * A case study whose cover is a video file (3 of them) cannot use it as an image: <img>, og:image, JSON-LD and the cards all need a picture.
 * On the front end only, the cover meta then reads as that video's generated poster (inc/core/video-posters.json: video key -> poster key),
 * or as '' (the placeholder) when no poster exists. The stored value is never changed, and wp-admin still sees the real one.
 */
add_filter('get_post_metadata', function ($v, $id, $key, $single) {
    static $busy = false;
    if ($key !== 'wugweb_cs_cover_image' || $busy || is_admin()) { return $v; }
    $busy = true; $raw = get_post_meta($id, $key, true); $busy = false;
    if (!is_string($raw) || !preg_match('#\.(?:mp4|webm|mov)(?:[?\#]|$)#i', $raw)) { return $v; }
    static $map = null;
    if (null === $map) { $f = dirname(__DIR__) . '/video-posters.json'; $map = is_readable($f) ? (array) json_decode((string) file_get_contents($f), true) : array(); }
    $k = wugweb_r2_key_from_url($raw);
    $out = ($k !== '' && !empty($map[$k])) ? wugweb_r2_key_url($map[$k]) : '';
    return $single ? $out : array($out);
}, 10, 4);

/**
 * Case-study pages only: a lazy body <video> (src in data-src, preload none) has nothing to show until it loads. When the video has a generated poster
 * (inc/core/video-posters.json), add it as the poster attribute. Listing pages are left alone on purpose: a poster is fetched at once, and /work/ has ~20 videos.
 * Needs the option wugweb_video_posters_enabled = 1.
 */
add_filter('render_block', function ($html) {
    if (!is_string($html) || strpos($html, '<video') === false || !get_option('wugweb_video_posters_enabled', 0) || !is_singular('case_study')) { return $html; }
    static $map = null;
    if (null === $map) { $f = dirname(__DIR__) . '/video-posters.json'; $map = is_readable($f) ? (array) json_decode((string) file_get_contents($f), true) : array(); }
    if (!$map) { return $html; }
    return preg_replace_callback('#<video\b[^>]*>#i', function ($m) use ($map) {
        $tag = $m[0];
        if (stripos($tag, ' poster=') !== false || !preg_match('#\s(?:data-)?src="([^"]+)"#i', $tag, $s)) { return $tag; }
        $k = wugweb_r2_key_from_url(html_entity_decode($s[1], ENT_QUOTES));
        if ($k === '' || empty($map[$k])) { return $tag; }
        return substr($tag, 0, -1) . ' poster="' . esc_url(wugweb_r2_key_url($map[$k])) . '">';
    }, $html);
}, 19);

