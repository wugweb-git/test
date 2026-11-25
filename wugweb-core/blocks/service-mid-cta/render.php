<?php
$d=wugweb_core_service_data($attributes['pillar']??'brand'); if(!$d){return;}
$wrap=get_block_wrapper_attributes(array('class'=>'wugweb-service-mid-cta','style'=>'--pillar-accent:'.esc_attr($d['accent']).';'));
?><section <?php echo $wrap; ?>><div><h2><?php echo esc_html($d['cta']['t']); ?></h2><p><?php echo esc_html($d['cta']['p']); ?></p><a class="wugweb-service-mid-cta-button" href="<?php echo esc_url(home_url('/contact-us/')); ?>"><?php echo esc_html($d['cta']['btn'][0]); ?></a></div></section>
