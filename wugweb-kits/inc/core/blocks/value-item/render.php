<?php
require_once get_template_directory() . '/inc/core/includes/value-card-html.php';
echo '<div ' . get_block_wrapper_attributes(array('class' => 'values-list', 'style' => 'margin:0;display:block')) . '>' . wugweb_value_card_html((string) ($attributes['n'] ?? ''), (string) ($attributes['title'] ?? ''), (string) ($attributes['copy'] ?? '')) . '</div>';
