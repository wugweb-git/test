<?php
/** Portfolio Card: pages/index.astro #work item via the shared wugweb_work_card_html(). */
$id = wugweb_block_post_id($block ?? null, $attributes, array('case_study'));
$c = array('client' => $attributes['eyebrow'] ?? '', 'title' => $attributes['title'] ?? '', 'url' => $attributes['url'] ?? '#', 'media' => $attributes['imageUrl'] ?? '');
if ($id) { $f = wugweb_fields_case_study($id); $c = array('client' => $f['client'] . ' — ' . $f['discipline'], 'title' => $f['title'], 'url' => $f['url'], 'media' => $f['image']); }
echo '<ul ' . get_block_wrapper_attributes(array('class' => 'work-grid')) . '>' . wugweb_work_card_html($c) . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core 
