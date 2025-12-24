<?php

class Dear_Survey_DB {
	private $wpdb;
	private $table_surveys;
	private $table_responses;

	public function __construct() {
		global $wpdb;
		$this->wpdb = $wpdb;
		$this->table_surveys = $wpdb->prefix . 'ds_surveys';
		$this->table_responses = $wpdb->prefix . 'ds_responses';
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
}
