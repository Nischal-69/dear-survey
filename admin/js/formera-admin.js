/**
 * Formera Admin JS
 * Visual Only Interactions: Modal Handlers & UI Elements
 */

jQuery(document).ready(function ($) {
    // Copy Shortcode
    window.copyShortcode = function(el) {
        var code = $(el).find('code').text().trim();
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(code).then(function() {
                showCopied(el);
            });
        } else {
            var textarea = document.createElement('textarea');
            textarea.value = code;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            showCopied(el);
        }
    };

    function showCopied(el) {
        var $el = $(el);
        $el.addClass('ds-copied');
        var $btn = $el.find('.ds-copy-btn');
        var origHtml = $btn.html();
        $btn.html('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>');
        setTimeout(function() {
            $el.removeClass('ds-copied');
            $btn.html(origHtml);
        }, 1500);
    }

    // Delete Modal Logic
    let deleteUrl = '';
    const modal = $('#ds-delete-modal');

    $(document).on('click', '.ds-delete-trigger', function (e) {
        e.preventDefault();
        deleteUrl = $(this).attr('data-href') || $(this).attr('href');
        modal.fadeIn(200).css('display', 'flex');
    });

    $('#ds-cancel-delete-btn').on('click', function () {
        modal.fadeOut(200);
        deleteUrl = '';
    });

    $('#ds-confirm-delete-btn').on('click', function () {
        if (deleteUrl) {
            window.location.href = deleteUrl;
        }
    });

    // Close on click outside content
    modal.on('click', function (e) {
        if (e.target === this) {
            modal.fadeOut(200);
        }
    });

    // Toggle Sections (Visual only)
    window.dsToggleQuestion = function (id) {
        const body = $(`#q-body-${id}`);
        const icon = $(`#toggle-icon-${id}`);
        body.slideToggle(300);
        icon.toggleClass('rotated');
    };
});
