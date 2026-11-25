<?php
/** Shared allocation for the blog showcase: each post appears once in the mosaic; the main grid lists everything. */
defined('ABSPATH') || exit;
function wugweb_blog_showcase() {
  static $d = null; if ($d !== null) { return $d; }
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
$hero = array_shift($cards);
// Allocation (each post appears once in the showcase blocks; the main grid lists everything and hides the repeats until a filter/search is active).
$side = array_slice($cards, 0, 4);                      // "Featured articles" ruled list
$picks_rows = array_slice($cards, 4, 4);                // ruled rows with square thumbs
$picks_big = array_slice($cards, 8, 1);                 // one big card
$used = array();
foreach (array_merge(array($hero), $side, $picks_rows, $picks_big) as $u) { if ($u) { $used[$u['pid']] = true; } }
$side_label = (!empty($side) && !empty($side[0]['is_featured'])) ? 'Featured articles' : 'Latest articles';
$picks_label = (!empty($picks_rows) && !empty($picks_rows[0]['is_featured'])) ? "Editor's picks" : 'More recent articles';
// Real case studies (newest 3): wide strip + case-study cards.
$cs_ids = get_posts(array('post_type' => 'case_study', 'post_status' => 'publish', 'numberposts' => 3, 'orderby' => 'date', 'order' => 'DESC', 'fields' => 'ids'));
$cs = array(); foreach ($cs_ids as $cid) { $cs[] = array('id' => $cid, 'f' => wugweb_fields_case_study($cid)); }
$strip = $cs ? array_shift($cs) : null;
$cal = '<svg class="date-icon" viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>';
  $d = compact('cards','topics','hero','side','picks_rows','picks_big','used','side_label','picks_label','cs','strip','cal');
  return $d;
}
