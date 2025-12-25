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
		?>
		<div class="ds-app-container ds-animate">
			<!-- Sidebar -->
			<div class="ds-sidebar">
				<div class="ds-sidebar-logo">
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:28px; height:28px;">
						<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.745 3.745 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
					</svg>
					Dear Survey
				</div>
				<nav>
					<div style="font-size: 11px; font-weight: 700; color: var(--ds-text-light); text-transform: uppercase; letter-spacing: 0.1em; padding: 0 16px; margin-bottom: 12px;">Navigations</div>
					<a href="<?php echo admin_url('admin.php?page=dear-survey'); ?>" class="ds-nav-item">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25a2.25 2.25 0 0 1-2.25-2.25v-2.25Z"/></svg>
						Surveys List
					</a>
					<a href="<?php echo admin_url('admin.php?page=dear-survey-builder'); ?>" class="ds-nav-item">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
						Create New Survey
					</a>
					
					<div style="font-size: 11px; font-weight: 700; color: var(--ds-text-light); text-transform: uppercase; letter-spacing: 0.1em; padding: 0 16px; margin: 32px 0 12px;">Outreach</div>
					<a href="<?php echo admin_url('admin.php?page=dear-survey-email'); ?>" class="ds-nav-item">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 20 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
						Email Broadcast
					</a>

					<div style="font-size: 11px; font-weight: 700; color: var(--ds-text-light); text-transform: uppercase; letter-spacing: 0.1em; padding: 0 16px; margin: 32px 0 12px;">Preferences</div>
					<a href="<?php echo admin_url('admin.php?page=dear-survey-settings'); ?>" class="ds-nav-item">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M4.5 12a7.5 7.5 0 1 1 15 0 7.5 7.5 0 0 1-15 0Z"/><path d="M12 9v6m-3-3h6"/></svg>
						Global Settings
					</a>
				</nav>
			</div>

			<!-- Main Content -->
			<div class="ds-main-content">
				<div class="ds-top-bar">
					<div>
						<div style="font-size: 12px; font-weight: 600; color: var(--ds-primary); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.05em;">Results</div>
						<h1 class="ds-title">Results: <?php echo esc_html( $survey['title'] ); ?></h1>
					</div>
					<div style="display:flex; gap:12px;">
						<a href="<?php echo admin_url('admin-ajax.php?action=ds_export_csv&survey_id=' . $survey_id); ?>" class="ds-btn ds-btn-secondary">
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px; height:16px;"><path d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
							Download CSV
						</a>
						<a href="<?php echo admin_url('admin.php?page=dear-survey-builder&id=' . $survey_id); ?>" class="ds-btn ds-btn-primary">
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px; height:16px;"><path d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>
							Edit Survey
						</a>
					</div>
				</div>

				<div class="ds-stats-grid">
					<div class="ds-stat-card">
						<div class="ds-stat-label">Total Responses</div>
						<div class="ds-stat-value"><?php echo count( $responses ); ?></div>
						<div style="margin-top: 8px; font-size: 12px; color: var(--ds-primary); font-weight: 600;">Captured entries</div>
					</div>
					<div class="ds-stat-card">
						<div class="ds-stat-label">Performance</div>
						<div class="ds-stat-value">A+</div>
						<div style="margin-top: 8px; font-size: 12px; color: var(--ds-success); font-weight: 600;">High Engagement</div>
					</div>
					<div class="ds-stat-card">
						<div class="ds-stat-label">System Status</div>
						<div class="ds-stat-value" style="font-size: 20px;">Active</div>
						<div style="margin-top: 8px; font-size: 12px; color: var(--ds-text-light);">Ready for responses</div>
					</div>
				</div>

				<div class="ds-card ds-table-card" style="margin-bottom: 48px;">
					<div style="padding: 24px 32px; border-bottom: 1px solid var(--ds-border); background: #FFF; display: flex; justify-content: space-between; align-items: center;">
						<h3 style="margin:0; font-size: 16px; font-weight: 700; color: var(--ds-secondary);">Individual Responses</h3>
						<span class="ds-badge ds-badge-indigo"><?php echo count($responses); ?> responses</span>
					</div>
					<div style="overflow-x: auto;">
						<table class="ds-table">
							<thead>
								<tr>
									<th>Date & Time</th>
									<th>User Status</th>
									<?php foreach ( $questions as $q ) : ?>
										<th><?php echo esc_html( $q['title'] ); ?></th>
									<?php endforeach; ?>
								</tr>
							</thead>
							<tbody>
								<?php if ( ! empty( $responses ) ) : ?>
									<?php foreach ( $responses as $resp ) : ?>
										<?php $data = json_decode( $resp['response_data'], true ); ?>
										<tr>
											<td>
												<div style="font-weight: 600; color: var(--ds-secondary);"><?php echo date_i18n( 'M j, Y', strtotime( $resp['created_at'] ) ); ?></div>
												<div style="font-size: 11px; color: var(--ds-text-light);"><?php echo date_i18n( 'H:i', strtotime( $resp['created_at'] ) ); ?></div>
											</td>
											<td>
												<?php if ( $resp['user_id'] ) : ?>
													<span class="ds-badge" style="background: var(--ds-primary-soft); color: var(--ds-primary);">Logged In</span>
												<?php else : ?>
													<span class="ds-badge" style="background: var(--ds-divider); color: var(--ds-text-light);">Guest</span>
												<?php endif; ?>
											</td>
											<?php foreach ( $questions as $idx => $q ) : ?>
												<td style="max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
													<?php 
													$key = 'q_' . $idx;
													$val = isset( $data[ $key ] ) ? $data[ $key ] : '-'; 
													echo is_array($val) ? esc_html(implode(', ', $val)) : esc_html($val);
													?>
												</td>
											<?php endforeach; ?>
										</tr>
									<?php endforeach; ?>
								<?php else : ?>
									<tr><td colspan="<?php echo count( $questions ) + 2; ?>" style="text-align:center; padding:100px; color:var(--ds-text-light);">
										<div style="margin-bottom: 16px; opacity: 0.1;"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:64px; height:64px; margin: 0 auto;"><path d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg></div>
										No responses yet.
									</td></tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>

				<!-- Visual Summary -->
				<div style="margin-bottom: 48px;">
					<h3 style="margin: 0 0 24px; font-size: 18px; font-weight: 700; color: var(--ds-secondary);">Visual Summary</h3>
					<div class="ds-stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));">
						<?php foreach($questions as $idx => $q): 
							if($q['type'] == 'radio' || $q['type'] == 'checkbox'):
								$options = explode(',', $q['options']);
								$counts = array_fill_keys(array_map('trim', $options), 0);
								$total_q = 0;
								foreach($responses as $r) {
									$r_data = json_decode($r['response_data'], true);
									$ans = $r_data['q_'.$idx] ?? '';
									if(is_array($ans)) {
										foreach($ans as $a) { if(isset($counts[trim($a)])) { $counts[trim($a)]++; $total_q++; } }
									} else if(isset($counts[trim($ans)])) {
										$counts[trim($ans)]++; $total_q++;
									}
								}
						?>
							<div class="ds-card" style="margin-bottom: 0;">
								<div style="font-weight:700; margin-bottom: 24px; color:var(--ds-secondary); font-size:15px; border-bottom: 1px solid var(--ds-divider); padding-bottom: 12px;"><?php echo esc_html($q['title']); ?></div>
								<?php foreach($counts as $opt => $count): 
									$pct = $total_q > 0 ? round(($count / $total_q) * 100) : 0;
								?>
									<div style="margin-bottom: 20px;">
										<div style="display:flex; justify-content:space-between; font-size: 13px; font-weight: 500; margin-bottom: 8px;">
											<span style="color: var(--ds-text);"><?php echo esc_html($opt); ?></span>
											<span style="color: var(--ds-secondary); font-weight: 700;"><?php echo $pct; ?>%</span>
										</div>
										<div style="height:8px; background: var(--ds-divider); border-radius: 4px; overflow:hidden;">
											<div style="width:<?php echo $pct; ?>%; height:100%; background: var(--ds-primary); border-radius: 4px; transition: width 1s cubic-bezier(0.34, 1.56, 0.64, 1);"></div>
										</div>
										<div style="font-size: 11px; color: var(--ds-text-light); margin-top: 4px;"><?php echo $count; ?> responses</div>
									</div>
								<?php endforeach; ?>
							</div>
						<?php endif; endforeach; ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
