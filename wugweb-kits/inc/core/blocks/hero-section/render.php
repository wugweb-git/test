<?php
$base = 'https://pub-39ad1d03baec440faf48303be95c3782.r2.dev/assets/';
$video = $attributes['videoUrl'] ?? ($base . 'site/home/home.mp4'); $poster = $attributes['posterUrl'] ?? ($base . 'site/home/home-poster.webp');
 // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core ?><section <?php echo get_block_wrapper_attributes(array('class' => 'hero', 'id' => 'hero', 'aria-labelledby' => 'hero-title')); ?>>
<video aria-hidden="true" class="hero-video" autoplay muted loop playsinline preload="none" <?php if ($poster) echo 'poster="' . esc_url($poster) . '"'; ?> <?php if ($video) echo 'src="' . esc_url($video) . '"'; ?>></video><div class="hero-overlay"></div>
<div class="wrap hero-content"><h1 class="hero-headline t-display" data-headline id="hero-title"><?php echo esc_html($attributes['heading'] ?? 'Where Vision Meets Velocity'); ?></h1>
<div class="hero-ctas" data-reveal><a href="<?php echo esc_url($attributes['secondaryUrl'] ?? '#services-content'); ?>" class="btn btn-ghost"><?php echo esc_html($attributes['secondaryLabel'] ?? 'Explore Wugweb'); ?></a><a href="<?php echo esc_url($attributes['buttonUrl'] ?? '/contact-us/'); ?>" class="btn btn-solid"><?php echo esc_html($attributes['buttonLabel'] ?? 'Get in Touch'); ?></a></div></div>
<div class="scroll-cue" data-reveal aria-hidden="true"></div></section>
