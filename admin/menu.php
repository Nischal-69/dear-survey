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
		add_submenu_page( 'dear-survey', 'Email Broadcast', 'Email Broadcast', 'manage_options', 'dear-survey-email', array( $this, 'render_email' ) );
		add_submenu_page( 'dear-survey', 'Add New', 'Add New', 'manage_options', 'dear-survey-builder', array( $this, 'render_builder' ) );
		add_submenu_page( 'dear-survey', 'Settings', 'Settings', 'manage_options', 'dear-survey-settings', array( $this, 'render_settings' ) );
	}

	public function render_email() {
		require_once dirname( __FILE__ ) . '/email.php';
		$email = new Dear_Survey_Email( $this->db );
		$email->render_page();
	}

	public function render_dashboard() {
		$surveys = $this->db->get_surveys();
		$total_surveys = count( $surveys );
		$total_responses = 0;
		foreach ( $surveys as $s ) {
			$total_responses += count( $this->db->get_responses( $s['id'] ) );
		}
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
					<a href="<?php echo admin_url('admin.php?page=dear-survey'); ?>" class="ds-nav-item active">
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
						<div style="font-size: 12px; font-weight: 600; color: var(--ds-primary); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.05em;">Overview</div>
						<h1 class="ds-title">Dashboard</h1>
					</div>
					<a href="<?php echo admin_url('admin.php?page=dear-survey-builder'); ?>" class="ds-btn ds-btn-primary">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:18px; height:18px;"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
						Create New Survey
					</a>
				</div>

				<!-- Stats Grid -->
				<div class="ds-stats-grid">
					<div class="ds-stat-card">
						<div class="ds-stat-label">Total Surveys</div>
						<div class="ds-stat-value"><?php echo $total_surveys; ?></div>
						<div style="margin-top: 12px; font-size: 12px; color: var(--ds-success); font-weight: 600; display: flex; align-items: center; gap: 4px;">
							Active on site
						</div>
					</div>
					<div class="ds-stat-card">
						<div class="ds-stat-label">Total Responses</div>
						<div class="ds-stat-value"><?php echo $total_responses; ?></div>
						<div style="margin-top: 12px; font-size: 12px; color: var(--ds-text-light); font-weight: 600;">Captured across all surveys</div>
					</div>
					<div class="ds-stat-card">
						<div class="ds-stat-label">Success Rate</div>
						<div class="ds-stat-value">94.2%</div>
						<div style="margin-top: 12px; font-size: 12px; color: var(--ds-success); font-weight: 600; display: flex; align-items: center; gap: 4px;">
							High engagement
						</div>
					</div>
				</div>

				<div class="ds-card" style="padding: 0; overflow: hidden;">
					<div style="padding: 24px 32px; border-bottom: 1px solid var(--ds-border); display: flex; justify-content: space-between; align-items: center;">
						<h2 style="font-size: 18px; font-weight: 700; color: var(--ds-secondary); margin: 0;">Your Surveys</h2>
						<span style="font-size: 12px; font-weight: 600; color: var(--ds-text-light); text-transform: uppercase;">List of all surveys</span>
					</div>

					<div class="ds-table-container">
						<table class="ds-table">
							<thead>
								<tr>
									<th>Survey Name</th>
									<th>Shortcode</th>
									<th>Responses</th>
									<th style="text-align:right;">Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php if ( empty( $surveys ) ) : ?>
									<tr><td colspan="4" style="text-align:center; padding:100px; color:var(--ds-text-light);">
										<div style="margin-bottom: 16px; opacity: 0.1;"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:64px; height:64px; margin: 0 auto;"><path d="M12 4.5v15m7.5-7.5h-15"/></svg></div>
										No surveys found. Create your first one!
									</td></tr>
								<?php else : ?>
									<?php foreach ( $surveys as $survey ) : ?>
										<?php $resp_count = (int) count( $this->db->get_responses( $survey['id'] ) ); ?>
										<tr>
											<td>
												<div style="font-weight:700; font-size:15px; color:var(--ds-secondary); margin-bottom: 2px;"><?php echo esc_html( $survey['title'] ); ?></div>
												<div style="font-size:12px; color:var(--ds-text-light);">Created on <?php echo date_i18n( 'M j, Y', strtotime( $survey['created_at'] ) ); ?></div>
											</td>
											<td>
												<code style="background:var(--ds-primary-soft); color:var(--ds-primary); padding:6px 10px; border-radius:6px; font-size:12px; font-weight: 600; font-family: 'JetBrains Mono', monospace;">[dear_survey id="<?php echo $survey['id']; ?>"]</code>
											</td>
											<td>
												<a href="<?php echo admin_url( 'admin-ajax.php?action=ds_view_results&id=' . $survey['id'] ); ?>" style="text-decoration:none;">
													<span class="ds-badge" style="background: #E0F2FE; color: #0369A1;">
														<?php echo $resp_count; ?> entries
													</span>
												</a>
											</td>
											<td>
												<div class="ds-row-actions" style="display:flex; justify-content:flex-end; gap:8px;">
													<a href="<?php echo admin_url( 'admin.php?page=dear-survey-builder&id=' . $survey['id'] ); ?>" class="ds-btn ds-btn-secondary" style="padding: 8px 14px; font-size: 13px;">Edit</a>
													<button class="ds-btn ds-delete-trigger" data-href="<?php echo admin_url( 'admin.php?page=dear-survey&action=delete&id=' . $survey['id'] ); ?>" style="padding: 8px 12px; background: transparent; border-color: transparent; color: var(--ds-error);">
														<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width:18px; height:18px;"><path d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
													</button>
												</div>
											</td>
										</tr>
									<?php endforeach; ?>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<!-- Delete Confirmation Modal -->
			<div id="ds-delete-modal" class="ds-modal">
				<div class="ds-modal-content ds-animate">
					<div class="ds-modal-icon">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width:28px; height:28px;"><path d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
					</div>
					<h3 style="font-size:20px; font-weight:800; color:var(--ds-secondary); margin: 0 0 8px;">Delete Survey?</h3>
					<p style="color:var(--ds-text-light); font-size: 14px; margin-bottom:32px;">Are you sure? This will permanently delete the survey and all its responses.</p>
					<div style="display:flex; gap:12px;">
						<button id="ds-cancel-delete-btn" class="ds-btn ds-btn-secondary" style="flex:1;">Cancel</button>
						<button id="ds-confirm-delete-btn" class="ds-btn ds-btn-danger-solid" style="flex:1;">Delete Now</button>
					</div>
				</div>
			</div>
		</div>

		<script>
		jQuery(document).ready(function($) {
			$('.ds-delete-trigger').on('click', function() {
				const href = $(this).data('href');
				$('#ds-confirm-delete-btn').data('href', href);
				$('#ds-delete-modal').css('display', 'flex');
			});

			$('#ds-cancel-delete-btn').on('click', function() {
				$('#ds-delete-modal').hide();
			});

			$('#ds-confirm-delete-btn').on('click', function() {
				window.location.href = $(this).data('href');
			});
		});
		</script>
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
		$settings->render_settings();
	}
}
