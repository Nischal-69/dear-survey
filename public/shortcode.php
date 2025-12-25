<?php
/**
 * Shortcode Renderer
 * UI/UX Refactor: SaaS-style Premium Form Markup
 */

class Dear_Survey_Shortcode {

	private $db;

	public function __construct( $db ) { $this->db = $db; }

	public function render( $atts ) {
		$atts = shortcode_atts( array( 'id' => 0 ), $atts, 'dear_survey' );
		$survey_id = intval( $atts['id'] );

		if ( ! $survey_id ) return '<p style="text-align:center; color:red;">Survey ID is missing.</p>';

		$survey = $this->db->get_survey( $survey_id );
		if ( ! $survey ) return '<p style="text-align:center; color:red;">Survey not found.</p>';

		$questions = json_decode( $survey['questions'], true );
		if ( ! is_array( $questions ) || empty( $questions ) ) return '<p style="text-align:center; color:gray;">This survey has no questions.</p>';

		ob_start();
		?>
		<div class="ds-survey-container ds-animate" id="ds-survey-<?php echo $survey_id; ?>">
			<div class="ds-survey-header">
				<h1 class="ds-survey-title"><?php echo esc_html( $survey['title'] ); ?></h1>
			</div>

			<form class="ds-survey-form" data-id="<?php echo $survey_id; ?>">
				<?php foreach ( $questions as $index => $q ) : ?>
					<div class="ds-question-field">
						<label class="ds-question-label">
							<?php echo esc_html( $q['title'] ); ?>
						</label>
						
						<?php if ( $q['type'] === 'text' ) : ?>
							<input type="text" name="q_<?php echo $index; ?>" class="ds-input-field" placeholder="Enter your response..." required>
						
						<?php elseif ( $q['type'] === 'textarea' ) : ?>
							<textarea name="q_<?php echo $index; ?>" class="ds-input-field" rows="4" placeholder="Enter your response..." required></textarea>
						
						<?php elseif ( $q['type'] === 'radio' ) : ?>
							<div class="ds-radio-group">
								<?php $options = explode( ',', $q['options'] ); ?>
								<?php foreach ( $options as $opt ) : $opt = trim($opt); if(!$opt) continue; ?>
									<label class="ds-inline-option">
										<input type="radio" name="q_<?php echo $index; ?>" value="<?php echo esc_attr( $opt ); ?>" required>
										<span><?php echo esc_html( $opt ); ?></span>
									</label>
								<?php endforeach; ?>
							</div>

						<?php elseif ( $q['type'] === 'checkbox' ) : ?>
							<div class="ds-checkbox-group">
								<?php $options = explode( ',', $q['options'] ); ?>
								<?php foreach ( $options as $opt ) : $opt = trim($opt); if(!$opt) continue; ?>
									<label class="ds-inline-option">
										<input type="checkbox" name="q_<?php echo $index; ?>[]" value="<?php echo esc_attr( $opt ); ?>">
										<span><?php echo esc_html( $opt ); ?></span>
									</label>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
				
				<div class="ds-form-footer">
					<div class="ds-message"></div>
					<button type="submit" class="ds-submit-btn">Finish & Submit</button>
				</div>
			</form>
		</div>
		<?php
		return ob_get_clean();
	}
}
