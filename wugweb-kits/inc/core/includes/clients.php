<?php
/**
 * Client helpers. Clients are the `client` taxonomy (defined in acf-json); a client can have child terms
 * (a product or sub-brand). A case study is assigned to its most specific client term.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Client terms of a case study (most specific first as stored). */
function wugweb_cs_client_terms( $post_id ) {
	$t = get_the_terms( (int) $post_id, 'client' );
	return is_array( $t ) ? $t : array();
}

/** Display name of a case study's client(s): "Airtel XSafe", or "Adtech & iMedia" when it has two. */
function wugweb_cs_client( $post_id ) {
	$n = array();
	foreach ( wugweb_cs_client_terms( $post_id ) as $t ) { $n[] = html_entity_decode( $t->name, ENT_QUOTES ); }
	return implode( ' & ', $n );
}

/** Logo URL of a case study's client: the term's own logo, else the nearest parent that has one. */
function wugweb_cs_client_logo( $post_id ) {
	foreach ( wugweb_cs_client_terms( $post_id ) as $t ) {
		$cur = $t;
		while ( $cur && ! is_wp_error( $cur ) ) {
			$u = wugweb_icon_url( 'term', $cur->term_id );
			if ( $u ) { return $u; }
			$cur = $cur->parent ? get_term( $cur->parent, 'client' ) : null;
		}
	}
	return '';
}

/** Number of clients: top-level client terms (products and sub-brands sit under their company). */
function wugweb_client_count() {
	$ids = get_terms( array( 'taxonomy' => 'client', 'hide_empty' => false, 'parent' => 0, 'fields' => 'ids' ) );
	return is_wp_error( $ids ) ? 0 : count( $ids );
}
