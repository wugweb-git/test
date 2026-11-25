<?php
$d=wugweb_core_service_data($attributes['pillar']??'brand'); $tk=wugweb_core_service_toolkit(); if(!$tk){return;}
$wrap=get_block_wrapper_attributes(array('class'=>'wugweb-toolkit-grid','style'=>'--pillar-accent:'.esc_attr($d['accent']??'#FFBE1A').';'));
?><section <?php echo $wrap; ?>><h2><?php echo esc_html($tk['title']); ?></h2><?php foreach($tk['groups'] as $g=>$items): ?><h3><?php echo esc_html($g); ?></h3><div class="wugweb-toolkit-grid-items"><?php foreach($items as $t): ?><span><?php echo esc_html($t); ?></span><?php endforeach; ?></div><?php endforeach; ?></section>
