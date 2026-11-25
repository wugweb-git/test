<?php
/** TeamGrid.astro card markup (shared by team-card and team-grid). */
if (!function_exists('wugweb_team_card_html')) {
function wugweb_team_card_html(array $m, $i = 0, $suffix = 'at wugweb', $tag = 'li', $wrapper = '') {
    $name = (string) ($m['name'] ?? ''); $role = (string) ($m['role'] ?? ''); $img = (string) ($m['photo'] ?? '');
    $stagger = ($i % 2 === 0) ? 0 : 1;
    $o = '<' . $tag . ' ' . ($wrapper ?: 'class="team-card"') . ' data-reveal data-reveal-delay="' . esc_attr((string) ($i * 0.05)) . '" style="--stagger:' . $stagger . '"><div class="team-photo">';
    if ($img) $o .= '<img src="' . esc_url($img) . '" alt="' . esc_attr($name) . '" loading="lazy" decoding="async">';
    else $o .= '<span class="wugweb-ph" style="--ph-ar:3/4;height:100%" aria-hidden="true"></span>';
    $o .= '</div><div class="team-meta"><p class="team-name">' . esc_html($name) . '</p>' . ($role !== '' ? '<p class="team-role">' . esc_html($role) . '</p>' : '') . '<p class="team-suffix">' . esc_html($suffix) . '</p></div></' . $tag . '>';
    return $o;
}}
