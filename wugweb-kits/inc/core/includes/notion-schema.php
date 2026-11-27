<?php
/**
 * Notion master data: client post type, taxonomies and case study / client fields.
 * Fields are post meta / term meta exposed in REST so they appear in the editor and API.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function wugweb_core_client_fields() {
	return array(
  'client_id'=>'string',
  'entity_type'=>'string',
  'logo'=>'string',
  'website'=>'string',
  'country_legacy'=>'string',
  'year_first_worked'=>'integer',
  'date'=>'string',
  'engagements'=>'string',
  'partnered_by'=>'string',
  'projects'=>'string',
  'case_studies'=>'string',
  'blocked_by'=>'string',
  'blocking'=>'string',
  'status'=>'string'
	);
}
function wugweb_core_cs_fields() {
	return array(
  'case_study_id'=>'string',
  'client'=>'string',
  'featured'=>'boolean',
  'primary_discipline'=>'string',
  'additional_disciplines'=>'string',
  'engagement_type'=>'string',
  'project_type'=>'string',
  'platform'=>'string',
  'partner'=>'string',
  'team'=>'string',
  'special_thanks'=>'string',
  'timeline'=>'string',
  'year'=>'string',
  'country'=>'string',
  'live_url'=>'string',
  'prototype_url'=>'string',
  'seo_score'=>'string',
  'subtitle'=>'string',
  'short_title'=>'string',
  'ai_summary'=>'string',
  'ai_keywords'=>'string',
  'results'=>'string',
  'word_count'=>'integer',
  'read_time'=>'integer',
  'cover_image'=>'string',
  'client_logo'=>'string',
  'assets'=>'string',
  'categories'=>'string',
  'services'=>'string',
  'industry'=>'string',
  'sector'=>'string',
  'related_posts'=>'string',
  'related_cases'=>'string',
  'owner'=>'string',
  'date_last_updated_start'=>'string',
  'publications'=>'string',
  'published_at'=>'string',
  'filter_category'=>'string',
	);
}

/**
 * Icon / logo for a term, client or other post: meta `wugweb_icon_path` (R2 path such as assets/..., or a full URL).
 * Term meta for taxonomies, post meta for clients. Clients fall back to the older `wugweb_client_logo`. Returns '' when unset.
 */
function wugweb_icon_url( $kind, $id ) {
	$p = trim( (string) ( 'term' === $kind ? get_term_meta( (int) $id, 'wugweb_icon_path', true ) : get_post_meta( (int) $id, 'wugweb_icon_path', true ) ) );
	if ( '' === $p && 'post' === $kind ) { $p = trim( (string) get_post_meta( (int) $id, 'wugweb_client_logo', true ) ); }
	if ( '' === $p ) { return ''; }
	if ( preg_match( '#^https?://#i', $p ) ) { return $p; }
	return function_exists( 'wugweb_r2_key_url' ) ? wugweb_r2_key_url( $p ) : $p;
}

/**
 * Tool and platform chips for a case study: small icon (wugweb_icon_path, alt="" as the name sits beside it) + name.
 * Tools come from the tool terms; platforms from the platform meta text matched to platform terms. A missing icon shows the name only.
 * Returns an HTML string (escaped), '' when there is nothing to show.
 */
function wugweb_tool_chips_html( $id, $kind = 'tool', $limit = 0 ) {
	$names = array();
	if ( 'tool' === $kind ) {
		// $id may be an array of post ids: the union of their tools (first $limit).
		$seen = array();
		foreach ( (array) $id as $one ) {
			$t = get_the_terms( (int) $one, 'tool' );
			foreach ( (array) ( $t && ! is_wp_error( $t ) ? $t : array() ) as $term ) { if ( ! isset( $seen[ $term->term_id ] ) ) { $seen[ $term->term_id ] = 1; $names[] = $term; } }
		}
		if ( $limit > 0 ) { $names = array_slice( $names, 0, $limit ); }
	} else {
		foreach ( preg_split( '/\s*,\s*/', (string) get_post_meta( $id, 'wugweb_cs_platform', true ), -1, PREG_SPLIT_NO_EMPTY ) as $nm ) {
			$term = get_term_by( 'name', $nm, 'platform' );
			$names[] = $term ? $term : (object) array( 'name' => $nm, 'term_id' => 0 );
		}
	}
	if ( ! $names ) { return ''; }
	$o = '<ul class="wugweb-chip-list">';
	foreach ( $names as $term ) {
		$icon = $term->term_id ? wugweb_icon_url( 'term', $term->term_id ) : '';
		$o .= '<li class="wugweb-chip">' . ( $icon ? '<img src="' . esc_url( $icon ) . '" alt="" width="16" height="16" loading="lazy" decoding="async">' : '' ) . '<span>' . esc_html( html_entity_decode( $term->name, ENT_QUOTES ) ) . '</span></li>';
	}
	return $o . '</ul>';
}

/** Lower-case alphanumerics only, so "Next.js", "next js" and "NextJS" compare equal. */
function wugweb_tool_key( $s ) { return preg_replace( '/[^a-z0-9]+/', '', strtolower( html_entity_decode( (string) $s, ENT_QUOTES ) ) ); }

/**
 * Icon URL for a displayed tool/platform NAME: matches a `tool` (then `platform`) term on name, then slug,
 * case and punctuation insensitive, and reads wugweb_icon_path from it. '' when no term or no icon.
 */
function wugweb_tool_icon_by_name( $name ) {
	static $map = null;
	if ( null === $map ) {
		$map = array();
		foreach ( array( 'platform', 'tool' ) as $tax ) { // tool last so it wins on a clash
			$terms = get_terms( array( 'taxonomy' => $tax, 'hide_empty' => false ) );
			foreach ( (array) ( is_wp_error( $terms ) ? array() : $terms ) as $t ) {
				$u = wugweb_icon_url( 'term', $t->term_id );
				if ( '' === $u ) { continue; }
				$map[ wugweb_tool_key( $t->name ) ] = $u;
				$k = wugweb_tool_key( $t->slug ); if ( ! isset( $map[ $k ] ) ) { $map[ $k ] = $u; }
			}
		}
	}
	return $map[ wugweb_tool_key( $name ) ] ?? '';
}

/** Chip list for a list of displayed tool names (icon matched by name; none = name only). Escaped HTML, '' for no names. */
function wugweb_tool_name_chips_html( $names, $limit = 0 ) {
	$names = array_values( array_unique( array_filter( array_map( 'trim', (array) $names ) ) ) );
	if ( $limit > 0 ) { $names = array_slice( $names, 0, $limit ); }
	if ( ! $names ) { return ''; }
	$o = '<ul class="wugweb-chip-list">';
	foreach ( $names as $nm ) {
		$icon = wugweb_tool_icon_by_name( $nm );
		$o .= '<li class="wugweb-chip">' . ( $icon ? '<img src="' . esc_url( $icon ) . '" alt="" width="16" height="16" loading="lazy" decoding="async">' : '' ) . '<span>' . esc_html( $nm ) . '</span></li>';
	}
	return $o . '</ul>';
}

add_action( 'init', function () {
	add_post_type_support( 'case_study', 'custom-fields' );
	add_post_type_support( 'case_study', 'excerpt' );
	register_post_type( 'client', array(
		'labels' => array( 'name' => 'Clients', 'singular_name' => 'Client' ),
		'public' => false, 'show_ui' => true, 'show_in_rest' => true, 'rest_base' => 'client',
		'menu_icon' => 'dashicons-businessperson', 'has_archive' => false, 'rewrite' => false,
		'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields', 'excerpt' ),
		'taxonomies' => array( 'industry', 'country', 'sector' ),
	) );

	$tax = array(
		'sector' => array( 'Sectors', array( 'client', 'case_study' ), true ),
		'country' => array( 'Countries', array( 'client', 'case_study' ), true ),
		'platform' => array( 'Platforms', array(), true ),
		'tool' => array( 'Tool Stack', array( 'case_study' ), true ),
		'asset_type' => array( 'Asset Types', array(), false ),
		'content_type' => array( 'Content Types', array(), false ),
		'department' => array( 'Departments', array(), false ),
		'owner' => array( 'Owners', array( 'post', 'case_study' ), false ),
		'service_group' => array( 'Service Sub-Groups', array(), false ),
	);
	foreach ( $tax as $slug => $t ) {
		register_taxonomy( $slug, $t[1], array(
			'labels' => array( 'name' => $t[0], 'singular_name' => rtrim( $t[0], 's' ) ),
			'public' => $t[2], 'publicly_queryable' => false, 'show_ui' => true, 'show_in_rest' => true,
			'rest_base' => $slug, 'hierarchical' => false, 'rewrite' => false, 'show_admin_column' => true,
		) );
	}
	// Tags are not used on case studies or clients; Categories/Tags stay on Posts only. Detach only: terms stay in the database.
	foreach ( array( 'industry', 'discipline' ) as $s ) {
		register_taxonomy_for_object_type( $s, 'case_study' );
	}
	foreach ( array( 'industry', 'sector' ) as $s ) { register_taxonomy_for_object_type( $s, 'client' ); }
	foreach ( array( 'industry', 'service_group' ) as $s ) { register_taxonomy_for_object_type( $s, 'post' ); }

	foreach ( array_merge( array_keys( $tax ), array( 'industry', 'discipline', 'post_tag' ) ) as $s ) {
		foreach ( array( 'notion_url', 'notion_data' ) as $m ) {
			register_term_meta( $s, 'wugweb_' . $m, array( 'type' => 'string', 'single' => true, 'show_in_rest' => true ) );
		}
	}

	foreach ( array_merge( array_keys( $tax ), array( 'industry', 'discipline' ) ) as $s ) {
		register_term_meta( $s, 'wugweb_icon_path', array( 'type' => 'string', 'single' => true, 'show_in_rest' => true, 'sanitize_callback' => 'sanitize_text_field' ) );
	}
	register_post_meta( 'client', 'wugweb_icon_path', array( 'type' => 'string', 'single' => true, 'show_in_rest' => true, 'sanitize_callback' => 'sanitize_text_field' ) );

	foreach ( wugweb_core_client_fields() as $k => $t ) {
		register_post_meta( 'client', 'wugweb_client_' . $k, array( 'type' => $t, 'single' => true, 'show_in_rest' => true ) );
	}
	foreach ( wugweb_core_cs_fields() as $k => $t ) {
		register_post_meta( 'case_study', 'wugweb_cs_' . $k, array( 'type' => $t, 'single' => true, 'show_in_rest' => true ) );
	}
}, 30 );
