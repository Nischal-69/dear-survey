<?php
/**
 * Formera Uninstall
 * Removes all plugin data on uninstall
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Delete all posts of a custom post type
 *
 * @param string $post_type The post type to delete.
 */
function formera_delete_all_cpt_posts( $post_type ) {
	$posts = get_posts( array(
		'post_type'      => $post_type,
		'posts_per_page' => -1,
		'post_status'    => 'any',
		'fields'         => 'ids',
	) );

	foreach ( $posts as $post_id ) {
		wp_delete_post( $post_id, true );
	}
}

// Delete all Custom Post Type data
$formera_post_types = array(
	'formera_survey',
	'formera_response',
	'formera_broadcast',
	'formera_template',
	'formera_subscriber',
);

foreach ( $formera_post_types as $post_type ) {
	formera_delete_all_cpt_posts( $post_type );
}

// Delete plugin options
delete_option( 'formera_db_version' );
delete_option( 'formera_gdpr_mode' );
delete_option( 'formera_brand_color' );
delete_option( 'formera_container_width' );

// Clean up any orphaned post meta
delete_post_meta_by_key( '_formera_status' );
delete_post_meta_by_key( '_formera_survey_id' );
delete_post_meta_by_key( '_formera_user_ip' );
delete_post_meta_by_key( '_formera_user_agent' );
delete_post_meta_by_key( '_formera_email' );
delete_post_meta_by_key( '_formera_name' );
delete_post_meta_by_key( '_formera_date_subscribed' );
delete_post_meta_by_key( '_formera_questions' );
delete_post_meta_by_key( '_formera_settings' );
delete_post_meta_by_key( '_formera_responses_data' );
