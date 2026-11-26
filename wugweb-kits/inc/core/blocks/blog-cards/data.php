<?php
/** Shared allocation for the blog showcase: each post appears once in the mosaic; the main grid lists everything. */
defined('ABSPATH') || exit;
/** Find the wugweb/blog-bento block in the current page content (editor choices); nothing found = automatic allocation. */
function wugweb_blog_bento_attrs($blocks) {
  foreach ($blocks as $b) {
    if (($b['blockName'] ?? '') === 'wugweb/blog-bento') { return (array) ($b['attrs'] ?? array()); }
    if (!empty($b['innerBlocks'])) { $r = wugweb_blog_bento_attrs($b['innerBlocks']); if ($r !== null) { return $r; } }
  }
  return null;
}
function wugweb_blog_showcase() {
  static $d = null; if ($d !== null) { return $d; }
  if (!isset($GLOBALS['wugweb_blog_bento'])) {
    $q = get_queried_object();
    $a = ($q instanceof WP_Post && has_block('wugweb/blog-bento', $q)) ? wugweb_blog_bento_attrs(parse_blocks($q->post_content)) : null;
    if ($a !== null) { $GLOBALS['wugweb_blog_bento'] = $a; $GLOBALS['wugweb_blog_bento_present'] = true; }
  }
$ids = get_posts(array('post_type' => 'post', 'post_status' => 'publish', 'numberposts' => 200, 'orderby' => 'date', 'order' => 'DESC', 'fields' => 'ids', 'ignore_sticky_posts' => true));
if (!$ids) { return array(); }
$feat = array(); $rest = array();
foreach ($ids as $id) { if (in_array(strtolower((string) get_post_meta($id, 'wugweb_featured', true)), array('1', 'true', 'yes'), true)) { $feat[] = $id; } else { $rest[] = $id; } }
$ids = array_merge($feat, $rest);
$cards = array(); $topics = array();
foreach ($ids as $id) {
  $c = wugweb_post_card_data($id);
  $f = wugweb_fields_post($id);
  $c['slug_topic'] = $c['topic'] !== '' ? sanitize_title($c['topic']) : '';
  $c['date'] = wugweb_post_date_label($id);
  $c['author'] = $f['author'];
  $c['is_featured'] = in_array($id, $feat, true);
  $c['pid'] = $id;
  $c['tags'] = array_slice(array_values(array_filter((array) $f['tags'])), 0, 3);
  $cards[] = $c;
  if ($c['slug_topic'] !== '') { if (!isset($topics[$c['slug_topic']])) { $topics[$c['slug_topic']] = array('name' => $c['topic'], 'count' => 0); } $topics[$c['slug_topic']]['count']++; }
}
uasort($topics, function ($a, $b) { return $b['count'] <=> $a['count'] ?: strcasecmp($a['name'], $b['name']); });
$all = array(); foreach ($cards as $c0) { $all[$c0['pid']] = $c0; }
$hero = array_shift($cards);
// Allocation (each post appears once in the showcase blocks; the main grid lists everything and hides the repeats until a filter/search is active).
$side = array_slice($cards, 0, 4);                      // "Featured articles" ruled list
$picks_rows = array_slice($cards, 4, 4);                // ruled rows with square thumbs
$picks_big = array_slice($cards, 8, 1);                 // one big card
// Editor choices (wugweb/blog-bento attributes, comma-separated post IDs); anything left blank keeps the default above.
$ov = isset($GLOBALS['wugweb_blog_bento']) && is_array($GLOBALS['wugweb_blog_bento']) ? $GLOBALS['wugweb_blog_bento'] : array();
$pick = function ($csv) use ($all) { $o = array(); foreach (array_filter(array_map('intval', explode(',', (string) $csv))) as $i) { if (isset($all[$i])) { $o[] = $all[$i]; } } return $o; };
if (!empty($ov['hero'])) { $h = $pick($ov['hero']); if ($h) { $hero = $h[0]; } }
if (!empty($ov['side'])) { $x = $pick($ov['side']); if ($x) { $side = $x; } }
if (!empty($ov['picksRows'])) { $x = $pick($ov['picksRows']); if ($x) { $picks_rows = $x; } }
if (!empty($ov['picksBig'])) { $x = $pick($ov['picksBig']); if ($x) { $picks_big = array($x[0]); } }
$used = array();
foreach (array_merge(array($hero), $side, $picks_rows, $picks_big) as $u) { if ($u) { $used[$u['pid']] = true; } }
$side_label = !empty($ov['sideLabel']) ? (string) $ov['sideLabel'] : ((!empty($side) && !empty($side[0]['is_featured'])) ? 'Featured articles' : 'Latest articles');
$picks_label = !empty($ov['picksLabel']) ? (string) $ov['picksLabel'] : ((!empty($picks_rows) && !empty($picks_rows[0]['is_featured'])) ? "Editor's picks" : 'More recent articles');
// Real case studies (newest 3): wide strip + case-study cards.
$cs_ids = get_posts(array('post_type' => 'case_study', 'post_status' => 'publish', 'numberposts' => 3, 'orderby' => 'date', 'order' => 'DESC', 'fields' => 'ids'));
$cs = array(); foreach ($cs_ids as $cid) { $cs[] = array('id' => $cid, 'f' => wugweb_fields_case_study($cid)); }
if (!empty($ov['cases'])) { $cs = array(); foreach (array_filter(array_map('intval', explode(',', (string) $ov['cases']))) as $cid) { if (get_post_type($cid) === 'case_study' && get_post_status($cid) === 'publish') { $cs[] = array('id' => $cid, 'f' => wugweb_fields_case_study($cid)); } } }
$strip = $cs ? array_shift($cs) : null;
if (!empty($ov['hideStrip'])) { $strip = null; }
if (!empty($ov['hideCases'])) { $cs = array(); }
$cal = '<svg class="date-icon" viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>';
  $d = compact('cards','topics','hero','side','picks_rows','picks_big','used','side_label','picks_label','cs','strip','cal');
  return $d;
}
