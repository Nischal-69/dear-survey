<?php
/**
 * Survey Results View
 * Markup Overhaul for Premium UI/UX Refactor
 */

class Dear_Survey_Results {

	private $db;

	public function __construct( $db ) { $this->db = $db; }

	public function render_results() {
		$survey_id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;
		$survey = $this->db->get_survey( $survey_id );
		if ( ! $survey ) {
			echo '<div class="notice notice-error"><p>Survey not found.</p></div>';
			return;
		}
		$responses = $this->db->get_responses( $survey_id );
		$questions = json_decode( $survey['questions'], true );

		require_once dirname( __FILE__ ) . '/menu.php';
		$menu = new Dear_Survey_Menu( $this->db );
		?>
		<div class="ds-app-container ds-animate">
			<?php $menu->get_sidebar('surveys'); ?>

			<!-- Main Content -->
			<div class="ds-main-content">
				<div class="ds-top-bar">
					<div>
						<div style="font-size: 12px; font-weight: 600; color: var(--ds-primary); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.05em;">Results</div>
						<h1 class="ds-title"><?php echo esc_html( $survey['title'] ); ?></h1>
					</div>
					<div style="display:flex; gap:12px;">
						<a href="<?php echo admin_url('admin-ajax.php?action=ds_export_csv&survey_id=' . $survey_id); ?>" class="ds-btn ds-btn-secondary">
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px; height:16px;"><path d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
							Export CSV
						</a>
						<a href="<?php echo admin_url('admin.php?page=dear-survey-builder&id=' . $survey_id); ?>" class="ds-btn ds-btn-primary">Edit Survey</a>
					</div>
				</div>

				<div class="ds-card">
					<div class="ds-table-container">
						<table class="ds-table">
							<thead>
								<tr>
									<th>Date</th>
									<th>User/IP</th>
									<?php foreach ( $questions as $q ) : ?>
										<th><?php echo esc_html( $q['title'] ); ?></th>
									<?php endforeach; ?>
								</tr>
							</thead>
							<tbody>
								<?php if ( empty( $responses ) ) : ?>
									<tr><td colspan="<?php echo count($questions) + 2; ?>" style="text-align:center; padding: 100px; color: var(--ds-text-light);">No responses found.</td></tr>
								<?php else : ?>
									<?php foreach ( $responses as $resp ) : 
										$data = json_decode( $resp['response_data'], true );
										$email = $data['ds_responder_email'] ?? 'Anonymous';
										?>
										<tr>
											<td><?php echo date_i18n( 'M j, Y H:i', strtotime( $resp['created_at'] ) ); ?></td>
											<td>
												<div style="font-weight:700;"><?php echo esc_html($email); ?></div>
												<div style="font-size:11px; color:var(--ds-text-light);"><?php echo esc_html($resp['ip_address']); ?></div>
											</td>
											<?php foreach ( $questions as $index => $q ) : ?>
												<?php 
													$field_key = 'q' . $index;
													$val = isset( $data[$field_key] ) ? $data[$field_key] : '-';
												?>
												<td><?php echo esc_html( is_array($val) ? implode(', ', $val) : $val ); ?></td>
											<?php endforeach; ?>
										</tr>
									<?php endforeach; ?>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
