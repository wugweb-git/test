<?php
$d=wugweb_core_service_data($attributes['pillar']??'brand'); if(!$d){return;}
$wrap=get_block_wrapper_attributes(array('class'=>'wugweb-capability-showcase','style'=>'--pillar-accent:'.esc_attr($d['accent']).';'));
?><section <?php echo $wrap; ?>><h2><?php echo esc_html($d['capsTitle']); ?></h2><div class="wugweb-capability-showcase-grid"><?php foreach($d['caps'] as $i=>$c): ?><details class="wugweb-capability-showcase-item"<?php echo $i===0?' open':''; ?>><summary><span><?php echo esc_html(str_pad($i+1,2,'0',STR_PAD_LEFT)); ?></span> <?php echo esc_html($c['t']); ?></summary><?php if($c['d']!==''): ?><p><?php echo esc_html($c['d']); ?></p><?php endif; ?></details><?php endforeach; ?></div></section>
