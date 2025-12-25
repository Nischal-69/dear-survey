<?php

class Dear_Survey_Submit {

	private $db;

	public function __construct( $db ) {
		$this->db = $db;
	}

	public function ajax_handle_submit() {
		$survey_id = isset( $_POST['survey_id'] ) ? intval( $_POST['survey_id'] ) : 0;
		$response_data_raw = isset( $_POST['response_data'] ) ? wp_unslash( $_POST['response_data'] ) : '';

		if ( ! $survey_id || ! $response_data_raw ) {
			wp_send_json_error( array( 'message' => 'Invalid data' ) );
		}

		$data = array(
			'survey_id' => $survey_id,
			'response_data' => $response_data_raw
		);

		$result = $this->db->save_response( $data );

		if ( $result ) {
			$survey = $this->db->get_survey( $survey_id );
			if ( $survey ) {
				$settings = json_decode( $survey['settings'], true );
				$response_data = json_decode( $response_data_raw, true );
				
				// Identify responder email (from dedicated field or scan)
				$responder_email = '';
				if ( ! empty( $response_data['ds_responder_email'] ) ) {
					$responder_email = sanitize_email( $response_data['ds_responder_email'] );
				}

				// 1. Admin Notification
				if ( ! empty( $settings['admin_email'] ) ) {
					$admin_email = sanitize_email( $settings['admin_email'] );
					$subject = "New Entry: " . $survey['title'];
					$contact_info = $responder_email ? "from $responder_email" : "(anonymous)";
					$message = "You have received a new survey entry $contact_info for '{$survey['title']}'.\n\nPlease check your dashboard for full details.";
					wp_mail( $admin_email, $subject, $message );
				}

				// 2. Autoresponder to Participant
				if ( ! empty( $settings['collect_email'] ) && ! empty( $responder_email ) ) {
					$auto_subject = ! empty( $settings['auto_subject'] ) ? $settings['auto_subject'] : "Response Received: " . $survey['title'];
					$auto_body = ! empty( $settings['auto_body'] ) ? $settings['auto_body'] : $settings['thank_you_body'];
					
					if ( ! empty( $auto_body ) ) {
						wp_mail( $responder_email, $auto_subject, $auto_body );
					}
				}
			}
			wp_send_json_success( array( 'message' => $settings['thank_you_body'] ?: 'Thank you for your response!' ) );
		} else {
			wp_send_json_error( array( 'message' => 'Database error' ) );
		}
	}
}
