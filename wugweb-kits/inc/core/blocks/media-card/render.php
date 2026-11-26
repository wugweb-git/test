<?php
/** Media Card (horizontal): image left, title + description right; grey box when no image. Reusable anywhere. */
defined('ABSPATH') || exit;
$eyebrow = (string) ($attributes['eyebrow'] ?? '');
$title = (string) ($attributes['title'] ?? '');
$text = (string) ($attributes['text'] ?? '');
$url = (string) ($attributes['url'] ?? '');
$img = (string) ($attributes['imageUrl'] ?? '');
$cta = (string) ($attributes['cta'] ?? '');
$alt = (string) ($attributes['imageAlt'] ?? '');
$right = ($attributes['mediaSide'] ?? 'left') === 'right';
if ($title === '' && $text === '' && $img === '') { return; }
$tag = $url !== '' ? 'a' : 'div';
$wrap = get_block_wrapper_attributes(array('class' => 'media-card' . ($right ? ' is-media-right' : ''), 'data-reveal' => ''));
$open = '<' . $tag . ' ' . $wrap . ($url !== '' ? ' href="' . esc_url($url) . '"' : '') . '>';
echo $open; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tag name is a fixed a/div choice; attributes escaped 
?>
<span class="media-card-media"><?php if ($img !== '') : ?><img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($alt); ?>" loading="lazy" decoding="async"><?php else : ?><span class="wugweb-ph" aria-hidden="true"></span><?php endif; ?></span>
<span class="media-card-body">
  <?php if ($eyebrow !== '') : ?><span class="media-card-eyebrow"><?php echo esc_html($eyebrow); ?></span><?php endif; ?>
  <?php if ($title !== '') : ?><span class="media-card-title"><?php echo esc_html($title); ?></span><?php endif; ?>
  <?php if ($text !== '') : ?><span class="media-card-text"><?php echo esc_html($text); ?></span><?php endif; ?>
  <?php if ($cta !== '') : ?><span class="media-card-cta"><?php echo esc_html($cta); ?> <span aria-hidden="true">&rarr;</span></span><?php endif; ?>
</span>
<?php echo '</' . $tag . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- tag name is a fixed a/div choice; attributes escaped 
