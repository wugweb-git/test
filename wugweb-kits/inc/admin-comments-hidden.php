<?php
/**
 * The site has no use for comments: hide the Comments menu and the admin-bar bubble.
 * Menu/toolbar entries only; the comments system itself and stored data are untouched.
 */
defined('ABSPATH') || exit;

add_action('admin_menu', function () { remove_menu_page('edit-comments.php'); }, 999);
add_action('admin_bar_menu', function ($bar) { $bar->remove_node('comments'); }, 999);
