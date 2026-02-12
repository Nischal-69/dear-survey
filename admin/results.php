<?php
/**
 * Survey Results View
 * Markup Overhaul for Premium UI/UX Refactor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Formera_Results {

	private $db;

	public function __construct( $db ) { $this->db = $db; }

	public function render_results() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- ID parameter is only used for loading survey data
		$survey_id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;
		$survey = $this->db->get_survey( $survey_id );
		if ( ! $survey ) {
			echo '<div class="notice notice-error"><p>Survey not found.</p></div>';
			return;
		}
		$responses = $this->db->get_responses( $survey_id );
		$questions = json_decode( $survey['questions'], true );

		require_once dirname( __FILE__ ) . '/menu.php';
		$menu = new Formera_Menu( $this->db );
		?>
		<div class="ds-app-container ds-animate">
			<?php $menu->get_sidebar('surveys'); ?>

			<!-- Main Content -->
			<div class="ds-main-content">
				<div class="ds-top-bar">
					<div>
						<div style="font-size: 12px; font-weight: 500; color: var(--ds-primary); margin-bottom: 4px; letter-spacing: 0.5px;">RESPONSES</div>
						<h1 class="ds-title"><?php echo esc_html( $survey['title'] ); ?></h1>
					</div>
					<div style="display: flex; gap: 12px;">
						<a href="<?php echo esc_url( admin_url('admin-ajax.php?action=formera_export_csv&survey_id=' . $survey_id) ); ?>" class="ds-btn ds-btn-secondary">
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px; height:16px;"><path d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
							Export CSV
						</a>
						<a href="<?php echo esc_url( admin_url('admin.php?page=formera-builder&id=' . $survey_id) ); ?>" class="ds-btn ds-btn-primary">Edit Survey</a>
					</div>
				</div>

				<!-- Stats Summary -->
				<div class="ds-stats-grid" style="margin-bottom: 24px;">
					<div class="ds-stat-card" style="border-top: 4px solid var(--ds-primary);">
						<div class="ds-stat-label">Total Responses</div>
						<div class="ds-stat-value"><?php echo esc_html( count($responses) ); ?></div>
					</div>
					<div class="ds-stat-card" style="border-top: 4px solid var(--ds-info);">
						<div class="ds-stat-label">Questions</div>
						<div class="ds-stat-value"><?php echo esc_html( count($questions) ); ?></div>
					</div>
				</div>

				<div class="ds-card" style="padding: 0;">
					<div class="ds-table-container">
						<table class="ds-table">
							<thead>
								<tr>
									<th style="min-width: 140px;">Date</th>
									<th style="min-width: 180px;">Respondent</th>
									<?php foreach ( $questions as $q ) : ?>
										<th style="min-width: 160px;"><?php echo esc_html( $q['title'] ); ?></th>
									<?php endforeach; ?>
								</tr>
							</thead>
							<tbody>
								<?php if ( empty( $responses ) ) : ?>
									<tr>
										<td colspan="<?php echo esc_attr( count($questions) + 2 ); ?>">
											<div class="ds-empty-state">
												<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
													<path d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
												</svg>
												<h3>No responses yet</h3>
												<p>Share your survey to start collecting responses.</p>
											</div>
										</td>
									</tr>
								<?php else : ?>
									<?php foreach ( $responses as $resp ) : 
										$data = json_decode( $resp['response_data'], true );
										$email = (string) ( $data['formera_responder_email'] ?? 'Anonymous' );
										?>
										<tr>
											<td style="color: var(--ds-text-light); font-size: 13px;"><?php echo esc_html( date_i18n( 'M j, Y H:i', strtotime( $resp['created_at'] ) ) ); ?></td>
											<td>
												<div style="display: flex; align-items: center; gap: 10px;">
													<div style="width: 32px; height: 32px; border-radius: 50%; background: var(--ds-primary-soft); color: var(--ds-primary); display: flex; align-items: center; justify-content: center; font-weight: 500; font-size: 12px;">
														<?php echo esc_html( strtoupper( substr( $email, 0, 1 ) ) ); ?>
													</div>
													<div>
														<div style="font-weight: 500;"><?php echo esc_html($email); ?></div>
														<div style="font-size: 11px; color: var(--ds-text-light);"><?php echo esc_html($resp['ip_address']); ?></div>
													</div>
												</div>
											</td>
											<?php foreach ( $questions as $index => $q ) : ?>
												<?php 
													$field_key = 'q_' . $index;
													$val = isset( $data[$field_key] ) ? $data[$field_key] : '-';
												?>
												<td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;"><?php echo esc_html( is_array($val) ? implode(', ', $val) : $val ); ?></td>
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
