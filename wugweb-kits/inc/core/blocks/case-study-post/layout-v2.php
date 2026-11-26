<?php
/**
 * Case-study layout v2 renderer: the Astro case-page (src/components/work/Case*.astro) as PHP.
 * Included from render.php only when wugweb_case_layout_v2_active(); $id is the case study's post id.
 * Markup, class names and element order follow the Astro components so layout-v2.css applies unchanged.
 */
defined('ABSPATH') || exit;
require_once __DIR__ . '/layout-v2-resolver.php';
require_once __DIR__ . '/layout-v2-components.php';
require_once __DIR__ . '/layout-v2-media.php';
require_once __DIR__ . '/layout-v2-media-components.php';
if (function_exists('wugweb_case_layout_v2_enqueue')) { wugweb_case_layout_v2_enqueue(); } // normally already in <head>; this covers a block used outside the single template

$wugweb_cl2 = wugweb_cl2_resolve(wugweb_cl2_collect($id));
if (wugweb_cl3_active() && wugweb_cl3_has_script_blocks($wugweb_cl2)) { wugweb_cl3_enqueue_script(); } // rail, chips, viewer and loops need the small script

// Group like CaseBlocks.astro: a chapter owns the blocks that carry its chapterId; everything else sits beside it.
$wugweb_cl2_groups = array(); $wugweb_cl2_cur = null;
foreach ($wugweb_cl2 as $b) {
    if ($b['type'] === 'chapter') { if ($wugweb_cl2_cur) { $wugweb_cl2_groups[] = $wugweb_cl2_cur; } $wugweb_cl2_cur = array('chapter' => $b, 'items' => array()); continue; }
    if (!empty($b['chapterId']) && $wugweb_cl2_cur && $wugweb_cl2_cur['chapter']['id'] === $b['chapterId']) { $wugweb_cl2_cur['items'][] = $b; continue; }
    if ($wugweb_cl2_cur) { $wugweb_cl2_groups[] = $wugweb_cl2_cur; $wugweb_cl2_cur = null; }
    $wugweb_cl2_groups[] = array('solo' => $b);
}
if ($wugweb_cl2_cur) { $wugweb_cl2_groups[] = $wugweb_cl2_cur; }
?>
<div <?php echo get_block_wrapper_attributes(array('class' => 'wugweb-case-study-v2')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escapes ?>>
<section class="case-page" aria-label="Case study">
<?php foreach ($wugweb_cl2_groups as $g) :
    if (isset($g['solo'])) { echo wugweb_cl2_block($g['solo']); continue; } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- every value is escaped inside wugweb_cl2_block()
    $c = $g['chapter']; ?>
<article class="case-chapter-wrap" aria-labelledby="<?php echo esc_attr($c['id']); ?>-head">
  <article class="case-chapter" id="<?php echo esc_attr($c['id']); ?>" aria-labelledby="<?php echo esc_attr($c['id']); ?>-head">
    <header class="case-chapter-head" data-reveal>
      <h2 class="case-chapter-title" id="<?php echo esc_attr($c['id']); ?>-head"><?php echo esc_html($c['title']); ?></h2>
      <?php if ($c['lead'] !== '') : ?><p class="case-chapter-lead"><?php echo wugweb_cl2_inline($c['lead']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in wugweb_cl2_inline() ?></p><?php endif; ?>
    </header>
    <?php if ($c['paras']) : ?><div class="case-chapter-body" data-reveal><?php foreach ($c['paras'] as $p) { echo '<p class="case-chapter-para">' . wugweb_cl2_inline($p) . '</p>'; } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in wugweb_cl2_inline() ?></div><?php endif; ?>
  </article>
  <?php foreach ($g['items'] as $it) { echo wugweb_cl2_block($it); } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- every value is escaped inside wugweb_cl2_block() ?>
</article>
<?php endforeach; ?>
</section>
</div>
