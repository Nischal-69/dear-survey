<?php
/**
 * Admin Dashboard View
 * Markup Overhaul for Premium UI/UX
 */

class Dear_Survey_Menu {

	private $db;

	public function __construct( $db ) {
		$this->db = $db;
	}

	public function register_menus() {
		// Handle Delete Action
		if ( isset( $_GET['action'] ) && $_GET['action'] === 'delete' && isset( $_GET['id'] ) ) {
			$this->db->delete_survey( intval( $_GET['id'] ) );
			wp_redirect( admin_url( 'admin.php?page=dear-survey&deleted=true' ) );
			exit;
		}

		add_menu_page( 'Dear Survey', 'Dear Survey', 'manage_options', 'dear-survey', array( $this, 'render_dashboard' ), 'dashicons-clipboard', 25 );
		add_submenu_page( 'dear-survey', 'Surveys', 'Surveys', 'manage_options', 'dear-survey', array( $this, 'render_dashboard' ) );
		add_submenu_page( 'dear-survey', 'Add New', 'Add New', 'manage_options', 'dear-survey-builder', array( $this, 'render_builder' ) );
		add_submenu_page( 'dear-survey', 'Settings', 'Settings', 'manage_options', 'dear-survey-settings', array( $this, 'render_settings' ) );
	}

	public function render_dashboard() {
		$surveys = $this->db->get_surveys();
		
		// Global Stats Calculation
		$total_surveys = count($surveys);
		global $wpdb;
		$total_responses = $total_surveys > 0 ? (int) $wpdb->get_var( "SELECT count(*) FROM {$wpdb->prefix}ds_responses" ) : 0;
		?>
		<div class="ds-wrap">
			<div class="ds-header">
				<div>
					<h1 class="ds-title">Dear Survey</h1>
					<p class="ds-subtitle">Create and manage your surveys effortlessly.</p>
				</div>
				<a href="<?php echo admin_url( 'admin.php?page=dear-survey-builder' ); ?>" class="ds-btn ds-btn-primary">+ Create New Survey</a>
			</div>

			<!-- Stats Banner -->
			<div class="ds-stats-grid">
				<div class="ds-stat-card">
					<div class="ds-stat-label">Total Active Surveys</div>
					<div class="ds-stat-value"><?php echo $total_surveys; ?></div>
				</div>
				<div class="ds-stat-card">
					<div class="ds-stat-label">Total Submissions</div>
					<div class="ds-stat-value"><?php echo $total_responses; ?></div>
				</div>
				<div class="ds-stat-card" style="border-left-color: #CBD5E0; opacity: 0.7;">
					<div class="ds-stat-label">Avg. Completion <span class="ds-badge ds-badge-pro">PRO</span></div>
					<div class="ds-stat-value">--</div>
				</div>
			</div>

			<?php if ( empty( $surveys ) ) : ?>
				<!-- Clean Empty State -->
				<div class="ds-card" style="text-align:center; padding: 80px 40px;">
					<div style="font-size: 64px; margin-bottom: 20px;">📝</div>
					<h2 style="font-size: 24px; font-weight: 800; margin-bottom: 10px;">Ready to start?</h2>
					<p style="color:var(--ds-text-light); margin-bottom: 30px; font-size: 16px;">You haven't created any surveys yet. Launch your first one in minutes.</p>
					<a href="<?php echo admin_url( 'admin.php?page=dear-survey-builder' ); ?>" class="ds-btn ds-btn-primary">Build Your First Survey</a>
				</div>
			<?php else : ?>
				<!-- Redesigned Surveys Table -->
				<div class="ds-table-container">
					<table class="ds-table">
						<thead>
							<tr>
								<th width="45%">Survey Detail</th>
								<th>Shortcode</th>
								<th>Responses</th>
								<th style="text-align:right;">Manage</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $surveys as $survey ) : ?>
								<?php $resp_count = (int) count( $this->db->get_responses( $survey['id'] ) ); ?>
								<tr>
									<td>
										<div style="font-weight: 700; font-size: 16px; margin-bottom: 4px;">
											<a href="<?php echo admin_url( 'admin.php?page=dear-survey-builder&id=' . $survey['id'] ); ?>" style="color:var(--ds-text); text-decoration:none;">
												<?php echo esc_html( $survey['title'] ); ?>
											</a>
										</div>
										<div style="font-size: 12px; color:var(--ds-text-light);">
											Created on <?php echo date_i18n( 'M j, Y', strtotime( $survey['created_at'] ) ); ?>
										</div>
									</td>
									<td>
										<code style="background:var(--ds-bg); padding:6px 12px; border-radius:6px; font-size:13px; color:var(--ds-primary);">[dear_survey id="<?php echo $survey['id']; ?>"]</code>
									</td>
									<td>
										<a href="<?php echo admin_url( 'admin.php?page=dear-survey-builder&view=results&id=' . $survey['id'] ); ?>" style="text-decoration:none;">
											<span class="ds-badge" style="background:#EBF8FF; color:#2B6CB0;"><?php echo $resp_count; ?> Responses</span>
										</a>
									</td>
									<td style="text-align:right;">
										<div style="display:flex; justify-content:flex-end; gap:8px;">
											<a href="<?php echo admin_url( 'admin.php?page=dear-survey-builder&id=' . $survey['id'] ); ?>" class="ds-btn ds-btn-secondary" style="padding:6px 12px; font-size:12px;">Edit</a>
											<a href="<?php echo admin_url( 'admin.php?page=dear-survey-builder&view=results&id=' . $survey['id'] ); ?>" class="ds-btn ds-btn-secondary" style="padding:6px 12px; font-size:12px;">Results</a>
											<button class="ds-btn ds-btn-danger ds-delete-trigger" data-href="<?php echo admin_url( 'admin.php?page=dear-survey&action=delete&id=' . $survey['id'] ); ?>" style="padding:6px 8px; border:none; background:none; font-size:14px;">🗑️</button>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>

			<!-- Redesigned Confirmation Modal -->
			<div id="ds-delete-modal" class="ds-modal">
				<div class="ds-modal-content">
					<div style="font-size: 48px; margin-bottom: 15px;">⚠️</div>
					<h3 style="font-size: 22px; font-weight: 800; margin-bottom: 10px;">Are you absolutely sure?</h3>
					<p style="color:var(--ds-text-light); margin-bottom: 30px;">Deleting this survey will permanently remove all associated responses. This action is irreversible.</p>
					<div style="display:flex; gap:15px; justify-content:center;">
						<button id="ds-cancel-delete-btn" class="ds-btn ds-btn-secondary" style="flex:1;">Keep it</button>
						<button id="ds-confirm-delete-btn" class="ds-btn ds-btn-primary" style="flex:1; background-color:#E53E3E;">Yes, Delete It</button>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	public function render_builder() {
		require_once dirname( __FILE__ ) . '/survey-builder.php';
		$builder = new Dear_Survey_Builder( $this->db );
		$builder->render();
	}

	public function render_settings() {
		require_once dirname( __FILE__ ) . '/settings.php';
		$settings = new Dear_Survey_Settings();
		$settings->render();
	}
}
