<?php
// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$table_surveys = $wpdb->prefix . 'ds_surveys';
$table_responses = $wpdb->prefix . 'ds_responses';

// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table names cannot be prepared
$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i', $table_surveys ) );
// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table names cannot be prepared
$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i', $table_responses ) );

delete_option( 'dear_survey_db_version' );
