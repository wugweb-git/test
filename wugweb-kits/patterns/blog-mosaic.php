<?php
/**
 * Title: Blog Mosaic
 * Slug: wugweb-kits/blog-mosaic
 * Categories: featured
 * Description: Blog index mosaic on GenerateBlocks grids (columns per breakpoint are editable in the block editor); cards are real posts / case studies.
 * Keywords: blog,mosaic,featured
 * Inserter: no
 */
defined('ABSPATH') || exit;
if (!function_exists('wugweb_gb_grid')) {
  /** GenerateBlocks 2 element used as a responsive grid. $cols = array(breakpoint-query|'' => grid-template-columns). */
  function wugweb_gb_grid($uid, $class, $cols, $gap, $inner) {
    $styles = array('display' => 'grid', 'gap' => $gap, 'alignItems' => 'start'); $css = '';
    $rules = array('display:grid', 'gap:' . $gap, 'align-items:start');
    foreach ($cols as $mq => $tpl) {
      if ($mq === '') { $styles['gridTemplateColumns'] = $tpl; $rules[] = 'grid-template-columns:' . $tpl; }
      else { $styles['@media ' . $mq] = array('gridTemplateColumns' => $tpl); }
    }
    $css = '.gb-element-' . $uid . '{' . implode(';', $rules) . '}';
    foreach ($cols as $mq => $tpl) { if ($mq !== '') { $css .= '@media ' . $mq . '{.gb-element-' . $uid . '{grid-template-columns:' . $tpl . '}}'; } }
    return '<!-- wp:generateblocks/element ' . wp_json_encode(array('uniqueId' => $uid, 'tagName' => 'div', 'styles' => $styles, 'css' => $css, 'className' => $class), JSON_UNESCAPED_SLASHES) . ' --><div class="gb-element-' . $uid . ' ' . $class . '">' . $inner . '<!-- /wp:generateblocks/element -->'; 
  }
  function wugweb_blog_slot($slot) { return '<!-- wp:wugweb/blog-cards ' . wp_json_encode(array('slot' => $slot)) . ' /-->'; }
}
$one = 'minmax(0,1fr)';
echo wugweb_gb_grid('b10feat1', 'featured-grid', array('' => $one, '(min-width:1100px)' => 'minmax(0,7fr) minmax(0,5fr)'), 'var(--gap-10)', wugweb_blog_slot('featured') . wugweb_blog_slot('list') . '</div>'); // phpcs:ignore
echo wugweb_blog_slot('strip'); // phpcs:ignore
echo wugweb_gb_grid('b10pick1', 'picks-grid', array('' => $one, '(min-width:1100px)' => 'minmax(0,5fr) minmax(0,7fr)'), 'var(--gap-10)', wugweb_blog_slot('picks-rows') . wugweb_blog_slot('picks-big') . '</div>'); // phpcs:ignore
echo wugweb_gb_grid('b10case1', 'blog-work', array('' => $one), 'var(--grid-gap)', wugweb_blog_slot('cases') . '</div>'); // phpcs:ignore
echo wugweb_blog_slot('newsletter'); // phpcs:ignore
