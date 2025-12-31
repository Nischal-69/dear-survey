<?php
/**
 * Shortcode Renderer
 * Google Forms-Inspired UI/UX Design
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Dear_Survey_Shortcode {

	private $db;

	public function __construct( $db ) { $this->db = $db; }

	public function render( $atts ) {
		$atts = shortcode_atts( array( 'id' => 0 ), $atts, 'dear_survey' );
		$survey_id = intval( $atts['id'] );

		if ( ! $survey_id ) return '<p class="ds-error-msg">Survey ID is missing.</p>';

		$survey = $this->db->get_survey( $survey_id );
		if ( ! $survey ) return '<p class="ds-error-msg">Survey not found.</p>';

		$questions = json_decode( $survey['questions'], true );
		if ( ! is_array( $questions ) || empty( $questions ) ) return '<p class="ds-error-msg">This survey has no questions.</p>';

		$settings = json_decode( $survey['settings'], true );

		ob_start();
		?>
		<div class="gf-survey-wrap" id="ds-survey-<?php echo $survey_id; ?>">
			<!-- Header Card with Title -->
			<div class="gf-header-card">
				<div class="gf-header-accent"></div>
				<div class="gf-header-content">
					<h1 class="gf-survey-title"><?php echo esc_html( $survey['title'] ); ?></h1>
					<?php if ( ! empty( $survey['description'] ) ) : ?>
						<p class="gf-survey-desc"><?php echo esc_html( $survey['description'] ); ?></p>
					<?php endif; ?>
				</div>
			</div>

			<form class="gf-survey-form" data-id="<?php echo $survey_id; ?>">
				
				<?php if ( ! empty( $settings['collect_email'] ) ) : ?>
				<!-- Email Collection Card -->
				<div class="gf-question-card gf-email-card">
					<div class="gf-q-content">
						<label class="gf-q-label">
							Email address <span class="gf-required">*</span>
						</label>
						<input type="email" name="ds_responder_email" class="gf-text-input" placeholder="Your email" required>
						<p class="gf-helper-text">We'll send a copy of your response to this email.</p>
					</div>
				</div>
				<?php endif; ?>

				<?php foreach ( $questions as $index => $q ) : 
					$is_required = ! empty( $q['required'] );
				?>
				<!-- Question Card -->
				<div class="gf-question-card" data-question="<?php echo $index; ?>">
					<div class="gf-q-content">
						<label class="gf-q-label">
							<?php echo esc_html( $q['title'] ); ?>
							<?php if ( $is_required ) : ?><span class="gf-required">*</span><?php endif; ?>
						</label>
						
						<?php if ( $q['type'] === 'text' ) : ?>
							<input type="text" name="q_<?php echo $index; ?>" class="gf-text-input" placeholder="Your answer" <?php echo $is_required ? 'required' : ''; ?>>
						
						<?php elseif ( $q['type'] === 'textarea' ) : ?>
							<textarea name="q_<?php echo $index; ?>" class="gf-textarea-input" rows="3" placeholder="Your answer" <?php echo $is_required ? 'required' : ''; ?>></textarea>
						
						<?php elseif ( $q['type'] === 'radio' ) : ?>
							<div class="gf-options-list" role="radiogroup">
								<?php $options = explode( ',', $q['options'] ); ?>
								<?php foreach ( $options as $optIndex => $opt ) : $opt = trim($opt); if(!$opt) continue; ?>
									<label class="gf-option-item gf-radio-option">
										<span class="gf-radio-circle">
											<span class="gf-radio-inner"></span>
										</span>
										<input type="radio" name="q_<?php echo $index; ?>" value="<?php echo esc_attr( $opt ); ?>" <?php echo $is_required ? 'required' : ''; ?>>
										<span class="gf-option-text"><?php echo esc_html( $opt ); ?></span>
									</label>
								<?php endforeach; ?>
							</div>

						<?php elseif ( $q['type'] === 'checkbox' ) : ?>
							<div class="gf-options-list" role="group">
								<?php $options = explode( ',', $q['options'] ); ?>
								<?php foreach ( $options as $optIndex => $opt ) : $opt = trim($opt); if(!$opt) continue; ?>
									<label class="gf-option-item gf-checkbox-option">
										<span class="gf-checkbox-box">
											<svg class="gf-check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
												<polyline points="20 6 9 17 4 12"></polyline>
											</svg>
										</span>
										<input type="checkbox" name="q_<?php echo $index; ?>[]" value="<?php echo esc_attr( $opt ); ?>">
										<span class="gf-option-text"><?php echo esc_html( $opt ); ?></span>
									</label>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
				<?php endforeach; ?>
				
				<!-- Submit Footer -->
				<div class="gf-form-footer">
					<button type="submit" class="gf-submit-btn">
						<span class="gf-btn-text">Submit</span>
						<span class="gf-btn-loading">
							<svg class="gf-spinner" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="60" stroke-linecap="round"/></svg>
						</span>
					</button>
					<button type="button" class="gf-clear-btn">Clear form</button>
				</div>
				
				<div class="gf-message-wrap">
					<div class="gf-message"></div>
				</div>
			</form>

			<!-- Success Screen (Hidden by default) -->
			<div class="gf-success-screen" style="display: none;">
				<div class="gf-success-card">
					<div class="gf-success-icon">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
							<polyline points="22 4 12 14.01 9 11.01"/>
						</svg>
					</div>
					<h2 class="gf-success-title">Your response has been recorded</h2>
					<p class="gf-success-text">Thank you for completing this survey.</p>
					<button type="button" class="gf-submit-another">Submit another response</button>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
}
