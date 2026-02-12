<?php
/**
 * Survey Builder View
 * Google Forms-inspired UI/UX (Visual Only)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class Formera_Builder {

	private $db;

	public function __construct( $db ) { $this->db = $db; }

	public function render() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- ID parameter is only used for loading survey data
		$survey_id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;
		$survey = $survey_id ? $this->db->get_survey( $survey_id ) : null;
		$title = $survey ? $survey['title'] : '';
		$questions = $survey ? json_decode( $survey['questions'], true ) : [];
		$settings = $survey ? json_decode( $survey['settings'], true ) : [];

		require_once dirname( __FILE__ ) . '/menu.php';
		$menu = new Formera_Menu( $this->db );

		// Enqueue builder assets
		wp_enqueue_style( 'formera-builder', FORMERA_URL . 'admin/css/formera-builder.css', array(), FORMERA_VERSION );
		wp_enqueue_script( 'formera-builder', FORMERA_URL . 'admin/js/formera-builder.js', array( 'jquery' ), FORMERA_VERSION, true );
		wp_localize_script( 'formera-builder', 'formera_builder', array(
			'questions'      => $questions,
			'outreach_nonce' => wp_create_nonce( 'formera_outreach' ),
			'save_nonce'     => wp_create_nonce( 'formera_save_survey' ),
		) );
		?>

		<div class="gf-builder-wrap">
			<div class="gf-builder-inner">
				<!-- Skip Link for Accessibility -->
				<a href="#questions-container" class="gf-skip-link">Skip to questions</a>
				
				<!-- Top Bar -->
				<div class="gf-top-bar" role="banner">
					<div class="gf-logo">
						<svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
							<rect x="3" y="3" width="18" height="18" rx="2" fill="var(--gf-primary, #673AB7)"/>
							<path d="M7 12l3 3 7-7" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
						<span style="color: var(--gf-primary); font-weight: 600;">Dear Survey</span>
					</div>
					<div style="display:flex; gap:12px; align-items:center;">
						<a href="<?php echo esc_url( admin_url('admin.php?page=formera-list') ); ?>" style="padding:10px 16px; color:#5F6368; text-decoration:none; font-size:14px;">Cancel</a>
						<button type="submit" form="ds-survey-form" class="gf-btn-primary">
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:18px; height:18px;"><path d="M4.5 12.75l6 6 9-13.5"/></svg>
							<?php echo $survey_id ? 'Save' : 'Create'; ?>
						</button>
					</div>
				</div>
				
				<form id="ds-survey-form">
					<input type="hidden" id="survey_id" value="<?php echo esc_attr( $survey_id ); ?>">
					
					<!-- Header Card -->
					<div class="gf-card gf-card-header gf-animate">
						<div class="gf-card-body">
							<label for="survey_title" class="gf-sr-only">Survey Title</label>
							<input type="text" id="survey_title" class="gf-title-input" value="<?php echo esc_attr( $title ); ?>" placeholder="Untitled form" required autocomplete="off" spellcheck="false" aria-label="Survey title">
							<label for="survey_description" class="gf-sr-only">Survey Description</label>
							<textarea id="survey_description" class="gf-desc-input" placeholder="Form description" rows="1" autocomplete="off" aria-label="Survey description"></textarea>
						</div>
					</div>

					<div id="questions-container" role="list" aria-label="Survey questions">
						<!-- JavaScript dynamic render -->
					</div>

					<!-- Settings Card -->
					<div class="gf-card gf-animate" style="margin-top:24px;">
						<div class="gf-settings-toggle" onclick="jQuery(this).next().toggleClass('gf-open'); jQuery(this).find('.gf-chevron').toggleClass('gf-rotated');" role="button" aria-expanded="false" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' '){this.click();event.preventDefault();}">
							<div class="gf-settings-title">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:24px;height:24px;color:var(--gf-primary, #673AB7);" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
								Settings
							</div>
							<svg class="gf-chevron" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px;height:20px;color:#5F6368;transition:transform 0.2s;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
						</div>
						<div class="gf-settings-body">
							<div class="gf-setting-row">
								<div>
									<div style="font-size:14px;color:#202124;">Collect email addresses</div>
									<div style="font-size:12px;color:#5F6368;">Respondents will be required to sign in</div>
								</div>
								<label class="ds-toggle" style="margin:0;">
									<input type="checkbox" id="ds-setting-collect-email" <?php checked($settings['collect_email'] ?? false); ?>>
									<span class="ds-toggle-slider"></span>
								</label>
							</div>
							
							<div style="margin-top:20px;">
								<label class="gf-label">Confirmation message</label>
								<textarea id="ds-setting-thank-you" class="gf-input gf-textarea" placeholder="Your response has been recorded."><?php echo esc_textarea($settings['thank_you_body'] ?? ''); ?></textarea>
							</div>
							
							<div style="margin-top:16px;">
								<label class="gf-label">Send notification to</label>
								<input type="email" id="ds-setting-admin-email" class="gf-input" value="<?php echo esc_attr($settings['admin_email'] ?? ''); ?>" placeholder="admin@example.com">
							</div>

							<div id="autoresponder-section" style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #E0E0E0; <?php echo ($settings['collect_email'] ?? false) ? 'display:block' : 'display:none'; ?>">
								<div style="font-size:14px;font-weight:500;color:#202124;margin-bottom:16px;">Autoresponder Email</div>
								<div style="margin-bottom:16px;">
									<label class="gf-label">Subject</label>
									<input type="text" id="ds-setting-auto-subject" class="gf-input" value="<?php echo esc_attr($settings['auto_subject'] ?? ''); ?>" placeholder="Thank you for your feedback!">
								</div>
								<div>
									<label class="gf-label">Body</label>
									<textarea id="ds-setting-auto-body" class="gf-input gf-textarea" placeholder="Thank you for participating..."><?php echo esc_textarea($settings['auto_body'] ?? ''); ?></textarea>
								</div>
							</div>
						</div>
					</div>

					<?php if ($survey_id) : ?>
					<!-- Send Card -->
					<div class="gf-card gf-animate">
						<div class="gf-settings-toggle" onclick="jQuery(this).next().toggleClass('gf-open'); jQuery(this).find('.gf-chevron').toggleClass('gf-rotated');">
							<div class="gf-settings-title">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:24px;height:24px;color:#673AB7;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
								Send
							</div>
							<svg class="gf-chevron" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px;height:20px;color:#5F6368;transition:transform 0.2s;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
						</div>
						<div class="gf-settings-body">
							<div style="margin-bottom:16px;">
								<label class="gf-label">Email addresses (comma-separated)</label>
								<textarea id="ds-outreach-emails" class="gf-input gf-textarea" style="min-height:60px;" placeholder="user1@example.com, user2@example.com"></textarea>
							</div>
							<div style="margin-bottom:16px;">
								<label class="gf-label">Subject</label>
								<input type="text" id="ds-outreach-subject" class="gf-input" value="Check out our new survey: <?php echo esc_attr($title); ?>">
							</div>
							<div style="margin-bottom:16px;">
								<label class="gf-label">Message</label>
								<textarea id="ds-outreach-message" class="gf-input gf-textarea">Hi, we'd love to hear your thoughts on this survey!</textarea>
							</div>
							<button type="button" id="ds-send-outreach" class="gf-btn-primary" style="width:100%;">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
								Send Invitations
							</button>
						</div>
					</div>
					<?php endif; ?>
				</form>
			</div>
			
			<!-- Floating Toolbar -->
			<div class="gf-floating-toolbar">
				<button type="button" class="gf-toolbar-btn" id="add-question" title="Add question">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="1.5"/><path fill-rule="evenodd" d="M12 7.5a.75.75 0 01.75.75v3h3a.75.75 0 010 1.5h-3v3a.75.75 0 01-1.5 0v-3h-3a.75.75 0 010-1.5h3v-3A.75.75 0 0112 7.5z" clip-rule="evenodd"/></svg>
				</button>
			</div>
		</div>
		<?php
	}

	public function ajax_save_survey() {
		check_ajax_referer( 'formera_save_survey', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Forbidden' );
		}
		
		$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
		$title = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
		$questions = isset( $_POST['questions'] ) ? sanitize_text_field( wp_unslash( $_POST['questions'] ) ) : '[]';
		$settings = isset( $_POST['settings'] ) ? sanitize_text_field( wp_unslash( $_POST['settings'] ) ) : '{}';
		
		// Basic validation
		json_decode( $questions );
		if ( json_last_error() !== JSON_ERROR_NONE ) wp_send_json_error( 'Invalid Questions JSON' );
		json_decode( $settings );
		if ( json_last_error() !== JSON_ERROR_NONE ) wp_send_json_error( 'Invalid Settings JSON' );

		$data = array( 
			'id' => $id, 
			'title' => $title, 
			'questions' => $questions,
			'settings' => $settings,
			'status' => 'active'
		);
		
		$new_id = $this->db->save_survey( $data );
		if ( $new_id ) wp_send_json_success( array( 'id' => $id ? $id : $new_id ) );
		else wp_send_json_error( 'DB Error' );
	}

	public function ajax_send_outreach() {
		check_ajax_referer( 'formera_outreach', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Forbidden' );
		}

		$survey_id = isset( $_POST['survey_id'] ) ? intval( $_POST['survey_id'] ) : 0;
		$recipients_raw = isset( $_POST['recipients'] ) ? sanitize_text_field( wp_unslash( $_POST['recipients'] ) ) : '';
		$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
		$message_body = isset( $_POST['message'] ) ? wp_kses_post( wp_unslash( $_POST['message'] ) ) : '';

		$survey = $this->db->get_survey( $survey_id );
		if ( ! $survey ) wp_send_json_error( 'Survey not found' );

		$emails = array_map( 'trim', explode( ',', $recipients_raw ) );
		$emails = array_filter( $emails, 'is_email' );

		if ( empty( $emails ) ) wp_send_json_error( 'No valid email addresses provided' );

		$survey_link = home_url( '/?formera_survey=' . $survey_id ); // Simple link format
		
		$full_message = $message_body . "\n\nParticipate here: " . $survey_link;
		
		$sent_count = 0;
		foreach ( $emails as $email ) {
			if ( wp_mail( $email, $subject, $full_message ) ) {
				$sent_count++;
			}
		}

		if ( $sent_count > 0 ) {
			wp_send_json_success( $sent_count . ' emails sent' );
		} else {
			wp_send_json_error( 'Failed to send emails' );
		}
	}
}
