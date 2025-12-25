<?php

class Dear_Survey_DB {
	private $wpdb;
	private $table_surveys;
	private $table_responses;

	private $table_broadcasts;
	private $table_templates;
	private $table_subscribers;

	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
		$this->table_surveys = $wpdb->prefix . 'ds_surveys';
		$this->table_responses = $wpdb->prefix . 'ds_responses';
		$this->table_broadcasts = $wpdb->prefix . 'ds_broadcasts';
		$this->table_templates = $wpdb->prefix . 'ds_templates';
		$this->table_subscribers = $wpdb->prefix . 'ds_subscribers';
	}

	public function get_surveys( $args = array() ) {
		$defaults = array(
			'orderby' => 'created_at',
			'order'   => 'DESC',
			'limit'   => 20,
			'offset'  => 0,
		);
		$args = wp_parse_args( $args, $defaults );
		
		$sql = "SELECT * FROM {$this->table_surveys} ORDER BY {$args['orderby']} {$args['order']} LIMIT %d OFFSET %d";
		return $this->wpdb->get_results( $this->wpdb->prepare( $sql, $args['limit'], $args['offset'] ), ARRAY_A );
	}

	public function get_survey( $id ) {
		$sql = "SELECT * FROM {$this->table_surveys} WHERE id = %d";
		return $this->wpdb->get_row( $this->wpdb->prepare( $sql, $id ), ARRAY_A );
	}

	public function save_survey( $data ) {
		$format = array( '%s', '%s', '%s', '%s', '%s' );
		$defaults = array(
			'title'     => 'Untitled Survey',
			'questions' => '[]',
			'settings'  => '{}',
			'status'    => 'draft',
			'created_at' => current_time( 'mysql' ),
		);
		
		$data = wp_parse_args( $data, $defaults );

		if ( isset( $data['id'] ) && $data['id'] > 0 ) {
			// Update
			return $this->wpdb->update(
				$this->table_surveys,
				array(
					'title'     => $data['title'],
					'questions' => $data['questions'],
					'settings'  => $data['settings'],
					'status'    => $data['status'],
				),
				array( 'id' => $data['id'] ),
				array( '%s', '%s', '%s', '%s' ),
				array( '%d' )
			);
		} else {
			// Insert
			unset( $data['id'] );
			$this->wpdb->insert(
				$this->table_surveys,
				$data,
				$format
			);
			return $this->wpdb->insert_id;
		}
	}

	public function delete_survey( $id ) {
		return $this->wpdb->delete( $this->table_surveys, array( 'id' => $id ), array( '%d' ) );
	}

	public function save_response( $data ) {
		$result = $this->wpdb->insert(
			$this->table_responses,
			array(
				'survey_id'     => $data['survey_id'],
				'user_id'       => get_current_user_id(),
				'ip_address'    => $_SERVER['REMOTE_ADDR'],
				'response_data' => $data['response_data'],
				'created_at'    => current_time( 'mysql' ),
			),
			array( '%d', '%d', '%s', '%s', '%s' )
		);

		if ( false === $result ) {
			if ( defined( 'DEAR_SURVEY_DEBUG' ) && DEAR_SURVEY_DEBUG ) {
				error_log( 'Dear Survey DB Error: ' . $this->wpdb->last_error );
			}
			return false;
		}

		return $this->wpdb->insert_id;
	}

	public function get_responses( $survey_id ) {
		$sql = "SELECT * FROM {$this->table_responses} WHERE survey_id = %d ORDER BY created_at DESC";
		return $this->wpdb->get_results( $this->wpdb->prepare( $sql, $survey_id ), ARRAY_A );
	}

	// Broadcast Methods
	public function get_broadcasts() {
		return $this->wpdb->get_results( "SELECT * FROM {$this->table_broadcasts} ORDER BY sent_at DESC", ARRAY_A );
	}

	public function save_broadcast( $data ) {
		return $this->wpdb->insert( $this->table_broadcasts, $data, array( '%s', '%s', '%s', '%s' ) );
	}

	// Template Methods
	public function get_templates() {
		return $this->wpdb->get_results( "SELECT * FROM {$this->table_templates} ORDER BY updated_at DESC", ARRAY_A );
	}

	public function get_template( $id ) {
		return $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM {$this->table_templates} WHERE id = %d", $id ), ARRAY_A );
	}

	public function save_template( $data ) {
		if ( isset( $data['id'] ) && $data['id'] > 0 ) {
			return $this->wpdb->update(
				$this->table_templates,
				array( 'name' => $data['name'], 'subject' => $data['subject'], 'content' => $data['content'], 'updated_at' => current_time( 'mysql' ) ),
				array( 'id' => $data['id'] ),
				array( '%s', '%s', '%s', '%s' ),
				array( '%d' )
			);
		} else {
			$data['updated_at'] = current_time( 'mysql' );
			return $this->wpdb->insert( $this->table_templates, $data, array( '%s', '%s', '%s', '%s' ) );
		}
	}

	// Subscriber Methods
	public function get_subscribers() {
		return $this->wpdb->get_results( "SELECT * FROM {$this->table_subscribers} ORDER BY created_at DESC", ARRAY_A );
	}

	public function add_subscriber( $email, $name = '' ) {
		return $this->wpdb->insert(
			$this->table_subscribers,
			array( 'email' => $email, 'name' => $name, 'created_at' => current_time( 'mysql' ) ),
			array( '%s', '%s', '%s' )
		);
	}

	public function delete_subscriber( $id ) {
		return $this->wpdb->delete( $this->table_subscribers, array( 'id' => $id ), array( '%d' ) );
	}
}
