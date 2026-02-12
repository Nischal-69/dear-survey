/**
 * Formera Email Admin Scripts
 * 
 * Handles broadcast sending, template saving, and subscriber management.
 */

(function($) {
	'use strict';

	// Broadcast Form Handling
	function initBroadcastForm() {
		$('.ds-sub-checkbox').change(function() {
			var selected = [];
			$('.ds-sub-checkbox:checked').each(function() { 
				selected.push($(this).val()); 
			});
			$('#ds-broadcast-recipients').val(selected.join(', '));
		});

		$('#ds-template-selector').change(function() {
			var opt = $(this).find('option:selected');
			if (opt.val()) {
				$('#ds-broadcast-subject').val(opt.data('subject'));
				$('#ds-broadcast-message').val(opt.data('content'));
			}
		});

		// Auto-load template if pre-selected via URL
		if ($('#ds-template-selector').val()) {
			$('#ds-template-selector').trigger('change');
		}

		$('#ds-broadcast-form').submit(function(e) {
			e.preventDefault();
			var btn = $(this).find('button');
			btn.prop('disabled', true).text('Sending...');
			
			var message = $('#ds-broadcast-message').val();
			var surveyId = $('#ds-survey-selector').val();
			
			var data = {
				action: 'formera_send_broadcast',
				recipients: $('#ds-broadcast-recipients').val(),
				subject: $('#ds-broadcast-subject').val(),
				message: message,
				survey_id: surveyId,
				security: formera_email.broadcast_nonce
			};

			$.post(ajaxurl, data, function(res) {
				if (res.success) {
					alert('Broadcast sent successfully!');
					window.location.href = formera_email.email_page_url;
				} else {
					alert('Error: ' + res.data);
					btn.prop('disabled', false).text('Send Survey Link');
				}
			});
		});
	}

	// Template Form Handling
	function initTemplateForm() {
		$('#ds-template-form').submit(function(e) {
			e.preventDefault();
			var btn = $(this).find('button');
			btn.prop('disabled', true).text('Saving...');
			
			var data = {
				action: 'formera_save_template',
				id: $('#template_id').val(),
				name: $('#template_name').val(),
				subject: $('#template_subject').val(),
				content: $('#template_content').val(),
				security: formera_email.template_nonce
			};

			$.post(ajaxurl, data, function(res) {
				if (res.success) {
					window.location.href = formera_email.template_page_url;
				} else {
					alert('Save failed.');
					btn.prop('disabled', false).text('Save Template');
				}
			});
		});
	}

	// Subscriber Form Handling
	function initSubscriberForm() {
		$('#ds-add-sub-form').submit(function(e) {
			e.preventDefault();
			var btn = $(this).find('button');
			btn.prop('disabled', true).text('Saving...');
			$.post(ajaxurl, {
				action: 'formera_add_subscriber',
				name: $('#ds-sub-name').val(),
				email: $('#ds-sub-email').val(),
				security: formera_email.subscriber_nonce
			}, function(res) {
				if (res.success) {
					window.location.reload();
				} else { 
					alert(res.data); 
					btn.prop('disabled', false).text('Add Contact'); 
				}
			});
		});

		$('#ds-select-all-subs').change(function() {
			$('.ds-sub-item-check').prop('checked', $(this).prop('checked'));
		});

		$('#ds-bulk-send').click(function() {
			var selected = [];
			$('.ds-sub-item-check:checked').each(function() { 
				selected.push($(this).val()); 
			});
			if (selected.length === 0) { 
				alert('Select contacts first.'); 
				return; 
			}
			window.location.href = formera_email.new_broadcast_url + encodeURIComponent(selected.join(','));
		});

		$('.ds-delete-sub').click(function() {
			if (!confirm('Remove this contact?')) {
				return;
			}
			var id = $(this).data('id');
			$.post(ajaxurl, {
				action: 'formera_remove_subscriber',
				id: id,
				security: formera_email.subscriber_nonce
			}, function() { 
				$('#sub-row-' + id).fadeOut(); 
			});
		});
	}

	// Initialize on document ready
	$(document).ready(function() {
		initBroadcastForm();
		initTemplateForm();
		initSubscriberForm();
	});

})(jQuery);
