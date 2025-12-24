/**
 * Dear Survey Admin JS
 * Visual Only Interactions: Modal Handlers & UI Elements
 */

jQuery(document).ready(function ($) {
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
