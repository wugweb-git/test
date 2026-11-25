<?php
$d=wugweb_core_service_data($attributes['pillar']??'brand'); if(!$d){return;}
$wrap=get_block_wrapper_attributes(array('class'=>'wugweb-service-impact-bento','style'=>'--pillar-accent:'.esc_attr($d['accent']).';'));
?><section <?php echo $wrap; ?>><h2><?php echo esc_html($d['bentoTitle']); ?></h2><div class="wugweb-service-impact-bento-grid"><?php foreach($d['bento'] as $c): ?><article class="wugweb-card"><h3><?php echo esc_html($c['t']); ?></h3><p><?php echo esc_html($c['p']); ?></p></article><?php endforeach; ?></div></section>
