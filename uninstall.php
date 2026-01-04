<?php
/**
 * Dear Survey Uninstall
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
function dearsurvey_delete_all_cpt_posts( $post_type ) {
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
$dearsurvey_post_types = array(
	'ds_survey',
	'ds_response',
	'ds_broadcast',
	'ds_template',
	'ds_subscriber',
);

foreach ( $dearsurvey_post_types as $post_type ) {
	dearsurvey_delete_all_cpt_posts( $post_type );
}

// Delete plugin options
delete_option( 'dearsurvey_db_version' );
delete_option( 'ds_gdpr_mode' );
delete_option( 'ds_brand_color' );
delete_option( 'ds_container_width' );

// Clean up any orphaned post meta
delete_post_meta_by_key( '_ds_status' );
delete_post_meta_by_key( '_ds_survey_id' );
delete_post_meta_by_key( '_ds_user_ip' );
delete_post_meta_by_key( '_ds_user_agent' );
delete_post_meta_by_key( '_ds_email' );
delete_post_meta_by_key( '_ds_name' );
delete_post_meta_by_key( '_ds_date_subscribed' );
delete_post_meta_by_key( '_ds_questions' );
delete_post_meta_by_key( '_ds_settings' );
delete_post_meta_by_key( '_ds_responses_data' );
