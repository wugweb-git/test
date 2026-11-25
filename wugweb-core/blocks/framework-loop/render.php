<?php
$d=wugweb_core_service_data($attributes['pillar']??'brand'); if(!$d){return;}
$wrap=get_block_wrapper_attributes(array('class'=>'wugweb-framework-loop','style'=>'--pillar-accent:'.esc_attr($d['accent']).';'));
?><section <?php echo $wrap; ?>><h2><?php echo esc_html($d['fwTitle']); ?></h2><div class="wugweb-framework-loop-grid"><?php foreach($d['phases'] as $i=>$ph): ?><article class="wugweb-card"><strong><?php echo esc_html(str_pad($i+1,2,'0',STR_PAD_LEFT)); ?></strong><h3><?php echo esc_html($ph['t']); ?></h3><ul><?php foreach($ph['items'] as $it): ?><li><?php echo esc_html($it); ?></li><?php endforeach; ?></ul></article><?php endforeach; ?></div><p class="wugweb-fw-note"><?php echo esc_html($d['fwNote']); ?></p></section>
