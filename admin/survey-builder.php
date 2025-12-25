<?php
/**
 * Survey Builder View
 * Markup Overhaul for Premium UI/UX Refactor
 */

class Dear_Survey_Builder {

	private $db;

	public function __construct( $db ) { $this->db = $db; }

	public function render() {
		if ( isset( $_GET['view'] ) && $_GET['view'] === 'results' && isset( $_GET['id'] ) ) {
			require_once dirname( __FILE__ ) . '/results.php';
			$results = new Dear_Survey_Results( $this->db );
			$results->render( intval( $_GET['id'] ) );
			return;
		}

		$survey_id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;
		$survey = $survey_id ? $this->db->get_survey( $survey_id ) : null;
		$title = $survey ? $survey['title'] : '';
		$questions = $survey ? json_decode( $survey['questions'], true ) : [];
		$settings = $survey ? json_decode( $survey['settings'], true ) : [];
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
					<div style="font-size: 11px; font-weight: 700; color: var(--ds-text-light); text-transform: uppercase; letter-spacing: 0.1em; padding: 0 16px; margin-bottom: 12px;">Main Navigation</div>
					<a href="<?php echo admin_url('admin.php?page=dear-survey'); ?>" class="ds-nav-item">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25a2.25 2.25 0 0 1-2.25-2.25v-2.25Z"/></svg>
						Analytics Overview
					</a>
					<a href="<?php echo admin_url('admin.php?page=dear-survey-builder'); ?>" class="ds-nav-item active">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
						Draft Interaction
					</a>
					
					<div style="font-size: 11px; font-weight: 700; color: var(--ds-text-light); text-transform: uppercase; letter-spacing: 0.1em; padding: 0 16px; margin: 32px 0 12px;">Growth & Outreach</div>
					<a href="<?php echo admin_url('admin.php?page=dear-survey-email'); ?>" class="ds-nav-item">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 20 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
						Email Campaigns
					</a>

					<div style="font-size: 11px; font-weight: 700; color: var(--ds-text-light); text-transform: uppercase; letter-spacing: 0.1em; padding: 0 16px; margin: 32px 0 12px;">System Settings</div>
					<a href="<?php echo admin_url('admin.php?page=dear-survey-settings'); ?>" class="ds-nav-item">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M4.5 12a7.5 7.5 0 1 1 15 0 7.5 7.5 0 0 1-15 0Z"/><path d="M12 9v6m-3-3h6"/></svg>
						Configuration
					</a>
				</nav>
			</div>

			<!-- Main Content -->
			<div class="ds-main-content">
				<div class="ds-top-bar">
					<div>
						<div style="font-size: 12px; font-weight: 600; color: var(--ds-primary); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.05em;">Editor</div>
						<h1 class="ds-title"><?php echo $survey_id ? 'Edit Survey' : 'Create Survey'; ?></h1>
					</div>
					<button type="submit" form="ds-survey-form" class="ds-btn ds-btn-primary">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:18px; height:18px;"><path d="M4.5 12.75l6 6 9-13.5"/></svg>
						Save Survey
					</button>
				</div>

				<form id="ds-survey-form">
					<input type="hidden" id="survey_id" value="<?php echo $survey_id; ?>">
					
					<div class="ds-card" style="padding: 32px; margin-bottom: 24px;">
						<label class="ds-label" style="color: var(--ds-text-light);">Survey Name</label>
						<input type="text" id="survey_title" class="ds-input-field" style="font-size: 24px; font-weight: 700; border: none; padding: 8px 0; background: transparent; border-bottom: 2px solid var(--ds-border); border-radius: 0; color: var(--ds-secondary); font-family: 'Outfit', sans-serif;" value="<?php echo esc_attr( $title ); ?>" placeholder="Enter survey name..." required>
					</div>

					<div id="questions-container">
						<!-- JavaScript dynamic render -->
					</div>
					
					<div style="text-align:center; padding: 48px; border: 2px dashed var(--ds-border); border-radius: var(--ds-radius-md); margin-bottom: 48px; background: rgba(234, 236, 240, 0.2); transition: all 0.2s;" onmouseover="this.style.borderColor='var(--ds-primary)'; this.style.background='var(--ds-primary-soft)';" onmouseout="this.style.borderColor='var(--ds-border)'; this.style.background='rgba(234, 236, 240, 0.2)';">
						<button type="button" class="ds-btn ds-btn-secondary" id="add-question" style="background: var(--ds-white);">
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:18px; height:18px;"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
							Add Question
						</button>
					</div>

					<!-- Settings Card -->
					<div class="ds-card" style="background: var(--ds-white); border-style: solid;">
						<div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
							<div style="width: 32px; height: 32px; background: var(--ds-primary-soft); color: var(--ds-primary); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px; height:20px;"><path d="M10.34 15.84c-.68 0-1.25-.57-1.25-1.25V6.75c0-.68.57-1.25 1.25-1.25h3.32c.68 0 1.25.57 1.25 1.25v7.84c0 .68-.57 1.25-1.25 1.25h-3.32Z"/><path d="M3 6.75A1.75 1.75 0 0 1 4.75 5h14.5A1.75 1.75 0 0 1 21 6.75v10.5A1.75 1.75 0 0 1 19.25 19H4.75A1.75 1.75 0 0 1 3 17.25V6.75Z"/></svg>
							</div>
							<h3 style="margin: 0; font-size: 16px; font-weight: 700; color: var(--ds-secondary);">Survey Settings</h3>
						</div>
						<div style="display:grid; grid-template-columns: 1fr 1fr; gap: 32px;">
							<div>
								<label class="ds-label">Thank You Message</label>
								<textarea id="ds-setting-thank-you" class="ds-input-field" rows="4" style="font-size: 14px; line-height: 1.6;" placeholder="Message to show after submission..."><?php echo esc_textarea($settings['thank_you_body'] ?? ''); ?></textarea>
							</div>
							<div>
								<label class="ds-label">Admin Notification Email</label>
								<input type="email" id="ds-setting-admin-email" class="ds-input-field" value="<?php echo esc_attr($settings['admin_email'] ?? ''); ?>" placeholder="admin@example.com">
								<p style="font-size: 12px; color: var(--ds-text-light); margin-top: 8px;">We will send entry notifications to this email address.</p>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>

		<script>
		jQuery(document).ready(function($) {
			let questions = <?php echo json_encode( $questions ); ?>;

			window.dsToggleQuestion = function(index) {
				const item = $(`#q-item-${index}`);
				const body = $(`#q-body-${index}`);
				const icon = $(`#toggle-icon-${index}`);
				
				$('.ds-q-item-body').not(body).slideUp(250);
				$('.ds-q-item').not(item).removeClass('active');
				$('.ds-q-item-header .dashicons').not(icon).css('transform', 'rotate(0deg)');

				item.toggleClass('active');
				body.slideToggle(250);
				icon.css('transform', item.hasClass('active') ? 'rotate(180deg)' : 'rotate(0deg)');
			}

			function renderQuestions() {
				$('#questions-container').empty();
				questions.forEach((q, index) => {
					let html = `
						<div class="ds-q-item ds-animate" id="q-item-${index}" style="animation-delay: ${index * 0.05}s">
							<div class="ds-q-item-header" onclick="window.dsToggleQuestion(${index})">
								<div style="display:flex; align-items:center; gap:16px;">
									<div style="width: 32px; height: 32px; background: var(--ds-bg); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--ds-primary); font-size: 14px;">
										${index + 1}
									</div>
									<div>
										<div style="font-weight:700; color:var(--ds-secondary); font-size:15px; margin-bottom: 2px;">
											<span class="q-header-title-text">${q.title || 'New Question'}</span>
										</div>
										<div style="font-size: 11px; font-weight: 600; color: var(--ds-text-light); text-transform: uppercase; letter-spacing: 0.05em;">
											${q.type ? q.type.toUpperCase() : 'TEXT'} FIELD
										</div>
									</div>
								</div>
								<div style="display: flex; align-items: center; gap: 12px;">
									<button type="button" class="ds-btn ds-btn-secondary" style="padding: 8px; border:none; color: var(--ds-error);" onclick="event.stopPropagation(); deleteQuestion(${index});">
										<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px; height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
									</button>
									<span class="dashicons dashicons-arrow-down-alt2" id="toggle-icon-${index}" style="font-size:16px; width:16px; height:16px; color:var(--ds-text-light); transition:0.3s cubic-bezier(0.16, 1, 0.3, 1);"></span>
								</div>
							</div>
							
							<div class="ds-q-item-body" id="q-body-${index}" style="display: none;">
								<div style="margin-bottom: 24px;">
									<label class="ds-label">Question Label</label>
									<input type="text" class="ds-input-field q-title" data-index="${index}" value="${q.title || ''}" placeholder="Enter question here...">
								</div>
								
								<div style="margin-bottom: 24px;">
									<label class="ds-label">Question Type</label>
									<select class="ds-input-field q-type" data-index="${index}">
										<option value="text" ${q.type === 'text' ? 'selected' : ''}>Short Text</option>
										<option value="textarea" ${q.type === 'textarea' ? 'selected' : ''}>Long Text</option>
										<option value="radio" ${q.type === 'radio' ? 'selected' : ''}>Multiple Choice (Single)</option>
										<option value="checkbox" ${q.type === 'checkbox' ? 'selected' : ''}>Checkboxes (Multiple)</option>
									</select>
								</div>

								<div class="options-container" style="${(q.type === 'radio' || q.type === 'checkbox') ? 'display:block' : 'display:none'}">
									<label class="ds-label">Options</label>
									<div class="dynamic-options-list" data-index="${index}" style="margin-bottom: 16px;"></div>
									<button type="button" class="ds-btn ds-btn-secondary" style="font-size:12px; height: 36px; border-style: dashed;" onclick="addOption(${index})">
										<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:14px; height:14px;"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
										Add Option
									</button>
								</div>
							</div>
						</div>
					`;
					$('#questions-container').append(html);
					if (q.type === 'radio' || q.type === 'checkbox') { renderOptions(index); }
				});
			}

			function renderOptions(qIndex) {
				const q = questions[qIndex];
				const list = $(`.dynamic-options-list[data-index="${qIndex}"]`);
				list.empty();
				const opts = q.options ? q.options.split(',') : [];

				opts.forEach((opt, oIndex) => {
					list.append(`
						<div style="display:flex; gap:12px; margin-bottom:12px; align-items:center;">
							<div style="cursor: grab; color: var(--ds-border);">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px; height:16px;"><path d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" /></svg>
							</div>
							<input type="text" class="ds-input-field option-input" style="padding:10px 14px; font-size:14px;" data-qindex="${qIndex}" data-oindex="${oIndex}" value="${opt.trim()}" placeholder="Option text">
							<button type="button" style="border:none; background:var(--ds-bg); color: var(--ds-text-light); width: 32px; height: 32px; border-radius: 8px; cursor:pointer; display: flex; align-items: center; justify-content: center;" onclick="removeOption(${qIndex}, ${oIndex})">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px; height:14px;"><path d="M6 18L18 6M6 6l12 12" /></svg>
							</button>
						</div>
					`);
				});
			}

			window.deleteQuestion = function(index) {
				if(confirm('Are you sure you want to delete this question?')) { 
					questions.splice(index, 1); 
					renderQuestions(); 
				}
			};

			window.addOption = function(qIndex) {
				const q = questions[qIndex];
				let currentOpts = q.options ? q.options.split(',') : [];
				currentOpts.push('New Option');
				questions[qIndex].options = currentOpts.join(',');
				renderOptions(qIndex);
			};

			window.removeOption = function(qIndex, oIndex) {
				let currentOpts = questions[qIndex].options.split(',');
				currentOpts.splice(oIndex, 1);
				questions[qIndex].options = currentOpts.join(',');
				renderOptions(qIndex);
			};

			$('#add-question').on('click', function() {
				questions.push({ title: '', type: 'text', options: '' });
				renderQuestions();
				window.dsToggleQuestion(questions.length - 1);
			});

			$(document).on('input', '.q-title', function() {
				const index = $(this).data('index');
				questions[index].title = $(this).val();
				$(`#q-item-${index} .q-header-title-text`).text($(this).val() || 'New Question');
			});

			$(document).on('change', '.q-type', function() {
				const index = $(this).data('index');
				questions[index].type = $(this).val();
				renderQuestions();
				window.dsToggleQuestion(index);
			});

			$(document).on('input', '.option-input', function() {
				const qIndex = $(this).data('qindex');
				const oIndex = $(this).data('oindex');
				let currentOpts = questions[qIndex].options.split(',');
				currentOpts[oIndex] = $(this).val();
				questions[qIndex].options = currentOpts.join(',');
			});

			$('#ds-survey-form').on('submit', function(e) {
				e.preventDefault();
				const saveBtn = $('button[form="ds-survey-form"]');
				const originalHtml = saveBtn.html();
				saveBtn.prop('disabled', true).text('Saving...');
				
				let data = {
					action: 'ds_save_survey',
					id: $('#survey_id').val(),
					title: $('#survey_title').val(),
					questions: JSON.stringify(questions),
					settings: JSON.stringify({
						thank_you_body: $('#ds-setting-thank-you').val(),
						admin_email: $('#ds-setting-admin-email').val()
					}),
					security: '<?php echo wp_create_nonce("ds_save_survey"); ?>'
				};

				$.post(ajaxurl, data, function(response) {
					saveBtn.prop('disabled', false).html(originalHtml);
					if (response.success) {
						if (!data.id || data.id == 0) { 
							window.location.href = 'admin.php?page=dear-survey-builder&id=' + response.data.id; 
						} else {
							saveBtn.css('background', 'var(--ds-success)').text('Saved');
							setTimeout(() => { saveBtn.css('background', '').html(originalHtml); }, 2000);
						}
					} else { alert('Save failed. Check permissions.'); }
				});
			});

			renderQuestions();
		});
		</script>
		<?php
	}

	public function ajax_save_survey() {
		check_ajax_referer( 'ds_save_survey', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) wp_send_json_error( 'Forbidden' );
		$id = intval( $_POST['id'] );
		$title = sanitize_text_field( $_POST['title'] );
		$questions = wp_unslash( $_POST['questions'] );
		json_decode( $questions );
		if ( json_last_error() !== JSON_ERROR_NONE ) wp_send_json_error( 'Invalid JSON' );
		$data = array( 'id' => $id, 'title' => $title, 'questions' => $questions );
		$new_id = $this->db->save_survey( $data );
		if ( $new_id ) wp_send_json_success( array( 'id' => $id ? $id : $new_id ) );
		else wp_send_json_error( 'DB Error' );
	}
}
