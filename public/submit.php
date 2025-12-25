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
				
				// 1. Admin Notification
				if ( ! empty( $settings['admin_email'] ) ) {
					$admin_email = sanitize_email( $settings['admin_email'] );
					$subject = "New Response: " . $survey['title'];
					$message = "You have received a new response for your survey '{$survey['title']}'.\n\nView details in your dashboard.";
					wp_mail( $admin_email, $subject, $message );
				}

				// 2. Thank You Email to Responder
				if ( ! empty( $settings['thank_you_body'] ) ) {
					$responder_email = '';
					// Scan response data for anything that looks like an email
					foreach ( $response_data as $val ) {
						if ( is_string( $val ) && is_email( trim( $val ) ) ) {
							$responder_email = trim( $val );
							break;
						}
					}

					if ( $responder_email ) {
						$subject = "Thank you for your response - " . $survey['title'];
						$message = $settings['thank_you_body'];
						wp_mail( $responder_email, $subject, $message );
					}
				}
			}
			wp_send_json_success( array( 'message' => 'Thank you for your response!' ) );
		} else {
			wp_send_json_error( array( 'message' => 'Database error' ) );
		}
	}
}
