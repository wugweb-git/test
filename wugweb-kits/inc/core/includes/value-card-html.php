<?php
if (!function_exists('wugweb_value_card_html')) { function wugweb_value_card_html($n, $title, $copy) { return '<div class="value-card"><span class="value-n">' . esc_html($n) . '</span><h3>' . esc_html($title) . '</h3>' . ($copy !== '' ? '<p>' . esc_html($copy) . '</p>' : '') . '</div>'; } }
