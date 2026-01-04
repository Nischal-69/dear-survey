/**
 * Dear Survey Public JS
 * Modern & Attractive Interactions
 */

jQuery(document).ready(function ($) {
    
    // Update progress bar
    function updateProgress($form) {
        const surveyId = $form.data('id');
        const total = parseInt($form.data('total')) || 0;
        if (total === 0) return;
        
        let answered = 0;
        
        $form.find('.gf-question-card[data-question]').each(function() {
            const $card = $(this);
            const hasTextInput = $card.find('.gf-text-input, .gf-textarea-input').length > 0;
            const hasRadio = $card.find('input[type="radio"]').length > 0;
            const hasCheckbox = $card.find('input[type="checkbox"]').length > 0;
            
            if (hasTextInput && $card.find('.gf-text-input, .gf-textarea-input').val().trim() !== '') {
                answered++;
            } else if (hasRadio && $card.find('input[type="radio"]:checked').length > 0) {
                answered++;
            } else if (hasCheckbox && $card.find('input[type="checkbox"]:checked').length > 0) {
                answered++;
            }
        });
        
        const percent = Math.round((answered / total) * 100);
        
        $(`#progress-fill-${surveyId}`).css('width', percent + '%');
        $(`#progress-status-${surveyId}`).text(`${answered} of ${total} answered`);
        $(`#progress-percent-${surveyId}`).text(percent + '%');
    }
    
    // Initialize progress on page load
    $('.gf-survey-form').each(function() {
        updateProgress($(this));
    });
    
    // Update progress on input change
    $(document).on('input change', '.gf-survey-form input, .gf-survey-form textarea', function() {
        updateProgress($(this).closest('.gf-survey-form'));
    });
    
    // Handle form submission
    $('.gf-survey-form').on('submit', function (e) {
        e.preventDefault();

        const $form = $(this);
        const $wrap = $form.closest('.gf-survey-wrap');
        const $btn = $form.find('.gf-submit-btn');
        const $msg = $form.find('.gf-message');
        const $successScreen = $wrap.find('.gf-success-screen');
        const surveyId = $form.data('id');

        // Clear previous errors
        $form.find('.gf-question-card').removeClass('has-error');
        $msg.removeClass('is-visible is-error is-success').text('');

        // Loading state
        $btn.addClass('is-loading').prop('disabled', true);

        // Collect form data
        const formData = $form.serializeArray();
        const responseData = {};
        
        formData.forEach(item => {
            if (item.name.endsWith('[]')) {
                const key = item.name.replace('[]', '');
                if (!responseData[key]) responseData[key] = [];
                responseData[key].push(item.value);
            } else {
                responseData[item.name] = item.value;
            }
        });

        // AJAX submission
        $.ajax({
            url: dearsurvey_obj.ajax_url,
            type: 'POST',
            data: {
                action: 'ds_submit_survey',
                survey_id: surveyId,
                response_data: JSON.stringify(responseData),
                security: dearsurvey_obj.nonce
            },
            success: function (response) {
                $btn.removeClass('is-loading').prop('disabled', false);
                
                if (response.success) {
                    // Show success screen with animation
                    $form.fadeOut(250, function() {
                        $wrap.find('.gf-header-card').fadeOut(200);
                        $successScreen.css('display', 'block').hide().fadeIn(400);
                    });
                    
                    // Scroll to top of survey
                    setTimeout(function() {
                        $('html, body').animate({
                            scrollTop: $wrap.offset().top - 50
                        }, 400);
                    }, 300);
                } else {
                    // Show error message
                    $msg.addClass('is-visible is-error').html('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:18px;height:18px;flex-shrink:0;"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>' + (response.data || 'An error occurred. Please try again.'));
                    
                    // Scroll to message
                    $('html, body').animate({
                        scrollTop: $msg.offset().top - 100
                    }, 300);
                }
            },
            error: function () {
                $btn.removeClass('is-loading').prop('disabled', false);
                $msg.addClass('is-visible is-error').html('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:18px;height:18px;flex-shrink:0;"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg>Connection error. Please check your internet and try again.');
            }
        });
    });

    // Clear form button
    $('.gf-clear-btn').on('click', function () {
        const $form = $(this).closest('.gf-survey-form');
        
        // Animate clear
        $form.find('.gf-question-card').each(function(i) {
            const $card = $(this);
            setTimeout(function() {
                $card.css('opacity', '0.5');
                setTimeout(function() {
                    $card.css('opacity', '1');
                }, 150);
            }, i * 50);
        });
        
        // Reset all inputs
        setTimeout(function() {
            $form.find('input[type="text"], input[type="email"], textarea').val('');
            $form.find('input[type="radio"], input[type="checkbox"]').prop('checked', false);
            
            // Clear error states
            $form.find('.gf-question-card').removeClass('has-error');
            $form.find('.gf-message').removeClass('is-visible is-error is-success').text('');
            
            // Update progress
            updateProgress($form);
            
            // Scroll to top
            $('html, body').animate({
                scrollTop: $form.closest('.gf-survey-wrap').offset().top - 50
            }, 400);
        }, 200);
    });

    // Submit another response button
    $('.gf-submit-another').on('click', function () {
        const $wrap = $(this).closest('.gf-survey-wrap');
        const $form = $wrap.find('.gf-survey-form');
        const $successScreen = $wrap.find('.gf-success-screen');
        
        // Reset form
        $form[0].reset();
        $form.find('.gf-question-card').removeClass('has-error');
        $form.find('.gf-message').removeClass('is-visible is-error is-success').text('');
        
        // Update progress
        updateProgress($form);
        
        // Show form again with animation
        $successScreen.fadeOut(250, function() {
            $wrap.find('.gf-header-card').fadeIn(200);
            $form.fadeIn(400);
        });
        
        // Scroll to top
        setTimeout(function() {
            $('html, body').animate({
                scrollTop: $wrap.offset().top - 50
            }, 400);
        }, 300);
    });

    // Radio/Checkbox visual feedback with ripple effect
    $('.gf-option-item input').on('change', function () {
        const $input = $(this);
        const $card = $input.closest('.gf-question-card');
        const $option = $input.closest('.gf-option-item');
        
        // Remove error state when user selects an option
        $card.removeClass('has-error');
        
        // Add selection animation
        $option.addClass('is-selected');
        setTimeout(function() {
            $option.removeClass('is-selected');
        }, 300);
        
        // For radio buttons, update visual state
        if ($input.attr('type') === 'radio') {
            const name = $input.attr('name');
            $('input[name="' + name + '"]').closest('.gf-option-item').removeClass('is-active');
            $option.addClass('is-active');
        }
    });

    // Text input focus effects with enhanced styling
    $('.gf-text-input, .gf-textarea-input').on('focus', function () {
        $(this).closest('.gf-question-card').addClass('is-focused');
    }).on('blur', function () {
        $(this).closest('.gf-question-card').removeClass('is-focused');
    }).on('input', function () {
        // Remove error state when user starts typing
        $(this).closest('.gf-question-card').removeClass('has-error');
    });

    // Keyboard navigation for options
    $('.gf-option-item').on('keypress', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            const $input = $(this).find('input');
            
            if ($input.attr('type') === 'radio') {
                $input.prop('checked', true).trigger('change');
            } else {
                $input.prop('checked', !$input.prop('checked')).trigger('change');
            }
        }
    });

    // Make option items focusable
    $('.gf-option-item').attr('tabindex', '0');
    
    // Smooth scroll animation for long forms
    $('.gf-question-card').on('focusin', function() {
        const $card = $(this);
        const cardTop = $card.offset().top;
        const windowTop = $(window).scrollTop();
        const windowHeight = $(window).height();
        
        // Only scroll if card is near edges of viewport
        if (cardTop < windowTop + 100 || cardTop > windowTop + windowHeight - 200) {
            $('html, body').animate({
                scrollTop: cardTop - 120
            }, 300);
        }
    });
});
