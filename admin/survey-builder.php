<?php
/**
 * Survey Builder View
 * Google Forms-inspired UI/UX (Visual Only)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class DearSurvey_Builder {

	private $db;

	public function __construct( $db ) { $this->db = $db; }

	public function render() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- ID parameter is only used for loading survey data
		$survey_id = isset( $_GET['id'] ) ? intval( $_GET['id'] ) : 0;
		$survey = $survey_id ? $this->db->get_survey( $survey_id ) : null;
		$title = $survey ? $survey['title'] : '';
		$questions = $survey ? json_decode( $survey['questions'], true ) : [];
		$settings = $survey ? json_decode( $survey['settings'], true ) : [];

		require_once dirname( __FILE__ ) . '/menu.php';
		$menu = new DearSurvey_Menu( $this->db );
		?>
		<!-- Google Forms Inspired Styles -->
		<style>
		/* ========================================
		   DESIGN SYSTEM - Google Forms Inspired
		   ======================================== */
		
		/* CSS Custom Properties (Design Tokens) */
		:root {
			/* Primary Colors */
			--gf-primary: #673ab7;
			--gf-primary-dark: #5e35b1;
			--gf-primary-light: #d7c4f2;
			--gf-primary-bg: #f0ebf8;
			
			/* Neutral Colors */
			--gf-background: #f1f3f4;
			--gf-surface: #ffffff;
			--gf-border: #dadce0;
			--gf-border-light: #e0e0e0;
			
			/* Text Colors (WCAG AA Compliant) */
			--gf-text-primary: #202124;
			--gf-text-secondary: #5f6368;
			--gf-text-disabled: #80868b;
			--gf-text-on-primary: #ffffff;
			
			/* Semantic Colors */
			--gf-error: #d93025;
			--gf-error-bg: #fce8e6;
			--gf-success: #1e8e3e;
			--gf-success-bg: #e6f4ea;
			--gf-info: #1a73e8;
			--gf-info-bg: #e8f0fe;
			
			/* Spacing Scale */
			--gf-space-xs: 4px;
			--gf-space-sm: 8px;
			--gf-space-md: 16px;
			--gf-space-lg: 24px;
			--gf-space-xl: 32px;
			
			/* Typography */
			--gf-font-family: 'Google Sans', 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
			--gf-font-size-xs: 12px;
			--gf-font-size-sm: 13px;
			--gf-font-size-base: 14px;
			--gf-font-size-lg: 16px;
			--gf-font-size-xl: 18px;
			--gf-font-size-2xl: 24px;
			--gf-font-size-3xl: 32px;
			
			/* Line Heights */
			--gf-line-height-tight: 1.25;
			--gf-line-height-normal: 1.5;
			--gf-line-height-relaxed: 1.75;
			
			/* Border Radius */
			--gf-radius-sm: 4px;
			--gf-radius-md: 8px;
			--gf-radius-lg: 12px;
			--gf-radius-full: 9999px;
			
			/* Shadows */
			--gf-shadow-sm: 0 1px 2px 0 rgba(60,64,67,.3), 0 1px 3px 1px rgba(60,64,67,.15);
			--gf-shadow-md: 0 1px 3px 0 rgba(60,64,67,.3), 0 4px 8px 3px rgba(60,64,67,.15);
			--gf-shadow-lg: 0 1px 3px 0 rgba(60,64,67,.3), 0 8px 16px 4px rgba(60,64,67,.15);
			
			/* Transitions */
			--gf-transition-fast: 0.15s ease;
			--gf-transition-normal: 0.2s cubic-bezier(.4,0,.2,1);
			--gf-transition-slow: 0.3s ease;
			
			/* Focus Ring (Accessibility) */
			--gf-focus-ring: 0 0 0 2px var(--gf-surface), 0 0 0 4px var(--gf-primary);
		}
		
		/* ========================================
		   BASE STYLES
		   ======================================== */
		
		/* Main Layout */
		.gf-builder-wrap { 
			background: var(--gf-background); 
			min-height: 100vh; 
			margin-left: -20px; 
			padding: 0; 
			font-family: var(--gf-font-family);
			font-size: var(--gf-font-size-base);
			line-height: var(--gf-line-height-normal);
			color: var(--gf-text-primary);
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
		}
		.gf-builder-inner { 
			max-width: 720px; 
			margin: 0 auto; 
			padding: 24px 16px 80px; 
			position: relative;
		}
		
		/* Card Base */
		.gf-card { 
			background: #fff; 
			border-radius: 8px; 
			box-shadow: 0 2px 1px -1px rgba(0,0,0,.2), 0 1px 1px 0 rgba(0,0,0,.14), 0 1px 3px 0 rgba(0,0,0,.12); 
			margin-bottom: 12px; 
			position: relative;
			transition: box-shadow 0.2s cubic-bezier(.4,0,.2,1), transform 0.15s ease;
			will-change: box-shadow, transform;
		}
		.gf-card-header { 
			border-radius: 8px 8px 0 0; 
			border-top: 10px solid #673AB7; 
		}
		.gf-card-body { 
			padding: 22px 24px 24px; 
		}
		
		/* Title Input - Google Forms Style */
		.gf-title-input { 
			font-size: 32px; 
			font-weight: 400; 
			border: none; 
			border-bottom: 2px solid transparent; 
			width: 100%; 
			padding: 0 0 8px 0; 
			margin-bottom: 8px;
			background: transparent; 
			color: #202124; 
			line-height: 40px;
			transition: border-color 0.2s ease;
			font-family: 'Google Sans', 'Roboto', -apple-system, BlinkMacSystemFont, sans-serif;
		}
		.gf-title-input:hover { 
			border-bottom-color: #e0e0e0; 
		}
		.gf-title-input:focus { 
			outline: none; 
			border-bottom-color: #673AB7; 
		}
		.gf-title-input::placeholder {
			color: #80868b;
		}
		
		/* Description Input - Google Forms Style */
		.gf-desc-input { 
			font-size: 14px; 
			font-weight: 400;
			border: none; 
			border-bottom: 2px solid transparent; 
			width: 100%; 
			padding: 8px 0; 
			background: transparent; 
			color: #5f6368; 
			resize: none; 
			line-height: 20px;
			transition: border-color 0.2s ease;
			font-family: 'Google Sans', 'Roboto', -apple-system, BlinkMacSystemFont, sans-serif;
			overflow: hidden;
		}
		.gf-desc-input:hover { 
			border-bottom-color: #e0e0e0; 
		}
		.gf-desc-input:focus { 
			outline: none; 
			border-bottom-color: #673AB7; 
		}
		.gf-desc-input::placeholder {
			color: #80868b;
		}
		
		/* Question Card - Google Forms Style */
		.gf-q-card { 
			border-left: 6px solid transparent; 
			transition: border-color 0.2s cubic-bezier(.4,0,.2,1), 
			             box-shadow 0.2s cubic-bezier(.4,0,.2,1),
			             transform 0.15s ease; 
			cursor: pointer;
			overflow: hidden;
			will-change: border-color, box-shadow;
		}
		.gf-q-card:hover { 
			box-shadow: 0 1px 3px 0 rgba(60,64,67,.3), 0 4px 8px 3px rgba(60,64,67,.15); 
		}
		.gf-q-card.gf-focused { 
			border-left-color: #673AB7; 
			box-shadow: 0 1px 3px 0 rgba(60,64,67,.3), 0 4px 8px 3px rgba(60,64,67,.15); 
		}
		.gf-q-card.gf-new {
			animation: gfCardAppear 0.35s cubic-bezier(.4,0,.2,1) forwards;
		}
		
		/* Question Header */
		.gf-q-header { 
			display: flex; 
			gap: 24px; 
			align-items: flex-start; 
			flex-wrap: wrap;
		}
		.gf-q-title-wrap {
			flex: 1;
			min-width: 250px;
		}
		
		/* Question Title Input - Large Text Style */
		.gf-q-title-input { 
			width: 100%;
			font-size: 16px; 
			font-weight: 400;
			border: none; 
			border-bottom: 2px solid transparent;
			padding: 16px; 
			background: #f8f9fa; 
			border-radius: 4px 4px 0 0; 
			color: #202124; 
			line-height: 24px;
			transition: background 0.2s ease, border-color 0.2s ease;
			font-family: 'Google Sans', 'Roboto', -apple-system, BlinkMacSystemFont, sans-serif;
		}
		.gf-q-title-input::placeholder {
			color: #80868b;
		}
		.gf-q-title-input:hover { 
			background: #f1f3f4; 
		}
		.gf-q-title-input:focus { 
			outline: none; 
			border-bottom-color: #673AB7; 
			background: #f0ebf8; 
		}
		
		/* Card inactive state - hide footer */
		.gf-q-card .gf-q-footer {
			max-height: 0;
			padding: 0 24px;
			overflow: hidden;
			border-top: none;
			transition: max-height 0.25s ease, padding 0.25s ease, border-top 0.1s ease;
		}
		
		/* Card active state - show footer */
		.gf-q-card.gf-focused .gf-q-footer {
			max-height: 80px;
			padding: 16px 24px;
			border-top: 1px solid #dadce0;
		}
		
		/* Type Dropdown - Google Forms Style */
		.gf-type-select-wrap { 
			position: relative; 
			min-width: 208px;
			flex-shrink: 0;
		}
		
		/* Custom dropdown display */
		.gf-type-display {
			display: flex;
			align-items: center;
			gap: 12px;
			padding: 12px 40px 12px 12px;
			border: 1px solid #dadce0;
			border-radius: 4px;
			background: #fff;
			cursor: pointer;
			transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
			position: relative;
			user-select: none;
		}
		.gf-type-display:hover {
			background: #f8f9fa;
			border-color: #c0c0c0;
		}
		.gf-type-display.gf-open {
			border-color: #673AB7;
			box-shadow: 0 0 0 2px rgba(103,58,183,0.1);
			border-radius: 4px 4px 0 0;
		}
		.gf-type-display::after {
			content: '';
			position: absolute;
			right: 14px;
			top: 50%;
			transform: translateY(-50%);
			border-left: 5px solid transparent;
			border-right: 5px solid transparent;
			border-top: 5px solid #5f6368;
			transition: transform 0.2s ease, border-color 0.2s ease;
		}
		.gf-type-display.gf-open::after {
			transform: translateY(-50%) rotate(180deg);
			border-top-color: #673AB7;
		}
		.gf-type-icon {
			width: 24px;
			height: 24px;
			color: #5f6368;
			flex-shrink: 0;
		}
		.gf-type-label {
			font-size: 14px;
			color: #202124;
			font-family: 'Google Sans', 'Roboto', -apple-system, BlinkMacSystemFont, sans-serif;
			line-height: 20px;
		}
		
		/* Dropdown menu */
		.gf-type-menu {
			position: absolute;
			top: 100%;
			left: 0;
			right: 0;
			background: #fff;
			border: 1px solid #dadce0;
			border-top: none;
			border-radius: 0 0 4px 4px;
			box-shadow: 0 2px 6px 2px rgba(60,64,67,.15);
			z-index: 200;
			display: none;
			overflow: hidden;
		}
		.gf-type-menu.gf-open {
			display: block;
		}
		.gf-type-option {
			display: flex;
			align-items: center;
			gap: 12px;
			padding: 12px 16px;
			cursor: pointer;
			transition: background 0.15s ease;
		}
		.gf-type-option:hover {
			background: #f1f3f4;
		}
		.gf-type-option.gf-selected {
			background: #e8f0fe;
		}
		.gf-type-option.gf-selected:hover {
			background: #d2e3fc;
		}
		.gf-type-option .gf-type-icon {
			color: #5f6368;
		}
		.gf-type-option.gf-selected .gf-type-icon {
			color: #673AB7;
		}
		.gf-type-option .gf-type-label {
			color: #202124;
		}
		.gf-type-option.gf-selected .gf-type-label {
			color: #673AB7;
			font-weight: 500;
		}
		
		/* Hidden native select */
		.gf-type-select {
			position: absolute;
			opacity: 0;
			pointer-events: none;
			width: 100%;
			height: 100%;
			top: 0;
			left: 0;
		}
		
		/* Options List - Google Forms Style */
		.gf-options-container {
			margin-top: 20px;
			padding-left: 0;
		}
		.gf-option-row { 
			display: flex; 
			align-items: center; 
			gap: 12px; 
			padding: 8px 8px 8px 0;
			margin: 0 -8px 4px 0;
			border-radius: 4px;
			transition: background 0.15s ease;
		}
		.gf-option-row:hover {
			background: #f8f9fa;
		}
		
		/* Radio Circle Icon */
		.gf-option-icon { 
			width: 20px; 
			height: 20px; 
			border: 2px solid #5f6368; 
			border-radius: 50%; 
			flex-shrink: 0;
			transition: border-color 0.15s ease;
			box-sizing: border-box;
		}
		.gf-option-row:hover .gf-option-icon {
			border-color: #673AB7;
		}
		
		/* Checkbox Square Icon */
		.gf-option-icon.gf-checkbox { 
			border-radius: 3px; 
		}
		
		/* Option Text Input */
		.gf-option-input { 
			flex: 1; 
			border: none; 
			border-bottom: 1px solid transparent; 
			padding: 8px 0; 
			font-size: 14px; 
			color: #202124; 
			background: transparent; 
			line-height: 20px;
			font-family: 'Google Sans', 'Roboto', -apple-system, BlinkMacSystemFont, sans-serif;
			transition: border-color 0.2s ease;
		}
		.gf-option-input::placeholder {
			color: #80868b;
		}
		.gf-option-row:hover .gf-option-input {
			border-bottom-color: #e0e0e0;
		}
		.gf-option-input:focus { 
			outline: none; 
			border-bottom: 2px solid #673AB7; 
			padding-bottom: 7px;
		}
		
		/* Delete Button */
		.gf-option-delete { 
			width: 32px; 
			height: 32px; 
			border: none; 
			background: transparent; 
			cursor: pointer; 
			border-radius: 50%; 
			display: flex; 
			align-items: center; 
			justify-content: center; 
			color: #5f6368; 
			opacity: 0; 
			transition: opacity 0.15s ease, background 0.15s ease, color 0.15s ease; 
			padding: 0;
			flex-shrink: 0;
		}
		.gf-option-delete svg {
			width: 18px;
			height: 18px;
		}
		.gf-option-row:hover .gf-option-delete { 
			opacity: 1; 
		}
		.gf-option-delete:hover { 
			background: #fce8e6;
			color: #d93025;
		}
		
		/* Add Option Button */
		.gf-add-option { 
			display: flex; 
			align-items: center; 
			gap: 12px; 
			padding: 8px 0;
			margin-top: 4px;
			cursor: pointer; 
			font-size: 14px;
			border-radius: 4px;
			transition: background 0.15s ease;
		}
		.gf-add-option:hover {
			background: #f8f9fa;
			margin-left: -8px;
			margin-right: -8px;
			padding-left: 8px;
			padding-right: 8px;
		}
		.gf-add-option .gf-option-icon {
			border-color: #dadce0;
			border-style: dashed;
		}
		.gf-add-option:hover .gf-option-icon {
			border-color: #673AB7;
		}
		.gf-add-option-text {
			color: #673AB7;
			font-weight: 500;
			padding-bottom: 1px;
			border-bottom: 1px solid transparent;
			transition: border-color 0.15s ease;
		}
		.gf-add-option:hover .gf-add-option-text { 
			border-bottom-color: #673AB7;
		}
		
		/* Or add "Other" link */
		.gf-add-other {
			color: #1a73e8;
			font-size: 13px;
			margin-left: 8px;
			cursor: pointer;
		}
		.gf-add-other:hover {
			text-decoration: underline;
		}
		
		/* Text preview */
		.gf-text-preview { 
			border-bottom: 1px dotted #dadce0; 
			padding: 8px 0; 
			color: #70757a; 
			font-size: 14px; 
			margin-top: 24px;
			width: 50%;
		}
		.gf-textarea-preview { 
			border-bottom: 1px dotted #dadce0; 
			padding: 8px 0; 
			color: #70757a; 
			font-size: 14px; 
			margin-top: 24px;
			width: 80%;
		}
		
		/* Question Footer - Action Icons */
		.gf-q-footer { 
			display: flex; 
			align-items: center; 
			justify-content: flex-end; 
			gap: 0; 
			background: #fff;
		}
		.gf-icon-btn { 
			width: 40px; 
			height: 40px; 
			border: none; 
			background: transparent; 
			cursor: pointer; 
			border-radius: 50%; 
			display: flex; 
			align-items: center; 
			justify-content: center; 
			color: #5f6368; 
			transition: background 0.15s ease, color 0.15s ease; 
			margin: 0 2px;
		}
		.gf-icon-btn:hover { 
			background: #f1f3f4;
			color: #202124;
		}
		.gf-icon-btn:active {
			background: #e8eaed;
		}
		.gf-icon-btn svg {
			width: 20px;
			height: 20px;
		}
		.gf-icon-btn.gf-delete:hover {
			color: #d93025;
			background: #fce8e6;
		}
		.gf-icon-btn.gf-add-q-btn:hover {
			color: #673AB7;
			background: #f0ebf8;
		}
		/* Vertical Divider */
		.gf-divider-v { 
			width: 1px; 
			height: 32px; 
			background: #dadce0; 
			margin: 0 12px 0 8px; 
		}
		
		/* Required Toggle Container */
		.gf-required-wrap { 
			display: flex; 
			align-items: center; 
			gap: 12px; 
			font-size: 14px; 
			color: #202124;
			padding-left: 4px;
		}
		.gf-required-wrap span {
			font-family: 'Google Sans', 'Roboto', -apple-system, BlinkMacSystemFont, sans-serif;
			user-select: none;
		}
		
		/* Toggle Switch - Google Forms Style (Purple) */
		.gf-toggle { 
			position: relative; 
			width: 34px; 
			height: 14px; 
			background: #dadce0; 
			border-radius: 7px; 
			cursor: pointer; 
			transition: background 0.2s ease;
			flex-shrink: 0;
		}
		.gf-toggle::after { 
			content: ''; 
			position: absolute; 
			width: 20px; 
			height: 20px; 
			background: #fff; 
			border-radius: 50%; 
			top: -3px; 
			left: -1px; 
			box-shadow: 0 1px 3px 0 rgba(0,0,0,.3), 0 1px 1px 0 rgba(0,0,0,.15); 
			transition: left 0.15s ease, background 0.15s ease, box-shadow 0.15s ease; 
		}
		.gf-toggle:hover::after {
			box-shadow: 0 1px 3px 0 rgba(0,0,0,.3), 0 1px 1px 0 rgba(0,0,0,.15), 0 0 0 8px rgba(0,0,0,.04);
		}
		.gf-toggle.gf-on { 
			background: #d7c4f2; 
		}
		.gf-toggle.gf-on::after { 
			left: 15px; 
			background: #673AB7; 
		}
		.gf-toggle.gf-on:hover::after {
			box-shadow: 0 1px 3px 0 rgba(0,0,0,.3), 0 1px 1px 0 rgba(0,0,0,.15), 0 0 0 8px rgba(103,58,183,.12);
		}
		
		/* Floating Toolbar */
		.gf-floating-toolbar { 
			position: fixed; 
			left: calc(50% + 380px); 
			top: 200px; 
			background: #fff; 
			border-radius: 8px; 
			box-shadow: 0 2px 1px -1px rgba(0,0,0,.2), 0 1px 1px 0 rgba(0,0,0,.14), 0 1px 3px 0 rgba(0,0,0,.12); 
			display: flex; 
			flex-direction: column; 
			padding: 6px 0; 
			z-index: 100; 
		}
		.gf-toolbar-btn { 
			width: 48px; 
			height: 48px; 
			border: none; 
			background: transparent; 
			cursor: pointer; 
			display: flex; 
			align-items: center; 
			justify-content: center; 
			color: #5f6368; 
			transition: background 0.15s ease; 
		}
		.gf-toolbar-btn:hover { 
			background: #f1f3f4; 
		}
		.gf-toolbar-btn svg {
			width: 24px;
			height: 24px;
		}
		.gf-toolbar-divider { 
			height: 1px; 
			background: #dadce0; 
			margin: 6px 12px; 
		}
		
		/* Top Bar */
		.gf-top-bar { 
			background: #fff; 
			border-bottom: 1px solid #dadce0; 
			padding: 12px 16px; 
			display: flex; 
			align-items: center; 
			justify-content: space-between; 
			margin: -24px -16px 24px -16px; 
			position: sticky; 
			top: 32px; 
			z-index: 99; 
		}
		.gf-logo { 
			display: flex; 
			align-items: center; 
			gap: 10px; 
			font-size: 18px; 
			color: #5f6368; 
		}
		.gf-logo svg { 
			width: 40px; 
			height: 40px; 
		}
		.gf-btn-primary { 
			background: #673AB7; 
			color: #fff; 
			border: none; 
			padding: 10px 24px; 
			border-radius: 4px; 
			font-size: 14px; 
			font-weight: 500; 
			cursor: pointer; 
			display: flex; 
			align-items: center; 
			gap: 8px; 
			transition: all 0.2s ease; 
			letter-spacing: 0.25px;
		}
		.gf-btn-primary:hover { 
			background: #5e35b1; 
			box-shadow: 0 1px 2px 0 rgba(60,64,67,.3), 0 1px 3px 1px rgba(60,64,67,.15); 
		}
		.gf-btn-text {
			background: transparent;
			color: #5f6368;
			border: none;
			padding: 10px 16px;
			border-radius: 4px;
			font-size: 14px;
			font-weight: 500;
			cursor: pointer;
			text-decoration: none;
			transition: background 0.15s ease;
		}
		.gf-btn-text:hover {
			background: #f1f3f4;
		}
		
		/* Settings Card */
		.gf-settings-toggle { 
			padding: 20px 24px; 
			display: flex; 
			align-items: center; 
			justify-content: space-between; 
			cursor: pointer; 
		}
		.gf-settings-toggle:hover { 
			background: #f8f9fa; 
		}
		.gf-settings-title { 
			display: flex; 
			align-items: center; 
			gap: 16px; 
			font-size: 14px; 
			font-weight: 500; 
			color: #202124; 
		}
		.gf-settings-body { 
			padding: 0 24px 24px; 
			display: none; 
			border-top: 1px solid #dadce0;
		}
		.gf-settings-body.gf-open { 
			display: block; 
		}
		.gf-setting-row { 
			display: flex; 
			align-items: center; 
			justify-content: space-between; 
			padding: 16px 0; 
			border-bottom: 1px solid #f1f3f4; 
		}
		.gf-setting-row:last-child { 
			border-bottom: none; 
		}
		.gf-input { 
			width: 100%; 
			padding: 12px 16px; 
			border: 1px solid #dadce0; 
			border-radius: 4px; 
			font-size: 14px; 
			line-height: 20px;
			color: #202124;
		}
		.gf-input:focus { 
			outline: none; 
			border-color: #673AB7; 
			box-shadow: 0 0 0 2px rgba(103,58,183,0.1); 
		}
		.gf-textarea { 
			min-height: 80px; 
			resize: vertical; 
		}
		.gf-label { 
			font-size: 12px; 
			font-weight: 500; 
			color: #5f6368; 
			margin-bottom: 8px; 
			display: block; 
			text-transform: uppercase;
			letter-spacing: 0.5px;
		}
		
		/* Animations */
		@keyframes gfSlideIn { 
			from { opacity: 0; transform: translateY(8px); } 
			to { opacity: 1; transform: translateY(0); } 
		}
		@keyframes gfCardAppear {
			0% { 
				opacity: 0; 
				transform: translateY(20px) scale(0.97); 
			}
			60% {
				transform: translateY(-4px) scale(1.01);
			}
			100% { 
				opacity: 1; 
				transform: translateY(0) scale(1); 
			}
		}
		@keyframes gfPulse {
			0% { box-shadow: 0 0 0 0 rgba(103,58,183,.4); }
			70% { box-shadow: 0 0 0 10px rgba(103,58,183,0); }
			100% { box-shadow: 0 0 0 0 rgba(103,58,183,0); }
		}
		@keyframes gfFadeIn {
			from { opacity: 0; }
			to { opacity: 1; }
		}
		.gf-animate { 
			animation: gfSlideIn 0.25s ease-out forwards; 
		}
		.gf-fade-in {
			animation: gfFadeIn 0.3s ease-out forwards;
		}
		.gf-pulse {
			animation: gfPulse 0.6s ease-out;
		}
		
		/* Smooth scroll behavior */
		.gf-builder-wrap {
			scroll-behavior: smooth;
		}
		
		/* Chevron rotation */
		.gf-chevron { 
			transition: transform var(--gf-transition-normal); 
		}
		.gf-chevron.gf-rotated { 
			transform: rotate(180deg); 
		}
		
		/* Hide default WP admin styling conflicts */
		.gf-builder-wrap .ds-sidebar { display: none; }
		
		/* ========================================
		   ACCESSIBILITY
		   ======================================== */
		
		/* Focus Visible Styles (keyboard navigation) */
		.gf-builder-wrap *:focus-visible {
			outline: none;
			box-shadow: var(--gf-focus-ring);
		}
		
		.gf-builder-wrap input:focus-visible,
		.gf-builder-wrap textarea:focus-visible,
		.gf-builder-wrap select:focus-visible {
			box-shadow: none;
			border-color: var(--gf-primary);
		}
		
		.gf-builder-wrap button:focus-visible {
			box-shadow: var(--gf-focus-ring);
		}
		
		/* High Contrast Mode Support */
		@media (prefers-contrast: high) {
			:root {
				--gf-border: #000;
				--gf-text-secondary: #202124;
			}
			.gf-q-card.gf-focused {
				border-left-width: 8px;
			}
		}
		
		/* Reduced Motion Support */
		@media (prefers-reduced-motion: reduce) {
			*,
			*::before,
			*::after {
				animation-duration: 0.01ms !important;
				animation-iteration-count: 1 !important;
				transition-duration: 0.01ms !important;
			}
			.gf-builder-wrap {
				scroll-behavior: auto;
			}
		}
		
		/* Screen Reader Only (visually hidden) */
		.gf-sr-only {
			position: absolute;
			width: 1px;
			height: 1px;
			padding: 0;
			margin: -1px;
			overflow: hidden;
			clip: rect(0, 0, 0, 0);
			white-space: nowrap;
			border: 0;
		}
		
		/* Skip Link (accessibility) */
		.gf-skip-link {
			position: absolute;
			top: -40px;
			left: 0;
			background: var(--gf-primary);
			color: var(--gf-text-on-primary);
			padding: var(--gf-space-sm) var(--gf-space-md);
			z-index: 1000;
			transition: top var(--gf-transition-fast);
		}
		.gf-skip-link:focus {
			top: 0;
		}
		
		/* ========================================
		   RESPONSIVE
		   ======================================== */
		
		/* Large screens - toolbar beside content */
		@media (max-width: 1200px) {
			.gf-floating-toolbar {
				left: auto;
				right: 16px;
			}
		}
		
		/* Tablets and smaller laptops */
		@media (max-width: 900px) {
			.gf-builder-inner {
				max-width: 100%;
				padding: var(--gf-space-md) var(--gf-space-md) 100px;
			}
			.gf-floating-toolbar {
				right: 12px;
			}
		}
		
		/* Tablets */
		@media (max-width: 768px) {
			/* Floating Toolbar - Bottom horizontal */
			.gf-floating-toolbar {
				position: fixed;
				bottom: 0;
				top: auto;
				left: 0;
				right: 0;
				transform: none;
				flex-direction: row;
				justify-content: center;
				padding: var(--gf-space-sm) var(--gf-space-md);
				border-radius: 0;
				box-shadow: 0 -2px 8px rgba(0,0,0,.15);
				gap: var(--gf-space-xs);
			}
			.gf-toolbar-btn {
				width: 44px;
				height: 44px;
			}
			.gf-toolbar-divider {
				width: 1px;
				height: 32px;
				margin: 0 var(--gf-space-sm);
			}
			
			/* Question Header - Stack vertically */
			.gf-q-header {
				flex-direction: column;
				gap: var(--gf-space-md);
			}
			.gf-q-title-wrap {
				min-width: 100%;
			}
			.gf-type-select-wrap {
				width: 100%;
				min-width: 100%;
			}
			
			/* Top Bar */
			.gf-top-bar {
				padding: var(--gf-space-sm) var(--gf-space-md);
				flex-wrap: wrap;
				gap: var(--gf-space-sm);
			}
			.gf-logo span {
				font-size: var(--gf-font-size-base);
			}
			
			/* Cards */
			.gf-card-body {
				padding: var(--gf-space-md);
			}
			
			/* Title input */
			.gf-title-input {
				font-size: var(--gf-font-size-2xl);
			}
			
			/* Question Footer - wrap for smaller screens */
			.gf-q-card.gf-focused .gf-q-footer {
				flex-wrap: wrap;
				gap: var(--gf-space-sm);
				justify-content: flex-end;
			}
			
			/* Builder padding for bottom toolbar */
			.gf-builder-inner {
				padding-bottom: 80px;
			}
		}
		
		/* Mobile phones */
		@media (max-width: 480px) {
			/* Main container */
			.gf-builder-wrap {
				margin-left: -10px;
				margin-right: -10px;
			}
			.gf-builder-inner {
				padding: var(--gf-space-sm) var(--gf-space-sm) 90px;
			}
			
			/* Top Bar - Stack on very small screens */
			.gf-top-bar {
				padding: var(--gf-space-sm);
				position: relative;
				flex-direction: column;
				align-items: stretch;
				gap: var(--gf-space-sm);
			}
			.gf-top-bar > div:first-child {
				justify-content: center;
			}
			.gf-top-bar > div:last-child {
				display: flex;
				justify-content: center;
				gap: var(--gf-space-sm);
			}
			
			/* Cards - Full width, reduced padding */
			.gf-card {
				border-radius: var(--gf-radius-sm);
				margin-bottom: var(--gf-space-sm);
			}
			.gf-card-header {
				border-radius: var(--gf-radius-sm) var(--gf-radius-sm) 0 0;
				border-top-width: 8px;
			}
			.gf-card-body {
				padding: var(--gf-space-md) var(--gf-space-sm);
			}
			
			/* Title - Smaller on mobile */
			.gf-title-input {
				font-size: var(--gf-font-size-xl);
				line-height: 1.3;
			}
			
			/* Question title input */
			.gf-q-title-input {
				padding: var(--gf-space-sm) var(--gf-space-sm);
				font-size: var(--gf-font-size-base);
			}
			
			/* Type dropdown - Full width, larger touch target */
			.gf-type-display {
				padding: var(--gf-space-sm) 40px var(--gf-space-sm) var(--gf-space-sm);
				min-height: 48px;
			}
			.gf-type-option {
				padding: var(--gf-space-sm) var(--gf-space-md);
				min-height: 48px;
			}
			
			/* Options - Better touch targets */
			.gf-option-row {
				padding: var(--gf-space-sm) var(--gf-space-xs);
				min-height: 48px;
			}
			.gf-option-input {
				font-size: var(--gf-font-size-base);
				padding: var(--gf-space-sm) 0;
			}
			.gf-option-delete {
				width: 40px;
				height: 40px;
				opacity: 0.7;
			}
			.gf-add-option {
				min-height: 48px;
				padding: var(--gf-space-sm) 0;
			}
			
			/* Footer actions - Rearrange for mobile */
			.gf-q-card .gf-q-footer {
				padding: var(--gf-space-sm) var(--gf-space-sm);
			}
			.gf-q-card.gf-focused .gf-q-footer {
				max-height: none;
				flex-wrap: wrap;
			}
			.gf-icon-btn {
				width: 44px;
				height: 44px;
			}
			.gf-divider-v {
				display: none;
			}
			.gf-required-wrap {
				width: 100%;
				justify-content: flex-end;
				padding-top: var(--gf-space-sm);
				border-top: 1px solid var(--gf-border);
				margin-top: var(--gf-space-xs);
			}
			
			/* Settings panel */
			.gf-settings-toggle {
				padding: var(--gf-space-md) var(--gf-space-sm);
			}
			.gf-settings-body {
				padding: 0 var(--gf-space-sm) var(--gf-space-md);
			}
			.gf-setting-row {
				flex-direction: column;
				align-items: flex-start;
				gap: var(--gf-space-sm);
			}
			
			/* Floating Toolbar - Compact on mobile */
			.gf-floating-toolbar {
				padding: var(--gf-space-xs) var(--gf-space-sm);
				gap: 0;
			}
			.gf-toolbar-btn {
				width: 48px;
				height: 48px;
			}
			.gf-toolbar-btn svg {
				width: 22px;
				height: 22px;
			}
			
			/* Inputs - Larger touch targets */
			.gf-input {
				padding: var(--gf-space-sm) var(--gf-space-sm);
				font-size: 16px; /* Prevents iOS zoom */
				min-height: 48px;
			}
			.gf-textarea {
				min-height: 100px;
			}
			
			/* Buttons - Larger touch targets */
			.gf-btn-primary {
				padding: var(--gf-space-sm) var(--gf-space-md);
				min-height: 44px;
				font-size: var(--gf-font-size-base);
			}
		}
		
		/* Very small phones */
		@media (max-width: 360px) {
			.gf-builder-inner {
				padding: var(--gf-space-xs) var(--gf-space-xs) 90px;
			}
			.gf-card-body {
				padding: var(--gf-space-sm);
			}
			.gf-title-input {
				font-size: var(--gf-font-size-lg);
			}
			.gf-toolbar-btn {
				width: 44px;
				height: 44px;
			}
			.gf-logo svg {
				width: 32px;
				height: 32px;
			}
		}
		
		/* Touch device optimizations */
		@media (hover: none) and (pointer: coarse) {
			/* Always show delete buttons on touch devices */
			.gf-option-delete {
				opacity: 0.6;
			}
			
			/* Larger touch targets */
			.gf-toggle {
				width: 44px;
				height: 18px;
			}
			.gf-toggle::after {
				width: 24px;
				height: 24px;
				top: -3px;
			}
			.gf-toggle.gf-on::after {
				left: 20px;
			}
			
			/* Remove hover states that might stick on touch */
			.gf-q-card:hover {
				box-shadow: 0 2px 1px -1px rgba(0,0,0,.2), 0 1px 1px 0 rgba(0,0,0,.14), 0 1px 3px 0 rgba(0,0,0,.12);
			}
			.gf-q-card.gf-focused:hover {
				box-shadow: var(--gf-shadow-md);
			}
		}
		
		/* Landscape mobile */
		@media (max-height: 500px) and (orientation: landscape) {
			.gf-floating-toolbar {
				position: fixed;
				right: var(--gf-space-sm);
				left: auto;
				bottom: auto;
				top: 50%;
				transform: translateY(-50%);
				flex-direction: column;
				border-radius: var(--gf-radius-md);
			}
			.gf-toolbar-divider {
				width: 100%;
				height: 1px;
				margin: var(--gf-space-xs) 0;
			}
			.gf-builder-inner {
				padding-bottom: var(--gf-space-xl);
			}
		}
		
		/* Print Styles */
		@media print {
			.gf-builder-wrap {
				background: #fff;
			}
			.gf-floating-toolbar,
			.gf-top-bar,
			.gf-q-footer {
				display: none !important;
			}
			.gf-q-card {
				break-inside: avoid;
				box-shadow: none;
				border: 1px solid var(--gf-border);
			}
		}
		</style>
		
		<div class="gf-builder-wrap">
			<div class="gf-builder-inner">
				<!-- Skip Link for Accessibility -->
				<a href="#questions-container" class="gf-skip-link">Skip to questions</a>
				
				<!-- Top Bar -->
				<div class="gf-top-bar" role="banner">
					<div class="gf-logo">
						<svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
							<rect x="3" y="3" width="18" height="18" rx="2" fill="var(--gf-primary, #673AB7)"/>
							<path d="M7 12l3 3 7-7" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
						<span style="color: var(--gf-primary); font-weight: 600;">Dear Survey</span>
					</div>
					<div style="display:flex; gap:12px; align-items:center;">
						<a href="<?php echo esc_url( admin_url('admin.php?page=dear-survey-list') ); ?>" style="padding:10px 16px; color:#5F6368; text-decoration:none; font-size:14px;">Cancel</a>
						<button type="submit" form="ds-survey-form" class="gf-btn-primary">
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width:18px; height:18px;"><path d="M4.5 12.75l6 6 9-13.5"/></svg>
							<?php echo $survey_id ? 'Save' : 'Create'; ?>
						</button>
					</div>
				</div>
				
				<form id="ds-survey-form">
					<input type="hidden" id="survey_id" value="<?php echo esc_attr( $survey_id ); ?>">
					
					<!-- Header Card -->
					<div class="gf-card gf-card-header gf-animate">
						<div class="gf-card-body">
							<label for="survey_title" class="gf-sr-only">Survey Title</label>
							<input type="text" id="survey_title" class="gf-title-input" value="<?php echo esc_attr( $title ); ?>" placeholder="Untitled form" required autocomplete="off" spellcheck="false" aria-label="Survey title">
							<label for="survey_description" class="gf-sr-only">Survey Description</label>
							<textarea id="survey_description" class="gf-desc-input" placeholder="Form description" rows="1" autocomplete="off" aria-label="Survey description"></textarea>
						</div>
					</div>

					<div id="questions-container" role="list" aria-label="Survey questions">
						<!-- JavaScript dynamic render -->
					</div>

					<!-- Settings Card -->
					<div class="gf-card gf-animate" style="margin-top:24px;">
						<div class="gf-settings-toggle" onclick="jQuery(this).next().toggleClass('gf-open'); jQuery(this).find('.gf-chevron').toggleClass('gf-rotated');" role="button" aria-expanded="false" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' '){this.click();event.preventDefault();}">
							<div class="gf-settings-title">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:24px;height:24px;color:var(--gf-primary, #673AB7);" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
								Settings
							</div>
							<svg class="gf-chevron" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px;height:20px;color:#5F6368;transition:transform 0.2s;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
						</div>
						<div class="gf-settings-body">
							<div class="gf-setting-row">
								<div>
									<div style="font-size:14px;color:#202124;">Collect email addresses</div>
									<div style="font-size:12px;color:#5F6368;">Respondents will be required to sign in</div>
								</div>
								<label class="ds-toggle" style="margin:0;">
									<input type="checkbox" id="ds-setting-collect-email" <?php checked($settings['collect_email'] ?? false); ?>>
									<span class="ds-toggle-slider"></span>
								</label>
							</div>
							
							<div style="margin-top:20px;">
								<label class="gf-label">Confirmation message</label>
								<textarea id="ds-setting-thank-you" class="gf-input gf-textarea" placeholder="Your response has been recorded."><?php echo esc_textarea($settings['thank_you_body'] ?? ''); ?></textarea>
							</div>
							
							<div style="margin-top:16px;">
								<label class="gf-label">Send notification to</label>
								<input type="email" id="ds-setting-admin-email" class="gf-input" value="<?php echo esc_attr($settings['admin_email'] ?? ''); ?>" placeholder="admin@example.com">
							</div>

							<div id="autoresponder-section" style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #E0E0E0; <?php echo ($settings['collect_email'] ?? false) ? 'display:block' : 'display:none'; ?>">
								<div style="font-size:14px;font-weight:500;color:#202124;margin-bottom:16px;">Autoresponder Email</div>
								<div style="margin-bottom:16px;">
									<label class="gf-label">Subject</label>
									<input type="text" id="ds-setting-auto-subject" class="gf-input" value="<?php echo esc_attr($settings['auto_subject'] ?? ''); ?>" placeholder="Thank you for your feedback!">
								</div>
								<div>
									<label class="gf-label">Body</label>
									<textarea id="ds-setting-auto-body" class="gf-input gf-textarea" placeholder="Thank you for participating..."><?php echo esc_textarea($settings['auto_body'] ?? ''); ?></textarea>
								</div>
							</div>
						</div>
					</div>

					<?php if ($survey_id) : ?>
					<!-- Send Card -->
					<div class="gf-card gf-animate">
						<div class="gf-settings-toggle" onclick="jQuery(this).next().toggleClass('gf-open'); jQuery(this).find('.gf-chevron').toggleClass('gf-rotated');">
							<div class="gf-settings-title">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:24px;height:24px;color:#673AB7;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
								Send
							</div>
							<svg class="gf-chevron" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px;height:20px;color:#5F6368;transition:transform 0.2s;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
						</div>
						<div class="gf-settings-body">
							<div style="margin-bottom:16px;">
								<label class="gf-label">Email addresses (comma-separated)</label>
								<textarea id="ds-outreach-emails" class="gf-input gf-textarea" style="min-height:60px;" placeholder="user1@example.com, user2@example.com"></textarea>
							</div>
							<div style="margin-bottom:16px;">
								<label class="gf-label">Subject</label>
								<input type="text" id="ds-outreach-subject" class="gf-input" value="Check out our new survey: <?php echo esc_attr($title); ?>">
							</div>
							<div style="margin-bottom:16px;">
								<label class="gf-label">Message</label>
								<textarea id="ds-outreach-message" class="gf-input gf-textarea">Hi, we'd love to hear your thoughts on this survey!</textarea>
							</div>
							<button type="button" id="ds-send-outreach" class="gf-btn-primary" style="width:100%;">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
								Send Invitations
							</button>
						</div>
					</div>
					<?php endif; ?>
				</form>
			</div>
			
			<!-- Floating Toolbar -->
			<div class="gf-floating-toolbar">
				<button type="button" class="gf-toolbar-btn" id="add-question" title="Add question">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="1.5"/><path fill-rule="evenodd" d="M12 7.5a.75.75 0 01.75.75v3h3a.75.75 0 010 1.5h-3v3a.75.75 0 01-1.5 0v-3h-3a.75.75 0 010-1.5h3v-3A.75.75 0 0112 7.5z" clip-rule="evenodd"/></svg>
				</button>
			</div>
		</div>

		<script>
		jQuery(document).ready(function($) {
			let questions = <?php echo json_encode( $questions ); ?>;
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
					action: 'ds_send_outreach',
					survey_id: $('#survey_id').val(),
					recipients: recipients,
					subject: $('#ds-outreach-subject').val(),
					message: $('#ds-outreach-message').val(),
					security: '<?php echo esc_js( wp_create_nonce("ds_outreach") ); ?>'
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
					action: 'ds_save_survey',
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
					security: '<?php echo esc_js( wp_create_nonce("ds_save_survey") ); ?>'
				};

				$.post(ajaxurl, data, function(response) {
					saveBtn.prop('disabled', false).html(originalHtml);
					if (response.success) {
						if (!data.id || data.id == 0) { 
							window.location.href = 'admin.php?page=dear-survey-list&id=' + response.data.id; 
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
			
			// Add CSS for rotated chevron
			$('<style>.gf-chevron.gf-rotated { transform: rotate(180deg) !important; }</style>').appendTo('head');

			renderQuestions();
		});
		</script>
		<?php
	}

	public function ajax_save_survey() {
		check_ajax_referer( 'ds_save_survey', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Forbidden' );
		}
		
		$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
		$title = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
		$questions = isset( $_POST['questions'] ) ? sanitize_text_field( wp_unslash( $_POST['questions'] ) ) : '[]';
		$settings = isset( $_POST['settings'] ) ? sanitize_text_field( wp_unslash( $_POST['settings'] ) ) : '{}';
		
		// Basic validation
		json_decode( $questions );
		if ( json_last_error() !== JSON_ERROR_NONE ) wp_send_json_error( 'Invalid Questions JSON' );
		json_decode( $settings );
		if ( json_last_error() !== JSON_ERROR_NONE ) wp_send_json_error( 'Invalid Settings JSON' );

		$data = array( 
			'id' => $id, 
			'title' => $title, 
			'questions' => $questions,
			'settings' => $settings,
			'status' => 'active'
		);
		
		$new_id = $this->db->save_survey( $data );
		if ( $new_id ) wp_send_json_success( array( 'id' => $id ? $id : $new_id ) );
		else wp_send_json_error( 'DB Error' );
	}

	public function ajax_send_outreach() {
		check_ajax_referer( 'ds_outreach', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Forbidden' );
		}

		$survey_id = isset( $_POST['survey_id'] ) ? intval( $_POST['survey_id'] ) : 0;
		$recipients_raw = isset( $_POST['recipients'] ) ? sanitize_text_field( wp_unslash( $_POST['recipients'] ) ) : '';
		$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
		$message_body = isset( $_POST['message'] ) ? wp_kses_post( wp_unslash( $_POST['message'] ) ) : '';

		$survey = $this->db->get_survey( $survey_id );
		if ( ! $survey ) wp_send_json_error( 'Survey not found' );

		$emails = array_map( 'trim', explode( ',', $recipients_raw ) );
		$emails = array_filter( $emails, 'is_email' );

		if ( empty( $emails ) ) wp_send_json_error( 'No valid email addresses provided' );

		$survey_link = home_url( '/?ds_survey=' . $survey_id ); // Simple link format
		
		$full_message = $message_body . "\n\nParticipate here: " . $survey_link;
		
		$sent_count = 0;
		foreach ( $emails as $email ) {
			if ( wp_mail( $email, $subject, $full_message ) ) {
				$sent_count++;
			}
		}

		if ( $sent_count > 0 ) {
			wp_send_json_success( $sent_count . ' emails sent' );
		} else {
			wp_send_json_error( 'Failed to send emails' );
		}
	}
}
