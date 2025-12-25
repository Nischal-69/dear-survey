<?php
/**
 * Settings View
 * Markup Overhaul for Premium UI/UX Refactor
 */

class Dear_Survey_Settings {

	public function render_settings() {
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
					<a href="<?php echo admin_url('admin.php?page=dear-survey-builder'); ?>" class="ds-nav-item">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
						Draft Interaction
					</a>
					
					<div style="font-size: 11px; font-weight: 700; color: var(--ds-text-light); text-transform: uppercase; letter-spacing: 0.1em; padding: 0 16px; margin: 32px 0 12px;">Growth & Outreach</div>
					<a href="<?php echo admin_url('admin.php?page=dear-survey-email'); ?>" class="ds-nav-item">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 20 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
						Email Campaigns
					</a>

					<div style="font-size: 11px; font-weight: 700; color: var(--ds-text-light); text-transform: uppercase; letter-spacing: 0.1em; padding: 0 16px; margin: 32px 0 12px;">System Settings</div>
					<a href="<?php echo admin_url('admin.php?page=dear-survey-settings'); ?>" class="ds-nav-item active">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M4.5 12a7.5 7.5 0 1 1 15 0 7.5 7.5 0 0 1-15 0Z"/><path d="M12 9v6m-3-3h6"/></svg>
						Configuration
					</a>
				</nav>
			</div>

			<!-- Main Content -->
			<div class="ds-main-content">
				<div class="ds-top-bar">
					<div>
						<div style="font-size: 12px; font-weight: 600; color: var(--ds-primary); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.05em;">Preferences / Core</div>
						<h1 class="ds-title">System Configuration</h1>
					</div>
				</div>

				<form method="post" action="options.php">
					<?php settings_fields( 'ds_settings_group' ); ?>
					
					<div class="ds-card">
						<div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
							<div style="width: 40px; height: 40px; background: var(--ds-primary-soft); color: var(--ds-primary); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px; height:20px;"><path d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.744c0 1.948.465 3.788 1.293 5.41a11.959 11.959 0 0 1-1.043 3.296 3.745 3.745 0 0 1 3.296 1.043A11.959 11.959 0 0 1 12 21c1.268 0 2.39-.63 3.068-1.593a3.746 3.746 0 0 1 3.296-1.043 3.745 3.745 0 0 1 1.043-3.296A11.959 11.959 0 0 0 21 9.744c0-1.29-.203-2.527-.582-3.69a11.959 11.959 0 0 1-7.125-3.09 11.959 11.959 0 0 1-1.293-.908Z" /></svg>
							</div>
							<div>
								<h3 style="margin: 0; font-size: 16px; font-weight: 700; color: var(--ds-secondary);">Security & Compliance</h3>
								<p style="margin: 4px 0 0; font-size: 12px; color: var(--ds-text-light);">Manage data integrity and access controls.</p>
							</div>
						</div>

						<div style="display: flex; flex-direction: column; gap: 24px;">
							<div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 20px; border-bottom: 1px solid var(--ds-divider);">
								<div>
									<div style="font-weight: 700; color: var(--ds-secondary); font-size: 14px; margin-bottom: 4px;">Anti-Spam Verification</div>
									<div style="font-size: 13px; color: var(--ds-text-light);">Shield interactions with high-intensity sanitization.</div>
								</div>
								<label class="ds-toggle">
									<input type="checkbox" name="ds_security_enabled" value="1" <?php checked( 1, get_option( 'ds_security_enabled' ) ); ?>>
									<span class="ds-toggle-slider"></span>
								</label>
							</div>

							<div style="display: flex; justify-content: space-between; align-items: center;">
								<div>
									<div style="font-weight: 700; color: var(--ds-secondary); font-size: 14px; margin-bottom: 4px;">GDPR Compliant Redaction</div>
									<div style="font-size: 13px; color: var(--ds-text-light);">Anonymize sensitive data nodes automatically.</div>
								</div>
								<label class="ds-toggle">
									<input type="checkbox" name="ds_gdpr_redact" value="1" <?php checked( 1, get_option( 'ds_gdpr_redact' ) ); ?>>
									<span class="ds-toggle-slider"></span>
								</label>
							</div>
						</div>
					</div>

					<div class="ds-card">
						<div style="display: flex; align-items: center; gap: 12px; margin-bottom: 24px;">
							<div style="width: 40px; height: 40px; background: var(--ds-indigo-soft); color: var(--ds-indigo); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px; height:20px;"><path d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
							</div>
							<div>
								<h3 style="margin: 0; font-size: 16px; font-weight: 700; color: var(--ds-secondary);">Notification Relay</h3>
								<p style="margin: 4px 0 0; font-size: 12px; color: var(--ds-text-light);">Configure automated reporting channels.</p>
							</div>
						</div>

						<div style="display: flex; flex-direction: column; gap: 20px;">
							<div>
								<label class="ds-label">Primary Admin Gateway</label>
								<input type="email" name="ds_notify_email" class="ds-input-field" value="<?php echo esc_attr( get_option( 'ds_notify_email' ) ); ?>" placeholder="admin@network.local">
							</div>
							<div style="display: flex; justify-content: space-between; align-items: center; padding-top: 10px;">
								<div>
									<div style="font-weight: 700; color: var(--ds-secondary); font-size: 14px; margin-bottom: 4px;">Real-time Transmission</div>
									<div style="font-size: 13px; color: var(--ds-text-light);">Notify immediately upon new capture.</div>
								</div>
								<label class="ds-toggle">
									<input type="checkbox" name="ds_notify_trigger" value="1" <?php checked( 1, get_option( 'ds_notify_trigger' ) ); ?>>
									<span class="ds-toggle-slider"></span>
								</label>
							</div>
						</div>
					</div>

					<div style="padding: 24px; border: 1px solid var(--ds-border); border-radius: var(--ds-radius-md); background: var(--ds-bg); margin-bottom: 48px; display: flex; align-items: center; justify-content: space-between;">
						<div style="display: flex; align-items: center; gap: 16px;">
							<div style="width: 10px; height: 10px; background: var(--ds-success); border-radius: 50%; box-shadow: 0 0 10px var(--ds-success-soft);"></div>
							<span style="font-size: 14px; font-weight: 600; color: var(--ds-secondary);">System Status: All Engines Operational</span>
						</div>
						<?php submit_button( 'Sync Transformations', 'ds-btn ds-btn-primary', 'submit', false ); ?>
					</div>
				</form>
			</div>
		</div>
		<?php
	}
}
