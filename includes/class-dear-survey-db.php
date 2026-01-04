<?php
/**
 * Dear Survey Database Class
 * Uses WordPress Custom Post Types instead of raw SQL queries
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DearSurvey_DB {

	/**
	 * Custom Post Type names
	 */
	const CPT_SURVEY     = 'ds_survey';
	const CPT_RESPONSE   = 'ds_response';
	const CPT_BROADCAST  = 'ds_broadcast';
	const CPT_TEMPLATE   = 'ds_template';
	const CPT_SUBSCRIBER = 'ds_subscriber';

	/**
	 * Track if CPTs have been registered
	 */
	private static $cpts_registered = false;

	public function __construct() {
		// CPTs are registered via register_post_types() method
	}

	/**
	 * Register all custom post types
	 */
	public function register_post_types() {
		// Prevent double registration
		if ( self::$cpts_registered ) {
			return;
		}
		self::$cpts_registered = true;
		// Survey CPT
		register_post_type( self::CPT_SURVEY, array(
			'labels' => array(
				'name'          => __( 'Surveys', 'dear-survey' ),
				'singular_name' => __( 'Survey', 'dear-survey' ),
			),
			'public'              => false,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'show_in_rest'        => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'supports'            => array( 'title' ),
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
		) );

		// Response CPT
		register_post_type( self::CPT_RESPONSE, array(
			'labels' => array(
				'name'          => __( 'Survey Responses', 'dear-survey' ),
				'singular_name' => __( 'Response', 'dear-survey' ),
			),
			'public'              => false,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'show_in_rest'        => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'supports'            => array( 'title' ),
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
		) );

		// Broadcast CPT
		register_post_type( self::CPT_BROADCAST, array(
			'labels' => array(
				'name'          => __( 'Broadcasts', 'dear-survey' ),
				'singular_name' => __( 'Broadcast', 'dear-survey' ),
			),
			'public'              => false,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'show_in_rest'        => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'supports'            => array( 'title' ),
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
		) );

		// Template CPT
		register_post_type( self::CPT_TEMPLATE, array(
			'labels' => array(
				'name'          => __( 'Email Templates', 'dear-survey' ),
				'singular_name' => __( 'Template', 'dear-survey' ),
			),
			'public'              => false,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'show_in_rest'        => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'supports'            => array( 'title' ),
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
		) );

		// Subscriber CPT
		register_post_type( self::CPT_SUBSCRIBER, array(
			'labels' => array(
				'name'          => __( 'Subscribers', 'dear-survey' ),
				'singular_name' => __( 'Subscriber', 'dear-survey' ),
			),
			'public'              => false,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'show_in_rest'        => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'supports'            => array( 'title' ),
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
		) );
	}

	/* =========================================
	 * SURVEY METHODS
	 * ========================================= */

	/**
	 * Get all surveys
	 *
	 * @param array $args Query arguments.
	 * @return array Array of surveys.
	 */
	public function get_surveys( $args = array() ) {
		$defaults = array(
			'orderby' => 'created_at',
			'order'   => 'DESC',
			'limit'   => 20,
			'offset'  => 0,
		);
		$args = wp_parse_args( $args, $defaults );

		// Map custom orderby to WP_Query orderby
		$orderby_map = array(
			'created_at' => 'date',
			'id'         => 'ID',
			'title'      => 'title',
			'status'     => 'meta_value',
		);

		$query_args = array(
			'post_type'      => self::CPT_SURVEY,
			'posts_per_page' => $args['limit'],
			'offset'         => $args['offset'],
			'orderby'        => isset( $orderby_map[ $args['orderby'] ] ) ? $orderby_map[ $args['orderby'] ] : 'date',
			'order'          => strtoupper( $args['order'] ) === 'ASC' ? 'ASC' : 'DESC',
			'post_status'    => 'any',
		);

		if ( $args['orderby'] === 'status' ) {
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- Required for sorting by survey status.
			$query_args['meta_key'] = '_ds_status';
		}

		$query = new WP_Query( $query_args );
		$surveys = array();

		foreach ( $query->posts as $post ) {
			$surveys[] = $this->format_survey_from_post( $post );
		}

		return $surveys;
	}

	/**
	 * Get a single survey by ID
	 *
	 * @param int $id Survey ID (post ID).
	 * @return array|null Survey data or null.
	 */
	public function get_survey( $id ) {
		$post = get_post( $id );
		
		if ( ! $post || $post->post_type !== self::CPT_SURVEY ) {
			return null;
		}

		return $this->format_survey_from_post( $post );
	}

	/**
	 * Save a survey (create or update)
	 *
	 * @param array $data Survey data.
	 * @return int|false Post ID on success, false on failure.
	 */
	public function save_survey( $data ) {
		$defaults = array(
			'title'     => 'Untitled Survey',
			'questions' => '[]',
			'settings'  => '{}',
			'status'    => 'draft',
		);
		$data = wp_parse_args( $data, $defaults );

		$post_data = array(
			'post_title'  => $data['title'],
			'post_type'   => self::CPT_SURVEY,
			'post_status' => 'publish', // Always publish, use custom status meta
		);

		if ( isset( $data['id'] ) && $data['id'] > 0 ) {
			// Update existing survey
			$post_data['ID'] = $data['id'];
			$result = wp_update_post( $post_data, true );
			
			if ( is_wp_error( $result ) ) {
				return false;
			}
			
			$post_id = $data['id'];
		} else {
			// Create new survey
			$post_id = wp_insert_post( $post_data, true );
			
			if ( is_wp_error( $post_id ) ) {
				return false;
			}
		}

		// Save meta data
		update_post_meta( $post_id, '_ds_questions', $data['questions'] );
		update_post_meta( $post_id, '_ds_settings', $data['settings'] );
		update_post_meta( $post_id, '_ds_status', $data['status'] );

		return $post_id;
	}

	/**
	 * Delete a survey and its responses
	 *
	 * @param int $id Survey ID.
	 * @return bool True on success.
	 */
	public function delete_survey( $id ) {
		// Delete all responses for this survey first
		$responses = get_posts( array(
			'post_type'      => self::CPT_RESPONSE,
			'posts_per_page' => -1,
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Required to find all responses for deletion.
			'meta_query'     => array(
				array(
					'key'   => '_ds_survey_id',
					'value' => $id,
				),
			),
			'fields' => 'ids',
		) );

		foreach ( $responses as $response_id ) {
			wp_delete_post( $response_id, true );
		}

		// Delete the survey
		$result = wp_delete_post( $id, true );
		
		return $result !== false && $result !== null;
	}

	/**
	 * Format survey post to legacy array format
	 *
	 * @param WP_Post $post Post object.
	 * @return array Survey data array.
	 */
	private function format_survey_from_post( $post ) {
		$questions = get_post_meta( $post->ID, '_ds_questions', true );
		$settings  = get_post_meta( $post->ID, '_ds_settings', true );
		$status    = get_post_meta( $post->ID, '_ds_status', true );
		
		return array(
			'id'         => $post->ID,
			'title'      => $post->post_title,
			'questions'  => ! empty( $questions ) ? (string) $questions : '[]',
			'settings'   => ! empty( $settings ) ? (string) $settings : '{}',
			'status'     => ! empty( $status ) ? (string) $status : 'draft',
			'created_at' => $post->post_date,
		);
	}

	/* =========================================
	 * RESPONSE METHODS
	 * ========================================= */

	/**
	 * Save a survey response
	 *
	 * @param array $data Response data.
	 * @return int|false Response ID on success, false on failure.
	 */
	public function save_response( $data ) {
		// Get IP address safely
		$ip_address = '';
		if ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			$ip_address = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		}

		$post_data = array(
			'post_title'  => sprintf( 'Response - Survey %d - %s', $data['survey_id'], current_time( 'mysql' ) ),
			'post_type'   => self::CPT_RESPONSE,
			'post_status' => 'publish',
		);

		$post_id = wp_insert_post( $post_data, true );

		if ( is_wp_error( $post_id ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Intentional debug logging when WP_DEBUG is enabled
				error_log( 'Dear Survey Response Error: ' . $post_id->get_error_message() );
			}
			return false;
		}

		// Save meta data
		update_post_meta( $post_id, '_ds_survey_id', $data['survey_id'] );
		update_post_meta( $post_id, '_ds_user_id', get_current_user_id() );
		update_post_meta( $post_id, '_ds_ip_address', $ip_address );
		update_post_meta( $post_id, '_ds_response_data', $data['response_data'] );

		return $post_id;
	}

	/**
	 * Get responses for a specific survey
	 *
	 * @param int $survey_id Survey ID.
	 * @return array Array of responses.
	 */
	public function get_responses( $survey_id ) {
		$query = new WP_Query( array(
			'post_type'      => self::CPT_RESPONSE,
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Required to filter responses by survey ID.
			'meta_query'     => array(
				array(
					'key'   => '_ds_survey_id',
					'value' => $survey_id,
				),
			),
		) );

		$responses = array();
		foreach ( $query->posts as $post ) {
			$responses[] = $this->format_response_from_post( $post );
		}

		return $responses;
	}

	/**
	 * Get all responses
	 *
	 * @return array Array of all responses.
	 */
	public function get_all_responses() {
		$query = new WP_Query( array(
			'post_type'      => self::CPT_RESPONSE,
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		) );

		$responses = array();
		foreach ( $query->posts as $post ) {
			$responses[] = $this->format_response_from_post( $post );
		}

		return $responses;
	}

	/**
	 * Format response post to legacy array format
	 *
	 * @param WP_Post $post Post object.
	 * @return array Response data array.
	 */
	private function format_response_from_post( $post ) {
		$survey_id     = get_post_meta( $post->ID, '_ds_survey_id', true );
		$user_id       = get_post_meta( $post->ID, '_ds_user_id', true );
		$ip_address    = get_post_meta( $post->ID, '_ds_ip_address', true );
		$response_data = get_post_meta( $post->ID, '_ds_response_data', true );
		
		return array(
			'id'            => $post->ID,
			'survey_id'     => ! empty( $survey_id ) ? (string) $survey_id : '',
			'user_id'       => ! empty( $user_id ) ? (int) $user_id : 0,
			'ip_address'    => ! empty( $ip_address ) ? (string) $ip_address : '',
			'response_data' => ! empty( $response_data ) ? (string) $response_data : '',
			'created_at'    => $post->post_date,
		);
	}

	/* =========================================
	 * BROADCAST METHODS
	 * ========================================= */

	/**
	 * Get all broadcasts
	 *
	 * @return array Array of broadcasts.
	 */
	public function get_broadcasts() {
		$query = new WP_Query( array(
			'post_type'      => self::CPT_BROADCAST,
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		) );

		$broadcasts = array();
		foreach ( $query->posts as $post ) {
			$broadcasts[] = $this->format_broadcast_from_post( $post );
		}

		return $broadcasts;
	}

	/**
	 * Save a broadcast record
	 *
	 * @param array $data Broadcast data.
	 * @return int|false Broadcast ID on success, false on failure.
	 */
	public function save_broadcast( $data ) {
		$post_data = array(
			'post_title'  => $data['subject'],
			'post_type'   => self::CPT_BROADCAST,
			'post_status' => 'publish',
		);

		$post_id = wp_insert_post( $post_data, true );

		if ( is_wp_error( $post_id ) ) {
			return false;
		}

		update_post_meta( $post_id, '_ds_message', $data['message'] );
		update_post_meta( $post_id, '_ds_recipients', $data['recipients'] );
		update_post_meta( $post_id, '_ds_sent_at', $data['sent_at'] );

		return $post_id;
	}

	/**
	 * Format broadcast post to legacy array format
	 *
	 * @param WP_Post $post Post object.
	 * @return array Broadcast data array.
	 */
	private function format_broadcast_from_post( $post ) {
		$message    = get_post_meta( $post->ID, '_ds_message', true );
		$recipients = get_post_meta( $post->ID, '_ds_recipients', true );
		$sent_at    = get_post_meta( $post->ID, '_ds_sent_at', true );
		
		return array(
			'id'         => $post->ID,
			'subject'    => $post->post_title,
			'message'    => ! empty( $message ) ? (string) $message : '',
			'recipients' => ! empty( $recipients ) ? (string) $recipients : '',
			'sent_at'    => ! empty( $sent_at ) ? (string) $sent_at : $post->post_date,
		);
	}

	/* =========================================
	 * TEMPLATE METHODS
	 * ========================================= */

	/**
	 * Get all templates
	 *
	 * @return array Array of templates.
	 */
	public function get_templates() {
		$query = new WP_Query( array(
			'post_type'      => self::CPT_TEMPLATE,
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		) );

		$templates = array();
		foreach ( $query->posts as $post ) {
			$templates[] = $this->format_template_from_post( $post );
		}

		return $templates;
	}

	/**
	 * Get a single template by ID
	 *
	 * @param int $id Template ID.
	 * @return array|null Template data or null.
	 */
	public function get_template( $id ) {
		$post = get_post( $id );
		
		if ( ! $post || $post->post_type !== self::CPT_TEMPLATE ) {
			return null;
		}

		return $this->format_template_from_post( $post );
	}

	/**
	 * Save a template (create or update)
	 *
	 * @param array $data Template data.
	 * @return int|false Template ID on success, false on failure.
	 */
	public function save_template( $data ) {
		$post_data = array(
			'post_title'  => $data['name'],
			'post_type'   => self::CPT_TEMPLATE,
			'post_status' => 'publish',
		);

		if ( isset( $data['id'] ) && $data['id'] > 0 ) {
			$post_data['ID'] = $data['id'];
			$result = wp_update_post( $post_data, true );
			
			if ( is_wp_error( $result ) ) {
				return false;
			}
			
			$post_id = $data['id'];
		} else {
			$post_id = wp_insert_post( $post_data, true );
			
			if ( is_wp_error( $post_id ) ) {
				return false;
			}
		}

		update_post_meta( $post_id, '_ds_subject', $data['subject'] );
		update_post_meta( $post_id, '_ds_content', $data['content'] );
		update_post_meta( $post_id, '_ds_updated_at', current_time( 'mysql' ) );

		return $post_id;
	}

	/**
	 * Format template post to legacy array format
	 *
	 * @param WP_Post $post Post object.
	 * @return array Template data array.
	 */
	private function format_template_from_post( $post ) {
		$subject    = get_post_meta( $post->ID, '_ds_subject', true );
		$content    = get_post_meta( $post->ID, '_ds_content', true );
		$updated_at = get_post_meta( $post->ID, '_ds_updated_at', true );
		
		return array(
			'id'         => $post->ID,
			'name'       => $post->post_title,
			'subject'    => ! empty( $subject ) ? (string) $subject : '',
			'content'    => ! empty( $content ) ? (string) $content : '',
			'updated_at' => ! empty( $updated_at ) ? (string) $updated_at : $post->post_date,
		);
	}

	/* =========================================
	 * SUBSCRIBER METHODS
	 * ========================================= */

	/**
	 * Get all subscribers
	 *
	 * @return array Array of subscribers.
	 */
	public function get_subscribers() {
		$query = new WP_Query( array(
			'post_type'      => self::CPT_SUBSCRIBER,
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		) );

		$subscribers = array();
		foreach ( $query->posts as $post ) {
			$subscribers[] = $this->format_subscriber_from_post( $post );
		}

		return $subscribers;
	}

	/**
	 * Add a new subscriber
	 *
	 * @param string $email Subscriber email.
	 * @param string $name  Subscriber name.
	 * @return int|false Subscriber ID on success, false on failure.
	 */
	public function add_subscriber( $email, $name = '' ) {
		// Check if email already exists
		$existing = get_posts( array(
			'post_type'      => self::CPT_SUBSCRIBER,
			'posts_per_page' => 1,
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Required to check for duplicate email addresses.
			'meta_query'     => array(
				array(
					'key'   => '_ds_email',
					'value' => $email,
				),
			),
			'fields' => 'ids',
		) );

		if ( ! empty( $existing ) ) {
			// Update existing subscriber
			$post_id = $existing[0];
			if ( $name ) {
				wp_update_post( array(
					'ID'         => $post_id,
					'post_title' => $name ?: $email,
				) );
				update_post_meta( $post_id, '_ds_name', $name );
			}
			return $post_id;
		}

		$post_data = array(
			'post_title'  => $name ?: $email,
			'post_type'   => self::CPT_SUBSCRIBER,
			'post_status' => 'publish',
		);

		$post_id = wp_insert_post( $post_data, true );

		if ( is_wp_error( $post_id ) ) {
			return false;
		}

		update_post_meta( $post_id, '_ds_email', $email );
		update_post_meta( $post_id, '_ds_name', $name );

		return $post_id;
	}

	/**
	 * Delete a subscriber
	 *
	 * @param int $id Subscriber ID.
	 * @return bool True on success.
	 */
	public function delete_subscriber( $id ) {
		$result = wp_delete_post( $id, true );
		return $result !== false && $result !== null;
	}

	/**
	 * Format subscriber post to legacy array format
	 *
	 * @param WP_Post $post Post object.
	 * @return array Subscriber data array.
	 */
	private function format_subscriber_from_post( $post ) {
		$email = get_post_meta( $post->ID, '_ds_email', true );
		$name  = get_post_meta( $post->ID, '_ds_name', true );
		
		return array(
			'id'         => $post->ID,
			'email'      => ! empty( $email ) ? (string) $email : '',
			'name'       => ! empty( $name ) ? (string) $name : '',
			'created_at' => $post->post_date,
		);
	}
}
