<?php
/**
 * Admin Dashboard & Navigation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Formera_Menu {

	private $db;

	public function __construct( $db ) {
		$this->db = $db;
	}

	public function register_menus() {
		// Handle Delete Action with nonce verification
		if ( isset( $_GET['action'] ) && $_GET['action'] === 'delete' && isset( $_GET['id'] ) ) {
			if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'formera_delete_survey_' . intval( $_GET['id'] ) ) ) {
				wp_die( esc_html__( 'Security check failed.', 'formera' ) );
			}
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'Unauthorized access.', 'formera' ) );
			}
			$this->db->delete_survey( intval( $_GET['id'] ) );
			wp_safe_redirect( admin_url( 'admin.php?page=formera-list&deleted=true' ) );
			exit;
		}

		// Root: Homepage
		add_menu_page( 'Formera', 'Formera', 'manage_options', 'formera', array( $this, 'render_homepage' ), 'dashicons-feedback', 6 );
		
		
		// Submenus
		add_submenu_page( 'formera', 'Home', 'Home', 'manage_options', 'formera', array( $this, 'render_homepage' ) );
		add_submenu_page( 'formera', 'Forms', 'Forms List', 'manage_options', 'formera-list', array( $this, 'render_dashboard' ) );
		add_submenu_page( 'formera', 'Contact List', 'Contact List', 'manage_options', 'formera-contacts', array( $this, 'render_contacts' ) );
		add_submenu_page( 'formera', 'Add New', 'Add New', 'manage_options', 'formera-builder', array( $this, 'render_builder' ) );
		// Hidden page for viewing survey submissions
		add_submenu_page( null, 'Form Results', 'Results', 'manage_options', 'formera-results', array( $this, 'render_survey_results' ) );
	}

	public function get_sidebar( $active_page = 'home' ) {
		?>
		<div class="ds-sidebar">
			<div class="ds-sidebar-logo">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:32px; height:32px; color: var(--ds-primary);">
					<path fill-rule="evenodd" d="M7.502 6h7.128A3.375 3.375 0 0118 9.375v9.375a3 3 0 003-3V6.108c0-1.505-1.125-2.811-2.664-2.94a48.972 48.972 0 00-.673-.05A3 3 0 0015 1.5h-1.5a3 3 0 00-2.663 1.618c-.225.015-.45.032-.673.05C8.662 3.295 7.554 4.542 7.502 6zM13.5 3A1.5 1.5 0 0012 4.5h4.5A1.5 1.5 0 0015 3h-1.5z" clip-rule="evenodd" />
					<path fill-rule="evenodd" d="M3 9.375C3 8.339 3.84 7.5 4.875 7.5h9.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-9.75A1.875 1.875 0 013 20.625V9.375zm9.586 4.594a.75.75 0 00-1.172-.938l-2.476 3.096-.908-.907a.75.75 0 00-1.06 1.06l1.5 1.5a.75.75 0 001.116-.062l3-3.75z" clip-rule="evenodd" />
				</svg>
				Formera
			</div>
			<nav>
				<a href="<?php echo esc_url( admin_url('admin.php?page=formera') ); ?>" class="ds-nav-item <?php echo esc_attr( $active_page == 'home' ? 'active' : '' ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
					Home
				</a>
				<a href="<?php echo esc_url( admin_url('admin.php?page=formera-list') ); ?>" class="ds-nav-item <?php echo esc_attr( $active_page == 'surveys' ? 'active' : '' ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" /></svg>
					Forms
				</a>
				<a href="<?php echo esc_url( admin_url('admin.php?page=formera-contacts') ); ?>" class="ds-nav-item <?php echo esc_attr( $active_page == 'contacts' ? 'active' : '' ); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
					Contacts
				</a>
				<a href="<?php echo esc_url( admin_url('admin.php?page=formera-builder') ); ?>" class="ds-nav-item" style="margin-top: 16px; background: var(--ds-primary); color: white; border-radius: 4px; margin-left: 0; padding-left: 16px;">
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
					New Form
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
		?>
		<div class="ds-app-container ds-animate">
			<?php $this->get_sidebar('home'); ?>
			<div class="ds-main-content">
				<div class="ds-top-bar">
					<div>
						<div style="font-size: 12px; font-weight: 500; color: var(--ds-primary); margin-bottom: 4px; letter-spacing: 0.5px;">DASHBOARD</div>
						<h1 class="ds-title">Formera</h1>
					</div>
					<a href="<?php echo esc_url( admin_url('admin.php?page=formera-builder') ); ?>" class="ds-btn ds-btn-primary">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px; height:18px;"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
						Create Form
					</a>
				</div>

				<div class="ds-stats-grid">
					<div class="ds-stat-card" style="border-top: 4px solid var(--ds-primary);">
						<div class="ds-stat-label">Total Forms</div>
						<div class="ds-stat-value"><?php echo esc_html( $total_surveys ); ?></div>
						<div style="margin-top: 12px; font-size: 13px; color: var(--ds-text-light);">Active forms in your workspace</div>
					</div>
					<div class="ds-stat-card" style="border-top: 4px solid var(--ds-info);">
						<div class="ds-stat-label">Total Responses</div>
						<div class="ds-stat-value"><?php echo esc_html( $total_responses ); ?></div>
						<div style="margin-top: 12px; font-size: 13px; color: var(--ds-text-light);">Collected submissions</div>
					</div>
				</div>

				<!-- Quick Actions -->
				<div class="ds-card" style="border-top: 4px solid var(--ds-primary);">
					<h3 style="margin: 0 0 20px; font-size: 16px; font-weight: 500; color: var(--ds-secondary);">Quick Actions</h3>
					<div style="display: flex; gap: 12px; flex-wrap: wrap;">
						<a href="<?php echo esc_url( admin_url('admin.php?page=formera-builder') ); ?>" class="ds-btn ds-btn-primary">
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px; height:16px;"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
							New Form
						</a>
						<a href="<?php echo esc_url( admin_url('admin.php?page=formera-contacts') ); ?>" class="ds-btn ds-btn-secondary">
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px; height:16px;"><path d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
							Contacts
						</a>
						<a href="<?php echo esc_url( admin_url('admin.php?page=formera-list') ); ?>" class="ds-btn ds-btn-secondary">
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px; height:16px;"><path d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" /></svg>
							All Forms
						</a>
					</div>
				</div>

				<!-- Recent Forms -->
				<?php if ( ! empty( $surveys ) ) : ?>
				<div class="ds-card" style="padding: 0;">
					<div style="padding: 20px 24px; border-bottom: 1px solid var(--ds-divider);">
						<h3 style="margin: 0; font-size: 16px; font-weight: 500; color: var(--ds-secondary);">Recent Forms</h3>
					</div>
					<div class="ds-table-container">
						<table class="ds-table">
							<thead>
								<tr>
									<th>Name</th>
									<th>Responses</th>
									<th>Shortcode</th>
									<th style="text-align:right;">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$recent = array_slice( $surveys, 0, 5 );
								foreach ( $recent as $survey ) : 
									$responses = $this->db->get_responses( $survey['id'] );
									$response_count = count( $responses );
								?>
									<tr>
										<td>
											<div style="font-weight: 500; color: var(--ds-secondary);"><?php echo esc_html( $survey['title'] ); ?></div>
											<div style="font-size: 12px; color: var(--ds-text-light); margin-top: 2px;"><?php echo esc_html( date_i18n( 'M j, Y', strtotime($survey['created_at']) ) ); ?></div>
										</td>
										<td>
											<span class="ds-badge ds-badge-indigo"><?php echo esc_html( $response_count ); ?> responses</span>
										</td>
										<td>
											<div class="ds-shortcode" onclick="copyShortcode(this)" title="Click to copy">
												<code>[formera id="<?php echo esc_attr( $survey['id'] ); ?>"]</code>
												<button type="button" class="ds-copy-btn" aria-label="Copy shortcode">
													<svg class="ds-copy-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" /></svg>
												</button>
											</div>
										</td>
										<td style="text-align: right;">
											<div style="display: flex; gap: 8px; justify-content: flex-end;">
												<a href="<?php echo esc_url( admin_url( 'admin.php?page=formera-builder&id=' . $survey['id'] ) ); ?>" class="ds-btn ds-btn-secondary" style="padding: 6px 16px;">Edit</a>
												<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=formera&action=delete&id=' . $survey['id'] ), 'formera_delete_survey_' . $survey['id'] ) ); ?>" 
												   class="ds-btn ds-btn-danger" 
												   style="padding: 6px 16px; background: #dc2626; color: white; border-color: #dc2626;" 
												   onclick="return confirm('Are you sure you want to delete this form? This action cannot be undone.');" 
												   title="Delete Form">
													<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px; height:16px;">
														<path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
													</svg>
												</a>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
				<?php endif; ?>
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
					<h1 class="ds-title">Forms</h1>
					<a href="<?php echo esc_url( admin_url('admin.php?page=formera-builder') ); ?>" class="ds-btn ds-btn-primary">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px; height:16px;"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
						New Form
					</a>
				</div>
				<?php if ( empty( $surveys ) ) : ?>
					<div class="ds-card">
						<div class="ds-empty-state">
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
								<path d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
							</svg>
							<h3>No forms yet</h3>
							<p>Create your first form to start collecting responses.</p>
							<a href="<?php echo esc_url( admin_url('admin.php?page=formera-builder') ); ?>" class="ds-btn ds-btn-primary" style="margin-top: 16px;">Create Form</a>
						</div>
					</div>
				<?php else : ?>
				<div class="ds-card" style="padding:0;">
					<div class="ds-table-container">
						<table class="ds-table">
							<thead>
								<tr>
									<th>Form Name</th>
									<th>Responses</th>
									<th>Shortcode</th>
									<th style="text-align:right;">Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $surveys as $survey ) : 
									$responses = $this->db->get_responses( $survey['id'] );
									$response_count = count( $responses );
								?>
									<tr>
										<td>
											<div style="font-weight: 500; color: var(--ds-secondary);"><?php echo esc_html( $survey['title'] ); ?></div>
											<div style="font-size: 12px; color: var(--ds-text-light); margin-top: 2px;"><?php echo esc_html( date_i18n( 'M j, Y', strtotime($survey['created_at']) ) ); ?></div>
										</td>
										<td>
											<a href="<?php echo esc_url( admin_url( 'admin.php?page=formera-results&id=' . $survey['id'] ) ); ?>" style="display: inline-flex; align-items: center; gap: 6px; text-decoration: none; color: var(--ds-primary); font-weight: 500;">
												<span class="ds-badge ds-badge-indigo"><?php echo esc_html( $response_count ); ?></span>
												View
											</a>
										</td>
										<td>
											<div class="ds-shortcode" onclick="copyShortcode(this)" title="Click to copy">
												<code>[formera id="<?php echo esc_attr( $survey['id'] ); ?>"]</code>
												<button type="button" class="ds-copy-btn" aria-label="Copy shortcode">
													<svg class="ds-copy-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" /></svg>
												</button>
											</div>
										</td>
										<td style="text-align:right;">
											<div style="display: flex; gap: 8px; justify-content: flex-end;">
												<a href="<?php echo esc_url( admin_url( 'admin.php?page=formera-builder&id=' . $survey['id'] ) ); ?>" class="ds-btn ds-btn-secondary" style="padding: 8px 16px;">Edit</a>
												<button class="ds-btn ds-btn-outlined ds-delete-trigger" data-href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=formera&action=delete&id=' . $survey['id'] ), 'formera_delete_survey_' . $survey['id'] ) ); ?>" style="padding: 8px 16px; color: var(--ds-error); border-color: var(--ds-error);">Delete</button>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
				<?php endif; ?>
			</div>
			<!-- Delete Confirmation -->
			<div id="ds-delete-modal" class="ds-modal">
				<div class="ds-modal-content ds-animate">
					<div class="ds-modal-icon">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 24px; height: 24px;">
							<path d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
						</svg>
					</div>
					<h3>Delete this form?</h3>
					<p style="color: var(--ds-text-light); font-size: 14px; margin: 8px 0 24px;">This will permanently remove the form and all its responses. This action cannot be undone.</p>
					<div style="display: flex; gap: 12px; justify-content: flex-end;">
						<button id="ds-cancel-delete-btn" class="ds-btn ds-btn-secondary">Cancel</button>
						<button id="ds-confirm-delete-btn" class="ds-btn ds-btn-danger-solid">Delete</button>
					</div>
				</div>
			</div>
		</div>
		<?php
		// Add inline script to formera-admin-js handle
		wp_add_inline_script( 'formera-admin-js', "
			jQuery(document).ready(function($) {
				$(document).on('click', '.ds-delete-trigger', function() {
					var href = $(this).data('href');
					$('#ds-confirm-delete-btn').data('href', href);
					$('#ds-delete-modal').css('display', 'flex');
				});
				$('#ds-cancel-delete-btn').on('click', function() { $('#ds-delete-modal').hide(); });
				$('#ds-confirm-delete-btn').on('click', function() { window.location.href = $(this).data('href'); });
			});
		" );
	}

	public function render_contacts() {
		$all_responses = $this->db->get_all_responses();
		$contacts = [];
		foreach ($all_responses as $resp) {
			$data = json_decode($resp['response_data'], true);
			if (!empty($data['formera_responder_email'])) {
				$email = strtolower( trim( (string) $data['formera_responder_email'] ) );
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
					<h1 class="ds-title">Contacts</h1>
					<span class="ds-badge ds-badge-indigo" style="padding: 8px 16px; font-size: 13px;"><?php echo esc_html( count($contacts) ); ?> contacts</span>
				</div>
				<?php if (empty($contacts)) : ?>
					<div class="ds-card">
						<div class="ds-empty-state">
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
								<path d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
							</svg>
							<h3>No contacts yet</h3>
							<p>Contacts will appear here when users submit forms with their email.</p>
						</div>
					</div>
				<?php else : ?>
				<div class="ds-card" style="padding:0;">
					<div class="ds-table-container">
						<table class="ds-table">
							<thead>
								<tr>
									<th>Email Address</th>
									<th>Forms Taken</th>
									<th>Last Activity</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($contacts as $contact) : ?>
									<tr>
										<td>
											<div style="display: flex; align-items: center; gap: 12px;">
												<div style="width: 36px; height: 36px; border-radius: 50%; background: var(--ds-primary-soft); color: var(--ds-primary); display: flex; align-items: center; justify-content: center; font-weight: 500; font-size: 14px;">
													<?php echo esc_html( strtoupper( substr( (string) ( $contact['email'] ?? '' ), 0, 1 ) ) ); ?>
												</div>
												<span style="font-weight: 500;"><?php echo esc_html($contact['email']); ?></span>
											</div>
										</td>
										<td>
											<div style="display: flex; flex-wrap: wrap; gap: 6px;">
											<?php foreach($contact['surveys'] as $id => $title) : ?>
												<span class="ds-badge ds-badge-indigo"><?php echo esc_html($title); ?></span>
											<?php endforeach; ?>
											</div>
										</td>
										<td style="color: var(--ds-text-light);"><?php echo esc_html( date_i18n('M j, Y', strtotime($contact['last_activity'])) ); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	public function render_builder() {
		require_once dirname( __FILE__ ) . '/survey-builder.php';
		$builder = new Formera_Builder( $this->db );
		$builder->render();
	}

	public function render_survey_results() {
		require_once dirname( __FILE__ ) . '/results.php';
		$results = new Formera_Results( $this->db );
		$results->render_results();
	}
}
