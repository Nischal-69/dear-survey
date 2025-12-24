<?php
// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$table_surveys = $wpdb->prefix . 'ds_surveys';
$table_responses = $wpdb->prefix . 'ds_responses';

$wpdb->query( "DROP TABLE IF EXISTS $table_surveys" );
$wpdb->query( "DROP TABLE IF EXISTS $table_responses" );

delete_option( 'dear_survey_db_version' );
