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
		$survey = $survey_id > 0 ? $this->db->get_survey( $survey_id ) : null;
		$title = $survey ? $survey['title'] : '';
		$questions = $survey ? json_decode( $survey['questions'], true ) : array();
		if ( ! is_array( $questions ) ) $questions = array();
		?>
		<div class="ds-wrap">
			<div class="ds-header">
				<div>
					<h1 class="ds-title"><?php echo $survey_id ? 'Refine Survey' : 'Design New Survey'; ?></h1>
					<p class="ds-subtitle"><?php echo $survey_id ? 'Editing existing survey configuration.' : 'Start building your interaction experience.'; ?></p>
				</div>
				<a href="<?php echo admin_url( 'admin.php?page=dear-survey' ); ?>" class="ds-btn ds-btn-secondary">← Back to Dashboard</a>
			</div>

			<form id="ds-survey-form">
				<input type="hidden" id="survey_id" value="<?php echo $survey_id; ?>">
				
				<div class="ds-card">
					<h2 class="ds-card-title">Survey Details</h2>
					<div class="ds-mb-4">
						<label class="ds-label">Survey Title <span style="color:#E53E3E;">*</span></label>
						<input type="text" id="survey_title" class="ds-input-field" value="<?php echo esc_attr( $title ); ?>" placeholder="e.g. Customer Satisfaction Survey 2024" required>
						<p style="font-size:12px; color:var(--ds-text-light); margin-top:8px;">Give your survey a recognizable name for internal management.</p>
					</div>
				</div>

				<div class="ds-card">
					<div class="ds-card-title">
						<span>Survey Questions</span>
						<button type="button" class="ds-btn ds-btn-primary" id="add-question" style="padding: 8px 16px; font-size: 13px;">+ Add Question</button>
					</div>
					
					<div id="questions-container">
						<!-- JavaScript dynamic render -->
					</div>
					
					<div id="empty-state" style="text-align:center; padding:60px 20px; color:var(--ds-text-light); <?php echo empty($questions) ? '' : 'display:none;'; ?>">
						<div style="font-size: 40px; margin-bottom: 15px;">🔍</div>
						<h3>No questions added yet.</h3>
						<p>Click the "Add Question" button above to start building your survey structure.</p>
					</div>
				</div>

				<div class="ds-text-right" style="position: sticky; bottom: 20px; background: rgba(255,255,255,0.9); padding: 15px; border-radius: 12px; box-shadow: 0 -4px 10px rgba(0,0,0,0.05); z-index: 10;">
					<span id="ds-autosave-indicator" style="margin-right: 20px; font-size: 13px; color:var(--ds-text-light); opacity: 0;">Changes saved locally...</span>
					<button type="submit" class="ds-btn ds-btn-primary" id="save-survey" style="width: 200px; font-size: 16px;">Publish Survey</button>
				</div>
			</form>
		</div>

		<script>
		jQuery(document).ready(function($) {
			let questions = <?php echo json_encode( $questions ); ?>;

			function renderQuestions() {
				$('#questions-container').empty();
				if (questions.length === 0) { $('#empty-state').show(); } else { $('#empty-state').hide(); }

				questions.forEach((q, index) => {
					let html = `
						<div class="ds-q-item" id="q-item-${index}">
							<div class="ds-q-item-header" onclick="window.dsToggleQuestion(${index})">
								<div style="display:flex; align-items:center; gap:12px;">
									<span class="dashicons dashicons-arrow-down-alt2" id="toggle-icon-${index}" style="color:var(--ds-text-light); transition:0.2s;"></span>
									<span style="font-weight:700; color:var(--ds-text);">Q${index + 1}: <span class="q-header-title-text">${q.title || 'Untitled'}</span></span>
									<span class="ds-badge" style="background:#EDF2F7; font-size:10px;">${q.type.toUpperCase()}</span>
								</div>
								<button type="button" class="ds-btn ds-btn-danger" style="padding:4px;" onclick="event.stopPropagation(); deleteQuestion(${index});">🗑️</button>
							</div>
							
							<div class="ds-q-item-body" id="q-body-${index}">
								<div class="ds-mb-4">
									<label class="ds-label">Question Text</label>
									<input type="text" class="ds-input-field q-title" data-index="${index}" value="${q.title || ''}" placeholder="Type your question here...">
								</div>
								
								<div class="ds-mb-4">
									<label class="ds-label">Answer Type</label>
									<select class="ds-input-field q-type" data-index="${index}">
										<option value="text" ${q.type === 'text' ? 'selected' : ''}>📝 Short Text</option>
										<option value="textarea" ${q.type === 'textarea' ? 'selected' : ''}>📄 Paragraph</option>
										<option value="radio" ${q.type === 'radio' ? 'selected' : ''}>◉ Single Select</option>
										<option value="checkbox" ${q.type === 'checkbox' ? 'selected' : ''}>☑️ Multi Select</option>
									</select>
								</div>

								<div class="options-container" style="${(q.type === 'radio' || q.type === 'checkbox') ? 'display:block' : 'display:none'}">
									<label class="ds-label">Options</label>
									<div class="dynamic-options-list" data-index="${index}" style="margin-bottom:15px;"></div>
									<button type="button" class="ds-btn ds-btn-secondary" style="font-size:12px; padding:8px 15px;" onclick="addOption(${index})">+ Add Choice</button>
								</div>

								<!-- Pro Features Mockup -->
								<div style="margin-top:25px; padding-top:20px; border-top:1px dashed var(--ds-border);">
									<div style="display:flex; align-items:center; justify-content:space-between; opacity:0.6;">
										<div>
											<span style="font-weight:700; font-size:14px;">Logic Jumps <span class="ds-badge ds-badge-pro">PRO</span></span>
											<p style="font-size:12px; margin:2px 0;">Show/Hide questions based on answers.</p>
										</div>
										<div class="ds-toggle"><input type="checkbox" disabled><span class="ds-slider"></span></div>
									</div>
								</div>
							</div>
						</div>
					`;
					$('#questions-container').append(html);
					if (q.type === 'radio' || q.type === 'checkbox') { renderOptions(index); }
				});
			}

			function renderOptions(qIndex) {
				let list = $(`.dynamic-options-list[data-index="${qIndex}"]`);
				list.empty();
				let opts = questions[qIndex].options ? questions[qIndex].options.split(',') : [];
				opts = opts.map(o => o.trim()).filter(o => o !== '');

				opts.forEach((opt, oIndex) => {
					list.append(`
						<div style="display:flex; gap:10px; margin-bottom:10px; align-items:center;">
							<span style="color:var(--ds-border); cursor:grab;">☰</span>
							<input type="text" class="ds-input-field option-input" style="padding:8px 12px; font-size:14px;" data-qindex="${qIndex}" data-oindex="${oIndex}" value="${opt}" placeholder="Option label">
							<button type="button" style="border:none; background:none; cursor:pointer;" onclick="removeOption(${qIndex}, ${oIndex})">❌</button>
						</div>
					`);
				});
			}

			window.deleteQuestion = function(index) {
				if(confirm('Delete this question?')) { questions.splice(index, 1); renderQuestions(); }
			};

			window.addOption = function(qIndex) {
				let currentOpts = questions[qIndex].options ? questions[qIndex].options.split(',') : [];
				currentOpts.push('New Choice');
				questions[qIndex].options = currentOpts.join(',');
				renderOptions(qIndex);
			};

			window.removeOption = function(qIndex, oIndex) {
				let currentOpts = questions[qIndex].options.split(',');
				currentOpts.splice(oIndex, 1);
				questions[qIndex].options = currentOpts.join(',');
				renderOptions(qIndex);
			};

			renderQuestions();

			$('#add-question').on('click', function() {
				questions.push({ title: '', type: 'text', options: '' });
				renderQuestions();
			});

			$(document).on('input', '.q-title', function() {
				let index = $(this).data('index');
				questions[index].title = $(this).val();
				$(`#q-item-${index} .q-header-title-text`).text($(this).val() || 'Untitled');
			});

			$(document).on('change', '.q-type', function() {
				let index = $(this).data('index');
				questions[index].type = $(this).val();
				renderQuestions(); 
			});

			$(document).on('input', '.option-input', function() {
				let qIndex = $(this).data('qindex');
				let oIndex = $(this).data('oindex');
				let currentOpts = questions[qIndex].options.split(',');
				currentOpts[oIndex] = $(this).val(); 
				questions[qIndex].options = currentOpts.join(',');
			});

			$('#ds-survey-form').on('submit', function(e) {
				e.preventDefault();
				$('#save-survey').prop('disabled', true).text('Publishing...');
				
				let data = {
					action: 'ds_save_survey',
					id: $('#survey_id').val(),
					title: $('#survey_title').val(),
					questions: JSON.stringify(questions),
					security: '<?php echo wp_create_nonce( "ds_save_survey" ); ?>'
				};

				$.post(ajaxurl, data, function(response) {
					$('#save-survey').prop('disabled', false).text('Publish Survey');
					if (response.success) {
						if (!data.id || data.id == 0) { window.location.href = 'admin.php?page=dear-survey-builder&id=' + response.data.id; } else { alert('Survey published successfully! 🚀'); }
					} else { alert('Failed to save survey. Please try again.'); }
				});
			});
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
