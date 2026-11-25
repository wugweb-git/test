<?php
if (!defined('ABSPATH')) { exit; }
$pillars = array(
 array('slug'=>'strategy','label'=>'Strategy','summary'=>'Product, Brand & Growth Architecture','base'=>'#FA4108','light'=>'#FF8F66'),
 array('slug'=>'brand','label'=>'Brand','summary'=>'Identity Systems & Narrative Design','base'=>'#E6309F','light'=>'#F17CC3'),
 array('slug'=>'experience','label'=>'Experience','summary'=>'UI/UX, Digital Platforms & Design Systems','base'=>'#09E2C0','light'=>'#6EF0DC'),
 array('slug'=>'engineering','label'=>'Engineering','summary'=>'Web, Mobile App & Platform Modernization','base'=>'#2167E9','light'=>'#7FA8F3'),
 array('slug'=>'growth','label'=>'Growth','summary'=>'Martech, CRO & Performance Engineering','base'=>'#3ED219','light'=>'#9BE97F'),
 array('slug'=>'operations','label'=>'Operations','summary'=>'Managed Application Support & Reliability','base'=>'#7A4DFF','light'=>'#B49CFF'),
);
$cta_url = esc_url($attributes['ctaUrl'] ?? '#');
$context = array('activeSlug'=>'strategy','selectedSlugs'=>array(),'pillars'=>$pillars,'baseCtaUrl'=>$cta_url);
$attrs = get_block_wrapper_attributes(array('class'=>'wugweb-pillar-scope','data-wp-interactive'=>'wugweb/pillar-scope','data-wp-context'=>wp_json_encode($context),'style'=>'--svc-base:#FA4108;--svc-light:#FF8F66;'));
?>
<div <?php echo $attrs; ?>><div class="wugweb-pillar-tabs" role="tablist" aria-label="Service Pillars"><?php foreach ($pillars as $pillar) : ?><button type="button" role="tab" class="wugweb-pillar-tab" data-slug="<?php echo esc_attr($pillar['slug']); ?>" data-base="<?php echo esc_attr($pillar['base']); ?>" data-light="<?php echo esc_attr($pillar['light']); ?>" data-wp-on--click="actions.activatePillar" data-wp-bind--aria-selected="state.isActive"><?php echo esc_html($pillar['label']); ?></button><?php endforeach; ?></div><div class="wugweb-pillar-active-card"><p class="wugweb-pillar-active-title" data-wp-text="state.activePillar.label"></p><p class="wugweb-pillar-active-summary" data-wp-text="state.activePillar.summary"></p><code class="wugweb-pillar-tokens" data-wp-text="state.tokenReadout"></code></div><div class="wugweb-scope-grid"><div class="wugweb-scope-header"><span>Select Scope Pillars</span><span data-wp-text="state.selectedCountLabel"></span></div><div class="wugweb-scope-items"><?php foreach ($pillars as $pillar) : ?><button type="button" class="wugweb-scope-chip" data-slug="<?php echo esc_attr($pillar['slug']); ?>" data-wp-on--click="actions.toggleScope" data-wp-class--is-selected="state.isSelected"><span class="wugweb-scope-label"><?php echo esc_html($pillar['label']); ?></span><span class="wugweb-scope-desc"><?php echo esc_html($pillar['summary']); ?></span></button><?php endforeach; ?></div><div class="wugweb-scope-footer"><a class="wugweb-scope-cta" data-wp-bind--href="state.scopedCtaUrl">Start with Selected Scope</a></div></div></div>
