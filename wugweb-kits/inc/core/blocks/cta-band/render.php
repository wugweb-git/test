<?php
$heading = $attributes['heading'] ?? "We speak your Industry's language.";
$copy = $attributes['text'] ?? 'Partner with Wugweb for domain-driven design and development that delivers.';
$label = $attributes['buttonLabel'] ?? 'Request Quote';
$href = $attributes['buttonUrl'] ?? '/contact-us/';
?><div <?php echo get_block_wrapper_attributes(array('class' => 'wrap final-cta-wrap')); ?>><section class="global-final-cta" id="global-final-cta" aria-labelledby="global-final-cta-title"><span class="cta-sheen" aria-hidden="true"></span><h2 id="global-final-cta-title"><?php echo esc_html($heading); ?></h2><div class="final-cta-inner"><div class="final-cta-copy"><p><?php echo esc_html($copy); ?></p></div><a href="<?php echo esc_url($href); ?>" class="btn btn-solid"><?php echo esc_html($label); ?></a></div></section></div>
