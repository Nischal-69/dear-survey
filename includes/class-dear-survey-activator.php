<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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

		$table_broadcasts = $wpdb->prefix . 'ds_broadcasts';
		$sql_broadcasts = "CREATE TABLE $table_broadcasts (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			subject varchar(255) NOT NULL,
			message longtext NOT NULL,
			recipients longtext NOT NULL,
			sent_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		$table_templates = $wpdb->prefix . 'ds_templates';
		$sql_templates = "CREATE TABLE $table_templates (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			name varchar(100) NOT NULL,
			subject varchar(255) NOT NULL,
			content longtext NOT NULL,
			updated_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		$table_subscribers = $wpdb->prefix . 'ds_subscribers';
		$sql_subscribers = "CREATE TABLE $table_subscribers (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			email varchar(100) NOT NULL,
			name varchar(100) DEFAULT '',
			created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY email (email)
		) $charset_collate;";

		dbDelta( $sql_surveys );
		dbDelta( $sql_responses );
		dbDelta( $sql_broadcasts );
		dbDelta( $sql_templates );
		dbDelta( $sql_subscribers );

		// Seed initial templates if empty
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Table names cannot be prepared
		$count = $wpdb->get_var( $wpdb->prepare( 'SELECT count(*) FROM %i', $table_templates ) );
		if ( ! $count ) {
			$wpdb->insert( $table_templates, [
				'name'    => 'Invitation - Customer Mood',
				'subject' => 'We value your perspective',
				'content' => "Hi there,\n\nWe are currently gathering insights to improve our services. Could you spare 2 minutes to share your thoughts?\n\n{survey_link}\n\nThank you,\nThe Team",
				'updated_at' => current_time('mysql')
			]);
			$wpdb->insert( $table_templates, [
				'name'    => 'Survey Launch - Direct Link',
				'subject' => 'Quick Question for you',
				'content' => "Hello,\n\nWe've just launched a new assessment and would love your input. Please click the link below to participate:\n\n{survey_link}\n\nBest regards,\nSupport Team",
				'updated_at' => current_time('mysql')
			]);
		}

		add_option( 'dear_survey_db_version', DEAR_SURVEY_DB_VERSION );
	}

	public static function deactivate() {
		// Flush rewrite rules if necessary
	}
}
