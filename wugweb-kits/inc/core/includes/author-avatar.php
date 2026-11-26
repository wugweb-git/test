<?php
/**
 * Author avatar from the user profile (field "Avatar URL"), used by every WordPress avatar call,
 * so post author blocks show the avatar next to the author name.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'init', function () {
	register_meta( 'user', 'wugweb_avatar_url', array(
		'type' => 'string', 'single' => true, 'show_in_rest' => true,
		'sanitize_callback' => 'esc_url_raw',
		'auth_callback' => function () { return current_user_can( 'edit_users' ); },
	) );
} );

add_filter( 'pre_get_avatar_data', function ( $args, $id_or_email ) {
	$uid = 0;
	if ( is_numeric( $id_or_email ) ) { $uid = (int) $id_or_email; }
	elseif ( $id_or_email instanceof WP_User ) { $uid = $id_or_email->ID; }
	elseif ( $id_or_email instanceof WP_Post ) { $uid = (int) $id_or_email->post_author; }
	elseif ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
		$u = get_user_by( 'email', $id_or_email ); $uid = $u ? $u->ID : 0;
	}
	if ( $uid ) {
		$url = get_user_meta( $uid, 'wugweb_avatar_url', true );
		if ( $url ) { $args['url'] = esc_url( $url ); $args['found_avatar'] = true; }
	}
	return $args;
}, 10, 2 );

foreach ( array( 'show_user_profile', 'edit_user_profile' ) as $hook ) {
	add_action( $hook, function ( $user ) {
		?>
		<h2>Avatar</h2>
		<table class="form-table"><tr><th><label for="wugweb_avatar_url">Avatar URL</label></th>
		<td><input type="url" class="regular-text" name="wugweb_avatar_url" id="wugweb_avatar_url" value="<?php echo esc_attr( get_user_meta( $user->ID, 'wugweb_avatar_url', true ) ); ?>" />
		<p class="description">Shown next to the author name on posts and case studies.</p></td></tr></table>
		<?php
	} );
}
foreach ( array( 'personal_options_update', 'edit_user_profile_update' ) as $hook ) {
	add_action( $hook, function ( $uid ) {
		// Same nonce core checks on the profile / user-edit screens (action update-user_<id>); verified again here before saving.
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'update-user_' . $uid ) ) { return; }
		if ( current_user_can( 'edit_user', $uid ) && isset( $_POST['wugweb_avatar_url'] ) ) {
			update_user_meta( $uid, 'wugweb_avatar_url', esc_url_raw( wp_unslash( $_POST['wugweb_avatar_url'] ) ) );
		}
	} );
}
