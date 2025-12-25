<?php
/**
 * Plugin Name: Dear Survey
 * Plugin URI:  https://example.com/dear-survey
 * Description: An advanced, high-performance Survey Maker with a premium SaaS-style interface.
 * Version:     1.0.0
 * Author:      Your Name
 * Text Domain: dear-survey
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define constants
define( 'DEAR_SURVEY_VERSION', '1.0.0' );
define( 'DEAR_SURVEY_PATH', plugin_dir_path( __FILE__ ) );
define( 'DEAR_SURVEY_URL', plugin_dir_url( __FILE__ ) );
define( 'DEAR_SURVEY_DB_VERSION', '1.0' );
define( 'DEAR_SURVEY_DEBUG', true ); // Added for debugging

// Include core classes
require_once DEAR_SURVEY_PATH . 'includes/class-dear-survey-db.php';
require_once DEAR_SURVEY_PATH . 'includes/class-dear-survey-activator.php';

// Activation hook
register_activation_hook( __FILE__, array( 'Dear_Survey_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Dear_Survey_Activator', 'deactivate' ) );

// Main Plugin Class
class Dear_Survey {

	protected $db;

	public function __construct() {
		$this->check_version();
		$this->db = new Dear_Survey_DB();
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	private function check_version() {
		if ( get_option( 'dear_survey_db_version' ) != DEAR_SURVEY_DB_VERSION ) {
			Dear_Survey_Activator::activate();
		}
	}

	private function load_dependencies() {
		// Admin
		if ( is_admin() ) {
			require_once DEAR_SURVEY_PATH . 'admin/menu.php';
			require_once DEAR_SURVEY_PATH . 'admin/survey-builder.php';
			require_once DEAR_SURVEY_PATH . 'admin/results.php';
			require_once DEAR_SURVEY_PATH . 'admin/settings.php';
		}

		// Public
		require_once DEAR_SURVEY_PATH . 'public/shortcode.php';
		require_once DEAR_SURVEY_PATH . 'public/submit.php';
	}

	private function define_admin_hooks() {
		if ( is_admin() ) {
			$menu = new Dear_Survey_Menu( $this->db );
			add_action( 'admin_menu', array( $menu, 'register_menus' ) );
			
			// AJAX for builder
			$builder = new Dear_Survey_Builder( $this->db );
			add_action( 'wp_ajax_ds_save_survey', array( $builder, 'ajax_save_survey' ) );
			add_action( 'wp_ajax_ds_send_outreach', array( $builder, 'ajax_send_outreach' ) );
			
			// CSV Export
			add_action( 'wp_ajax_ds_export_csv', array( $this, 'handle_export_csv' ) );
			
			// Enqueue Admin CSS & JS
			add_action( 'admin_enqueue_scripts', function() {
				wp_enqueue_style( 'dear-survey-admin', DEAR_SURVEY_URL . 'admin/css/dear-survey-admin.css', array(), DEAR_SURVEY_VERSION );
				wp_enqueue_script( 'dear-survey-admin-js', DEAR_SURVEY_URL . 'admin/js/dear-survey-admin.js', array( 'jquery' ), DEAR_SURVEY_VERSION, true );
			} );
		}
	}

	private function define_public_hooks() {
		$shortcode = new Dear_Survey_Shortcode( $this->db );
		add_shortcode( 'dear_survey', array( $shortcode, 'render' ) );

		$submit = new Dear_Survey_Submit( $this->db );
		add_action( 'wp_ajax_ds_submit_survey', array( $submit, 'ajax_handle_submit' ) );
		add_action( 'wp_ajax_nopriv_ds_submit_survey', array( $submit, 'ajax_handle_submit' ) );

		add_action( 'wp_enqueue_scripts', function() {
			wp_enqueue_style( 'dear-survey-front-css', DEAR_SURVEY_URL . 'public/css/dear-survey-public.css', array(), DEAR_SURVEY_VERSION );
			wp_enqueue_script( 'dear-survey-front', DEAR_SURVEY_URL . 'public/js/dear-survey-public.js', array( 'jquery' ), DEAR_SURVEY_VERSION, true );
			wp_localize_script( 'dear-survey-front', 'dear_survey_obj', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		} );
	}
}

// Run the plugin
function run_dear_survey() {
	$plugin = new Dear_Survey();
}
run_dear_survey();
