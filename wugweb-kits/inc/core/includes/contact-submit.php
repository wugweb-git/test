<?php
/**
 * Contact enquiry: receives the contact-form block's POST, checks it and hands it to Fluent Forms (entries, emails),
 * then sends the visitor on.
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

/**
 * Fluent Forms is where every enquiry and signup lands (entries, notifications, integrations).
 * The page markup and this handler's checks stay as they are; the validated fields are handed to Fluent Forms'
 * own submit endpoint, so its full pipeline runs. Form ids: option wugweb_ff_{contact|newsletter}, default 3 and 2.
 */
function wugweb_ff_form_id( $which ) {
	$defaults = array( 'contact' => 3, 'newsletter' => 2 );
	return (int) get_option( 'wugweb_ff_' . $which, $defaults[ $which ] ?? 0 );
}
function wugweb_ff_submit( $form_id, array $data ) {
	if ( ! $form_id ) { return false; }
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$res = wp_remote_post( admin_url( 'admin-ajax.php' ), array(
		'timeout' => 20,
		'headers' => array( 'X-Forwarded-For' => $ip, 'X-Real-IP' => $ip ),
		'body'    => array( 'action' => 'fluentform_submit', 'form_id' => $form_id, 'data' => http_build_query( $data ) ),
	) );
	if ( is_wp_error( $res ) || 200 !== (int) wp_remote_retrieve_response_code( $res ) ) {
		error_log( 'fluent forms submit failed: ' . ( is_wp_error( $res ) ? $res->get_error_message() : wp_remote_retrieve_response_code( $res ) . ' ' . substr( wp_remote_retrieve_body( $res ), 0, 200 ) ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		return false;
	}
	$body = json_decode( wp_remote_retrieve_body( $res ), true );
	return is_array( $body ) && ! empty( $body['success'] );
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

	set_transient( $key, $n + 1, DAY_IN_SECONDS );
	$ok = wugweb_ff_submit( wugweb_ff_form_id( 'contact' ), array(
		'name' => $f['name'], 'company' => $f['company'], 'email' => $f['email'], 'phone' => $f['phone'], 'topic' => $f['topic'],
		'budget' => $f['budget'], 'timeline' => $f['timeline'], 'interests' => $f['interests'], 'newsletter' => $f['newsletter'] ? array( 'yes' ) : array(),
	) );
	if ( ! $ok ) { wp_safe_redirect( home_url( '/contact-error/' ) ); exit; }
	do_action( 'wugweb_contact_submitted', $f, 0 );
	wp_safe_redirect( home_url( '/thank-you/' ) );
	exit;
}
add_action( 'admin_post_nopriv_wugweb_contact', 'wugweb_contact_handle' );
add_action( 'admin_post_wugweb_contact', 'wugweb_contact_handle' );

/** Newsletter signup (the "nl-form" forms): validate the address, then Fluent Forms stores it. Back to the page with ?nl=ok|fail. */
function wugweb_subscribe_handle() {
	$back = wp_get_referer() ?: home_url( '/' );
	$back = remove_query_arg( 'nl', $back );
	$ip   = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key  = 'wg_nl_' . md5( $ip );
	$n    = (int) get_transient( $key );
	$mail = trim( sanitize_email( wp_unslash( $_POST['email'] ?? '' ) ) ); // phpcs:ignore WordPress.Security.NonceVerification -- public form on a cached page; rate limit instead
	if ( ! is_email( $mail ) || $n >= 10 ) { wp_safe_redirect( add_query_arg( 'nl', 'fail', $back ) ); exit; }
	set_transient( $key, $n + 1, DAY_IN_SECONDS );
	$ok = wugweb_ff_submit( wugweb_ff_form_id( 'newsletter' ), array( 'email' => $mail ) );
	wp_safe_redirect( add_query_arg( 'nl', $ok ? 'ok' : 'fail', $back ) );
	exit;
}
add_action( 'admin_post_nopriv_wugweb_subscribe', 'wugweb_subscribe_handle' );
add_action( 'admin_post_wugweb_subscribe', 'wugweb_subscribe_handle' );
/** Status line for the nl-form forms after the redirect. */
function wugweb_subscribe_status() {
	$v = isset( $_GET['nl'] ) ? sanitize_key( wp_unslash( $_GET['nl'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	if ( 'ok' === $v ) { return '<p class="nl-status" role="status">Thanks, you are subscribed.</p>'; }
	if ( 'fail' === $v ) { return '<p class="nl-status" role="alert">That did not go through. Check the address and try again.</p>'; }
	return '';
}
