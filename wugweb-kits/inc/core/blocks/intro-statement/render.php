<?php
$paras = !empty($attributes['paragraphs']) ? $attributes['paragraphs'] : array('Wugweb is a digital experience agency where design meets engineering to shape what is next. We deliver tailored UX design services, rooted in user-centered design and driven by a clear user experience strategy.', 'Our experience design services and custom UX solutions help businesses craft impactful digital product design with precision. As a UI/UX-focused agency, we create digital experiences that not only look great but also perform with purpose.');
$tags = !empty($attributes['tags']) ? $attributes['tags'] : array('strategy', 'technology', 'design', 'we are here');
 // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core ?><section <?php echo get_block_wrapper_attributes(array('class' => 'wrap intro', 'id' => 'intro', 'data-scroll' => 'rise', 'aria-labelledby' => 'intro-title')); ?>>
<h2 data-reveal id="intro-title"><?php echo esc_html($attributes['heading'] ?? 'Wugweb is a tight team of thoughtful creatives, engineers, and strategists'); ?></h2>
<div class="intro-body"><div class="intro-copy" data-reveal><?php foreach ($paras as $p): ?><p><?php echo esc_html($p); ?></p><?php endforeach; ?></div><?php
$venn = array_values($tags); $center = isset($venn[3]) ? $venn[3] : 'we are here';
$names = array_slice(array_pad($venn, 3, ''), 0, 3);
$hue = array('var(--svc-strategy)', 'var(--svc-engineering)', 'var(--svc-brand)');
?><figure class="venn" data-venn role="img" aria-label="<?php echo esc_attr(implode(', ', $names) . ' overlap: ' . $center); ?>"><div class="venn-stage"><?php foreach ($names as $i => $n): ?><span class="venn-c venn-c<?php echo (int) $i + 1; ?>" data-venn-c style="--h:<?php echo esc_attr($hue[$i]); ?>"><span class="venn-l"><?php echo esc_html($n); ?></span></span><?php endforeach; ?><span class="venn-core" data-venn-core><?php echo esc_html($center); ?></span></div></figure></div></section>
