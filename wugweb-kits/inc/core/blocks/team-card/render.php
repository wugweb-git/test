<?php
/** Team Card: components/TeamGrid.astro card. title = name, eyebrow = role, meta = suffix, imageUrl = photo; or userId / post author. */
require_once get_template_directory() . '/inc/core/includes/team-card-html.php';
$uid = (int) ($attributes['userId'] ?? 0); if (!$uid && is_object($block ?? null) && !empty($block->context['postId'])) $uid = (int) get_post_field('post_author', (int) $block->context['postId']);
$u = $uid ? wugweb_fields_user($uid) : array();
$m = array('name' => (string) ($u['name'] ?? $attributes['title'] ?? ''), 'role' => (string) ($u['role'] ?? $attributes['eyebrow'] ?? ''), 'photo' => (string) ($u['image'] ?? $attributes['imageUrl'] ?? ''));
$suffix = (string) ($attributes['meta'] ?? '') !== '' ? $attributes['meta'] : 'at wugweb';
echo wugweb_team_card_html($m, 0, $suffix, 'div', get_block_wrapper_attributes(array('class' => 'team-card')));
