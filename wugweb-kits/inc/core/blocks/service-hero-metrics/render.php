<?php
$d=wugweb_core_service_data($attributes['pillar']??'brand'); if(!$d){return;}
$wrap=get_block_wrapper_attributes(array('class'=>'wugweb-service-hero-metrics','style'=>'--pillar-accent:'.esc_attr($d['accent']).';'));
?><section <?php echo $wrap; ?>><p class="wugweb-eyebrow">Service Pillar</p><h1><?php echo esc_html($d['h1']); ?></h1><p class="wugweb-lede"><?php echo esc_html($d['tagline']); ?></p><div class="wugweb-service-hero-metrics-grid"><?php foreach($d['stats'] as $s): ?><div><strong><?php echo esc_html($s['n']); ?></strong><span><?php echo esc_html($s['l']); ?></span></div><?php endforeach; ?></div></section>
