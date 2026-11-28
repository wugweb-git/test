<?php
/**
 * Taxonomy: one top-level admin menu group that holds every taxonomy once, and one shared structure for all content types
 * (posts and case studies carry the same taxonomies). Menu entries and post-type attachment only: no URLs or data change.
 * (Restores the grouping the retired Content Web plugin provided; the taxonomies now come from acf-json.)
 */
defined( 'ABSPATH' ) || exit;

function wugweb_master_taxonomies() {
	// taxonomy => menu label. The list screen opens under case_study when the taxonomy is attached to it, else post.
	return array(
		'category'      => 'Categories',
		'post_tag'      => 'Tags',
		'client'        => 'Clients',
		'industry'      => 'Industry',
		'sector'        => 'Sectors',
		'country'       => 'Countries',
		'discipline'    => 'Discipline',
		'tool'          => 'Tool Stack',
		'service_group' => 'Service Sub-Groups',
		'platform'      => 'Platforms',
		'project_type'  => 'Project Types',
	);
}

// Hide the per-post-type menu entries (each taxonomy keeps its post types).
add_filter( 'register_taxonomy_args', function ( $args, $taxonomy ) {
	if ( isset( wugweb_master_taxonomies()[ $taxonomy ] ) ) { $args['show_in_menu'] = false; }
	return $args;
}, 10, 2 );

function wugweb_master_post_type( $tax ) {
	$t = get_taxonomy( $tax );
	return ( $t && in_array( 'case_study', (array) $t->object_type, true ) ) ? 'case_study' : 'post';
}

// One structure for every content type: each custom taxonomy is available on posts and on case studies alike (categories and tags stay on posts).
add_action( 'init', function () {
	foreach ( array_keys( wugweb_master_taxonomies() ) as $tax ) {
		if ( in_array( $tax, array( 'category', 'post_tag' ), true ) || ! taxonomy_exists( $tax ) ) { continue; }
		foreach ( array( 'post', 'case_study' ) as $type ) {
			if ( post_type_exists( $type ) ) { register_taxonomy_for_object_type( $tax, $type ); }
		}
	}
}, 99 );

add_action( 'admin_menu', function () {
	$cap = 'manage_categories';
	add_menu_page( 'Taxonomy', 'Taxonomy', $cap, 'wugweb-taxonomy', '__return_null', 'dashicons-database', 58 );
	foreach ( wugweb_master_taxonomies() as $tax => $label ) {
		if ( taxonomy_exists( $tax ) ) {
			add_submenu_page( 'wugweb-taxonomy', $label, $label, $cap, 'edit-tags.php?taxonomy=' . $tax . '&post_type=' . wugweb_master_post_type( $tax ) );
		}
	}
	remove_submenu_page( 'wugweb-taxonomy', 'wugweb-taxonomy' ); // the group opens on its first taxonomy, no empty landing page
}, 99 );

// Keep the Taxonomy group open and the right entry highlighted on its taxonomy screens.
add_filter( 'parent_file', function ( $parent ) {
	global $current_screen;
	$tax = isset( $current_screen->taxonomy ) ? $current_screen->taxonomy : '';
	return ( $tax && isset( wugweb_master_taxonomies()[ $tax ] ) ) ? 'wugweb-taxonomy' : $parent;
} );
add_filter( 'submenu_file', function ( $submenu_file ) {
	global $current_screen;
	$tax = isset( $current_screen->taxonomy ) ? $current_screen->taxonomy : '';
	if ( $tax && isset( wugweb_master_taxonomies()[ $tax ] ) ) {
		return 'edit-tags.php?taxonomy=' . $tax . '&post_type=' . wugweb_master_post_type( $tax );
	}
	return $submenu_file;
} );
