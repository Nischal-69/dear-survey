<?php

class Dear_Survey_Results {

	private $db;

	public function __construct( $db ) {
		$this->db = $db;
	}

	public function render( $survey_id ) {
		$survey = $this->db->get_survey( $survey_id );
		$responses = $this->db->get_responses( $survey_id );
		
		if ( ! $survey ) {
			echo '<div class="ds-wrap"><p>Survey not found.</p></div>';
			return;
		}

		$questions = json_decode( $survey['questions'], true );
		?>
		<script>
			console.log("Backend Responses for Survey #<?php echo $survey_id; ?>:", <?php echo json_encode( $responses ); ?>);
		</script>
		<div class="ds-wrap">
			<div class="ds-header">
				<h1 class="ds-title">Results: <?php echo esc_html( $survey['title'] ); ?></h1>
				<a href="<?php echo admin_url( 'admin.php?page=dear-survey' ); ?>" class="ds-btn ds-btn-secondary">Back to Surveys</a>
			</div>

			<div class="ds-card">
				<h2>Summary</h2>
				<p>Total Responses: <strong><?php echo count( $responses ); ?></strong></p>
			</div>

			<div class="ds-card" style="padding:0; overflow:hidden;">
				<table class="ds-table">
					<thead>
						<tr>
							<th width="150">Date</th>
							<th width="150">User / IP</th>
							<?php foreach ( $questions as $q ) : ?>
								<th><?php echo esc_html( $q['title'] ); ?></th>
							<?php endforeach; ?>
						</tr>
					</thead>
					<tbody>
						<?php if ( ! empty( $responses ) ) : ?>
							<?php foreach ( $responses as $resp ) : ?>
								<?php 
								$data = json_decode( $resp['response_data'], true ); 
								?>
								<tr>
									<td><?php echo date_i18n( 'Y-m-d H:i', strtotime( $resp['created_at'] ) ); ?></td>
									<td>
										<?php 
										if ( $resp['user_id'] ) {
											$u = get_userdata( $resp['user_id'] );
											echo $u ? esc_html( $u->user_login ) : 'ID: ' . $resp['user_id'];
										} else {
											echo esc_html( $resp['ip_address'] );
										}
										?>
									</td>
									<?php foreach ( $questions as $idx => $q ) : ?>
										<td>
											<?php 
											$key = 'q_' . $idx;
											echo isset( $data[ $key ] ) ? esc_html( is_array($data[$key]) ? implode(', ', $data[$key]) : $data[$key] ) : '-'; 
											?>
										</td>
									<?php endforeach; ?>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<tr><td colspan="<?php echo count( $questions ) + 2; ?>" style="text-align:center; padding:20px;">No responses yet.</td></tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
		<?php
	}
}
