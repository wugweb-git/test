<?php
$items = array();
$icons = json_decode((string) file_get_contents(__DIR__ . '/../../includes/lucide-sector-icons.json'), true) ?: array();
$sectors = array();
foreach ((array) get_terms(array('taxonomy' => 'sector', 'hide_empty' => false)) as $t) {
    $d = json_decode((string) get_term_meta($t->term_id, 'wugweb_notion_data', true), true) ?: array();
    if (empty($d['show'])) continue;
    $sectors[] = array('t' => $t, 'order' => (int) ($d['order'] ?? 99), 'icon' => $d['icon'] ?? '');
}
usort($sectors, function ($a, $b) { return $a['order'] <=> $b['order']; });
$inds = (array) get_terms(array('taxonomy' => 'industry', 'hide_empty' => false, 'orderby' => 'name'));
foreach ($sectors as $s) {
    $names = array();
    foreach ($inds as $in) {
        $d = json_decode((string) get_term_meta($in->term_id, 'wugweb_notion_data', true), true) ?: array();
        if (!empty($d['show_on_sector_card']) && in_array($s['t']->slug, (array) ($d['sector'] ?? array()), true)) $names[] = html_entity_decode($in->name, ENT_QUOTES);
    }
    $items[] = array(html_entity_decode($s['t']->name, ENT_QUOTES), $s['t']->slug, implode(' · ', array_slice($names, 0, 2)), $icons[$s['icon']] ?? '');
};
$heading = $attributes['heading'] ?? 'We understand the context behind the product.';
$intro = $attributes['intro'] ?? 'From regulated sectors to fast-moving consumer markets, our experience spans industries that demand precision and creativity in equal measure.';
$cta = $attributes['buttonLabel'] ?? 'Explore All Industries'; $href = $attributes['buttonUrl'] ?? '/industries/';
?><section <?php echo get_block_wrapper_attributes(array('class' => 'sectors-section', 'id' => 'sectors', 'aria-labelledby' => 'sectors-title')); ?>><div class="wrap">
<div class="sectors-head"><h2 id="sectors-title" data-reveal><?php echo esc_html($heading); ?></h2><?php if ($intro): ?><p class="sectors-intro" data-reveal><?php echo esc_html($intro); ?></p><?php endif; ?></div>
<ul class="sectors-grid" role="list"><?php foreach ($items as $i => $c): ?><li class="sectors-item" data-reveal data-reveal-delay="<?php echo esc_attr((string) (min($i, 7) * 0.05)); ?>"><a class="sector-card" href="<?php echo esc_url($href . '#' . $c[1]); ?>"><div class="sector-icon-tile"><?php echo $c[3]; // phpcs:ignore ?></div><div class="sector-text"><h3 class="sector-name"><?php echo esc_html($c[0]); ?></h3><p class="sector-industries"><?php echo esc_html($c[2]); ?></p></div></a></li><?php endforeach; ?></ul>
<?php if ($cta && $href): ?><div class="sectors-cta"><a href="<?php echo esc_url($href); ?>" class="btn btn-ghost"><?php echo esc_html($cta); ?></a></div><?php endif; ?>
</div></section>
