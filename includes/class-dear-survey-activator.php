<?php

class Dear_Survey_Activator {

	public static function activate() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$table_surveys = $wpdb->prefix . 'ds_surveys';
		$sql_surveys = "CREATE TABLE $table_surveys (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			title tinytext NOT NULL,
			questions longtext NOT NULL,
			settings longtext DEFAULT '',
			created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			status varchar(20) DEFAULT 'draft' NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		$table_responses = $wpdb->prefix . 'ds_responses';
		$sql_responses = "CREATE TABLE $table_responses (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			survey_id mediumint(9) NOT NULL,
			user_id bigint(20) DEFAULT 0,
			ip_address varchar(100) DEFAULT '',
			response_data longtext NOT NULL,
			created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (id),
			KEY survey_id (survey_id)
		) $charset_collate;";

		dbDelta( $sql_surveys );
		dbDelta( $sql_responses );

		add_option( 'dear_survey_db_version', DEAR_SURVEY_DB_VERSION );
	}

	public static function deactivate() {
		// Flush rewrite rules if necessary
	}
}
