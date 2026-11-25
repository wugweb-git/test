<?php
$post = get_post();
$words = $post ? str_word_count(wp_strip_all_tags($post->post_content)) : 0;
$minutes = max(1, (int) ceil($words / 200));
?><span <?php echo get_block_wrapper_attributes(array('class' => 'wugweb-read-time')); ?>><?php echo esc_html($minutes . ' min read'); ?></span>
