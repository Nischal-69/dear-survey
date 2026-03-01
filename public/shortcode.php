<?php
/**
 * Shortcode Renderer
 * Modern & Attractive UI/UX Design
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Formera_Shortcode {

	private $db;

	public function __construct( $db ) { $this->db = $db; }

	public function render( $atts ) {
		$atts = shortcode_atts( array( 'id' => 0 ), $atts, 'formera' );
		$survey_id = intval( $atts['id'] );

		if ( ! $survey_id ) return '<p class="ds-error-msg">Form ID is missing.</p>';

		$survey = $this->db->get_survey( $survey_id );
		if ( ! $survey ) return '<p class="ds-error-msg">Form not found.</p>';

		$questions = json_decode( $survey['questions'], true );
		if ( ! is_array( $questions ) || empty( $questions ) ) return '<p class="ds-error-msg">This form has no questions.</p>';

		$settings = json_decode( $survey['settings'], true );
		$total_questions = count( $questions );

		// Build appearance CSS variable overrides
		$appearance_style = '';
		if ( ! empty( $settings['appearance'] ) ) {
			$app = $settings['appearance'];
			if ( ! empty( $app['color'] ) ) {
				$color = $app['color'];
				$appearance_style .= sprintf(
					'--ds-primary:%1$s;--ds-primary-hover:%2$s;--ds-primary-dark:%3$s;--ds-primary-light:%4$s;--ds-primary-rgb:%5$s;--ds-gradient:linear-gradient(135deg,%1$s 0%%,%6$s 100%%);--ds-shadow-glow:0 0 30px rgba(%5$s,0.2);',
					$color,
					self::darken_color( $color, 10 ),
					self::darken_color( $color, 30 ),
					self::lighten_color( $color, 85 ),
					self::hex_to_rgb( $color ),
					self::lighten_color( $color, 15 )
				);
				if ( isset( $app['bg'] ) ) {
					if ( 'white' === $app['bg'] ) {
						$appearance_style .= '--ds-gradient-bg:#ffffff;';
					} elseif ( 'light' === $app['bg'] ) {
						$appearance_style .= '--ds-gradient-bg:#f5f5f5;';
					} else {
						$appearance_style .= sprintf(
							'--ds-gradient-bg:linear-gradient(135deg,%s 0%%,%s 50%%,#f0f9ff 100%%);',
							self::lighten_color( $color, 95 ),
							self::lighten_color( $color, 92 )
						);
					}
				}
			}
			if ( ! empty( $app['font'] ) ) {
				$font = $app['font'];
				if ( 'System Default' === $font ) {
					$appearance_style .= "--ds-font:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;";
				} else {
					$appearance_style .= "--ds-font:'" . esc_attr( $font ) . "',-apple-system,BlinkMacSystemFont,sans-serif;";
					// Enqueue the selected Google Font
					$font_slug = str_replace( ' ', '+', $font );
					wp_enqueue_style( 'formera-gfont-' . sanitize_title( $font ), 'https://fonts.googleapis.com/css2?family=' . $font_slug . ':wght@400;500;600;700&display=swap', array(), null );
				}
			}
			if ( isset( $app['radius'] ) ) {
				$r = intval( $app['radius'] );
				$appearance_style .= sprintf( '--ds-radius:%dpx;--ds-radius-md:%dpx;--ds-radius-sm:%dpx;', $r, round( $r * 0.7 ), round( $r * 0.5 ) );
			}
		}

		ob_start();
		?>
		<div class="gf-survey-wrap" id="ds-survey-<?php echo esc_attr( $survey_id ); ?>"<?php echo $appearance_style ? ' style="' . esc_attr( $appearance_style ) . '"' : ''; ?>>
			<!-- Header Card with Title -->
			<div class="gf-header-card">
				<div class="gf-header-accent"></div>
				<div class="gf-header-content">
					<h1 class="gf-survey-title"><?php echo esc_html( $survey['title'] ?? '' ); ?></h1>
					<?php if ( ! empty( $survey['description'] ) ) : ?>
						<p class="gf-survey-desc"><?php echo esc_html( $survey['description'] ); ?></p>
					<?php endif; ?>
				</div>
				<!-- Progress Indicator -->
				<div class="gf-progress-wrap">
					<div class="gf-progress-bar">
						<div class="gf-progress-fill" id="progress-fill-<?php echo esc_attr( $survey_id ); ?>"></div>
					</div>
					<div class="gf-progress-text">
						<span id="progress-status-<?php echo esc_attr( $survey_id ); ?>">0 of <?php echo esc_html( $total_questions ); ?> answered</span>
						<span id="progress-percent-<?php echo esc_attr( $survey_id ); ?>">0%</span>
					</div>
				</div>
			</div>

			<form class="gf-survey-form" data-id="<?php echo esc_attr( $survey_id ); ?>" data-total="<?php echo esc_attr( $total_questions ); ?>">
				
				<?php if ( ! empty( $settings['collect_email'] ) ) : ?>
				<!-- Email Collection Card -->
				<div class="gf-question-card gf-email-card">
					<div class="gf-q-content">
						<label class="gf-q-label">
							Email address <span class="gf-required">*</span>
						</label>
						<input type="email" name="formera_responder_email" class="gf-text-input" placeholder="your.email@example.com" required autocomplete="email">
						<p class="gf-helper-text">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" /></svg>
							We'll send a confirmation to this email
						</p>
					</div>
				</div>
				<?php endif; ?>

				<?php foreach ( $questions as $index => $q ) : 
					$is_required = ! empty( $q['required'] );
					$question_num = $index + 1;
				?>
				<!-- Question Card -->
				<div class="gf-question-card" data-question="<?php echo esc_attr( $index ); ?>">
					<div class="gf-q-content">
						<div class="gf-q-number"><?php echo esc_html( $question_num ); ?></div>
						<label class="gf-q-label">
							<?php echo esc_html( $q['title'] ); ?>
							<?php if ( $is_required ) : ?><span class="gf-required">*</span><?php endif; ?>
						</label>
						
						<?php if ( $q['type'] === 'text' ) : ?>
							<input type="text" name="q_<?php echo esc_attr( $index ); ?>" class="gf-text-input" placeholder="Type your answer here..." <?php echo $is_required ? 'required' : ''; ?> autocomplete="off">
						
						<?php elseif ( $q['type'] === 'textarea' ) : ?>
							<textarea name="q_<?php echo esc_attr( $index ); ?>" class="gf-textarea-input" rows="4" placeholder="Share your thoughts..." <?php echo $is_required ? 'required' : ''; ?>></textarea>
						
						<?php elseif ( $q['type'] === 'radio' ) : ?>
							<div class="gf-options-list" role="radiogroup" aria-label="<?php echo esc_attr( $q['title'] ); ?>">
								<?php $options = explode( ',', $q['options'] ?? '' ); ?>
								<?php foreach ( $options as $optIndex => $opt ) : $opt = trim( (string) $opt ); if(!$opt) continue; ?>
									<label class="gf-option-item gf-radio-option">
										<input type="radio" name="q_<?php echo esc_attr( $index ); ?>" value="<?php echo esc_attr( $opt ); ?>" <?php echo $is_required ? 'required' : ''; ?>>
										<span class="gf-radio-circle">
											<span class="gf-radio-inner"></span>
										</span>
										<span class="gf-option-text"><?php echo esc_html( $opt ); ?></span>
									</label>
								<?php endforeach; ?>
							</div>

						<?php elseif ( $q['type'] === 'checkbox' ) : ?>
							<div class="gf-options-list" role="group" aria-label="<?php echo esc_attr( $q['title'] ); ?>">
								<?php $options = explode( ',', $q['options'] ?? '' ); ?>
								<?php foreach ( $options as $optIndex => $opt ) : $opt = trim( (string) $opt ); if(!$opt) continue; ?>
									<label class="gf-option-item gf-checkbox-option">
										<input type="checkbox" name="q_<?php echo esc_attr( $index ); ?>[]" value="<?php echo esc_attr( $opt ); ?>">
										<span class="gf-checkbox-box">
											<svg class="gf-check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
												<polyline points="20 6 9 17 4 12"></polyline>
											</svg>
										</span>
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
						<span class="gf-btn-text">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:18px;height:18px;margin-right:6px;">
								<path d="M3.105 2.289a.75.75 0 00-.826.95l1.414 4.925A1.5 1.5 0 005.135 9.25h6.115a.75.75 0 010 1.5H5.135a1.5 1.5 0 00-1.442 1.086l-1.414 4.926a.75.75 0 00.826.95 28.896 28.896 0 0015.293-7.154.75.75 0 000-1.115A28.897 28.897 0 003.105 2.289z" />
							</svg>
							Submit
						</span>
						<span class="gf-btn-loading">
							<svg class="gf-spinner" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="60" stroke-linecap="round" opacity="0.25"/><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="60" stroke-dashoffset="45" stroke-linecap="round"/></svg>
						</span>
					</button>
					<button type="button" class="gf-clear-btn">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px;margin-right:4px;opacity:0.7;">
							<path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.519.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
						</svg>
						Clear form
					</button>
				</div>
				
				<div class="gf-message-wrap">
					<div class="gf-message"></div>
				</div>
			</form>

			<!-- Success Screen (Hidden by default) -->
			<div class="gf-success-screen" style="display: none;">
				<div class="gf-success-card">
					<div class="gf-success-icon">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
							<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
							<polyline points="22 4 12 14.01 9 11.01"/>
						</svg>
					</div>
					<h2 class="gf-success-title">Thank you!</h2>
					<p class="gf-success-text"><?php echo esc_html( isset( $settings['thank_you_body'] ) && $settings['thank_you_body'] !== '' ? $settings['thank_you_body'] : 'Your response has been recorded successfully.' ); ?></p>
					<button type="button" class="gf-submit-another">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:16px;height:16px;margin-right:6px;">
							<path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 01-9.201 2.466l-.312-.311h2.433a.75.75 0 000-1.5H3.989a.75.75 0 00-.75.75v4.242a.75.75 0 001.5 0v-2.43l.31.31a7 7 0 0011.712-3.138.75.75 0 00-1.449-.39zm1.23-3.723a.75.75 0 00.219-.53V2.929a.75.75 0 00-1.5 0v2.43l-.31-.31A7 7 0 003.239 8.188a.75.75 0 101.448.389A5.5 5.5 0 0113.89 6.11l.311.31h-2.432a.75.75 0 000 1.5h4.243a.75.75 0 00.53-.219z" clip-rule="evenodd" />
						</svg>
						Submit another response
					</button>
				</div>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Color utility: Convert hex to "R,G,B" string.
	 */
	private static function hex_to_rgb( $hex ) {
		$hex = ltrim( $hex, '#' );
		return hexdec( substr( $hex, 0, 2 ) ) . ',' . hexdec( substr( $hex, 2, 2 ) ) . ',' . hexdec( substr( $hex, 4, 2 ) );
	}

	/**
	 * Color utility: Lighten a hex color by mixing with white.
	 */
	private static function lighten_color( $hex, $percent ) {
		$hex = ltrim( $hex, '#' );
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );
		$r = round( $r + ( 255 - $r ) * ( $percent / 100 ) );
		$g = round( $g + ( 255 - $g ) * ( $percent / 100 ) );
		$b = round( $b + ( 255 - $b ) * ( $percent / 100 ) );
		return sprintf( '#%02x%02x%02x', $r, $g, $b );
	}

	/**
	 * Color utility: Darken a hex color by mixing with black.
	 */
	private static function darken_color( $hex, $percent ) {
		$hex = ltrim( $hex, '#' );
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );
		$r = round( $r * ( 1 - $percent / 100 ) );
		$g = round( $g * ( 1 - $percent / 100 ) );
		$b = round( $b * ( 1 - $percent / 100 ) );
		return sprintf( '#%02x%02x%02x', $r, $g, $b );
	}
}
