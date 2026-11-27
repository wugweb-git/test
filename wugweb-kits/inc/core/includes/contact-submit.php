<?php
/**
 * Contact enquiry: receives the contact-form block's POST, checks it, stores it as a private
 * `contact_submission` post (Master menu), emails hello@wugweb.com and sends the visitor on.
 * Mirrors the Astro /api/contact route: name, valid email and a message of 20+ characters are required,
 * a hidden "website" field catches bots, and a failed save goes to /contact-error/ instead of a false thank-you.
 */
defined( 'ABSPATH' ) || exit;

const WUGWEB_CONTACT_MIN_MESSAGE = 20;

function wugweb_contact_options() {
	return array(
		'interests' => array( 'Strategy', 'Brand', 'Experience', 'Engineering', 'Growth', 'Operations' ),
		'budget'    => array( '<$5K', '$5K - $15K', '$15K - $35K', '$35K+' ),
		'timeline'  => array( 'ASAP (< 1 month)', '1 - 3 months', '3 - 6 months', 'Exploring / Planning' ),
	);
}

add_action( 'init', function () {
	register_post_type( 'contact_submission', array(
		'labels'          => array( 'name' => 'Enquiries', 'singular_name' => 'Enquiry', 'menu_name' => 'Enquiries', 'all_items' => 'Enquiries', 'edit_item' => 'Enquiry', 'search_items' => 'Search enquiries', 'not_found' => 'No enquiries yet' ),
		'public'          => false,
		'show_ui'         => true,
		'show_in_menu'    => true,
		'menu_icon'       => 'dashicons-email-alt',
		'menu_position'   => 26,
		'show_in_rest'    => false,
		'supports'        => array( 'title' ),
		'capability_type' => 'post',
		'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
		'map_meta_cap'    => true,
	) );
} );

/** Read and validate the POST. Returns array( fields, problems ). */
function wugweb_contact_read( array $src ) {
	$opt = wugweb_contact_options();
	$pick = function ( $v, $allowed ) { $v = sanitize_text_field( (string) $v ); return in_array( $v, $allowed, true ) ? $v : ''; };
	$interests = array();
	foreach ( (array) ( $src['interests'] ?? array() ) as $i ) { $i = $pick( $i, $opt['interests'] ); if ( $i ) { $interests[] = $i; } }
	$f = array(
		'name'       => trim( sanitize_text_field( wp_unslash( $src['name'] ?? '' ) ) ),
		'company'    => sanitize_text_field( wp_unslash( $src['company'] ?? '' ) ),
		'email'      => trim( sanitize_email( wp_unslash( $src['email'] ?? '' ) ) ),
		'phone'      => sanitize_text_field( wp_unslash( $src['phone'] ?? '' ) ),
		'topic'      => trim( sanitize_textarea_field( wp_unslash( $src['topic'] ?? '' ) ) ),
		'budget'     => $pick( wp_unslash( $src['budget'] ?? '' ), $opt['budget'] ),
		'timeline'   => $pick( wp_unslash( $src['timeline'] ?? '' ), $opt['timeline'] ),
		'interests'  => array_values( array_unique( $interests ) ),
		'newsletter' => ( 'yes' === ( $src['newsletter'] ?? '' ) ),
	);
	$problems = array();
	if ( '' === $f['name'] ) { $problems[] = 'missing-name'; }
	if ( ! is_email( $f['email'] ) ) { $problems[] = 'invalid-email'; }
	if ( mb_strlen( $f['topic'] ) < WUGWEB_CONTACT_MIN_MESSAGE ) { $problems[] = 'message-too-short'; }
	return array( $f, $problems );
}

function wugweb_contact_handle() {
	$home = home_url( '/' );
	$back = home_url( '/contact-us/' );
	// Bots fill the hidden field; answer as if it worked so they learn nothing.
	if ( ! empty( $_POST['website'] ) ) { wp_safe_redirect( home_url( '/thank-you/' ) ); exit; }
	// Fast scripted posts (the page stamps the load time; no stamp = no JS, allowed).
	$ts = isset( $_POST['wg_ts'] ) ? (int) $_POST['wg_ts'] : 0;
	if ( $ts && ( time() * 1000 - $ts ) < 2500 ) { wp_safe_redirect( home_url( '/thank-you/' ) ); exit; }
	// Five a day per address is plenty for a real person.
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key = 'wg_cf_' . md5( $ip );
	$n   = (int) get_transient( $key );
	if ( $n >= 5 ) { wp_safe_redirect( add_query_arg( 'cf', 'rate', $back ) . '#enquiry' ); exit; }

	list( $f, $problems ) = wugweb_contact_read( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification -- public form on a cached page; honeypot, timing and rate limit instead
	if ( $problems ) {
		$token = wp_generate_password( 12, false );
		set_transient( 'wg_cfv_' . $token, $f, 15 * MINUTE_IN_SECONDS );
		wp_safe_redirect( add_query_arg( array( 'cf' => implode( ',', $problems ), 'cft' => $token ), $back ) . '#enquiry' );
		exit;
	}

	$title = $f['name'] . ( $f['company'] ? ' (' . $f['company'] . ')' : '' );
	$id = wp_insert_post( array( 'post_type' => 'contact_submission', 'post_status' => 'private', 'post_title' => $title, 'post_content' => $f['topic'] ), true );
	if ( is_wp_error( $id ) || ! $id ) {
		error_log( 'contact: could not save enquiry' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		wp_safe_redirect( home_url( '/contact-error/' ) );
		exit;
	}
	foreach ( array( 'name', 'company', 'email', 'phone', 'budget', 'timeline' ) as $k ) { update_post_meta( $id, 'wugweb_cf_' . $k, $f[ $k ] ); }
	update_post_meta( $id, 'wugweb_cf_interests', implode( ', ', $f['interests'] ) );
	update_post_meta( $id, 'wugweb_cf_newsletter', $f['newsletter'] ? 'yes' : 'no' );
	set_transient( $key, $n + 1, DAY_IN_SECONDS );

	$lines = array(
		'Name: ' . $f['name'], 'Company: ' . $f['company'], 'Email: ' . $f['email'], 'Phone: ' . $f['phone'],
		'Pillars: ' . implode( ', ', $f['interests'] ), 'Budget: ' . $f['budget'], 'Timeline: ' . $f['timeline'],
		'Newsletter: ' . ( $f['newsletter'] ? 'yes' : 'no' ), '', $f['topic'], '', 'Saved: ' . admin_url( 'post.php?post=' . $id . '&action=edit' ),
	);
	wp_mail( 'hello@wugweb.com', 'New enquiry: ' . $title, implode( "\n", $lines ), array( 'Reply-To: ' . $f['name'] . ' <' . $f['email'] . '>' ) );

	// FluentCRM, when it is active: every enquiry is a contact; newsletter only if they opted in.
	if ( function_exists( 'FluentCrmApi' ) ) {
		try {
			$parts = explode( ' ', $f['name'], 2 );
			FluentCrmApi( 'contacts' )->createOrUpdate( array(
				'email' => $f['email'], 'first_name' => $parts[0], 'last_name' => $parts[1] ?? '', 'phone' => $f['phone'],
				'company' => $f['company'], 'status' => $f['newsletter'] ? 'subscribed' : 'transactional',
			) );
		} catch ( \Throwable $e ) { error_log( 'contact: FluentCRM ' . $e->getMessage() ); } // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}
	do_action( 'wugweb_contact_submitted', $f, $id );

	wp_safe_redirect( home_url( '/thank-you/' ) );
	exit;
}
add_action( 'admin_post_nopriv_wugweb_contact', 'wugweb_contact_handle' );
add_action( 'admin_post_wugweb_contact', 'wugweb_contact_handle' );

// The enquiry screen shows the fields, read-only.
add_action( 'add_meta_boxes_contact_submission', function () {
	add_meta_box( 'wugweb_cf', 'Enquiry', function ( $post ) {
		echo '<table class="widefat striped"><tbody>';
		foreach ( array( 'name' => 'Name', 'company' => 'Company', 'email' => 'Email', 'phone' => 'Phone', 'interests' => 'Pillars', 'budget' => 'Budget', 'timeline' => 'Timeline', 'newsletter' => 'Newsletter' ) as $k => $l ) {
			echo '<tr><th style="width:140px">' . esc_html( $l ) . '</th><td>' . esc_html( (string) get_post_meta( $post->ID, 'wugweb_cf_' . $k, true ) ) . '</td></tr>';
		}
		echo '<tr><th>Message</th><td>' . nl2br( esc_html( $post->post_content ) ) . '</td></tr></tbody></table>';
	}, null, 'normal', 'high' );
} );
add_filter( 'manage_contact_submission_posts_columns', function ( $c ) { return array( 'cb' => $c['cb'], 'title' => 'Enquiry', 'wg_email' => 'Email', 'wg_int' => 'Pillars', 'date' => 'Received' ); } );
add_action( 'manage_contact_submission_posts_custom_column', function ( $col, $id ) {
	if ( 'wg_email' === $col ) { echo esc_html( (string) get_post_meta( $id, 'wugweb_cf_email', true ) ); }
	if ( 'wg_int' === $col ) { echo esc_html( (string) get_post_meta( $id, 'wugweb_cf_interests', true ) ); }
}, 10, 2 );
