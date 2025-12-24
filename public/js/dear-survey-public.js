/**
 * Dear Survey Public JS
 * UI/UX Refactor: Visual Feedback & Smooth Submission
 */

jQuery(document).ready(function ($) {
    $('.ds-survey-form').on('submit', function (e) {
        e.preventDefault();

        const $form = $(this);
        const $btn = $form.find('.ds-submit-btn');
        const $msg = $form.find('.ds-message');
        const surveyId = $form.data('id');

        // Visual UX: Loading state
        $btn.prop('disabled', true).text('Submitting...');
        $msg.removeClass('success error').text('Processing your response...');

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

        // AJAX POST to WordPress
        $.ajax({
            url: dear_survey_obj.ajax_url,
            type: 'POST',
            data: {
                action: 'ds_submit_survey',
                survey_id: surveyId,
                response_data: JSON.stringify(responseData)
            },
            success: function (response) {
                if (response.success) {
                    $msg.addClass('success').text('Thank you! Your response has been recorded. 🎉');
                    $form.fadeOut(500, function () {
                        $form.html('<div style="text-align:center; padding:20px;"><h2 style="color:var(--ds-p-primary);">Success!</h2><p>Your survey has been submitted.</p></div>').fadeIn();
                    });
                } else {
                    $msg.addClass('error').text(response.data || 'An error occurred.');
                    $btn.prop('disabled', false).text('Submit Survey');
                }
            },
            error: function () {
                $msg.addClass('error').text('Submission failed. Please try again.');
                $btn.prop('disabled', false).text('Submit Survey');
            }
        });
    });
});
