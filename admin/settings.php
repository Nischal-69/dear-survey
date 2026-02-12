<?php
/**
 * Settings View
 * Markup Overhaul for Premium UI/UX Refactor
 */

class Formera_Settings {

	public function render_settings() {
		require_once dirname( __FILE__ ) . '/menu.php';
		global $formera_db;
		$menu = new Formera_Menu( $formera_db );
		?>
		<div class="ds-app-container ds-animate">
			<?php $menu->get_sidebar('settings'); ?>

			<!-- Main Content -->
			<div class="ds-main-content">
				<div class="ds-top-bar">
					<div>
						<div style="font-size: 12px; font-weight: 600; color: var(--ds-primary); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.05em;">Preferences</div>
						<h1 class="ds-title">Settings</h1>
					</div>
				</div>

				<form method="post" action="options.php">
					<?php settings_fields( 'formera_settings_group' ); ?>
					
					<div class="ds-card">
						<div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
							<div style="width: 40px; height: 40px; background: var(--ds-primary-soft); color: var(--ds-primary); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px; height:20px;"><path d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.744c0 1.948.465 3.788 1.293 5.41a11.959 11.959 0 0 1-1.043 3.296 3.745 3.745 0 0 1 3.296 1.043A11.959 11.959 0 0 1 12 21c1.268 0 2.39-.63 3.068-1.593a3.746 3.746 0 0 1 3.296-1.043 3.745 3.745 0 0 1 1.043-3.296A11.959 11.959 0 0 0 21 9.744c0-1.29-.203-2.527-.582-3.69a11.959 11.959 0 0 1-7.125-3.09 11.959 11.959 0 0 1-1.293-.908Z" /></svg>
							</div>
							<div>
								<h3 style="margin: 0; font-size: 16px; font-weight: 700; color: var(--ds-secondary);">Security & Privacy</h3>
								<p style="margin: 4px 0 0; font-size: 12px; color: var(--ds-text-light);">Manage form safety and data compliance.</p>
							</div>
						</div>

						<div style="display: flex; align-items: center; justify-content: space-between; padding: 20px; background: var(--ds-bg); border-radius: var(--ds-radius-md);">
							<div>
								<label style="font-weight: 700; color: var(--ds-secondary); display: block; margin-bottom: 4px;">GDPR Compliance Mode</label>
								<p style="margin: 0; font-size: 13px; color: var(--ds-text-light);">Anonymize IP addresses and add consent checkboxes.</p>
							</div>
							<label class="ds-toggle">
								<input type="checkbox" name="formera_gdpr_mode" value="1" <?php checked( get_option('formera_gdpr_mode'), 1 ); ?>>
								<span class="ds-toggle-slider"></span>
							</label>
						</div>

						<div style="margin-top: 32px; padding-top: 32px; border-top: 1px solid var(--ds-divider);">
							<h4 style="margin: 0 0 16px; font-size: 14px; font-weight: 700; color: var(--ds-secondary);">Global Form Styling</h4>
							<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
								<div>
									<label class="ds-label">Primary Brand Color</label>
									<div style="display: flex; gap: 12px;">
										<input type="text" name="formera_brand_color" class="ds-input-field" value="<?php echo esc_attr( get_option('formera_brand_color', '#1F7A5A') ); ?>" placeholder="#1F7A5A">
										<div style="width: 44px; height: 44px; border-radius: 8px; border: 1px solid var(--ds-border); background: <?php echo esc_attr( get_option('formera_brand_color', '#1F7A5A') ); ?>;"></div>
									</div>
								</div>
								<div>
									<label class="ds-label">Container Max-Width (px)</label>
									<input type="number" name="formera_container_width" class="ds-input-field" value="<?php echo esc_attr( get_option('formera_container_width', '800') ); ?>" placeholder="800">
								</div>
							</div>
						</div>
					</div>

					<div style="margin-top: 32px;">
						<?php submit_button( 'Update Global Preferences', 'primary button-hero', 'submit', true, [ 'style' => 'background: var(--ds-primary); border: none; padding: 12px 32px; height: auto; font-size: 15px; font-weight: 700; border-radius: 10px; box-shadow: var(--ds-shadow);' ] ); ?>
					</div>
				</form>
			</div>
		</div>
		<?php
	}
}
