<?php
/**
 * Admin Dashboard & Navigation
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
			wp_redirect( admin_url( 'admin.php?page=dear-survey-list&deleted=true' ) );
			exit;
		}

		// Root: Homepage
		add_menu_page( 'Dear Survey', 'Dear Survey', 'manage_options', 'dear-survey', array( $this, 'render_homepage' ), 'dashicons-clipboard', 25 );
		
		// Submenus
		add_submenu_page( 'dear-survey', 'Home', 'Home', 'manage_options', 'dear-survey', array( $this, 'render_homepage' ) );
		add_submenu_page( 'dear-survey', 'Surveys', 'Surveys List', 'manage_options', 'dear-survey-list', array( $this, 'render_dashboard' ) );
		add_submenu_page( 'dear-survey', 'Entries', 'Survey Entries', 'manage_options', 'dear-survey-entries', array( $this, 'render_entries' ) );
		add_submenu_page( 'dear-survey', 'Contact List', 'Contact List', 'manage_options', 'dear-survey-contacts', array( $this, 'render_contacts' ) );
		add_submenu_page( 'dear-survey', 'Add New', 'Add New', 'manage_options', 'dear-survey-builder', array( $this, 'render_builder' ) );
		add_submenu_page( 'dear-survey', 'Settings', 'Settings', 'manage_options', 'dear-survey-settings', array( $this, 'render_settings' ) );
	}

	public function get_sidebar( $active_page = 'home' ) {
		?>
		<div class="ds-sidebar">
			<div class="ds-sidebar-logo">
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:28px; height:28px;">
					<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.745 3.745 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
				</svg>
				Dear Survey
			</div>
			<nav>
				<div style="font-size: 11px; font-weight: 700; color: var(--ds-text-light); text-transform: uppercase; letter-spacing: 0.1em; padding: 0 16px; margin-bottom: 12px;">Navigations</div>
				<a href="<?php echo admin_url('admin.php?page=dear-survey'); ?>" class="ds-nav-item <?php echo $active_page == 'home' ? 'active' : ''; ?>">
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
					Home
				</a>
				<a href="<?php echo admin_url('admin.php?page=dear-survey-list'); ?>" class="ds-nav-item <?php echo $active_page == 'surveys' ? 'active' : ''; ?>">
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25a2.25 2.25 0 0 1-2.25-2.25v-2.25Z"/></svg>
					Surveys List
				</a>
				<a href="<?php echo admin_url('admin.php?page=dear-survey-entries'); ?>" class="ds-nav-item <?php echo $active_page == 'entries' ? 'active' : ''; ?>">
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M7.5 7.5h-.75A2.25 2.25 0 0 0 4.5 9.75v7.5a2.25 2.25 0 0 0 2.25 2.25h7.5a2.25 2.25 0 0 0 2.25-2.25v-7.5a2.25 2.25 0 0 0-2.25-2.25h-.75m-6 3.75 3 3m0 0 3-3m-3 3V1.5m6 9h.75a2.25 2.25 0 0 1 2.25 2.25v7.5a2.25 2.25 0 0 1-2.25 2.25h-7.5a2.25 2.25 0 0 1-2.25-2.25v-.75" /></svg>
					Survey Entries
				</a>
				<a href="<?php echo admin_url('admin.php?page=dear-survey-contacts'); ?>" class="ds-nav-item <?php echo $active_page == 'contacts' ? 'active' : ''; ?>">
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
					Contact List
				</a>
				
				<div style="font-size: 11px; font-weight: 700; color: var(--ds-text-light); text-transform: uppercase; letter-spacing: 0.1em; padding: 0 16px; margin: 32px 0 12px;">Preferences</div>
				<a href="<?php echo admin_url('admin.php?page=dear-survey-settings'); ?>" class="ds-nav-item <?php echo $active_page == 'settings' ? 'active' : ''; ?>">
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M4.5 12a7.5 7.5 0 1 1 15 0 7.5 7.5 0 0 1-15 0Z"/><path d="M12 9v6m-3-3h6"/></svg>
					Global Settings
				</a>
			</nav>
		</div>
		<?php
	}

	public function render_homepage() {
		$surveys = $this->db->get_surveys();
		$total_surveys = count( $surveys );
		$all_responses = $this->db->get_all_responses();
		$total_responses = count( $all_responses );
		$recent_entries = array_slice( $all_responses, 0, 5 );
		?>
		<div class="ds-app-container ds-animate">
			<?php $this->get_sidebar('home'); ?>
			<div class="ds-main-content">
				<div class="ds-top-bar">
					<div>
						<div style="font-size: 12px; font-weight: 600; color: var(--ds-primary); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.05em;">Welcome Back</div>
						<h1 class="ds-title">Dear Survey</h1>
					</div>
					<a href="<?php echo admin_url('admin.php?page=dear-survey-builder'); ?>" class="ds-btn ds-btn-primary">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:18px; height:18px;"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
						Quick Build
					</a>
				</div>

				<div class="ds-stats-grid">
					<div class="ds-stat-card" style="border-left: 4px solid #10B981; background: linear-gradient(to right, #F0FDF4, #FFFFFF); box-shadow: var(--ds-shadow-sm);">
						<div class="ds-stat-label" style="color: #059669;">Total Surveys</div>
						<div class="ds-stat-value" style="color: var(--ds-secondary);"><?php echo $total_surveys; ?></div>
						<div style="margin-top: 12px; font-size: 12px; color: #059669; font-weight: 600;">Managed surveys</div>
					</div>
					<div class="ds-stat-card" style="border-left: 4px solid #6366F1; background: linear-gradient(to right, #EEF2FF, #FFFFFF); box-shadow: var(--ds-shadow-sm);">
						<div class="ds-stat-label" style="color: #4F46E5;">Total Responses</div>
						<div class="ds-stat-value" style="color: var(--ds-secondary);"><?php echo $total_responses; ?></div>
						<div style="margin-top: 12px; font-size: 12px; color: #4F46E5; font-weight: 600;">Collective feedback</div>
					</div>
				</div>

				<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-top: 24px;">
					<!-- Recent Activity -->
					<div class="ds-card">
						<h3 style="margin: 0 0 20px; font-size: 16px; font-weight: 700; color: var(--ds-secondary);">Recent Entries</h3>
						<div class="ds-table-container">
							<?php if ( empty( $recent_entries ) ) : ?>
								<p style="text-align:center; padding: 40px; color: var(--ds-text-light);">No entries yet.</p>
							<?php else : ?>
								<table class="ds-table">
									<tbody>
										<?php foreach ( $recent_entries as $entry ) : 
											$s = $this->db->get_survey( $entry['survey_id'] ); ?>
										<tr>
											<td>
												<div style="font-weight:600; font-size:14px;"><?php echo $s ? esc_html($s['title']) : 'Deleted Survey'; ?></div>
												<div style="font-size:11px; color:var(--ds-text-light);"><?php echo date_i18n( 'M j, H:i', strtotime($entry['created_at']) ); ?></div>
											</td>
											<td style="text-align:right;">
												<a href="<?php echo admin_url('admin.php?page=dear-survey-entries'); ?>" style="color: var(--ds-primary); font-size: 12px; font-weight: 700; text-decoration:none;">View All</a>
											</td>
										</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							<?php endif; ?>
						</div>
					</div>

					<!-- Quick Actions -->
					<div class="ds-card" style="background: var(--ds-primary-soft); border-color: var(--ds-primary-glow);">
						<h3 style="margin: 0 0 20px; font-size: 16px; font-weight: 700; color: var(--ds-primary);">Quick Actions</h3>
						<div style="display: flex; flex-direction: column; gap: 12px;">
							<a href="<?php echo admin_url('admin.php?page=dear-survey-builder'); ?>" class="ds-btn ds-btn-primary" style="justify-content: center;">Create New Survey</a>
							<a href="<?php echo admin_url('admin.php?page=dear-survey-contacts'); ?>" class="ds-btn ds-btn-secondary" style="justify-content: center; background: white;">Manage Contacts</a>
							<a href="<?php echo admin_url('admin.php?page=dear-survey-settings'); ?>" class="ds-btn ds-btn-secondary" style="justify-content: center; background: white;">Configure Settings</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	public function render_dashboard() {
		$surveys = $this->db->get_surveys();
		?>
		<div class="ds-app-container ds-animate">
			<?php $this->get_sidebar('surveys'); ?>
			<div class="ds-main-content">
				<div class="ds-top-bar">
					<h1 class="ds-title">Surveys List</h1>
					<a href="<?php echo admin_url('admin.php?page=dear-survey-builder'); ?>" class="ds-btn ds-btn-primary">Add New Survey</a>
				</div>
				<div class="ds-card" style="padding:0;">
					<div class="ds-table-container">
						<table class="ds-table">
							<thead>
								<tr>
									<th>Survey Name</th>
									<th>Shortcode</th>
									<th style="text-align:right;">Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $surveys as $survey ) : ?>
									<tr>
										<td>
											<div style="font-weight:700; color:var(--ds-secondary);"><?php echo esc_html( $survey['title'] ); ?></div>
											<div style="font-size:12px; color:var(--ds-text-light);"><?php echo date_i18n( 'M j, Y', strtotime($survey['created_at']) ); ?></div>
										</td>
										<td><code>[dear_survey id="<?php echo $survey['id']; ?>"]</code></td>
										<td style="text-align:right;">
											<a href="<?php echo admin_url( 'admin.php?page=dear-survey-builder&id=' . $survey['id'] ); ?>" class="ds-btn ds-btn-secondary" style="padding: 6px 12px;">Edit</a>
											<button class="ds-btn ds-btn-secondary ds-delete-trigger" data-href="<?php echo admin_url( 'admin.php?page=dear-survey&action=delete&id=' . $survey['id'] ); ?>" style="padding: 6px 12px; color: var(--ds-error);">Delete</button>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<!-- Delete Confirmation -->
			<div id="ds-delete-modal" class="ds-modal">
				<div class="ds-modal-content ds-animate">
					<h3 style="margin:0 0 12px;">Delete Survey?</h3>
					<p style="color:var(--ds-text-light); font-size: 14px; margin-bottom:24px;">This will remove the survey and all its responses.</p>
					<div style="display:flex; gap:12px;">
						<button id="ds-cancel-delete-btn" class="ds-btn ds-btn-secondary" style="flex:1;">Cancel</button>
						<button id="ds-confirm-delete-btn" class="ds-btn ds-btn-danger-solid" style="flex:1;">Delete</button>
					</div>
				</div>
			</div>
		</div>
		<script>
		jQuery(document).ready(function($) {
			$(document).on('click', '.ds-delete-trigger', function() {
				const href = $(this).data('href');
				$('#ds-confirm-delete-btn').data('href', href);
				$('#ds-delete-modal').css('display', 'flex');
			});
			$('#ds-cancel-delete-btn').on('click', function() { $('#ds-delete-modal').hide(); });
			$('#ds-confirm-delete-btn').on('click', function() { window.location.href = $(this).data('href'); });
		});
		</script>
		<?php
	}

	public function render_entries() {
		$all_responses = $this->db->get_all_responses();
		?>
		<div class="ds-app-container ds-animate">
			<?php $this->get_sidebar('entries'); ?>
			<div class="ds-main-content">
				<div class="ds-top-bar">
					<h1 class="ds-title">All Survey Entries</h1>
				</div>
				<div class="ds-card" style="padding:0;">
					<div class="ds-table-container">
						<table class="ds-table">
							<thead>
								<tr>
									<th>Date</th>
									<th>Survey</th>
									<th>User/IP</th>
									<th>Response Summary</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($all_responses as $resp) : 
									$s = $this->db->get_survey($resp['survey_id']);
									$data = json_decode($resp['response_data'], true);
									$email = $data['ds_responder_email'] ?? 'Anonymous';
									?>
									<tr>
										<td><?php echo date_i18n('M j, Y H:i', strtotime($resp['created_at'])); ?></td>
										<td><strong><?php echo $s ? esc_html($s['title']) : 'Deleted'; ?></strong></td>
										<td>
											<div style="font-size:13px;"><?php echo esc_html($email); ?></div>
											<div style="font-size:11px; color:var(--ds-text-light);"><?php echo esc_html($resp['ip_address']); ?></div>
										</td>
										<td>
											<div style="font-size:12px; max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
												<?php foreach($data as $k => $v) {
													if($k == 'ds_responder_email') continue;
													echo esc_html($v) . " | ";
												} ?>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	public function render_contacts() {
		$all_responses = $this->db->get_all_responses();
		$contacts = [];
		foreach ($all_responses as $resp) {
			$data = json_decode($resp['response_data'], true);
			if (!empty($data['ds_responder_email'])) {
				$email = strtolower(trim($data['ds_responder_email']));
				if (!isset($contacts[$email])) {
					$contacts[$email] = [
						'email' => $email,
						'surveys' => [],
						'last_activity' => $resp['created_at']
					];
				}
				$s = $this->db->get_survey($resp['survey_id']);
				if ($s) $contacts[$email]['surveys'][$s['id']] = $s['title'];
				if (strtotime($resp['created_at']) > strtotime($contacts[$email]['last_activity'])) {
					$contacts[$email]['last_activity'] = $resp['created_at'];
				}
			}
		}
		?>
		<div class="ds-app-container ds-animate">
			<?php $this->get_sidebar('contacts'); ?>
			<div class="ds-main-content">
				<div class="ds-top-bar">
					<h1 class="ds-title">Collected Contact List</h1>
				</div>
				<div class="ds-card" style="padding:0;">
					<div class="ds-table-container">
						<table class="ds-table">
							<thead>
								<tr>
									<th>Email Address</th>
									<th>Surveys Taken</th>
									<th>Last Activity</th>
								</tr>
							</thead>
							<tbody>
								<?php if (empty($contacts)) : ?>
									<tr><td colspan="3" style="text-align:center; padding: 50px;">No contacts collected yet.</td></tr>
								<?php else : ?>
									<?php foreach ($contacts as $contact) : ?>
										<tr>
											<td><div style="font-weight:600;"><?php echo esc_html($contact['email']); ?></div></td>
											<td>
												<?php foreach($contact['surveys'] as $id => $title) : ?>
													<span class="ds-badge" style="background:var(--ds-primary-soft); color:var(--ds-primary); margin-right:4px;">
														<?php echo esc_html($title); ?>
													</span>
												<?php endforeach; ?>
											</td>
											<td><?php echo date_i18n('M j, Y', strtotime($contact['last_activity'])); ?></td>
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
