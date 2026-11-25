<?php
/** Copyright Year: SiteFooter.astro .footer-copy; end year is live. */
$year = gmdate('Y');
?><p <?php echo get_block_wrapper_attributes(array('class' => 'footer-copy')); ?>><?php echo esc_html('© 2020 to ' . $year . ' Wugweb. All rights reserved.'); ?></p>
