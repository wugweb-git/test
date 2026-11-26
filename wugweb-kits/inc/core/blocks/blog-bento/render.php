<?php
/** Blog Bento: choices for the blog mosaic (post IDs, comma-separated, in the sidebar; blank = automatic). On a page that also holds the Blog Index block, that block shows the mosaic with these choices and this block prints nothing; on any other page it renders the mosaic itself. */
defined('ABSPATH') || exit;
require_once dirname(__DIR__) . '/blog-cards/data.php';
if (defined('REST_REQUEST') && REST_REQUEST) { echo '<p style="padding:1rem;border:1px dashed currentColor;border-radius:8px">Blog mosaic: choices set here apply to the Blog Index on this page.</p>'; return; }
$q = get_queried_object();
if ($q instanceof WP_Post && has_block('wugweb/blog-index', $q)) { return; }
if (!isset($GLOBALS['wugweb_blog_bento'])) { $GLOBALS['wugweb_blog_bento'] = (array) $attributes; }
if (!wugweb_blog_showcase()) { return; }
wp_enqueue_style('wugweb-blog-index-style');
echo '<div class="wugweb-blog-index wugweb-blog-bento"><div class="blog-extra">';
ob_start(); include get_theme_file_path('patterns/blog-mosaic.php'); echo do_blocks(ob_get_clean()); // phpcs:ignore
echo '</div></div>';
