<?php
/** Stat Card: pages/services/index.astro .stat-card-wrap. eyebrow = number/value, title = label, text = copy. */
$n = (string) ($attributes['eyebrow'] ?? '');
$label = (string) ($attributes['title'] ?? '');
$copy = (string) ($attributes['text'] ?? '');
?><div <?php echo get_block_wrapper_attributes(array('class' => 'stat-card-wrap', 'data-reveal' => '')); ?>><span class="stat-n"><?php echo esc_html($n); ?></span><p class="stat-label"><?php echo esc_html($label); ?></p><?php if ($copy !== ''): ?><p class="stat-copy"><?php echo esc_html($copy); ?></p><?php endif; ?></div>
