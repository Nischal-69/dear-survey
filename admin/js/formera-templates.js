/**
 * Formera Templates – Frontend Preview + Appearance Customizer
 */
jQuery(document).ready(function($) {
    'use strict';

    /* Inject hover styles for appearance panel */
    var panelCSS = document.createElement('style');
    panelCSS.textContent = [
        '.formera-app-color:hover{transform:scale(1.15);z-index:2;}',
        '.formera-app-font-btn:hover{border-color:#bbb !important;background:#fafafa !important;}',
        '.formera-app-radius:hover{background:rgba(0,0,0,.04) !important;}',
        '.formera-app-bg:hover{border-color:#ccc !important;background:#fafafa !important;}',
        '#formera-app-reset:hover{background:#f0f0f0 !important;color:#555 !important;}',
        '#formera-app-custom-color::-webkit-color-swatch-wrapper{padding:2px;}',
        '#formera-app-custom-color::-webkit-color-swatch{border-radius:4px;border:none;}',
        '#formera-tpl-modal-overlay ::-webkit-scrollbar{width:6px;}',
        '#formera-tpl-modal-overlay ::-webkit-scrollbar-track{background:transparent;}',
        '#formera-tpl-modal-overlay ::-webkit-scrollbar-thumb{background:#ddd;border-radius:3px;}',
        '#formera-tpl-modal-overlay ::-webkit-scrollbar-thumb:hover{background:#bbb;}',
        '.fted-input:focus,.fted-textarea:focus,.fted-select:focus{border-color:#673ab7 !important;box-shadow:0 0 0 3px rgba(103,58,183,.1) !important;outline:none;}',
        '.fted-q-card:hover{border-color:#d0d0d0 !important;}',
        '.fted-btn-icon:hover{background:#f0f0f0 !important;}',
        '.fted-add-btn:hover{border-color:#673ab7 !important;color:#673ab7 !important;background:#f9f5ff !important;}',
        '.formera-app-section-btn:hover{background:#f5f5f5 !important;border-color:#ddd !important;}',
        '.formera-app-mini-btn:hover{background:#f0f0f0 !important;}',
        '.formera-app-slider::-webkit-slider-thumb:hover{box-shadow:0 0 0 8px rgba(103,58,183,0.1) !important;}'
    ].join('\n');
    document.head.appendChild(panelCSS);

    var templates = formera_templates.templates || {};
    var activeCategory = 'all';
    var currentSlug = '';
    var panelOpen = false;
    var modalStep = 'preview'; // 'preview' or 'edit'
    var editData = { title: '', questions: [] };
    var appearance = { 
        color: '#0d9488', font: 'Plus Jakarta Sans', radius: '20', bg: 'gradient',
        // Button styling
        btnSize: 'medium', btnStyle: 'filled', btnWidth: 'auto',
        // Spacing & Layout
        formWidth: 'normal', spacing: 'comfortable', questionGap: 'medium',
        // Animations
        entrance: 'fade', hover: 'lift',
        // Advanced colors
        textColor: 'auto', borderColor: 'auto', accentColor: 'auto',
        // Typography
        fontSize: 'medium', lineHeight: 'normal',
        // Form styling  
        shadow: 'soft', borders: 'clean'
    };
    var defaultAppearance = {
        color: '#0d9488', font: 'Plus Jakarta Sans', radius: '20', bg: 'gradient',
        btnSize: 'medium', btnStyle: 'filled', btnWidth: 'auto',
        formWidth: 'normal', spacing: 'comfortable', questionGap: 'medium',
        entrance: 'fade', hover: 'lift',
        textColor: 'auto', borderColor: 'auto', accentColor: 'auto',
        fontSize: 'medium', lineHeight: 'normal',
        shadow: 'soft', borders: 'clean'
    };
    var loadedFonts = {};

    /* ========== COLOR UTILITIES ========== */
    function hexToRgb(h) {
        h = h.replace('#', '');
        return parseInt(h.substr(0,2),16) + ',' + parseInt(h.substr(2,2),16) + ',' + parseInt(h.substr(4,2),16);
    }
    function mixWhite(h, p) {
        h = h.replace('#', '');
        var r = parseInt(h.substr(0,2),16), g = parseInt(h.substr(2,2),16), b = parseInt(h.substr(4,2),16);
        r = Math.round(r + (255-r) * (p/100));
        g = Math.round(g + (255-g) * (p/100));
        b = Math.round(b + (255-b) * (p/100));
        return '#' + [r,g,b].map(function(v) { return v.toString(16).padStart(2,'0'); }).join('');
    }
    function mixBlack(h, p) {
        h = h.replace('#', '');
        var r = parseInt(h.substr(0,2),16), g = parseInt(h.substr(2,2),16), b = parseInt(h.substr(4,2),16);
        r = Math.round(r * (1 - p/100));
        g = Math.round(g * (1 - p/100));
        b = Math.round(b * (1 - p/100));
        return '#' + [r,g,b].map(function(v) { return v.toString(16).padStart(2,'0'); }).join('');
    }
    function loadGFont(name) {
        if (name === 'System Default' || loadedFonts[name]) return;
        var l = document.createElement('link');
        l.rel = 'stylesheet';
        l.href = 'https://fonts.googleapis.com/css2?family=' + name.replace(/\s+/g, '+') + ':wght@400;500;600;700&display=swap';
        document.head.appendChild(l);
        loadedFonts[name] = true;
    }

    /* ========== HELPERS ========== */
    function esc(s) {
        if (!s) return '';
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(s));
        return d.innerHTML;
    }
    function findTemplate(slug) {
        for (var k in templates) {
            if (!templates.hasOwnProperty(k)) continue;
            var t = templates[k].templates || [];
            for (var i = 0; i < t.length; i++) {
                if (t[i].slug === slug) return t[i];
            }
        }
        return null;
    }

    /* ========== SEARCH / FILTER ========== */
    $(document).on('click', '.formera-tpl-tab', function() {
        activeCategory = $(this).data('category');
        $('.formera-tpl-tab').removeClass('active');
        $(this).addClass('active');
        filterTemplates();
    });

    var sTimer;
    $('#formera-tpl-search').on('input', function() {
        clearTimeout(sTimer);
        sTimer = setTimeout(filterTemplates, 200);
    });

    function filterTemplates() {
        var s = ($('#formera-tpl-search').val() || '').toLowerCase().trim(), vis = 0;
        $('.formera-tpl-card').each(function() {
            var $c = $(this);
            var cat = $c.data('category'), t = ($c.data('title') || '').toString(), d = ($c.data('description') || '').toString();
            var ok = (activeCategory === 'all' || cat === activeCategory) && (!s || t.indexOf(s) !== -1 || d.indexOf(s) !== -1);
            $c.toggle(ok);
            if (ok) vis++;
        });
        $('.formera-tpl-empty').toggle(vis === 0);
        $('.formera-tpl-grid').toggle(vis > 0);
    }

    /* ========== APPEARANCE PANEL CONFIG ========== */
    var colorPresets = [
        { name: 'Teal',   hex: '#0d9488', light: '#ccfbf1' },
        { name: 'Purple', hex: '#673ab7', light: '#ede7f6' },
        { name: 'Blue',   hex: '#2563eb', light: '#dbeafe' },
        { name: 'Red',    hex: '#dc2626', light: '#fee2e2' },
        { name: 'Green',  hex: '#16a34a', light: '#dcfce7' },
        { name: 'Amber',  hex: '#d97706', light: '#fef3c7' },
        { name: 'Pink',   hex: '#db2777', light: '#fce7f3' },
        { name: 'Indigo', hex: '#4f46e5', light: '#e0e7ff' },
        { name: 'Cyan',   hex: '#0891b2', light: '#cffafe' },
        { name: 'Rose',   hex: '#e11d48', light: '#ffe4e6' }
    ];
    var fonts = [
        { name: 'Plus Jakarta Sans', preview: 'Modern & Clean' },
        { name: 'Inter',             preview: 'Neutral & Readable' },
        { name: 'Roboto',            preview: 'Classic Material' },
        { name: 'Poppins',           preview: 'Geometric & Friendly' },
        { name: 'Open Sans',         preview: 'Humanist Sans' },
        { name: 'System Default',    preview: 'OS Native Font' }
    ];
    var radiusOpts = [
        { label: 'Round',    val: '20', icon: '<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><rect x="1" y="1" width="16" height="16" rx="8" stroke="currentColor" stroke-width="1.5"/></svg>' },
        { label: 'Soft',     val: '12', icon: '<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><rect x="1" y="1" width="16" height="16" rx="5" stroke="currentColor" stroke-width="1.5"/></svg>' },
        { label: 'Slight',   val: '6',  icon: '<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><rect x="1" y="1" width="16" height="16" rx="2.5" stroke="currentColor" stroke-width="1.5"/></svg>' },
        { label: 'Sharp',    val: '0',  icon: '<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><rect x="1" y="1" width="16" height="16" rx="0" stroke="currentColor" stroke-width="1.5"/></svg>' }
    ];
    var bgOpts = [
        { label: 'Gradient', val: 'gradient', desc: 'Color tinted' },
        { label: 'White',    val: 'white',    desc: 'Clean & minimal' },
        { label: 'Light',    val: 'light',    desc: 'Soft gray' }
    ];
    
    // Extended customization options
    var btnSizeOpts = [
        { label: 'Small', val: 'small', desc: '36px height' },
        { label: 'Medium', val: 'medium', desc: '44px height' },
        { label: 'Large', val: 'large', desc: '52px height' }
    ];
    var btnStyleOpts = [
        { label: 'Filled', val: 'filled', desc: 'Solid background' },
        { label: 'Outline', val: 'outline', desc: 'Border only' },
        { label: 'Ghost', val: 'ghost', desc: 'Text only' }
    ];
    var btnWidthOpts = [
        { label: 'Auto', val: 'auto', desc: 'Content width' },
        { label: 'Full', val: 'full', desc: 'Full width' },
        { label: 'Fixed', val: 'fixed', desc: '200px width' }
    ];
    var formWidthOpts = [
        { label: 'Narrow', val: 'narrow', desc: 'max-width: 480px' },
        { label: 'Normal', val: 'normal', desc: 'max-width: 640px' },
        { label: 'Wide', val: 'wide', desc: 'max-width: 800px' },
        { label: 'Full', val: 'full', desc: 'Full container' }
    ];
    var spacingOpts = [
        { label: 'Compact', val: 'compact', desc: 'Tight spacing' },
        { label: 'Comfortable', val: 'comfortable', desc: 'Standard spacing' },
        { label: 'Spacious', val: 'spacious', desc: 'Extra space' }
    ];
    var questionGapOpts = [
        { label: 'Small', val: 'small', desc: '12px gap' },
        { label: 'Medium', val: 'medium', desc: '20px gap' },
        { label: 'Large', val: 'large', desc: '32px gap' }
    ];
    var entranceOpts = [
        { label: 'None', val: 'none', desc: 'No animation' },
        { label: 'Fade', val: 'fade', desc: 'Fade in' },
        { label: 'Slide Up', val: 'slide', desc: 'Slide from bottom' },
        { label: 'Scale', val: 'scale', desc: 'Scale in' }
    ];
    var hoverOpts = [
        { label: 'None', val: 'none', desc: 'No hover effect' },
        { label: 'Lift', val: 'lift', desc: 'Lift on hover' },
        { label: 'Glow', val: 'glow', desc: 'Glow effect' },
        { label: 'Scale', val: 'scale', desc: 'Scale on hover' }
    ];
    var fontSizeOpts = [
        { label: 'Small', val: 'small', desc: '14px base' },
        { label: 'Medium', val: 'medium', desc: '16px base' },
        { label: 'Large', val: 'large', desc: '18px base' }
    ];
    var lineHeightOpts = [
        { label: 'Tight', val: 'tight', desc: '1.25' },
        { label: 'Normal', val: 'normal', desc: '1.5' },
        { label: 'Relaxed', val: 'relaxed', desc: '1.75' }
    ];
    var shadowOpts = [
        { label: 'None', val: 'none', desc: 'No shadow' },
        { label: 'Soft', val: 'soft', desc: 'Subtle shadow' },
        { label: 'Medium', val: 'medium', desc: 'Moderate shadow' },
        { label: 'Strong', val: 'strong', desc: 'Bold shadow' }
    ];
    var borderOpts = [
        { label: 'None', val: 'none', desc: 'No borders' },
        { label: 'Clean', val: 'clean', desc: 'Subtle borders' },
        { label: 'Defined', val: 'defined', desc: 'Clear borders' },
        { label: 'Bold', val: 'bold', desc: 'Thick borders' }
    ];

    /* ========== BUILD MODAL DOM ========== */
    function S(el, css) { el.style.cssText = css; return el; }

    var overlay = S(document.createElement('div'),
        'display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.55);z-index:999999999;align-items:center;justify-content:center;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;');
    overlay.id = 'formera-tpl-modal-overlay';

    var box = S(document.createElement('div'),
        'background:#fff;border-radius:16px;max-width:700px;width:94%;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 25px 60px rgba(0,0,0,0.28);animation:formeraTplFadeIn .25s ease-out;transition:max-width .3s ease;');

    // --- Header ---
    var mHeader = S(document.createElement('div'),
        'display:flex;align-items:center;justify-content:space-between;padding:16px 24px;border-bottom:1px solid #e8eaed;flex-shrink:0;');
    var mTitle = S(document.createElement('h2'),
        'margin:0;font-size:17px;font-weight:600;color:#202124;');
    var titleBadge = S(document.createElement('span'),
        'font-size:11px;font-weight:500;color:#5f6368;background:#f1f3f4;padding:3px 10px;border-radius:20px;margin-left:10px;');
    titleBadge.textContent = 'Preview';
    var mClose = document.createElement('button');
    mClose.type = 'button';
    S(mClose, 'background:none;border:none;cursor:pointer;padding:6px;color:#5f6368;border-radius:50%;display:flex;align-items:center;justify-content:center;');
    mClose.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px;height:20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
    var titleWrap = S(document.createElement('div'), 'display:flex;align-items:center;');
    titleWrap.appendChild(mTitle);
    titleWrap.appendChild(titleBadge);
    mHeader.appendChild(titleWrap);
    mHeader.appendChild(mClose);

    // --- Content (row: preview + appearance panel) ---
    var mContent = S(document.createElement('div'),
        'flex:1;display:flex;overflow:hidden;min-height:0;');

    // Preview area
    var previewArea = S(document.createElement('div'),
        'flex:1;overflow-y:auto;padding:0;min-width:0;');
    var previewWrap = document.createElement('div');
    previewWrap.className = 'gf-survey-wrap';
    S(previewWrap, 'min-height:auto!important;max-width:100%!important;padding:24px 16px!important;margin:0!important;box-sizing:border-box!important;');
    previewArea.appendChild(previewWrap);

    // Editor area (hidden by default)
    var editorArea = S(document.createElement('div'),
        'flex:1;overflow-y:auto;padding:24px;min-width:0;display:none;background:#f9fafb;');

    // Appearance panel
    var appPanel = S(document.createElement('div'),
        'display:none;width:340px;flex-shrink:0;border-left:1px solid #efefef;overflow-y:auto;background:#fff;scrollbar-width:thin;scrollbar-color:#ddd transparent;');
    mContent.appendChild(previewArea);
    mContent.appendChild(editorArea);
    mContent.appendChild(appPanel);

    // --- Footer ---
    var mFooter = S(document.createElement('div'),
        'display:flex;align-items:center;padding:14px 24px;border-top:1px solid #e8eaed;flex-shrink:0;gap:10px;');

    var appBtn = document.createElement('button');
    appBtn.type = 'button';
    S(appBtn, 'padding:9px 16px;border:1.5px solid #dadce0;border-radius:8px;background:#fff;color:#3c4043;font-size:13px;font-weight:500;cursor:pointer;display:inline-flex;align-items:center;gap:6px;font-family:inherit;transition:all .15s ease;');
    appBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42"/></svg>Appearance';

    var spacer = S(document.createElement('div'), 'flex:1;');

    var cancelBtn = document.createElement('button');
    cancelBtn.textContent = 'Cancel';
    S(cancelBtn, 'padding:9px 18px;border:1.5px solid #dadce0;border-radius:8px;background:#fff;color:#3c4043;font-size:13px;font-weight:500;cursor:pointer;font-family:inherit;');

    var useBtn = document.createElement('button');
    useBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;vertical-align:middle;margin-right:6px;"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>Use This Template';
    S(useBtn, 'padding:9px 18px;border:none;border-radius:8px;background:#673ab7;color:#fff;font-size:13px;font-weight:500;cursor:pointer;display:inline-flex;align-items:center;font-family:inherit;');

    // Edit-step buttons (hidden initially)
    var backBtn = document.createElement('button');
    backBtn.type = 'button';
    backBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg> Back to Preview';
    S(backBtn, 'padding:9px 16px;border:1.5px solid #dadce0;border-radius:8px;background:#fff;color:#3c4043;font-size:13px;font-weight:500;cursor:pointer;display:none;align-items:center;gap:6px;font-family:inherit;');

    var saveBtn = document.createElement('button');
    saveBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;vertical-align:middle;margin-right:6px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>Save Form';
    S(saveBtn, 'padding:9px 18px;border:none;border-radius:8px;background:#16a34a;color:#fff;font-size:13px;font-weight:600;cursor:pointer;display:none;align-items:center;font-family:inherit;');

    mFooter.appendChild(appBtn);
    mFooter.appendChild(backBtn);
    mFooter.appendChild(spacer);
    mFooter.appendChild(cancelBtn);
    mFooter.appendChild(useBtn);
    mFooter.appendChild(saveBtn);

    box.appendChild(mHeader);
    box.appendChild(mContent);
    box.appendChild(mFooter);
    overlay.appendChild(box);

    // Inject keyframe + preview tweaks
    var injectedStyle = document.createElement('style');
    injectedStyle.textContent = [
        '@keyframes formeraTplFadeIn{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}',
        '@keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}',
        '#formera-tpl-modal-overlay .gf-question-card{animation:none!important}',
        '#formera-tpl-modal-overlay .gf-question-card:hover{transform:none!important;box-shadow:var(--ds-shadow-sm)!important}',
        '#formera-tpl-modal-overlay .gf-submit-btn:hover{transform:none!important}',
        '#formera-tpl-modal-overlay .gf-header-card{animation:none!important}',
        '#formera-tpl-modal-overlay .gf-survey-wrap *{box-sizing:border-box!important}'
    ].join('');
    document.head.appendChild(injectedStyle);
    document.body.appendChild(overlay);

    // Preload all Google Fonts so font cards render in their typeface
    for (var pi = 0; pi < fonts.length; pi++) { loadGFont(fonts[pi].name); }

    /* ========== RENDER FRONTEND PREVIEW ========== */
    function renderFrontendPreview(tpl) {
        var qs = tpl.questions || [];
        var total = qs.length;
        var h = '';

        // Header card
        h += '<div class="gf-header-card">';
        h += '<div class="gf-header-accent"></div>';
        h += '<div class="gf-header-content">';
        h += '<h1 class="gf-survey-title">' + esc(tpl.title) + '</h1>';
        if (tpl.description) h += '<p class="gf-survey-desc">' + esc(tpl.description) + '</p>';
        h += '</div>';
        h += '<div class="gf-progress-wrap">';
        h += '<div class="gf-progress-bar"><div class="gf-progress-fill" style="width:0%"></div></div>';
        h += '<div class="gf-progress-text"><span>0 of ' + total + ' answered</span><span>0%</span></div>';
        h += '</div></div>';

        // Questions
        for (var i = 0; i < qs.length; i++) {
            var q = qs[i];
            h += '<div class="gf-question-card"><div class="gf-q-content">';
            h += '<div class="gf-q-number">' + (i + 1) + '</div>';
            h += '<label class="gf-q-label">' + esc(q.title);
            if (q.required) h += ' <span class="gf-required">*</span>';
            h += '</label>';

            if (q.type === 'text') {
                h += '<input type="text" class="gf-text-input" placeholder="Type your answer here..." disabled>';
            } else if (q.type === 'textarea') {
                h += '<textarea class="gf-textarea-input" rows="3" placeholder="Share your thoughts..." disabled></textarea>';
            } else if (q.type === 'radio') {
                h += '<div class="gf-options-list">';
                var opts = (q.options || '').split(',');
                for (var j = 0; j < opts.length; j++) {
                    var o = opts[j].trim();
                    if (!o) continue;
                    h += '<label class="gf-option-item gf-radio-option">';
                    h += '<input type="radio" disabled>';
                    h += '<span class="gf-radio-circle"><span class="gf-radio-inner"></span></span>';
                    h += '<span class="gf-option-text">' + esc(o) + '</span></label>';
                }
                h += '</div>';
            } else if (q.type === 'checkbox') {
                h += '<div class="gf-options-list">';
                var cbopts = (q.options || '').split(',');
                for (var k = 0; k < cbopts.length; k++) {
                    var co = cbopts[k].trim();
                    if (!co) continue;
                    h += '<label class="gf-option-item gf-checkbox-option">';
                    h += '<input type="checkbox" disabled>';
                    h += '<span class="gf-checkbox-box"><svg class="gf-check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></span>';
                    h += '<span class="gf-option-text">' + esc(co) + '</span></label>';
                }
                h += '</div>';
            }

            h += '</div></div>';
        }

        // Submit footer
        h += '<div class="gf-form-footer">';
        h += '<button type="button" class="gf-submit-btn" style="pointer-events:none;"><span class="gf-btn-text">';
        h += '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width:18px;height:18px;margin-right:6px;">';
        h += '<path d="M3.105 2.289a.75.75 0 00-.826.95l1.414 4.925A1.5 1.5 0 005.135 9.25h6.115a.75.75 0 010 1.5H5.135a1.5 1.5 0 00-1.442 1.086l-1.414 4.926a.75.75 0 00.826.95 28.896 28.896 0 0015.293-7.154.75.75 0 000-1.115A28.897 28.897 0 003.105 2.289z"/></svg>';
        h += 'Submit</span></button>';
        h += '<button type="button" class="gf-clear-btn" style="pointer-events:none;">Clear form</button>';
        h += '</div>';

        return h;
    }

    /* ========== RENDER TEMPLATE EDITOR ========== */
    function renderEditor() {
        var qs = editData.questions;
        var typeLabels = { text: 'Short Answer', textarea: 'Paragraph', radio: 'Multiple Choice', checkbox: 'Checkboxes' };
        var h = '';

        // Title input
        h += '<div style="margin-bottom:24px;">';
        h += '<label style="display:block;font-size:11px;font-weight:600;color:#888;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">Form Title</label>';
        h += '<input type="text" id="fted-title" class="fted-input" value="' + esc(editData.title) + '" style="width:100%;padding:12px 16px;border:1.5px solid #e0e0e0;border-radius:10px;font-size:16px;font-weight:600;color:#1a1a1a;background:#fff;font-family:inherit;box-sizing:border-box;transition:border-color .15s,box-shadow .15s;">';
        h += '</div>';

        // Questions
        h += '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">';
        h += '<span style="font-size:12px;font-weight:600;color:#888;text-transform:uppercase;letter-spacing:0.5px;">Questions (' + qs.length + ')</span>';
        h += '</div>';

        for (var i = 0; i < qs.length; i++) {
            var q = qs[i];
            var showOpts = (q.type === 'radio' || q.type === 'checkbox');
            h += '<div class="fted-q-card" data-index="' + i + '" style="background:#fff;border:1.5px solid #e8e8e8;border-radius:12px;padding:18px 20px;margin-bottom:12px;transition:border-color .15s;position:relative;">';

            // Question header row
            h += '<div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:14px;">';
            h += '<span style="display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:8px;background:#f3f0ff;color:#673ab7;font-size:12px;font-weight:700;flex-shrink:0;margin-top:2px;">' + (i + 1) + '</span>';
            h += '<div style="flex:1;min-width:0;">';
            h += '<input type="text" class="fted-input fted-q-title" data-index="' + i + '" value="' + esc(q.title) + '" placeholder="Question title" style="width:100%;padding:8px 12px;border:1.5px solid #e0e0e0;border-radius:8px;font-size:14px;font-weight:500;color:#1a1a1a;background:#fff;font-family:inherit;box-sizing:border-box;transition:border-color .15s,box-shadow .15s;">';
            h += '</div>';

            // Action buttons
            h += '<div style="display:flex;gap:2px;flex-shrink:0;">';
            // Move up
            if (i > 0) {
                h += '<button type="button" class="fted-btn-icon fted-move-up" data-index="' + i + '" title="Move up" style="width:30px;height:30px;border:none;border-radius:6px;background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#999;transition:all .15s;">';
                h += '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 3v10M4 7l4-4 4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></button>';
            }
            // Move down
            if (i < qs.length - 1) {
                h += '<button type="button" class="fted-btn-icon fted-move-down" data-index="' + i + '" title="Move down" style="width:30px;height:30px;border:none;border-radius:6px;background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#999;transition:all .15s;">';
                h += '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 13V3M4 9l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></button>';
            }
            // Delete
            if (qs.length > 1) {
                h += '<button type="button" class="fted-btn-icon fted-delete-q" data-index="' + i + '" title="Delete" style="width:30px;height:30px;border:none;border-radius:6px;background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#e55;transition:all .15s;">';
                h += '<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M4 4l8 8M12 4l-8 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></button>';
            }
            h += '</div></div>';

            // Type & Required row
            h += '<div style="display:flex;align-items:center;gap:10px;margin-bottom:' + (showOpts ? '14' : '0') + 'px;">';
            h += '<select class="fted-select fted-q-type" data-index="' + i + '" style="padding:7px 30px 7px 10px;border:1.5px solid #e0e0e0;border-radius:8px;font-size:12px;color:#555;background:#fff;font-family:inherit;cursor:pointer;transition:border-color .15s,box-shadow .15s;appearance:auto;">';
            var types = ['text', 'textarea', 'radio', 'checkbox'];
            for (var t = 0; t < types.length; t++) {
                h += '<option value="' + types[t] + '"' + (q.type === types[t] ? ' selected' : '') + '>' + typeLabels[types[t]] + '</option>';
            }
            h += '</select>';
            h += '<label style="display:flex;align-items:center;gap:6px;font-size:12px;color:#666;cursor:pointer;margin-left:auto;">';
            h += '<input type="checkbox" class="fted-q-required" data-index="' + i + '"' + (q.required ? ' checked' : '') + ' style="accent-color:#673ab7;width:15px;height:15px;cursor:pointer;"> Required';
            h += '</label>';
            h += '</div>';

            // Options (for radio/checkbox)
            if (showOpts) {
                var optList = (q.options || '').split(',').map(function(o) { return o.trim(); }).filter(Boolean);
                h += '<div class="fted-options-wrap" style="padding-left:36px;">';
                for (var oi = 0; oi < optList.length; oi++) {
                    h += '<div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">';
                    if (q.type === 'radio') {
                        h += '<span style="width:14px;height:14px;border:2px solid #ccc;border-radius:50%;flex-shrink:0;"></span>';
                    } else {
                        h += '<span style="width:14px;height:14px;border:2px solid #ccc;border-radius:3px;flex-shrink:0;"></span>';
                    }
                    h += '<input type="text" class="fted-input fted-opt-val" data-q="' + i + '" data-opt="' + oi + '" value="' + esc(optList[oi]) + '" style="flex:1;padding:6px 10px;border:1px solid #e8e8e8;border-radius:6px;font-size:13px;color:#333;background:#fafafa;font-family:inherit;box-sizing:border-box;transition:border-color .15s,box-shadow .15s;">';
                    if (optList.length > 1) {
                        h += '<button type="button" class="fted-btn-icon fted-remove-opt" data-q="' + i + '" data-opt="' + oi + '" title="Remove" style="width:24px;height:24px;border:none;border-radius:4px;background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#ccc;transition:all .15s;flex-shrink:0;">';
                        h += '<svg width="12" height="12" viewBox="0 0 12 12"><path d="M3 3l6 6M9 3l-6 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg></button>';
                    }
                    h += '</div>';
                }
                h += '<button type="button" class="fted-add-opt" data-q="' + i + '" style="display:inline-flex;align-items:center;gap:4px;padding:4px 0;border:none;background:transparent;color:#673ab7;font-size:12px;font-weight:500;cursor:pointer;font-family:inherit;margin-top:4px;">';
                h += '<svg width="14" height="14" viewBox="0 0 14 14"><path d="M7 2v10M2 7h10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg> Add option</button>';
                h += '</div>';
            }

            h += '</div>';
        }

        // Add question button
        h += '<button type="button" id="fted-add-question" class="fted-add-btn" style="width:100%;padding:14px;border:2px dashed #ddd;border-radius:12px;background:transparent;color:#888;font-size:13px;font-weight:500;cursor:pointer;font-family:inherit;display:flex;align-items:center;justify-content:center;gap:8px;transition:all .15s;margin-top:8px;">';
        h += '<svg width="18" height="18" viewBox="0 0 18 18"><path d="M9 3v12M3 9h12" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg> Add Question</button>';

        return h;
    }

    /* ========== SWITCH MODAL STEPS ========== */
    function switchToEdit() {
        var tpl = findTemplate(currentSlug);
        if (!tpl) return;
        modalStep = 'edit';
        editData.title = tpl.title;
        editData.questions = JSON.parse(JSON.stringify(tpl.questions)); // deep clone

        editorArea.innerHTML = renderEditor();
        previewArea.style.display = 'none';
        editorArea.style.display = 'block';

        titleBadge.textContent = 'Edit';
        titleBadge.style.background = '#f3e8ff';
        titleBadge.style.color = '#673ab7';

        // Show edit-step buttons, hide preview-step buttons
        appBtn.style.display = 'none';
        useBtn.style.display = 'none';
        backBtn.style.display = 'inline-flex';
        saveBtn.style.display = 'inline-flex';

        // Close appearance panel if open
        if (panelOpen) togglePanel();
    }

    function switchToPreview() {
        modalStep = 'preview';
        previewArea.style.display = 'block';
        editorArea.style.display = 'none';

        titleBadge.textContent = 'Preview';
        titleBadge.style.background = '#f1f3f4';
        titleBadge.style.color = '#5f6368';

        // Show preview-step buttons, hide edit-step buttons
        appBtn.style.display = 'inline-flex';
        useBtn.style.display = 'inline-flex';
        backBtn.style.display = 'none';
        saveBtn.style.display = 'none';
    }

    /* ========== BUILD APPEARANCE PANEL (Modern) ========== */
    var checkSvg = '<svg viewBox="0 0 16 16" fill="none" style="width:12px;height:12px;"><path d="M13.25 4.75L6 12 2.75 8.75" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

    function buildPanel() {
        var ac = appearance.color.toLowerCase();
        var h = '';
        h += '<div style="padding:0;">';

        // Panel header
        h += '<div style="padding:20px 22px 16px;border-bottom:1px solid #f0f0f0;">';
        h += '<div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">';
        h += '<div style="width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,' + ac + '22,' + ac + '44);display:flex;align-items:center;justify-content:center;">';
        h += '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="' + ac + '" style="width:15px;height:15px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42"/></svg>';
        h += '</div>';
        h += '<div>';
        h += '<div style="font-size:14px;font-weight:600;color:#1a1a1a;line-height:1.2;">Appearance</div>';
        h += '<div style="font-size:11px;color:#8c8c8c;margin-top:1px;">Style your form</div>';
        h += '</div></div></div>';

        // ── Color section ──
        h += '<div style="padding:18px 22px;border-bottom:1px solid #f0f0f0;">';
        h += '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">';
        h += '<span style="font-size:12px;font-weight:600;color:#444;letter-spacing:0.3px;">Color</span>';
        h += '<div style="display:flex;align-items:center;gap:6px;">';
        h += '<div style="width:14px;height:14px;border-radius:4px;background:' + ac + ';"></div>';
        h += '<span style="font-size:11px;color:#999;font-family:SFMono-Regular,Menlo,monospace;">' + appearance.color.toUpperCase() + '</span>';
        h += '</div></div>';

        // Color grid – circular swatches with check
        h += '<div style="display:grid;grid-template-columns:repeat(5,1fr);gap:10px;margin-bottom:14px;">';
        for (var i = 0; i < colorPresets.length; i++) {
            var cp = colorPresets[i];
            var isActive = (cp.hex.toLowerCase() === ac);
            h += '<button type="button" class="formera-app-color" data-color="' + cp.hex + '" title="' + cp.name + '" style="';
            h += 'width:40px;height:40px;border-radius:50%;border:none;cursor:pointer;position:relative;';
            h += 'background:' + cp.hex + ';outline:none;transition:all .2s cubic-bezier(.4,0,.2,1);';
            h += 'box-shadow:' + (isActive ? '0 0 0 2.5px #fff,0 0 0 4.5px ' + cp.hex + ',0 2px 8px ' + cp.hex + '44' : '0 2px 6px rgba(0,0,0,0.12),inset 0 -1px 2px rgba(0,0,0,0.1)') + ';';
            h += 'display:flex;align-items:center;justify-content:center;">';
            if (isActive) h += checkSvg;
            h += '</button>';
        }
        h += '</div>';

        // Custom picker row
        h += '<div style="display:flex;align-items:center;gap:10px;padding:8px 12px;border-radius:10px;background:#f7f7f8;border:1px solid #efefef;">';
        h += '<input type="color" id="formera-app-custom-color" value="' + appearance.color + '" style="width:28px;height:28px;border:none;border-radius:6px;cursor:pointer;padding:0;background:none;-webkit-appearance:none;">';
        h += '<span style="font-size:12px;color:#666;flex:1;">Custom</span>';
        h += '<span style="font-size:11px;color:#aaa;font-family:SFMono-Regular,Menlo,monospace;background:#fff;padding:2px 8px;border-radius:5px;border:1px solid #eee;">' + appearance.color + '</span>';
        h += '</div></div>';

        // ── Font section ──
        h += '<div style="padding:18px 22px;border-bottom:1px solid #f0f0f0;">';
        h += '<span style="display:block;font-size:12px;font-weight:600;color:#444;letter-spacing:0.3px;margin-bottom:12px;">Typography</span>';
        h += '<div style="display:flex;flex-direction:column;gap:6px;">';
        for (var fi = 0; fi < fonts.length; fi++) {
            var fn = fonts[fi];
            var fActive = (fn.name === appearance.font);
            var fontFamily = fn.name === 'System Default' ? '-apple-system,BlinkMacSystemFont,sans-serif' : "'" + fn.name + "',sans-serif";
            h += '<button type="button" class="formera-app-font-btn" data-font="' + fn.name + '" style="';
            h += 'display:flex;align-items:center;gap:10px;width:100%;padding:10px 12px;border-radius:10px;cursor:pointer;transition:all .15s;outline:none;text-align:left;';
            h += 'border:1.5px solid ' + (fActive ? ac : '#efefef') + ';';
            h += 'background:' + (fActive ? ac + '08' : '#fff') + ';">';
            // Color dot indicator
            h += '<div style="width:8px;height:8px;border-radius:50%;flex-shrink:0;';
            h += 'background:' + (fActive ? ac : '#d9d9d9') + ';transition:background .15s;"></div>';
            h += '<div style="flex:1;min-width:0;">';
            h += '<div style="font-size:13px;font-weight:' + (fActive ? '600' : '500') + ';color:' + (fActive ? '#1a1a1a' : '#555') + ';font-family:' + fontFamily + ';white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + fn.name + '</div>';
            h += '<div style="font-size:10px;color:#aaa;margin-top:1px;">' + fn.preview + '</div>';
            h += '</div>';
            if (fActive) {
                h += '<svg viewBox="0 0 16 16" fill="none" style="width:14px;height:14px;flex-shrink:0;"><path d="M13.25 4.75L6 12 2.75 8.75" stroke="' + ac + '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            }
            h += '</button>';
        }
        h += '</div></div>';

        // ── Corners section (segmented control) ──
        h += '<div style="padding:18px 22px;border-bottom:1px solid #f0f0f0;">';
        h += '<span style="display:block;font-size:12px;font-weight:600;color:#444;letter-spacing:0.3px;margin-bottom:12px;">Corners</span>';
        h += '<div style="display:flex;background:#f5f5f5;border-radius:10px;padding:3px;gap:2px;">';
        for (var ri = 0; ri < radiusOpts.length; ri++) {
            var ro = radiusOpts[ri];
            var rActive = (appearance.radius === ro.val);
            h += '<button type="button" class="formera-app-radius" data-radius="' + ro.val + '" style="';
            h += 'flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;padding:10px 6px;border:none;cursor:pointer;transition:all .2s;outline:none;font-family:inherit;';
            h += 'border-radius:8px;background:' + (rActive ? '#fff' : 'transparent') + ';';
            h += 'color:' + (rActive ? ac : '#999') + ';';
            h += (rActive ? 'box-shadow:0 1px 4px rgba(0,0,0,0.1);' : '') + '">';
            h += '<span style="display:flex;">' + ro.icon + '</span>';
            h += '<span style="font-size:10px;font-weight:' + (rActive ? '600' : '500') + ';">' + ro.label + '</span>';
            h += '</button>';
        }
        h += '</div></div>';

        // ── Background section (card selector) ──
        h += '<div style="padding:18px 22px;border-bottom:1px solid #f0f0f0;">';
        h += '<span style="display:block;font-size:12px;font-weight:600;color:#444;letter-spacing:0.3px;margin-bottom:12px;">Background</span>';
        h += '<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">';
        for (var bi = 0; bi < bgOpts.length; bi++) {
            var bo = bgOpts[bi];
            var bActive = (appearance.bg === bo.val);
            // Preview swatch background
            var swatchBg = 'linear-gradient(135deg,' + mixWhite(ac, 92) + ',' + mixWhite(ac, 96) + ')';
            if (bo.val === 'white') swatchBg = '#fff';
            else if (bo.val === 'light') swatchBg = '#f2f2f2';
            h += '<button type="button" class="formera-app-bg" data-bg="' + bo.val + '" style="';
            h += 'display:flex;flex-direction:column;align-items:center;gap:6px;padding:12px 6px 10px;cursor:pointer;transition:all .2s;outline:none;font-family:inherit;';
            h += 'border-radius:10px;border:1.5px solid ' + (bActive ? ac : '#efefef') + ';';
            h += 'background:' + (bActive ? ac + '06' : '#fff') + ';">';
            h += '<div style="width:100%;height:28px;border-radius:6px;background:' + swatchBg + ';border:1px solid rgba(0,0,0,0.06);"></div>';
            h += '<span style="font-size:11px;font-weight:' + (bActive ? '600' : '500') + ';color:' + (bActive ? '#1a1a1a' : '#777') + ';">' + bo.label + '</span>';
            h += '</button>';
        }
        h += '</div></div>';

        // ── Buttons section ──
        h += '<div style="padding:18px 22px;border-bottom:1px solid #f0f0f0;">';
        h += '<span style="display:block;font-size:12px;font-weight:600;color:#444;letter-spacing:0.3px;margin-bottom:12px;">Buttons</span>';
        
        // Button size
        h += '<div style="margin-bottom:14px;">';
        h += '<label style="display:block;font-size:11px;font-weight:500;color:#666;margin-bottom:8px;">Size</label>';
        h += '<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px;">';
        for (var bsi = 0; bsi < btnSizeOpts.length; bsi++) {
            var bso = btnSizeOpts[bsi];
            var bsActive = (appearance.btnSize === bso.val);
            h += '<button type="button" class="formera-app-btn-size" data-size="' + bso.val + '" style="';
            h += 'padding:8px 10px;border:1.5px solid ' + (bsActive ? ac : '#efefef') + ';border-radius:8px;';
            h += 'background:' + (bsActive ? ac + '08' : '#fff') + ';color:' + (bsActive ? '#1a1a1a' : '#666') + ';';
            h += 'font-size:11px;font-weight:' + (bsActive ? '600' : '500') + ';cursor:pointer;transition:all .15s;outline:none;font-family:inherit;">';
            h += bso.label + '</button>';
        }
        h += '</div></div>';
        
        // Button style
        h += '<div style="margin-bottom:14px;">';
        h += '<label style="display:block;font-size:11px;font-weight:500;color:#666;margin-bottom:8px;">Style</label>';
        h += '<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px;">';
        for (var bsti = 0; bsti < btnStyleOpts.length; bsti++) {
            var bsto = btnStyleOpts[bsti];
            var bstActive = (appearance.btnStyle === bsto.val);
            h += '<button type="button" class="formera-app-btn-style" data-style="' + bsto.val + '" style="';
            h += 'padding:8px 10px;border:1.5px solid ' + (bstActive ? ac : '#efefef') + ';border-radius:8px;';
            h += 'background:' + (bstActive ? ac + '08' : '#fff') + ';color:' + (bstActive ? '#1a1a1a' : '#666') + ';';
            h += 'font-size:11px;font-weight:' + (bstActive ? '600' : '500') + ';cursor:pointer;transition:all .15s;outline:none;font-family:inherit;">';
            h += bsto.label + '</button>';
        }
        h += '</div></div>';
        
        // Button width
        h += '<div>';
        h += '<label style="display:block;font-size:11px;font-weight:500;color:#666;margin-bottom:8px;">Width</label>';
        h += '<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px;">';
        for (var bwi = 0; bwi < btnWidthOpts.length; bwi++) {
            var bwo = btnWidthOpts[bwi];
            var bwActive = (appearance.btnWidth === bwo.val);
            h += '<button type="button" class="formera-app-btn-width" data-width="' + bwo.val + '" style="';
            h += 'padding:8px 6px;border:1.5px solid ' + (bwActive ? ac : '#efefef') + ';border-radius:8px;';
            h += 'background:' + (bwActive ? ac + '08' : '#fff') + ';color:' + (bwActive ? '#1a1a1a' : '#666') + ';';
            h += 'font-size:11px;font-weight:' + (bwActive ? '600' : '500') + ';cursor:pointer;transition:all .15s;outline:none;font-family:inherit;">';
            h += bwo.label + '</button>';
        }
        h += '</div></div></div>';

        // ── Layout section ──
        h += '<div style="padding:18px 22px;border-bottom:1px solid #f0f0f0;">';
        h += '<span style="display:block;font-size:12px;font-weight:600;color:#444;letter-spacing:0.3px;margin-bottom:12px;">Layout & Spacing</span>';
        
        // Form width
        h += '<div style="margin-bottom:14px;">';
        h += '<label style="display:block;font-size:11px;font-weight:500;color:#666;margin-bottom:8px;">Form Width</label>';
        h += '<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:6px;">';
        for (var fwi = 0; fwi < formWidthOpts.length; fwi++) {
            var fwo = formWidthOpts[fwi];
            var fwActive = (appearance.formWidth === fwo.val);
            h += '<button type="button" class="formera-app-form-width" data-width="' + fwo.val + '" style="';
            h += 'padding:8px 10px;border:1.5px solid ' + (fwActive ? ac : '#efefef') + ';border-radius:8px;';
            h += 'background:' + (fwActive ? ac + '08' : '#fff') + ';color:' + (fwActive ? '#1a1a1a' : '#666') + ';';
            h += 'font-size:11px;font-weight:' + (fwActive ? '600' : '500') + ';cursor:pointer;transition:all .15s;outline:none;font-family:inherit;">';
            h += fwo.label + '</button>';
        }
        h += '</div></div>';
        
        // Spacing
        h += '<div style="margin-bottom:14px;">';
        h += '<label style="display:block;font-size:11px;font-weight:500;color:#666;margin-bottom:8px;">Overall Spacing</label>';
        h += '<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px;">';
        for (var spi = 0; spi < spacingOpts.length; spi++) {
            var spo = spacingOpts[spi];
            var spActive = (appearance.spacing === spo.val);
            h += '<button type="button" class="formera-app-spacing" data-spacing="' + spo.val + '" style="';
            h += 'padding:8px 8px;border:1.5px solid ' + (spActive ? ac : '#efefef') + ';border-radius:8px;';
            h += 'background:' + (spActive ? ac + '08' : '#fff') + ';color:' + (spActive ? '#1a1a1a' : '#666') + ';';
            h += 'font-size:11px;font-weight:' + (spActive ? '600' : '500') + ';cursor:pointer;transition:all .15s;outline:none;font-family:inherit;">';
            h += spo.label + '</button>';
        }
        h += '</div></div>';
        
        // Question gap
        h += '<div>';
        h += '<label style="display:block;font-size:11px;font-weight:500;color:#666;margin-bottom:8px;">Question Gap</label>';
        h += '<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px;">';
        for (var qgi = 0; qgi < questionGapOpts.length; qgi++) {
            var qgo = questionGapOpts[qgi];
            var qgActive = (appearance.questionGap === qgo.val);
            h += '<button type="button" class="formera-app-question-gap" data-gap="' + qgo.val + '" style="';
            h += 'padding:8px 8px;border:1.5px solid ' + (qgActive ? ac : '#efefef') + ';border-radius:8px;';
            h += 'background:' + (qgActive ? ac + '08' : '#fff') + ';color:' + (qgActive ? '#1a1a1a' : '#666') + ';';
            h += 'font-size:11px;font-weight:' + (qgActive ? '600' : '500') + ';cursor:pointer;transition:all .15s;outline:none;font-family:inherit;">';
            h += qgo.label + '</button>';
        }
        h += '</div></div></div>';

        // ── Effects section ──
        h += '<div style="padding:18px 22px;border-bottom:1px solid #f0f0f0;">';
        h += '<span style="display:block;font-size:12px;font-weight:600;color:#444;letter-spacing:0.3px;margin-bottom:12px;">Effects & Animations</span>';
        
        // Entrance animation
        h += '<div style="margin-bottom:14px;">';
        h += '<label style="display:block;font-size:11px;font-weight:500;color:#666;margin-bottom:8px;">Entrance</label>';
        h += '<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:6px;">';
        for (var enti = 0; enti < entranceOpts.length; enti++) {
            var ento = entranceOpts[enti];
            var entActive = (appearance.entrance === ento.val);
            h += '<button type="button" class="formera-app-entrance" data-entrance="' + ento.val + '" style="';
            h += 'padding:8px 10px;border:1.5px solid ' + (entActive ? ac : '#efefef') + ';border-radius:8px;';
            h += 'background:' + (entActive ? ac + '08' : '#fff') + ';color:' + (entActive ? '#1a1a1a' : '#666') + ';';
            h += 'font-size:11px;font-weight:' + (entActive ? '600' : '500') + ';cursor:pointer;transition:all .15s;outline:none;font-family:inherit;">';
            h += ento.label + '</button>';
        }
        h += '</div></div>';
        
        // Hover effects
        h += '<div>';
        h += '<label style="display:block;font-size:11px;font-weight:500;color:#666;margin-bottom:8px;">Hover Effects</label>';
        h += '<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:6px;">';
        for (var hovi = 0; hovi < hoverOpts.length; hovi++) {
            var hovo = hoverOpts[hovi];
            var hovActive = (appearance.hover === hovo.val);
            h += '<button type="button" class="formera-app-hover" data-hover="' + hovo.val + '" style="';
            h += 'padding:8px 10px;border:1.5px solid ' + (hovActive ? ac : '#efefef') + ';border-radius:8px;';
            h += 'background:' + (hovActive ? ac + '08' : '#fff') + ';color:' + (hovActive ? '#1a1a1a' : '#666') + ';';
            h += 'font-size:11px;font-weight:' + (hovActive ? '600' : '500') + ';cursor:pointer;transition:all .15s;outline:none;font-family:inherit;">';
            h += hovo.label + '</button>';
        }
        h += '</div></div></div>';

        // ── Advanced section (collapsible) ──
        h += '<div style="padding:18px 22px;border-bottom:1px solid #f0f0f0;">';
        h += '<div class="formera-advanced-toggle" style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;cursor:pointer;">';
        h += '<span style="font-size:12px;font-weight:600;color:#444;letter-spacing:0.3px;">Advanced Styling</span>';
        h += '<svg width="14" height="14" viewBox="0 0 14 14" style="transition:transform .2s;color:#999;"><path d="M7 3l4 4-4 4" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        h += '</div>';
        h += '<div class="formera-advanced-content" style="display:none;">';
        
        // Typography details
        h += '<div style="margin-bottom:16px;">';
        h += '<label style="display:block;font-size:11px;font-weight:500;color:#666;margin-bottom:8px;">Font Size</label>';
        h += '<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px;">';
        for (var fsi = 0; fsi < fontSizeOpts.length; fsi++) {
            var fso = fontSizeOpts[fsi];
            var fsActive = (appearance.fontSize === fso.val);
            h += '<button type="button" class="formera-app-font-size" data-size="' + fso.val + '" style="';
            h += 'padding:8px 8px;border:1.5px solid ' + (fsActive ? ac : '#efefef') + ';border-radius:8px;';
            h += 'background:' + (fsActive ? ac + '08' : '#fff') + ';color:' + (fsActive ? '#1a1a1a' : '#666') + ';';
            h += 'font-size:11px;font-weight:' + (fsActive ? '600' : '500') + ';cursor:pointer;transition:all .15s;outline:none;font-family:inherit;">';
            h += fso.label + '</button>';
        }
        h += '</div></div>';
        
        // Line height
        h += '<div style="margin-bottom:16px;">';
        h += '<label style="display:block;font-size:11px;font-weight:500;color:#666;margin-bottom:8px;">Line Height</label>';
        h += '<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:6px;">';
        for (var lhi = 0; lhi < lineHeightOpts.length; lhi++) {
            var lho = lineHeightOpts[lhi];
            var lhActive = (appearance.lineHeight === lho.val);
            h += '<button type="button" class="formera-app-line-height" data-height="' + lho.val + '" style="';
            h += 'padding:8px 8px;border:1.5px solid ' + (lhActive ? ac : '#efefef') + ';border-radius:8px;';
            h += 'background:' + (lhActive ? ac + '08' : '#fff') + ';color:' + (lhActive ? '#1a1a1a' : '#666') + ';';
            h += 'font-size:11px;font-weight:' + (lhActive ? '600' : '500') + ';cursor:pointer;transition:all .15s;outline:none;font-family:inherit;">';
            h += lho.label + '</button>';
        }
        h += '</div></div>';
        
        // Shadow
        h += '<div style="margin-bottom:16px;">';
        h += '<label style="display:block;font-size:11px;font-weight:500;color:#666;margin-bottom:8px;">Form Shadow</label>';
        h += '<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:6px;">';
        for (var shi = 0; shi < shadowOpts.length; shi++) {
            var sho = shadowOpts[shi];
            var shActive = (appearance.shadow === sho.val);
            h += '<button type="button" class="formera-app-shadow" data-shadow="' + sho.val + '" style="';
            h += 'padding:8px 10px;border:1.5px solid ' + (shActive ? ac : '#efefef') + ';border-radius:8px;';
            h += 'background:' + (shActive ? ac + '08' : '#fff') + ';color:' + (shActive ? '#1a1a1a' : '#666') + ';';
            h += 'font-size:11px;font-weight:' + (shActive ? '600' : '500') + ';cursor:pointer;transition:all .15s;outline:none;font-family:inherit;">';
            h += sho.label + '</button>';
        }
        h += '</div></div>';
        
        // Borders
        h += '<div>';
        h += '<label style="display:block;font-size:11px;font-weight:500;color:#666;margin-bottom:8px;">Border Style</label>';
        h += '<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:6px;">';
        for (var boi = 0; boi < borderOpts.length; boi++) {
            var boo = borderOpts[boi];
            var boActive = (appearance.borders === boo.val);
            h += '<button type="button" class="formera-app-borders" data-borders="' + boo.val + '" style="';
            h += 'padding:8px 10px;border:1.5px solid ' + (boActive ? ac : '#efefef') + ';border-radius:8px;';
            h += 'background:' + (boActive ? ac + '08' : '#fff') + ';color:' + (boActive ? '#1a1a1a' : '#666') + ';';
            h += 'font-size:11px;font-weight:' + (boActive ? '600' : '500') + ';cursor:pointer;transition:all .15s;outline:none;font-family:inherit;">';
            h += boo.label + '</button>';
        }
        h += '</div></div>';
        
        h += '</div></div>'; // Close advanced section

        // ── Reset ──
        h += '<div style="padding:16px 22px;">';
        h += '<button type="button" id="formera-app-reset" style="width:100%;padding:10px;border:none;border-radius:10px;background:#f7f7f8;color:#888;font-size:12px;font-weight:500;cursor:pointer;font-family:inherit;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:6px;">';
        h += '<svg viewBox="0 0 16 16" fill="none" style="width:13px;height:13px;"><path d="M2 8a6 6 0 0110.968-3.375M14 8A6 6 0 013.032 11.375" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M13 1.5v3.5h-3.5M3 14.5V11h3.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        h += 'Reset to Default</button>';
        h += '</div></div>';
        return h;
    }

    appPanel.innerHTML = buildPanel();

    /* ========== APPLY APPEARANCE ========== */
    function applyAppearance() {
        var c = appearance.color;
        previewWrap.style.setProperty('--ds-primary', c);
        previewWrap.style.setProperty('--ds-primary-hover', mixBlack(c, 10));
        previewWrap.style.setProperty('--ds-primary-dark', mixBlack(c, 30));
        previewWrap.style.setProperty('--ds-primary-light', mixWhite(c, 85));
        previewWrap.style.setProperty('--ds-primary-rgb', hexToRgb(c));
        previewWrap.style.setProperty('--ds-gradient', 'linear-gradient(135deg, ' + c + ' 0%, ' + mixWhite(c, 15) + ' 100%)');
        previewWrap.style.setProperty('--ds-shadow-glow', '0 0 30px rgba(' + hexToRgb(c) + ', 0.2)');

        var f = appearance.font;
        if (f === 'System Default') {
            previewWrap.style.setProperty('--ds-font', '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif');
        } else {
            loadGFont(f);
            previewWrap.style.setProperty('--ds-font', "'" + f + "',-apple-system,BlinkMacSystemFont,sans-serif");
        }

        var r = parseInt(appearance.radius);
        previewWrap.style.setProperty('--ds-radius', r + 'px');
        previewWrap.style.setProperty('--ds-radius-md', Math.round(r * 0.7) + 'px');
        previewWrap.style.setProperty('--ds-radius-sm', Math.round(r * 0.5) + 'px');

        if (appearance.bg === 'white') {
            previewWrap.style.setProperty('--ds-gradient-bg', '#ffffff');
        } else if (appearance.bg === 'light') {
            previewWrap.style.setProperty('--ds-gradient-bg', '#f5f5f5');
        } else {
            previewWrap.style.setProperty('--ds-gradient-bg', 'linear-gradient(135deg, ' + mixWhite(c, 95) + ' 0%, ' + mixWhite(c, 92) + ' 50%, #f0f9ff 100%)');
        }

        // Form width
        var maxWidth = '640px';
        if (appearance.formWidth === 'narrow') maxWidth = '480px';
        else if (appearance.formWidth === 'wide') maxWidth = '800px';
        else if (appearance.formWidth === 'full') maxWidth = '100%';
        previewWrap.style.maxWidth = maxWidth;

        // Button styling
        var btnHeight = '44px', btnPadding = '12px 24px';
        if (appearance.btnSize === 'small') { btnHeight = '36px'; btnPadding = '8px 20px'; }
        else if (appearance.btnSize === 'large') { btnHeight = '52px'; btnPadding = '16px 28px'; }
        previewWrap.style.setProperty('--ds-btn-height', btnHeight);
        previewWrap.style.setProperty('--ds-btn-padding', btnPadding);

        var btnBg = c, btnColor = '#fff', btnBorder = 'none';
        if (appearance.btnStyle === 'outline') {
            btnBg = 'transparent'; btnColor = c; btnBorder = '2px solid ' + c;
        } else if (appearance.btnStyle === 'ghost') {
            btnBg = 'transparent'; btnColor = c; btnBorder = 'none';
        }
        previewWrap.style.setProperty('--ds-btn-bg', btnBg);
        previewWrap.style.setProperty('--ds-btn-color', btnColor);
        previewWrap.style.setProperty('--ds-btn-border', btnBorder);

        var btnWidthCSS = 'auto';
        if (appearance.btnWidth === 'full') btnWidthCSS = '100%';
        else if (appearance.btnWidth === 'fixed') btnWidthCSS = '200px';
        previewWrap.style.setProperty('--ds-btn-width', btnWidthCSS);

        // Spacing
        var formPadding = '24px', questionMargin = '20px';
        if (appearance.spacing === 'compact') { formPadding = '16px'; }
        else if (appearance.spacing === 'spacious') { formPadding = '32px'; }
        previewWrap.style.setProperty('--ds-form-padding', formPadding);

        if (appearance.questionGap === 'small') questionMargin = '12px';
        else if (appearance.questionGap === 'large') questionMargin = '32px';
        previewWrap.style.setProperty('--ds-question-margin', questionMargin);

        // Typography
        var baseFontSize = '16px';
        if (appearance.fontSize === 'small') baseFontSize = '14px';
        else if (appearance.fontSize === 'large') baseFontSize = '18px';
        previewWrap.style.setProperty('--ds-font-size', baseFontSize);

        var baseLineHeight = '1.5';
        if (appearance.lineHeight === 'tight') baseLineHeight = '1.25';
        else if (appearance.lineHeight === 'relaxed') baseLineHeight = '1.75';
        previewWrap.style.setProperty('--ds-line-height', baseLineHeight);

        // Shadow
        var formShadow = '0 4px 20px rgba(0,0,0,0.08)';
        if (appearance.shadow === 'none') formShadow = 'none';
        else if (appearance.shadow === 'medium') formShadow = '0 8px 30px rgba(0,0,0,0.12)';
        else if (appearance.shadow === 'strong') formShadow = '0 12px 40px rgba(0,0,0,0.16)';
        previewWrap.style.setProperty('--ds-form-shadow', formShadow);

        // Borders
        var inputBorder = '1.5px solid rgba(0,0,0,0.1)';
        if (appearance.borders === 'none') inputBorder = 'none';
        else if (appearance.borders === 'defined') inputBorder = '2px solid rgba(0,0,0,0.15)';
        else if (appearance.borders === 'bold') inputBorder = '3px solid rgba(0,0,0,0.2)';
        previewWrap.style.setProperty('--ds-input-border', inputBorder);

        // Effects (add classes to preview wrapper for CSS animations)
        previewWrap.className = 'gf-survey-wrap gf-entrance-' + appearance.entrance + ' gf-hover-' + appearance.hover;
    }

    /* ========== TOGGLE APPEARANCE PANEL ========== */
    function togglePanel() {
        panelOpen = !panelOpen;
        var ac = appearance.color;
        if (panelOpen) {
            appPanel.style.display = 'block';
            box.style.maxWidth = '1040px';
            appBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>Hide';
            appBtn.style.borderColor = ac;
            appBtn.style.color = ac;
            appBtn.style.background = mixWhite(ac, 92);
        } else {
            appPanel.style.display = 'none';
            box.style.maxWidth = '700px';
            appBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42"/></svg>Appearance';
            appBtn.style.borderColor = '#dadce0';
            appBtn.style.color = '#3c4043';
            appBtn.style.background = '#fff';
        }
    }

    /* ========== MODAL OPEN / CLOSE ========== */
    function openModal(slug) {
        var tpl = findTemplate(slug);
        if (!tpl) return;
        currentSlug = slug;
        appearance = $.extend({}, defaultAppearance);
        mTitle.textContent = tpl.title;
        previewWrap.innerHTML = renderFrontendPreview(tpl);
        appPanel.innerHTML = buildPanel();
        applyAppearance();
        if (panelOpen) togglePanel();
        // Always start on preview step
        switchToPreview();
        overlay.style.display = 'flex';
    }

    function closeModal() {
        overlay.style.display = 'none';
        currentSlug = '';
        modalStep = 'preview';
        if (panelOpen) togglePanel();
        // Reset views
        previewArea.style.display = 'block';
        editorArea.style.display = 'none';
    }

    /* ========== APPEARANCE EVENT HANDLERS ========== */
    $(document).on('click', '.formera-app-color', function() {
        appearance.color = $(this).data('color');
        appPanel.innerHTML = buildPanel();
        applyAppearance();
    });
    $(document).on('input', '#formera-app-custom-color', function() {
        appearance.color = $(this).val();
        applyAppearance();
    });
    $(document).on('change', '#formera-app-custom-color', function() {
        appearance.color = $(this).val();
        appPanel.innerHTML = buildPanel();
        applyAppearance();
    });
    $(document).on('click', '.formera-app-font-btn', function() {
        appearance.font = $(this).data('font');
        appPanel.innerHTML = buildPanel();
        applyAppearance();
    });
    $(document).on('click', '.formera-app-radius', function() {
        appearance.radius = $(this).data('radius') + '';
        appPanel.innerHTML = buildPanel();
        applyAppearance();
    });
    $(document).on('click', '.formera-app-bg', function() {
        appearance.bg = $(this).data('bg');
        appPanel.innerHTML = buildPanel();
        applyAppearance();
    });

    // Button styling handlers
    $(document).on('click', '.formera-app-btn-size', function() {
        appearance.btnSize = $(this).data('size');
        appPanel.innerHTML = buildPanel();
        applyAppearance();
    });
    $(document).on('click', '.formera-app-btn-style', function() {
        appearance.btnStyle = $(this).data('style');
        appPanel.innerHTML = buildPanel();
        applyAppearance();
    });
    $(document).on('click', '.formera-app-btn-width', function() {
        appearance.btnWidth = $(this).data('width');
        appPanel.innerHTML = buildPanel();
        applyAppearance();
    });

    // Layout handlers
    $(document).on('click', '.formera-app-form-width', function() {
        appearance.formWidth = $(this).data('width');
        appPanel.innerHTML = buildPanel();
        applyAppearance();
    });
    $(document).on('click', '.formera-app-spacing', function() {
        appearance.spacing = $(this).data('spacing');
        appPanel.innerHTML = buildPanel();
        applyAppearance();
    });
    $(document).on('click', '.formera-app-question-gap', function() {
        appearance.questionGap = $(this).data('gap');
        appPanel.innerHTML = buildPanel();
        applyAppearance();
    });

    // Effects handlers
    $(document).on('click', '.formera-app-entrance', function() {
        appearance.entrance = $(this).data('entrance');
        appPanel.innerHTML = buildPanel();
        applyAppearance();
    });
    $(document).on('click', '.formera-app-hover', function() {
        appearance.hover = $(this).data('hover');
        appPanel.innerHTML = buildPanel();
        applyAppearance();
    });

    // Advanced handlers
    $(document).on('click', '.formera-app-font-size', function() {
        appearance.fontSize = $(this).data('size');
        appPanel.innerHTML = buildPanel();
        applyAppearance();
    });
    $(document).on('click', '.formera-app-line-height', function() {
        appearance.lineHeight = $(this).data('height');
        appPanel.innerHTML = buildPanel();
        applyAppearance();
    });
    $(document).on('click', '.formera-app-shadow', function() {
        appearance.shadow = $(this).data('shadow');
        appPanel.innerHTML = buildPanel();
        applyAppearance();
    });
    $(document).on('click', '.formera-app-borders', function() {
        appearance.borders = $(this).data('borders');
        appPanel.innerHTML = buildPanel();
        applyAppearance();
    });

    $(document).on('click', '#formera-app-reset', function() {
        appearance = $.extend({}, defaultAppearance);
        appPanel.innerHTML = buildPanel();
        applyAppearance();
    });

    // Advanced section toggle
    $(document).on('click', '.formera-advanced-toggle', function() {
        var content = $(this).siblings('.formera-advanced-content');
        var svg = $(this).find('svg');
        if (content.is(':visible')) {
            content.hide();
            svg.css('transform', '');
        } else {
            content.show();
            svg.css('transform', 'rotate(90deg)');
        }
    });

    // Card click to preview
    $(document).on('click', '.formera-tpl-card', function(e) {
        if ($(e.target).closest('.formera-tpl-use-btn').length) return;
        openModal($(this).data('slug'));
    });

    // Close handlers
    mClose.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', function(e) { if (e.target === overlay) closeModal(); });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape' && overlay.style.display === 'flex') closeModal(); });
    appBtn.addEventListener('click', togglePanel);
    backBtn.addEventListener('click', switchToPreview);

    // "Use This Template" → go to edit step (instead of directly creating)
    useBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (currentSlug) switchToEdit();
    });
    $(document).on('click', '.formera-tpl-use-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var slug = $(this).data('slug');
        // Open modal first, then go to edit
        openModal(slug);
        switchToEdit();
    });

    /* ========== EDITOR EVENT HANDLERS ========== */
    // Title change
    $(document).on('input', '#fted-title', function() {
        editData.title = $(this).val();
        mTitle.textContent = editData.title || 'Untitled Form';
    });

    // Question title change
    $(document).on('input', '.fted-q-title', function() {
        var idx = parseInt($(this).data('index'));
        editData.questions[idx].title = $(this).val();
    });

    // Question type change
    $(document).on('change', '.fted-q-type', function() {
        var idx = parseInt($(this).data('index'));
        var newType = $(this).val();
        editData.questions[idx].type = newType;
        // Add default options if switching to radio/checkbox and none exist
        if ((newType === 'radio' || newType === 'checkbox') && !editData.questions[idx].options) {
            editData.questions[idx].options = 'Option 1,Option 2';
        }
        editorArea.innerHTML = renderEditor();
    });

    // Required toggle
    $(document).on('change', '.fted-q-required', function() {
        var idx = parseInt($(this).data('index'));
        editData.questions[idx].required = $(this).is(':checked');
    });

    // Option value change
    $(document).on('input', '.fted-opt-val', function() {
        var qi = parseInt($(this).data('q'));
        var oi = parseInt($(this).data('opt'));
        var opts = (editData.questions[qi].options || '').split(',').map(function(o) { return o.trim(); });
        opts[oi] = $(this).val();
        editData.questions[qi].options = opts.join(',');
    });

    // Add option
    $(document).on('click', '.fted-add-opt', function() {
        var qi = parseInt($(this).data('q'));
        var opts = (editData.questions[qi].options || '').split(',').map(function(o) { return o.trim(); }).filter(Boolean);
        opts.push('Option ' + (opts.length + 1));
        editData.questions[qi].options = opts.join(',');
        editorArea.innerHTML = renderEditor();
    });

    // Remove option
    $(document).on('click', '.fted-remove-opt', function() {
        var qi = parseInt($(this).data('q'));
        var oi = parseInt($(this).data('opt'));
        var opts = (editData.questions[qi].options || '').split(',').map(function(o) { return o.trim(); }).filter(Boolean);
        opts.splice(oi, 1);
        editData.questions[qi].options = opts.join(',');
        editorArea.innerHTML = renderEditor();
    });

    // Move question up
    $(document).on('click', '.fted-move-up', function() {
        var idx = parseInt($(this).data('index'));
        if (idx <= 0) return;
        var q = editData.questions.splice(idx, 1)[0];
        editData.questions.splice(idx - 1, 0, q);
        editorArea.innerHTML = renderEditor();
    });

    // Move question down
    $(document).on('click', '.fted-move-down', function() {
        var idx = parseInt($(this).data('index'));
        if (idx >= editData.questions.length - 1) return;
        var q = editData.questions.splice(idx, 1)[0];
        editData.questions.splice(idx + 1, 0, q);
        editorArea.innerHTML = renderEditor();
    });

    // Delete question
    $(document).on('click', '.fted-delete-q', function() {
        var idx = parseInt($(this).data('index'));
        if (editData.questions.length <= 1) return;
        editData.questions.splice(idx, 1);
        editorArea.innerHTML = renderEditor();
    });

    // Add new question
    $(document).on('click', '#fted-add-question', function() {
        editData.questions.push({
            title: 'New Question',
            type: 'text',
            options: '',
            required: false
        });
        editorArea.innerHTML = renderEditor();
        // Scroll to the new question
        var cards = editorArea.querySelectorAll('.fted-q-card');
        if (cards.length) cards[cards.length - 1].scrollIntoView({ behavior: 'smooth', block: 'center' });
    });

    /* ========== SAVE FORM (from editor) ========== */
    function saveForm() {
        // Validate
        var title = (editData.title || '').trim();
        if (!title) {
            alert('Please enter a form title.');
            return;
        }
        if (!editData.questions.length) {
            alert('Please add at least one question.');
            return;
        }
        for (var vi = 0; vi < editData.questions.length; vi++) {
            if (!(editData.questions[vi].title || '').trim()) {
                alert('Question ' + (vi + 1) + ' needs a title.');
                return;
            }
        }

        saveBtn.disabled = true;
        saveBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;vertical-align:middle;margin-right:6px;animation:spin 1s linear infinite;"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/></svg>Creating...';

        $.post(formera_templates.ajax_url, {
            action: 'formera_use_template',
            slug: currentSlug,
            custom_title: title,
            custom_questions: JSON.stringify(editData.questions),
            appearance: JSON.stringify(appearance),
            security: formera_templates.nonce
        }, function(resp) {
            if (resp.success && resp.data.redirect) {
                window.location.href = resp.data.redirect;
            } else {
                alert('Error: ' + (resp.data || 'Failed to create form'));
                resetSaveBtn();
            }
        }).fail(function() {
            alert('Network error. Please try again.');
            resetSaveBtn();
        });

        function resetSaveBtn() {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;vertical-align:middle;margin-right:6px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>Save Form';
        }
    }

    saveBtn.addEventListener('click', function(e) {
        e.preventDefault();
        saveForm();
    });
});
