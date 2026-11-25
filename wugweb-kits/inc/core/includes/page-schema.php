<?php
/** Page management schema: every column of the Notion "Pages" table, stored as page meta and shown as list columns. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function wugweb_core_page_fields() {
	return array(
		'page_id' => 'string', 'route' => 'string', 'language' => 'string', 'schema_type' => 'string',
		'search_intent' => 'string', 'seo_score' => 'string', 'seo_title' => 'string', 'seo_description' => 'string',
		'focus_keyword' => 'string', 'secondary_keywords' => 'string', 'canonical_url' => 'string', 'no_index' => 'boolean',
		'og_title' => 'string', 'og_description' => 'string', 'og_image' => 'string', 'hero_path' => 'string',
		'media_paths' => 'string', 'ai_summary' => 'string', 'key_takeaways' => 'string', 'breadcrumb_label' => 'string',
		'http_status' => 'string', 'redirect_to' => 'string', 'last_reviewed_at' => 'string', 'sync_status' => 'string', 'sanity_id' => 'string',
	);
}

add_action( 'init', function () {
	add_post_type_support( 'page', 'custom-fields' );
	foreach ( wugweb_core_page_fields() as $k => $t ) {
		register_post_meta( 'page', 'wugweb_page_' . $k, array( 'type' => $t, 'single' => true, 'show_in_rest' => true ) );
	}
} );

add_action( 'admin_init', function () {
	$pg = array();
	foreach ( wugweb_core_page_fields() as $k => $t ) { $pg[ 'wugweb_page_' . $k ] = $t; }
	wugweb_admin_post_columns( 'page', 'wugweb_page_', $pg, array( 'wugweb_page_route', 'wugweb_page_seo_title', 'wugweb_page_http_status', 'wugweb_page_no_index' ) );
} );
