<?php
$logos = array();
foreach ((array) get_terms(array('taxonomy' => 'client', 'hide_empty' => false, 'meta_key' => 'wugweb_wall_order', 'orderby' => 'meta_value_num', 'order' => 'ASC')) as $t) {
  if (is_wp_error($t)) { continue; }
  $u = (string) get_term_meta($t->term_id, 'wugweb_wall_logo', true); if ($u === '') { continue; }
  $logos[] = array('url' => $u, 'name' => html_entity_decode($t->name, ENT_QUOTES));
}
if (!$logos) { return; }
$split = 14; $heading = $attributes['heading'] ?? 'Trusted by'; $accent = $attributes['headingAccent'] ?? 'leading brands';
$tile = function ($l) { return '<li class="logo-tile">' . (!empty($l['url']) ? '<img src="' . esc_url($l['url']) . '" alt="' . esc_attr($l['name']) . ' logo" loading="lazy" decoding="async" />' : '<span class="wugweb-ph" style="--ph-ar:16/9;border-radius:inherit" aria-hidden="true"></span>') . '</li>'; };
 // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core ?><section <?php echo get_block_wrapper_attributes(array('class' => 'logos-band', 'id' => 'logos-band', 'aria-labelledby' => 'logos-band-title')); ?>><div class="wrap logos" data-scroll="fade-up"><ul class="logo-grid" data-scroll="assemble"><?php
foreach (array_slice($logos, 0, $split) as $l) echo $tile($l); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tile closure escapes its values 
?><li class="logo-grid-title" data-assemble-static><h2 class="logos-heading" data-reveal id="logos-band-title"><?php echo esc_html($heading); ?> <span><?php echo esc_html($accent); ?></span></h2></li><?php
foreach (array_slice($logos, $split) as $l) echo $tile($l); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tile closure escapes its values 
?></ul></div></section>
