<?php
/** Case Study Brief Matrix: components/work/CaseHero.astro meta list (dt/dd pairs). */
$id = get_the_ID();
if (!$id) { return; }
$m = function ($k) use ($id) { return trim((string) get_post_meta($id, 'wugweb_cs_' . $k, true)); };
$terms = function ($tax) use ($id) {
    $t = get_the_terms($id, $tax);
    return ($t && !is_wp_error($t)) ? implode(', ', wp_list_pluck($t, 'name')) : '';
};
$industry = $m('industry'); if ($industry === '') { $industry = $terms('industry'); }
$disc = array_filter(array_unique(array_map('trim', array_merge(explode(',', $m('primary_discipline')), explode(',', $m('additional_disciplines'))))));
$disciplines = $disc ? implode(', ', $disc) : $terms('discipline');
$meta = array(
    'Client' => $m('client'),
    'Partner' => $m('partner'),
    'Industry' => $industry,
    'Disciplines' => $disciplines,
    'Team' => $m('team'),
    'Special Thanks' => $m('special_thanks'),
);
$meta = array_filter($meta, function ($v) { return $v !== ''; });
if (!$meta) { return; }
?><dl <?php echo get_block_wrapper_attributes(array('class' => 'case-hero-meta')); ?>><?php foreach ($meta as $label => $value): ?><dt><?php echo esc_html($label); ?></dt><dd><?php echo esc_html($value); ?></dd><?php endforeach; ?></dl>
