<?php
/** wugweb/overlay: native <dialog>. Panel carries the block's wrapper attributes (colour/spacing/border from the Styles panel). */
defined('ABSPATH') || exit;
$layouts = array('modal', 'drawer-right', 'drawer-left', 'drawer-bottom', 'banner-bottom');
$layout = in_array($attributes['layout'] ?? 'modal', $layouts, true) ? $attributes['layout'] : 'modal';
$id = !empty($attributes['anchor']) ? sanitize_title($attributes['anchor']) : 'overlay-' . wp_unique_id();
$tid = $id . '-title';
$panel = get_block_wrapper_attributes(array('class' => 'wugweb-overlay__panel'));
?>
<dialog class="wugweb-overlay is-<?php echo esc_attr($layout); ?>" id="<?php echo esc_attr($id); ?>" aria-labelledby="<?php echo esc_attr($tid); ?>" data-overlay data-layout="<?php echo esc_attr($layout); ?>"<?php echo !empty($attributes['backdropClose']) ? ' data-backdrop-close' : ''; ?><?php echo !empty($attributes['openOnLoad']) ? ' data-open-on-load' : ''; ?>>
  <div <?php echo $panel; ?>>
    <?php if ($layout === 'drawer-bottom') : ?><span class="wugweb-overlay__grip" aria-hidden="true"></span><?php endif; ?>
    <header class="wugweb-overlay__head">
      <h2 class="wugweb-overlay__title" id="<?php echo esc_attr($tid); ?>"><?php echo esc_html($attributes['title'] ?? ''); ?></h2>
      <button type="button" class="wugweb-overlay__close" data-overlay-close aria-label="<?php echo esc_attr($attributes['closeLabel'] ?? 'Close'); ?>"><span aria-hidden="true">&times;</span></button>
    </header>
    <div class="wugweb-overlay__body"><?php echo $content; ?></div>
  </div>
</dialog>
