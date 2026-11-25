<?php
$logos = json_decode((string) file_get_contents(__DIR__ . '/../../includes/logos.json'), true) ?: array();
if (!$logos) { return; }
$split = 14; $heading = $attributes['heading'] ?? 'Trusted by'; $accent = $attributes['headingAccent'] ?? 'leading brands';
$tile = function ($l) { return '<li class="logo-tile"><img src="' . esc_url($l['url']) . '" alt="' . esc_attr($l['name']) . ' logo" loading="lazy" decoding="async" /></li>'; };
?><section <?php echo get_block_wrapper_attributes(array('class' => 'logos-band', 'id' => 'logos-band', 'aria-labelledby' => 'logos-band-title')); ?>><div class="wrap logos" data-scroll="fade-up"><ul class="logo-grid" data-scroll="assemble"><?php
foreach (array_slice($logos, 0, $split) as $l) echo $tile($l);
?><li class="logo-grid-title" data-assemble-static><h2 class="logos-heading" data-reveal id="logos-band-title"><?php echo esc_html($heading); ?> <span><?php echo esc_html($accent); ?></span></h2></li><?php
foreach (array_slice($logos, $split) as $l) echo $tile($l);
?></ul></div></section>
