<?php
/** Presentation helpers: icon URL for a term/client and tool chips. Data (taxonomies, meta) is registered by the Content Web plugin. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

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
