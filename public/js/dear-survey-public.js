/**
 * Dear Survey Public JS
 * Google Forms-Inspired Interactions
 */

jQuery(document).ready(function ($) {
    
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
            url: dear_survey_obj.ajax_url,
            type: 'POST',
            data: {
                action: 'ds_submit_survey',
                survey_id: surveyId,
                response_data: JSON.stringify(responseData),
                security: dear_survey_obj.nonce
            },
            success: function (response) {
                $btn.removeClass('is-loading').prop('disabled', false);
                
                if (response.success) {
                    // Show success screen
                    $form.hide();
                    $wrap.find('.gf-header-card').hide();
                    $successScreen.fadeIn(300);
                    
                    // Scroll to top of survey
                    $('html, body').animate({
                        scrollTop: $wrap.offset().top - 50
                    }, 300);
                } else {
                    // Show error message
                    $msg.addClass('is-visible is-error').text(response.data || 'An error occurred. Please try again.');
                }
            },
            error: function () {
                $btn.removeClass('is-loading').prop('disabled', false);
                $msg.addClass('is-visible is-error').text('Submission failed. Please check your connection and try again.');
            }
        });
    });

    // Clear form button
    $('.gf-clear-btn').on('click', function () {
        const $form = $(this).closest('.gf-survey-form');
        
        // Reset all inputs
        $form.find('input[type="text"], input[type="email"], textarea').val('');
        $form.find('input[type="radio"], input[type="checkbox"]').prop('checked', false);
        
        // Clear error states
        $form.find('.gf-question-card').removeClass('has-error');
        $form.find('.gf-message').removeClass('is-visible is-error is-success').text('');
        
        // Scroll to top
        $('html, body').animate({
            scrollTop: $form.closest('.gf-survey-wrap').offset().top - 50
        }, 300);
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
        
        // Show form again
        $successScreen.hide();
        $wrap.find('.gf-header-card').fadeIn(200);
        $form.fadeIn(300);
        
        // Scroll to top
        $('html, body').animate({
            scrollTop: $wrap.offset().top - 50
        }, 300);
    });

    // Radio/Checkbox visual feedback
    $('.gf-option-item input').on('change', function () {
        const $input = $(this);
        const $card = $input.closest('.gf-question-card');
        
        // Remove error state when user selects an option
        $card.removeClass('has-error');
        
        // For radio buttons, trigger visual update
        if ($input.attr('type') === 'radio') {
            const name = $input.attr('name');
            $('input[name="' + name + '"]').closest('.gf-option-item').removeClass('is-selected');
            $input.closest('.gf-option-item').addClass('is-selected');
        }
    });

    // Text input focus effects
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
});
