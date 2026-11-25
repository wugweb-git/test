<?php
$d=wugweb_core_service_data($attributes['pillar']??'brand'); if(!$d){return;}
$wrap=get_block_wrapper_attributes(array('class'=>'wugweb-process-timeline','style'=>'--pillar-accent:'.esc_attr($d['accent']).';'));
?><section <?php echo $wrap; ?>><h2><?php echo esc_html($d['approachTitle']); ?></h2><ol><?php foreach($d['steps'] as $i=>$s): ?><li><strong><?php echo esc_html(str_pad($i+1,2,'0',STR_PAD_LEFT).' '.$s['t']); ?></strong><span><?php echo esc_html($s['p']); ?></span></li><?php endforeach; ?></ol></section>
