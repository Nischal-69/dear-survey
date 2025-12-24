<?php

class Dear_Survey_Settings {

	public function render() {
		?>
		<div class="ds-wrap">
			<div class="ds-header">
				<h1 class="ds-title">Settings</h1>
			</div>

			<div class="ds-card">
				<h2>General Settings</h2>
				<p class="ds-mb-4">Configure global options for Dear Survey.</p>
				<form method="post" action="options.php">
					<?php settings_fields( 'ds_settings_group' ); ?>
					<?php do_settings_sections( 'ds-settings' ); ?>
					
					<div class="ds-mb-4">
						<label class="ds-label">Enable Pro Features (Demo)</label>
						<div style="background:#f3f4f6; padding:12px; border-radius:6px; display:inline-block;">
							<input type="checkbox" disabled checked />
							<span style="color:var(--ds-text-secondary); margin-left:8px;">Pro features are active in this template.</span>
						</div>
					</div>

					<button type="submit" class="ds-btn ds-btn-primary">Save Settings</button>
				</form>
			</div>
		</div>
		<?php
	}
}
