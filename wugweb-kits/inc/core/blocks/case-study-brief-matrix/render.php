<?php
/** Case Study Brief Matrix: pages/work/[slug].astro .work-meta (Client / Partnered by / Timeline / Disciplines / Industry / Team). */
$id = wugweb_block_post_id($block ?? null, $attributes, array('case_study'));
if (!$id) { return; }
$m = function ($k) use ($id) { return trim((string) get_post_meta($id, 'wugweb_cs_' . $k, true)); };
$terms = function ($tax) use ($id) { $t = get_the_terms($id, $tax); return ($t && !is_wp_error($t)) ? implode(', ', wp_list_pluck($t, 'name')) : ''; };
$industry = $m('industry'); if ($industry === '') { $industry = $terms('industry'); }
$disc = array_filter(array_unique(array_map('trim', array_merge(explode(',', $m('primary_discipline')), explode(',', $m('additional_disciplines'))))));
$disciplines = $disc ? implode(', ', $disc) : $terms('discipline');
$client = wugweb_cs_client($id);
$logo = wugweb_cs_client_logo($id);
$rows = array('Partnered by' => ($m('partner') !== 'None' ? $m('partner') : ''), 'Timeline' => $m('timeline'), 'Disciplines' => $disciplines, 'Industry' => $industry, 'Team' => $m('team'));
 // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core ?><div <?php echo get_block_wrapper_attributes(array('class' => 'work-meta')); ?>><div><span class="meta-label">Client / Brand</span><span class="work-meta-client"><?php if ($logo): ?><img src="<?php echo esc_url($logo); ?>" alt="" class="work-client-logo" loading="eager"><?php endif; ?><?php echo esc_html($client !== '' ? $client : '—'); ?></span></div><?php foreach ($rows as $label => $value): if ($value === '') continue; ?><div><span class="meta-label"><?php echo esc_html($label); ?></span><span><?php echo esc_html($value); ?></span></div><?php endforeach; ?><?php foreach (array('Tool Stack' => wugweb_tool_chips_html($id, 'tool'), 'Platform' => wugweb_tool_chips_html($id, 'platform')) as $chip_label => $chip_html) : if ($chip_html === '') continue; ?><div><span class="meta-label"><?php echo esc_html($chip_label); ?></span><?php echo $chip_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built with esc_url/esc_html ?></div><?php endforeach; ?></div>
