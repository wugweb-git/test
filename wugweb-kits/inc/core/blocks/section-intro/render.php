<?php
/** Section Intro — .services-intro / .why-wugweb bands from services/index.astro. */
defined('ABSPATH') || exit;
$v = (($attributes['variant'] ?? 'intro') === 'why') ? 'why' : 'intro';
$id = $v === 'why' ? 'why-wugweb' : 'services-intro';
$cls = $v === 'why' ? 'wrap why-wugweb' : 'wrap services-intro';
$h = (string) ($attributes['heading'] ?? '');
$t = (string) ($attributes['text'] ?? '');
if ($h === '' && $t === '') { return; }
?><section <?php echo get_block_wrapper_attributes(array('class' => $cls, 'id' => $id, 'aria-labelledby' => $id . '-title')); ?>>
<?php if ($h !== '') : ?><h2 data-reveal id="<?php echo esc_attr($id); ?>-title"><?php echo esc_html($h); ?></h2><?php endif; ?>
<?php if ($t !== '') : ?><p data-reveal><?php echo esc_html($t); ?></p><?php endif; ?>
</section>
