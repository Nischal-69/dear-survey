		jQuery(document).ready(function($) {
			let questions = formera_builder.questions;
			let activeQuestionIndex = null;

			// Type icons SVGs
			const typeIcons = {
				text: '<svg class="gf-type-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.75h15M4.5 12h9"/></svg>',
				textarea: '<svg class="gf-type-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.75h15M4.5 12h15M4.5 17.25h9"/></svg>',
				radio: '<svg class="gf-type-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><circle cx="12" cy="12" r="7.5"/><circle cx="12" cy="12" r="3" fill="currentColor"/></svg>',
				checkbox: '<svg class="gf-type-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><rect x="4.5" y="4.5" width="15" height="15" rx="2"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"/></svg>'
			};
			
			const typeLabels = {
				text: 'Short answer',
				textarea: 'Paragraph',
				radio: 'Multiple choice',
				checkbox: 'Checkboxes'
			};
			
			window.getTypeIcon = function(type) {
				return typeIcons[type] || typeIcons.text;
			};
			
			window.getTypeLabel = function(type) {
				return typeLabels[type] || typeLabels.text;
			};

			// Auto-resize description textarea
			function autoResizeTextarea(el) {
				el.style.height = 'auto';
				el.style.height = el.scrollHeight + 'px';
			}
			
			// Initialize description auto-resize
			const descTextarea = document.getElementById('survey_description');
			if (descTextarea) {
				descTextarea.addEventListener('input', function() {
					autoResizeTextarea(this);
				});
				// Trigger on load if there's content
				autoResizeTextarea(descTextarea);
			}

			window.dsToggleQuestion = function(index) {
				// Set active question visually
				$('.gf-q-card').removeClass('gf-focused');
				$(`#q-item-${index}`).addClass('gf-focused');
				activeQuestionIndex = index;
			}

			function renderQuestions() {
				$('#questions-container').empty();
				
				if (questions.length === 0) {
					$('#questions-container').html(`
						<div class="gf-card gf-animate" style="text-align:center; padding:48px; color:#5F6368;">
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" style="width:48px;height:48px;color:#DADCE0;margin-bottom:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
							<p style="margin:0 0 8px;font-size:16px;">No questions yet</p>
							<p style="margin:0;font-size:13px;">Click the + button to add your first question</p>
						</div>
					`);
					return;
				}
				
				questions.forEach((q, index) => {
					const isActive = index === activeQuestionIndex;
					const optionIconClass = q.type === 'checkbox' ? 'gf-checkbox' : '';
					
					let optionsHtml = '';
					if (q.type === 'radio' || q.type === 'checkbox') {
						const opts = q.options ? q.options.split(',') : [''];
						optionsHtml = `
							<div class="gf-options-container">
								${opts.map((opt, oIdx) => `
									<div class="gf-option-row">
										<div class="gf-option-icon ${optionIconClass}"></div>
										<input type="text" class="gf-option-input option-input" data-qindex="${index}" data-oindex="${oIdx}" value="${opt.trim()}" placeholder="Enter option ${oIdx + 1}" autocomplete="off">
										<button type="button" class="gf-option-delete" onclick="event.stopPropagation(); removeOption(${index}, ${oIdx})" title="Remove">
											<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
										</button>
									</div>
								`).join('')}
								<div class="gf-add-option" onclick="event.stopPropagation(); addOption(${index})">
									<div class="gf-option-icon ${optionIconClass}"></div>
									<span class="gf-add-option-text">Add option</span>
								</div>
							</div>
						`;
					} else if (q.type === 'textarea') {
						optionsHtml = '<div class="gf-textarea-preview" style="margin-top:16px;">Long answer text</div>';
					} else {
						optionsHtml = '<div class="gf-text-preview" style="margin-top:16px;">Short answer text</div>';
					}
					
					let html = `
						<div class="gf-card gf-q-card gf-animate ${isActive ? 'gf-focused' : ''}" id="q-item-${index}" onclick="window.dsToggleQuestion(${index})" style="animation-delay: ${index * 0.05}s">
							<div class="gf-card-body">
								<div class="gf-q-header">
									<div class="gf-q-title-wrap">
										<input type="text" class="gf-q-title-input q-title" data-index="${index}" value="${q.title || ''}" placeholder="Question" autocomplete="off">
									</div>
									<div class="gf-type-select-wrap">
										<select class="gf-type-select q-type" data-index="${index}">
											<option value="text" ${q.type === 'text' ? 'selected' : ''}>Short answer</option>
											<option value="textarea" ${q.type === 'textarea' ? 'selected' : ''}>Paragraph</option>
											<option value="radio" ${q.type === 'radio' ? 'selected' : ''}>Multiple choice</option>
											<option value="checkbox" ${q.type === 'checkbox' ? 'selected' : ''}>Checkboxes</option>
										</select>
										<div class="gf-type-display" data-index="${index}">
											${window.getTypeIcon(q.type)}
											<span class="gf-type-label">${window.getTypeLabel(q.type)}</span>
										</div>
										<div class="gf-type-menu" data-index="${index}">
											<div class="gf-type-option ${q.type === 'text' ? 'gf-selected' : ''}" data-value="text">
												${window.getTypeIcon('text')}
												<span class="gf-type-label">Short answer</span>
											</div>
											<div class="gf-type-option ${q.type === 'textarea' ? 'gf-selected' : ''}" data-value="textarea">
												${window.getTypeIcon('textarea')}
												<span class="gf-type-label">Paragraph</span>
											</div>
											<div class="gf-type-option ${q.type === 'radio' ? 'gf-selected' : ''}" data-value="radio">
												${window.getTypeIcon('radio')}
												<span class="gf-type-label">Multiple choice</span>
											</div>
											<div class="gf-type-option ${q.type === 'checkbox' ? 'gf-selected' : ''}" data-value="checkbox">
												${window.getTypeIcon('checkbox')}
												<span class="gf-type-label">Checkboxes</span>
											</div>
										</div>
									</div>
								</div>
								<div class="gf-q-options">
									${optionsHtml}
								</div>
							</div>
							<div class="gf-q-footer">
								<button type="button" class="gf-icon-btn" title="Duplicate" onclick="event.stopPropagation(); duplicateQuestion(${index})">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75"/></svg>
								</button>
								<button type="button" class="gf-icon-btn gf-delete" title="Delete" onclick="event.stopPropagation(); deleteQuestion(${index})">
									<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
								</button>
								<div class="gf-divider-v"></div>
								<div class="gf-required-wrap">
									<span>Required</span>
									<div class="gf-toggle ${q.required ? 'gf-on' : ''}" onclick="event.stopPropagation(); toggleRequired(${index})"></div>
								</div>
							</div>
						</div>
					`;
					$('#questions-container').append(html);
				});
			}

			function renderOptions(qIndex) {
				// Re-render is handled by renderQuestions now
				renderQuestions();
			}
			
			window.duplicateQuestion = function(index) {
				const copy = JSON.parse(JSON.stringify(questions[index]));
				questions.splice(index + 1, 0, copy);
				activeQuestionIndex = index + 1;
				renderQuestions();
				
				// Animate the duplicated card
				const newCard = document.getElementById(`q-item-${activeQuestionIndex}`);
				if (newCard) {
					newCard.classList.add('gf-new');
					setTimeout(() => {
						newCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
					}, 50);
					setTimeout(() => {
						newCard.classList.remove('gf-new');
					}, 600);
				}
			};
			
			window.addQuestionAfter = function(index) {
				const newQuestion = { title: '', type: 'radio', options: '', required: false };
				questions.splice(index + 1, 0, newQuestion);
				activeQuestionIndex = index + 1;
				renderQuestions();
				
				// Animate the new card
				const newCard = document.getElementById(`q-item-${activeQuestionIndex}`);
				if (newCard) {
					newCard.classList.add('gf-new');
					setTimeout(() => {
						newCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
					}, 50);
					setTimeout(() => {
						const titleInput = newCard.querySelector('.q-title');
						if (titleInput) titleInput.focus();
						newCard.classList.add('gf-pulse');
					}, 400);
					setTimeout(() => {
						newCard.classList.remove('gf-new', 'gf-pulse');
					}, 1000);
				}
			};
			
			window.toggleRequired = function(index) {
				questions[index].required = !questions[index].required;
				// Animate just the toggle, not full re-render
				const toggle = document.querySelector(`#q-item-${index} .gf-toggle`);
				if (toggle) {
					toggle.classList.toggle('gf-on', questions[index].required);
				}
			};

			window.deleteQuestion = function(index) {
				// Animate card out before removing
				const card = document.getElementById(`q-item-${index}`);
				if (card) {
					card.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
					card.style.opacity = '0';
					card.style.transform = 'scale(0.95)';
					setTimeout(() => {
						questions.splice(index, 1);
						// Focus the next available question or the previous one
						if (questions.length > 0) {
							activeQuestionIndex = Math.min(index, questions.length - 1);
						} else {
							activeQuestionIndex = null;
						}
						renderQuestions();
					}, 200);
				} else {
					questions.splice(index, 1);
					// Focus the next available question or the previous one
					if (questions.length > 0) {
						activeQuestionIndex = Math.min(index, questions.length - 1);
					} else {
						activeQuestionIndex = null;
					}
					renderQuestions();
				}
			};

			window.addOption = function(qIndex) {
				const q = questions[qIndex];
				let currentOpts = q.options ? q.options.split(',') : [];
				currentOpts.push('');
				questions[qIndex].options = currentOpts.join(',');
				renderQuestions();
			};

			window.removeOption = function(qIndex, oIndex) {
				let currentOpts = questions[qIndex].options.split(',');
				if (currentOpts.length > 1) {
					currentOpts.splice(oIndex, 1);
					questions[qIndex].options = currentOpts.join(',');
					renderQuestions();
				}
			};

			$('#add-question').on('click', function() {
				questions.push({ title: '', type: 'radio', options: '', required: false });
				activeQuestionIndex = questions.length - 1;
				renderQuestions();
				
				// Smooth scroll to new question and animate
				const newCard = document.getElementById(`q-item-${activeQuestionIndex}`);
				if (newCard) {
					// Add special animation class for new cards
					newCard.classList.add('gf-new');
					
					// Smooth scroll into view
					setTimeout(() => {
						newCard.scrollIntoView({ 
							behavior: 'smooth', 
							block: 'center' 
						});
					}, 50);
					
					// Focus input after scroll completes
					setTimeout(() => {
						const titleInput = newCard.querySelector('.q-title');
						if (titleInput) {
							titleInput.focus();
						}
						// Add pulse effect after focus
						newCard.classList.add('gf-pulse');
					}, 400);
					
					// Remove animation classes after complete
					setTimeout(() => {
						newCard.classList.remove('gf-new', 'gf-pulse');
					}, 1000);
				}
			});

			$(document).on('input', '.q-title', function() {
				const index = $(this).data('index');
				questions[index].title = $(this).val();
			});

			$(document).on('change', '.q-type', function() {
				const index = $(this).data('index');
				const newType = $(this).val();
				questions[index].type = newType;
				if ((newType === 'radio' || newType === 'checkbox') && !questions[index].options) {
					questions[index].options = '';
				}
				renderQuestions();
			});

			// Custom type dropdown interactions
			$(document).on('click', '.gf-type-display', function(e) {
				e.stopPropagation();
				const $wrap = $(this).closest('.gf-type-select-wrap');
				const $menu = $wrap.find('.gf-type-menu');
				const wasOpen = $(this).hasClass('gf-open');
				
				// Close all other dropdowns
				$('.gf-type-display').removeClass('gf-open');
				$('.gf-type-menu').removeClass('gf-open');
				
				if (!wasOpen) {
					$(this).addClass('gf-open');
					$menu.addClass('gf-open');
				}
			});
			
			$(document).on('click', '.gf-type-option', function(e) {
				e.stopPropagation();
				const $wrap = $(this).closest('.gf-type-select-wrap');
				const newValue = $(this).data('value');
				const $select = $wrap.find('.gf-type-select');
				
				// Update the hidden select (triggers existing change handler)
				$select.val(newValue).trigger('change');
				
				// Close dropdown
				$wrap.find('.gf-type-display').removeClass('gf-open');
				$wrap.find('.gf-type-menu').removeClass('gf-open');
			});
			
			// Close dropdown when clicking outside
			$(document).on('click', function(e) {
				if (!$(e.target).closest('.gf-type-select-wrap').length) {
					$('.gf-type-display').removeClass('gf-open');
					$('.gf-type-menu').removeClass('gf-open');
				}
			});

			$(document).on('input', '.option-input', function() {
				const qIndex = $(this).data('qindex');
				const oIndex = $(this).data('oindex');
				let currentOpts = questions[qIndex].options.split(',');
				currentOpts[oIndex] = $(this).val();
				questions[qIndex].options = currentOpts.join(',');
			});

			$('#ds-setting-collect-email').on('change', function() {
				if($(this).is(':checked')) { $('#autoresponder-section').slideDown(); }
				else { $('#autoresponder-section').slideUp(); }
			});

			$('#ds-send-outreach').on('click', function() {
				const btn = $(this);
				const recipients = $('#ds-outreach-emails').val();
				if(!recipients) { alert('Please enter at least one recipient email.'); return; }
				btn.prop('disabled', true).html('Sending...');
				$.post(ajaxurl, {
					action: 'formera_send_outreach',
					survey_id: $('#survey_id').val(),
					recipients: recipients,
					subject: $('#ds-outreach-subject').val(),
					message: $('#ds-outreach-message').val(),
					security: formera_builder.outreach_nonce
				}, function(res) {
					btn.prop('disabled', false).html('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg> Send Invitations');
					if(res.success) { alert('Emails sent successfully!'); $('#ds-outreach-emails').val(''); }
					else { alert('Error: ' + res.data); }
				});
			});

			$('#ds-survey-form').on('submit', function(e) {
				e.preventDefault();
				const saveBtn = $('button[form="ds-survey-form"]');
				const originalHtml = saveBtn.html();
				saveBtn.prop('disabled', true).html('Saving...');
				
				let data = {
					action: 'formera_save_survey',
					id: $('#survey_id').val(),
					title: $('#survey_title').val(),
					questions: JSON.stringify(questions),
					settings: JSON.stringify({
						collect_email: $('#ds-setting-collect-email').is(':checked'),
						thank_you_body: $('#ds-setting-thank-you').val(),
						admin_email: $('#ds-setting-admin-email').val(),
						auto_subject: $('#ds-setting-auto-subject').val(),
						auto_body: $('#ds-setting-auto-body').val()
					}),
					security: formera_builder.save_nonce
				};

				$.post(ajaxurl, data, function(response) {
					saveBtn.prop('disabled', false).html(originalHtml);
					if (response.success) {
						if (!data.id || data.id == 0) { 
							window.location.href = 'admin.php?page=formera-list&id=' + response.data.id; 
						} else {
							saveBtn.css('background', '#12B76A').html('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:18px;height:18px;"><path d="M4.5 12.75l6 6 9-13.5"/></svg> Saved!');
							setTimeout(() => { saveBtn.css('background', '').html(originalHtml); }, 2000);
						}
					} else { alert('Save failed. Check permissions.'); }
				});
			});

			// Chevron rotation for settings
			$('.gf-chevron').css('transform', 'rotate(0deg)');
			$(document).on('click', '.gf-settings-toggle', function() {
				$(this).find('.gf-chevron').toggleClass('gf-rotated');
			});

			renderQuestions();
		});
