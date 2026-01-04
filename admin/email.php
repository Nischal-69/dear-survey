<?php
/**
 * Email Management Class for Broadcasts, Templates, and Mailing Lists
 */

class DearSurvey_Email {

	private $db;

	public function __construct( $db ) {
		$this->db = $db;
	}

	public function render_page() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- View parameter is only used for display routing
		$view = isset( $_GET['view'] ) ? sanitize_text_field( wp_unslash( $_GET['view'] ) ) : 'broadcast';
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
					<a href="<?php echo esc_url( admin_url('admin.php?page=dear-survey') ); ?>" class="ds-nav-item">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25a2.25 2.25 0 0 1-2.25-2.25v-2.25Z"/></svg>
						Surveys List
					</a>
					<a href="<?php echo esc_url( admin_url('admin.php?page=dear-survey-builder') ); ?>" class="ds-nav-item">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
						Create New Survey
					</a>
					
					<div style="font-size: 11px; font-weight: 700; color: var(--ds-text-light); text-transform: uppercase; letter-spacing: 0.1em; padding: 0 16px; margin: 32px 0 12px;">Outreach</div>
					<a href="<?php echo esc_url( admin_url('admin.php?page=dear-survey-email') ); ?>" class="ds-nav-item active">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 20 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
						Email Broadcast
					</a>

					<div style="font-size: 11px; font-weight: 700; color: var(--ds-text-light); text-transform: uppercase; letter-spacing: 0.1em; padding: 0 16px; margin: 32px 0 12px;">Preferences</div>
					<a href="<?php echo esc_url( admin_url('admin.php?page=dear-survey-settings') ); ?>" class="ds-nav-item">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M4.5 12a7.5 7.5 0 1 1 15 0 7.5 7.5 0 0 1-15 0Z"/><path d="M12 9v6m-3-3h6"/></svg>
						Global Settings
					</a>
				</nav>
			</div>

			<!-- Main Content -->
			<div class="ds-main-content">
				<div class="ds-top-bar" style="margin-bottom:20px;">
					<div>
						<div style="font-size: 12px; font-weight: 600; color: var(--ds-primary); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.05em;">Outreach</div>
						<h1 class="ds-title">Email Broadcast</h1>
					</div>
					<div style="display:flex; gap:10px;">
						<a href="<?php echo esc_url( admin_url('admin.php?page=dear-survey-email&view=subscribers') ); ?>" class="ds-btn ds-btn-secondary">Contacts</a>
						<a href="<?php echo esc_url( admin_url('admin.php?page=dear-survey-email&view=template') ); ?>" class="ds-btn ds-btn-secondary">Templates</a>
						<a href="<?php echo esc_url( admin_url('admin.php?page=dear-survey-email&view=new_broadcast') ); ?>" class="ds-btn ds-btn-primary">Send Survey</a>
					</div>
				</div>

				<?php if ( $view === 'broadcast' ) : ?>
					<?php $this->render_broadcast_list(); ?>
				<?php elseif ( $view === 'new_broadcast' ) : ?>
					<?php $this->render_new_broadcast_form(); ?>
				<?php elseif ( $view === 'template' ) : ?>
					<?php $this->render_template_list(); ?>
				<?php elseif ( $view === 'new_template' || $view === 'edit_template' ) : ?>
					<?php $this->render_template_form(); ?>
				<?php elseif ( $view === 'subscribers' ) : ?>
					<?php $this->render_subscriber_list(); ?>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	private function render_broadcast_list() {
		$broadcasts = $this->db->get_broadcasts();
		?>
		<div class="ds-card ds-table-card">
			<table class="ds-table">
				<thead>
					<tr>
						<th>Subject</th>
						<th>Recipients</th>
						<th>Date Sent</th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $broadcasts ) ) : ?>
						<tr><td colspan="3" style="text-align:center; padding:100px; color:var(--ds-text-light);">No history found.</td></tr>
					<?php else : ?>
						<?php foreach ( $broadcasts as $b ) : ?>
							<tr>
								<td style="font-weight:700;"><?php echo esc_html($b['subject']); ?></td>
								<td><span class="ds-badge ds-badge-indigo"><?php echo esc_html( count(explode(',', $b['recipients'])) ); ?> People</span></td>
								<td><?php echo esc_html( date_i18n( 'M j, Y H:i', strtotime($b['sent_at']) ) ); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	private function render_new_broadcast_form() {
		$templates = $this->db->get_templates();
		$subscribers = $this->db->get_subscribers();
		$surveys = $this->db->get_surveys();
		?>
		<div class="ds-builder-grid">
			<div class="ds-column">
				<div class="ds-card">
					<form id="ds-broadcast-form">
						<div class="ds-mb-4" style="margin-bottom:25px;">
							<label class="ds-label">1. Select Survey</label>
							<select id="ds-survey-selector" class="ds-input-field">
								<option value="">Choose a survey...</option>
								<?php foreach($surveys as $s): ?>
									<option value="<?php echo esc_attr( $s['id'] ); ?>"><?php echo esc_html($s['title']); ?></option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="ds-mb-4" style="margin-bottom:25px;">
							<label class="ds-label">2. Select Recipients</label>
							<?php
							// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Recipients parameter is only used for pre-filling form
							$url_recipients = isset( $_GET['recipients'] ) ? sanitize_text_field( wp_unslash( $_GET['recipients'] ) ) : '';
							$url_recipients_array = $url_recipients ? explode( ',', $url_recipients ) : array();
							?>
							<div style="max-height:150px; overflow-y:auto; border:1px solid var(--ds-border); border-radius:10px; padding:15px; margin-bottom:10px;">
								<?php if(empty($subscribers)): ?>
									<p style="font-size:13px; color:var(--ds-text-light);">No contacts in your mailing list yet.</p>
								<?php else: ?>
									<?php foreach($subscribers as $sub): ?>
										<label style="display:flex; align-items:center; gap:10px; margin-bottom:10px; cursor:pointer; font-size:14px;">
											<input type="checkbox" class="ds-sub-checkbox" value="<?php echo esc_attr($sub['email']); ?>" <?php if( in_array( $sub['email'], $url_recipients_array, true ) ) echo 'checked'; ?>>
											<?php echo esc_html($sub['name'] ?: $sub['email']); ?>
										</label>
									<?php endforeach; ?>
								<?php endif; ?>
							</div>
							<textarea id="ds-broadcast-recipients" class="ds-input-field" rows="2" placeholder="Or enter emails manually (comma separated)..."><?php echo esc_textarea($url_recipients); ?></textarea>
						</div>

						<div class="ds-mb-4" style="margin-bottom:25px;">
							<label class="ds-label">3. Message</label>
							<select id="ds-template-selector" class="ds-input-field" style="margin-bottom:15px;">
								<option value="">Write your own</option>
							<?php
							// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- temp_id parameter is only used for pre-selecting template
							$pre_select_id = isset( $_GET['temp_id'] ) ? intval( $_GET['temp_id'] ) : 0;
								foreach($templates as $t): ?>
									<option value="<?php echo esc_attr( $t['id'] ); ?>" data-content="<?php echo esc_attr($t['content']); ?>" data-subject="<?php echo esc_attr($t['subject']); ?>" <?php selected($pre_select_id, $t['id']); ?>><?php echo esc_html($t['name']); ?> (Template)</option>
								<?php endforeach; ?>
							</select>
							<input type="text" id="ds-broadcast-subject" class="ds-input-field" placeholder="Email Subject" required style="margin-bottom:15px;">
							<textarea id="ds-broadcast-message" class="ds-input-field" rows="8" placeholder="Type your email message here..." required></textarea>
						</div>

						<button type="submit" class="ds-btn ds-btn-primary" style="width:100%; justify-content:center;">Send Survey Link</button>
					</form>
				</div>
			</div>
			
			<div class="ds-column">
				<div class="ds-card" style="padding:25px;">
					<h3 style="margin-top:0; font-size:16px; font-weight:800; border-bottom:1px solid var(--ds-border); padding-bottom:15px; margin-bottom:20px;">Quick Tips</h3>
					<ul style="padding:0; list-style:none; font-size:13px; color:var(--ds-text-light); line-height:1.6;">
						<li style="margin-bottom:15px;">• Use **Templates** to save time and keep your messages consistent.</li>
						<li style="margin-bottom:15px;">• Include **{survey_link}** in your message to show where people should click.</li>
						<li>• Send a test email to yourself first before sending to everyone.</li>
					</ul>
				</div>
			</div>
		</div>

		<script>
		jQuery(document).ready(function($) {
			$('.ds-sub-checkbox').change(function() {
				let selected = [];
				$('.ds-sub-checkbox:checked').each(function() { selected.push($(this).val()); });
				$('#ds-broadcast-recipients').val(selected.join(', '));
			});

			$('#ds-template-selector').change(function() {
				const opt = $(this).find('option:selected');
				if(opt.val()) {
					$('#ds-broadcast-subject').val(opt.data('subject'));
					$('#ds-broadcast-message').val(opt.data('content'));
				}
			});

			// Auto-load template if pre-selected via URL
			if($('#ds-template-selector').val()) {
				$('#ds-template-selector').trigger('change');
			}

			$('#ds-broadcast-form').submit(function(e) {
				e.preventDefault();
				const btn = $(this).find('button');
				btn.prop('disabled', true).text('Sending...');
				
				let message = $('#ds-broadcast-message').val();
				const surveyId = $('#ds-survey-selector').val();
				
				const data = {
					action: 'ds_send_broadcast',
					recipients: $('#ds-broadcast-recipients').val(),
					subject: $('#ds-broadcast-subject').val(),
					message: message,
					survey_id: surveyId,
					security: '<?php echo esc_js( wp_create_nonce("ds_broadcast") ); ?>'
				};

				$.post(ajaxurl, data, function(res) {
					if(res.success) {
						alert('Broadcast sent successfully!');
						window.location.href = '<?php echo esc_js( admin_url("admin.php?page=dear-survey-email") ); ?>';
					} else {
						alert('Error: ' + res.data);
						btn.prop('disabled', false).text('Send Survey Link');
					}
				});
			});
		});
		</script>
		<?php
	}

	private function render_template_list() {
		$templates = $this->db->get_templates();
		?>
		<div class="ds-card ds-table-card">
			<table class="ds-table">
				<thead>
					<tr>
						<th>Template Name</th>
						<th>Default Subject</th>
						<th style="text-align:right;">Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty($templates) ) : ?>
						<tr><td colspan="3" style="text-align:center; padding:60px; color:var(--ds-text-light);">No templates saved.</td></tr>
					<?php else : ?>
						<?php foreach($templates as $t): ?>
							<tr>
								<td style="font-weight:700;"><?php echo esc_html($t['name']); ?></td>
								<td><?php echo esc_html($t['subject']); ?></td>
								<td style="text-align:right;">
									<div style="display:flex; justify-content:flex-end; gap:8px;">
											<a href="<?php echo esc_url( admin_url('admin.php?page=dear-survey-email&view=new_broadcast&temp_id=' . $t['id']) ); ?>" class="ds-btn ds-btn-primary" style="padding:8px 14px; font-size:12px;">Use This</a>
											<a href="<?php echo esc_url( admin_url('admin.php?page=dear-survey-email&view=edit_template&id=' . $t['id']) ); ?>" class="ds-btn ds-btn-secondary" style="padding:8px 14px; font-size:12px;">Edit</a>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<div style="margin-top:20px; text-align:right;">
			<a href="<?php echo esc_url( admin_url('admin.php?page=dear-survey-email&view=new_template') ); ?>" class="ds-btn ds-btn-secondary">+ Create Template</a>
		</div>
		<?php
	}

	private function render_template_form() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- ID parameter is only used for loading template data
		$id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;
		$template = $id ? $this->db->get_template( $id ) : null;
		?>
		<div class="ds-card">
			<form id="ds-template-form">
				<input type="hidden" id="template_id" value="<?php echo esc_attr( $id ); ?>">
				<div class="ds-mb-4" style="margin-bottom:25px;">
					<label class="ds-label">Template Name</label>
					<input type="text" id="template_name" class="ds-input-field" value="<?php echo $template ? esc_attr($template['name']) : ''; ?>" placeholder="e.g. Feedback Invitation" required>
				</div>
				<div class="ds-mb-4" style="margin-bottom:25px;">
					<label class="ds-label">Default Subject</label>
					<input type="text" id="template_subject" class="ds-input-field" value="<?php echo $template ? esc_attr($template['subject']) : ''; ?>" placeholder="Your feedback is needed" required>
				</div>
				<div class="ds-mb-4" style="margin-bottom:35px;">
					<label class="ds-label">Message Body</label>
					<textarea id="template_content" class="ds-input-field" rows="12" placeholder="Hi, please fill out our survey here: {survey_link}" required><?php echo $template ? esc_textarea($template['content']) : ''; ?></textarea>
				</div>
				<button type="submit" class="ds-btn ds-btn-primary" style="width:100%; justify-content:center;">Save Template</button>
			</form>
		</div>
		<script>
		jQuery(document).ready(function($) {
			$('#ds-template-form').submit(function(e) {
				e.preventDefault();
				const btn = $(this).find('button');
				btn.prop('disabled', true).text('Saving...');
				
				const data = {
					action: 'ds_save_template',
					id: $('#template_id').val(),
					name: $('#template_name').val(),
					subject: $('#template_subject').val(),
					content: $('#template_content').val(),
					security: '<?php echo esc_js( wp_create_nonce("ds_template") ); ?>'
				};

				$.post(ajaxurl, data, function(res) {
					if(res.success) {
						window.location.href = '<?php echo esc_js( admin_url("admin.php?page=dear-survey-email&view=template") ); ?>';
					} else {
						alert('Save failed.');
						btn.prop('disabled', false).text('Save Template');
					}
				});
			});
		});
		</script>
		<?php
	}

	private function render_subscriber_list() {
		$subscribers = $this->db->get_subscribers();
		?>
		<div class="ds-builder-grid">
			<div class="ds-column">
				<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
					<h3 style="margin:0; font-size:18px; font-weight:800;">Contact List</h3>
					<button type="button" id="ds-bulk-send" class="ds-btn ds-btn-primary" style="font-size:13px; padding:10px 20px;">Message Selected</button>
				</div>
				<div class="ds-card ds-table-card">
					<table class="ds-table">
						<thead>
							<tr>
								<th style="width:40px;"><input type="checkbox" id="ds-select-all-subs"></th>
								<th>Name & Email</th>
								<th>Added On</th>
								<th style="text-align:right;">Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php if ( empty($subscribers) ) : ?>
								<tr><td colspan="4" style="text-align:center; padding:60px; color:var(--ds-text-light);">Your contact list is empty.</td></tr>
							<?php else : ?>
								<?php foreach($subscribers as $s): ?>
									<tr id="sub-row-<?php echo esc_attr( $s['id'] ); ?>">
										<td><input type="checkbox" class="ds-sub-item-check" value="<?php echo esc_attr($s['email']); ?>"></td>
										<td>
											<div style="font-weight:700; color:var(--ds-secondary);"><?php echo esc_html($s['name'] ?: 'No Name'); ?></div>
											<div style="font-size:13px; color:var(--ds-text-light);"><?php echo esc_html($s['email']); ?></div>
										</td>
										<td><?php echo esc_html( date_i18n( 'M j, Y', strtotime($s['created_at']) ) ); ?></td>
										<td style="text-align:right;">
											<div style="display:flex; justify-content:flex-end; gap:8px;">
												<a href="<?php echo esc_url( admin_url('admin.php?page=dear-survey-email&view=new_broadcast&recipients=' . urlencode($s['email'])) ); ?>" class="ds-btn ds-btn-secondary" style="padding:8px; border:none;" title="Send Message">
													<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width:18px; height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" /></svg>
												</a>
												<button type="button" class="ds-btn ds-btn-secondary ds-delete-sub" data-id="<?php echo esc_attr( $s['id'] ); ?>" style="padding:8px; color:#EF4444; border:none;">
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
			<div class="ds-column">
				<div class="ds-card">
					<h3 style="margin-top:0; font-size:16px; font-weight:800; border-bottom:1px solid var(--ds-border); padding-bottom:15px; margin-bottom:20px;">Add New Contact</h3>
					<form id="ds-add-sub-form">
						<div class="ds-mb-4" style="margin-bottom:15px;">
							<label class="ds-label">Contact Name</label>
							<input type="text" id="ds-sub-name" class="ds-input-field" placeholder="John Doe">
						</div>
						<div class="ds-mb-4" style="margin-bottom:25px;">
							<label class="ds-label">Email Address</label>
							<input type="email" id="ds-sub-email" class="ds-input-field" placeholder="john@example.com" required>
						</div>
						<button type="submit" class="ds-btn ds-btn-primary" style="width:100%; justify-content:center;">Add Contact</button>
					</form>
				</div>
			</div>
		</div>
		<script>
		jQuery(document).ready(function($) {
			$('#ds-add-sub-form').submit(function(e) {
				e.preventDefault();
				const btn = $(this).find('button');
				btn.prop('disabled', true).text('Saving...');
				$.post(ajaxurl, {
					action: 'ds_add_subscriber',
					name: $('#ds-sub-name').val(),
					email: $('#ds-sub-email').val(),
					security: '<?php echo esc_js( wp_create_nonce("ds_subscriber") ); ?>'
				}, function(res) {
					if(res.success) window.location.reload();
					else { alert(res.data); btn.prop('disabled', false).text('Add Contact'); }
				});
			});

			$('#ds-select-all-subs').change(function() {
				$('.ds-sub-item-check').prop('checked', $(this).prop('checked'));
			});

			$('#ds-bulk-send').click(function() {
				const selected = [];
				$('.ds-sub-item-check:checked').each(function() { selected.push($(this).val()); });
				if(selected.length === 0) { alert('Select contacts first.'); return; }
				window.location.href = '<?php echo esc_js( admin_url("admin.php?page=dear-survey-email&view=new_broadcast&recipients=") ); ?>' + encodeURIComponent(selected.join(','));
			});

			$('.ds-delete-sub').click(function() {
				if(!confirm('Remove this contact?')) return;
				const id = $(this).data('id');
				$.post(ajaxurl, {
					action: 'ds_remove_subscriber',
					id: id,
					security: '<?php echo esc_js( wp_create_nonce("ds_subscriber") ); ?>'
				}, function() { $('#sub-row-'+id).fadeOut(); });
			});
		});
		</script>
		<?php
	}

	public function ajax_add_subscriber() {
		check_ajax_referer( 'ds_subscriber', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Forbidden' );
		}
		$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		if ( ! $email ) {
			wp_send_json_error( 'Email required.' );
		}
		$this->db->add_subscriber( $email, $name );
		wp_send_json_success();
	}

	public function ajax_remove_subscriber() {
		check_ajax_referer( 'ds_subscriber', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Forbidden' );
		}
		$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
		if ( ! $id ) {
			wp_send_json_error( 'Invalid ID' );
		}
		$this->db->delete_subscriber( $id );
		wp_send_json_success();
	}

	public function ajax_send_broadcast() {
		check_ajax_referer( 'ds_broadcast', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Forbidden' );
		}

		$recipients_raw = isset( $_POST['recipients'] ) ? sanitize_text_field( wp_unslash( $_POST['recipients'] ) ) : '';
		$recipients = explode( ',', $recipients_raw );
		$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
		$message = isset( $_POST['message'] ) ? wp_kses_post( wp_unslash( $_POST['message'] ) ) : '';
		$survey_id = isset( $_POST['survey_id'] ) ? intval( $_POST['survey_id'] ) : 0;

		// Auto-append survey link if survey_id is provided
		if ($survey_id) {
			$survey_url = home_url('/?ds_survey=' . $survey_id); // Assuming we have a way to view it or just use shortcode page
			// Better: generate a link based on the page where the shortcode is, but let's just use home_url with param for now
			$link = "View Survey: " . $survey_url;
			if (strpos($message, '{survey_link}') !== false) {
				$message = str_replace('{survey_link}', $survey_url, $message);
			} else {
				$message .= "\n\n" . $link;
			}
		}

		$sent_to = [];
		foreach($recipients as $email) {
			$email = trim($email);
			if(is_email($email)) {
				wp_mail($email, $subject, $message);
				$sent_to[] = $email;
			}
		}

		if(empty($sent_to)) wp_send_json_error('No valid emails.');

		$this->db->save_broadcast([
			'subject' => $subject,
			'message' => $message,
			'recipients' => implode(',', $sent_to),
			'sent_at' => current_time('mysql')
		]);

		wp_send_json_success();
	}

	public function ajax_save_template() {
		check_ajax_referer( 'ds_template', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Forbidden' );
		}

		$this->db->save_template( array(
			'id'      => isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0,
			'name'    => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
			'subject' => isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '',
			'content' => isset( $_POST['content'] ) ? wp_kses_post( wp_unslash( $_POST['content'] ) ) : '',
		) );

		wp_send_json_success();
	}
}
