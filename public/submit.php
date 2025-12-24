<?php

class Dear_Survey_Submit {

	private $db;

	public function __construct( $db ) {
		$this->db = $db;
	}

	public function ajax_handle_submit() {
		// Ideally use a nonce, but for public frontend form simplify for template
		// check_ajax_referer('ds_frontend_nonce'); 

		$survey_id = isset( $_POST['survey_id'] ) ? intval( $_POST['survey_id'] ) : 0;
		$response_data = isset( $_POST['response_data'] ) ? wp_unslash( $_POST['response_data'] ) : '';

		if ( ! $survey_id || ! $response_data ) {
			wp_send_json_error( array( 'message' => 'Invalid data' ) );
		}

		$data = array(
			'survey_id' => $survey_id,
			'response_data' => $response_data
		);

		$result = $this->db->save_response( $data );

		if ( $result ) {
			wp_send_json_success( array( 'message' => 'Thank you for your response!' ) );
		} else {
			wp_send_json_error( array( 'message' => 'Database error' ) );
		}
	}
}
