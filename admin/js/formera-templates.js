/**
 * Formera Templates Page JavaScript
 * Modal is built entirely via DOM with inline styles to escape WP admin stacking contexts
 */
jQuery(document).ready(function($) {
    'use strict';

    var templates = formera_templates.templates || {};
    var activeCategory = 'all';
    var currentSlug = '';

    // =======================
    // Category Tab Filtering
    // =======================
    $(document).on('click', '.formera-tpl-tab', function() {
        var category = $(this).data('category');
        activeCategory = category;
        $('.formera-tpl-tab').removeClass('active');
        $(this).addClass('active');
        filterTemplates();
    });

    // =======================
    // Search
    // =======================
    var searchTimer = null;
    $('#formera-tpl-search').on('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            filterTemplates();
        }, 200);
    });

    function filterTemplates() {
        var search = ($('#formera-tpl-search').val() || '').toLowerCase().trim();
        var visibleCount = 0;
        $('.formera-tpl-card').each(function() {
            var $card = $(this);
            var cardCategory = $card.data('category');
            var cardTitle = ($card.data('title') || '').toString();
            var cardDesc = ($card.data('description') || '').toString();
            var matchesCategory = (activeCategory === 'all' || cardCategory === activeCategory);
            var matchesSearch = (!search || cardTitle.indexOf(search) !== -1 || cardDesc.indexOf(search) !== -1);
            if (matchesCategory && matchesSearch) {
                $card.show();
                visibleCount++;
            } else {
                $card.hide();
            }
        });
        if (visibleCount === 0) {
            $('.formera-tpl-empty').show();
            $('.formera-tpl-grid').hide();
        } else {
            $('.formera-tpl-empty').hide();
            $('.formera-tpl-grid').show();
        }
    }

    // =======================
    // Helpers
    // =======================
    function findTemplate(slug) {
        for (var catKey in templates) {
            if (templates.hasOwnProperty(catKey)) {
                var tpls = templates[catKey].templates || [];
                for (var i = 0; i < tpls.length; i++) {
                    if (tpls[i].slug === slug) return tpls[i];
                }
            }
        }
        return null;
    }

    var typeLabels = {
        text: 'Short answer',
        textarea: 'Paragraph',
        radio: 'Multiple choice',
        checkbox: 'Checkboxes'
    };

    function esc(str) {
        if (!str) return '';
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(str));
        return d.innerHTML;
    }

    // =======================
    // Build modal via DOM (pure inline styles, appended to body)
    // =======================
    var overlay = document.createElement('div');
    overlay.id = 'formera-tpl-modal-overlay';
    overlay.style.cssText = 'display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.65);z-index:999999999;align-items:center;justify-content:center;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;';

    var box = document.createElement('div');
    box.style.cssText = 'background:#fff;border-radius:12px;max-width:600px;width:92%;max-height:85vh;display:flex;flex-direction:column;box-shadow:0 25px 50px rgba(0,0,0,0.25);animation:formeraTplFadeIn 0.2s ease-out;';

    var header = document.createElement('div');
    header.style.cssText = 'display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #e8eaed;flex-shrink:0;';

    var title = document.createElement('h2');
    title.style.cssText = 'margin:0;font-size:18px;font-weight:600;color:#202124;';

    var closeBtn = document.createElement('button');
    closeBtn.type = 'button';
    closeBtn.style.cssText = 'background:none;border:none;cursor:pointer;padding:6px;color:#5f6368;border-radius:50%;display:flex;align-items:center;justify-content:center;';
    closeBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px;height:20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';

    header.appendChild(title);
    header.appendChild(closeBtn);

    var body = document.createElement('div');
    body.style.cssText = 'padding:24px;overflow-y:auto;flex:1;';

    var footer = document.createElement('div');
    footer.style.cssText = 'display:flex;align-items:center;justify-content:flex-end;gap:12px;padding:16px 24px;border-top:1px solid #e8eaed;flex-shrink:0;';

    var cancelBtn = document.createElement('button');
    cancelBtn.textContent = 'Cancel';
    cancelBtn.style.cssText = 'padding:10px 20px;border:1.5px solid #dadce0;border-radius:8px;background:#fff;color:#3c4043;font-size:14px;font-weight:500;cursor:pointer;font-family:inherit;';

    var useBtn = document.createElement('button');
    useBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;vertical-align:middle;margin-right:6px;"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>Use This Template';
    useBtn.style.cssText = 'padding:10px 20px;border:none;border-radius:8px;background:#673ab7;color:#fff;font-size:14px;font-weight:500;cursor:pointer;display:inline-flex;align-items:center;font-family:inherit;';

    footer.appendChild(cancelBtn);
    footer.appendChild(useBtn);
    box.appendChild(header);
    box.appendChild(body);
    box.appendChild(footer);
    overlay.appendChild(box);

    // Inject animation keyframe
    var animStyle = document.createElement('style');
    animStyle.textContent = '@keyframes formeraTplFadeIn{from{opacity:0;transform:translateY(16px);}to{opacity:1;transform:translateY(0);}}';
    document.head.appendChild(animStyle);

    // Append modal directly to document.body
    document.body.appendChild(overlay);

    // =======================
    // Modal open / close
    // =======================
    function openModal(slug) {
        var tpl = findTemplate(slug);
        if (!tpl) return;
        currentSlug = slug;
        title.textContent = tpl.title;
        body.innerHTML = renderPreview(tpl);
        overlay.style.display = 'flex';
    }

    function closeModal() {
        overlay.style.display = 'none';
        currentSlug = '';
    }

    function renderPreview(tpl) {
        var html = '<p style="margin:0 0 20px;font-size:13px;color:#5f6368;">' + esc(tpl.description) + '</p>';
        html += '<div style="font-size:12px;font-weight:600;color:#673ab7;margin-bottom:12px;letter-spacing:0.5px;">' + tpl.questions.length + ' FIELDS</div>';

        for (var i = 0; i < tpl.questions.length; i++) {
            var q = tpl.questions[i];
            html += '<div style="padding:16px;background:#f8f9fa;border-radius:8px;margin-bottom:12px;">';
            html += '<div style="font-size:14px;font-weight:500;color:#202124;margin-bottom:6px;">';
            html += '<span>' + (i + 1) + '. ' + esc(q.title || 'Untitled') + '</span>';
            if (q.required) html += ' <span style="color:#ea4335;font-weight:600;">*</span>';
            html += '</div>';
            html += '<div style="font-size:12px;color:#5f6368;margin-bottom:8px;">' + esc(typeLabels[q.type] || q.type) + '</div>';

            if (q.type === 'radio' || q.type === 'checkbox') {
                var opts = q.options ? q.options.split(',') : [];
                for (var j = 0; j < opts.length; j++) {
                    var br = q.type === 'checkbox' ? '3px' : '50%';
                    html += '<div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#3c4043;margin-bottom:5px;">';
                    html += '<div style="width:16px;height:16px;border-radius:' + br + ';border:2px solid #dadce0;flex-shrink:0;"></div>';
                    html += esc(opts[j].trim()) + '</div>';
                }
            } else if (q.type === 'textarea') {
                html += '<div style="height:50px;border:1.5px solid #dadce0;border-radius:6px;width:100%;"></div>';
            } else {
                html += '<div style="height:1px;border-bottom:1.5px solid #dadce0;width:60%;margin-top:8px;"></div>';
            }
            html += '</div>';
        }
        return html;
    }

    // Click card to open preview
    $(document).on('click', '.formera-tpl-card', function(e) {
        if ($(e.target).closest('.formera-tpl-use-btn').length) return;
        openModal($(this).data('slug'));
    });

    // Close handlers
    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) closeModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && overlay.style.display === 'flex') closeModal();
    });

    // =======================
    // Use Template
    // =======================
    function useTemplate(slug) {
        var btns = document.querySelectorAll('[data-slug="' + slug + '"].formera-tpl-use-btn');
        btns.forEach(function(b) { b.disabled = true; b.textContent = 'Creating...'; });
        useBtn.disabled = true;
        useBtn.textContent = 'Creating...';

        $.post(formera_templates.ajax_url, {
            action: 'formera_use_template',
            slug: slug,
            security: formera_templates.nonce
        }, function(response) {
            if (response.success && response.data.redirect) {
                window.location.href = response.data.redirect;
            } else {
                alert('Error: ' + (response.data || 'Failed to create form from template'));
                resetButtons();
            }
        }).fail(function() {
            alert('Network error. Please try again.');
            resetButtons();
        });

        function resetButtons() {
            btns.forEach(function(b) {
                b.disabled = false;
                b.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;"><path d="M12 4.5v15m7.5-7.5h-15"/></svg> Use Template';
            });
            useBtn.disabled = false;
            useBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;vertical-align:middle;margin-right:6px;"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>Use This Template';
        }
    }

    // Use template from card footer
    $(document).on('click', '.formera-tpl-use-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        useTemplate($(this).data('slug'));
    });

    // Use template from modal footer
    useBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (currentSlug) useTemplate(currentSlug);
    });
});
