<?php
/** Team Grid: components/TeamGrid.astro section. members JSON, or source=users to read WP users. */
require_once get_template_directory() . '/inc/core/includes/team-card-html.php';
$members = is_array($attributes['members'] ?? null) ? $attributes['members'] : array();
if (($attributes['source'] ?? 'manual') === 'users') {
  $members = array();
  foreach (get_users(array('who' => 'authors', 'orderby' => 'ID', 'number' => 20)) as $us) { $f = wugweb_fields_user($us->ID); $members[] = array('name' => $f['name'], 'role' => $f['role'], 'photo' => $f['image']); }
}
if (!$members) return;
$heading = (string) ($attributes['heading'] ?? 'The faces behind the work.'); $lead = max(0, (int) ($attributes['headingLead'] ?? 2)); $suffix = (string) ($attributes['suffix'] ?? 'at wugweb');
$w = explode(' ', $heading); $l = implode(' ', array_slice($w, 0, $lead)); $r = implode(' ', array_slice($w, $lead));
?><section <?php echo get_block_wrapper_attributes(array('class' => 'wrap team-block', 'id' => 'team-block', 'aria-labelledby' => 'team-block-title')); ?>><h2 class="team-heading" data-reveal id="team-block-title"><span class="team-heading-lead"><?php echo esc_html($l); ?></span> <span class="team-heading-rest"><?php echo esc_html($r); ?></span></h2><ul class="team-cards" data-team><?php foreach ($members as $i => $m) echo wugweb_team_card_html($m, $i, $suffix, 'li'); ?></ul></section>
