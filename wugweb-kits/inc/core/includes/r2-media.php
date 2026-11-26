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

/** Public URL for a bucket key: base + key, each path segment rawurlencoded, slashes kept. */
function wugweb_r2_key_url($key) {
    $base = rtrim((string) apply_filters('wugweb_r2_base_url', WUGWEB_R2_BASE_URL), '/');
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
