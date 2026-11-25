<?php
/** Editorial Card: PostCard.astro port. Renders the current post via the shared PostCard helper. */
$id = get_the_ID();
if (!$id || !function_exists('wugweb_post_card_data')) { return; }
echo '<div ' . get_block_wrapper_attributes() . '>' . wugweb_post_card_html(wugweb_post_card_data($id)) . '</div>';
