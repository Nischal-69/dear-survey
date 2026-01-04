<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DearSurvey_Activator {

	/**
	 * Plugin activation handler
	 * Registers CPTs and seeds initial data
	 */
	public static function activate() {
		// Ensure CPTs are registered before seeding
		self::register_post_types();
		
		// Seed initial templates if none exist
		self::seed_initial_templates();
		
		// Flush rewrite rules
		flush_rewrite_rules();
		
		// Store plugin version
		update_option( 'dearsurvey_db_version', DEAR_SURVEY_DB_VERSION );
	}

	/**
	 * Register custom post types during activation
	 */
	private static function register_post_types() {
		$post_types = array(
			'ds_survey' => array(
				'labels' => array(
					'name'          => __( 'Surveys', 'dear-survey' ),
					'singular_name' => __( 'Survey', 'dear-survey' ),
				),
			),
			'ds_response' => array(
				'labels' => array(
					'name'          => __( 'Survey Responses', 'dear-survey' ),
					'singular_name' => __( 'Response', 'dear-survey' ),
				),
			),
			'ds_broadcast' => array(
				'labels' => array(
					'name'          => __( 'Broadcasts', 'dear-survey' ),
					'singular_name' => __( 'Broadcast', 'dear-survey' ),
				),
			),
			'ds_template' => array(
				'labels' => array(
					'name'          => __( 'Email Templates', 'dear-survey' ),
					'singular_name' => __( 'Template', 'dear-survey' ),
				),
			),
			'ds_subscriber' => array(
				'labels' => array(
					'name'          => __( 'Subscribers', 'dear-survey' ),
					'singular_name' => __( 'Subscriber', 'dear-survey' ),
				),
			),
		);

		foreach ( $post_types as $post_type => $args ) {
			if ( ! post_type_exists( $post_type ) ) {
				register_post_type( $post_type, array_merge( $args, array(
					'public'              => false,
					'show_ui'             => false,
					'show_in_menu'        => false,
					'show_in_rest'        => false,
					'exclude_from_search' => true,
					'publicly_queryable'  => false,
					'supports'            => array( 'title' ),
					'capability_type'     => 'post',
					'map_meta_cap'        => true,
				) ) );
			}
		}
	}

	/**
	 * Seed initial email templates if none exist
	 */
	private static function seed_initial_templates() {
		// Check if templates already exist
		$existing = get_posts( array(
			'post_type'      => 'ds_template',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		) );

		if ( ! empty( $existing ) ) {
			return;
		}

		// Create initial templates
		$templates = array(
			array(
				'name'    => 'Invitation - Customer Mood',
				'subject' => 'We value your perspective',
				'content' => "Hi there,\n\nWe are currently gathering insights to improve our services. Could you spare 2 minutes to share your thoughts?\n\n{survey_link}\n\nThank you,\nThe Team",
			),
			array(
				'name'    => 'Survey Launch - Direct Link',
				'subject' => 'Quick Question for you',
				'content' => "Hello,\n\nWe've just launched a new assessment and would love your input. Please click the link below to participate:\n\n{survey_link}\n\nBest regards,\nSupport Team",
			),
		);

		foreach ( $templates as $template ) {
			$post_id = wp_insert_post( array(
				'post_title'  => $template['name'],
				'post_type'   => 'ds_template',
				'post_status' => 'publish',
			) );

			if ( ! is_wp_error( $post_id ) ) {
				update_post_meta( $post_id, '_ds_subject', $template['subject'] );
				update_post_meta( $post_id, '_ds_content', $template['content'] );
				update_post_meta( $post_id, '_ds_updated_at', current_time( 'mysql' ) );
			}
		}
	}

	/**
	 * Plugin deactivation handler
	 */
	public static function deactivate() {
		// Flush rewrite rules
		flush_rewrite_rules();
	}
}
