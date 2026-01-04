<?php
/*
 * Plugin Name: Dear Survey 
 * Plugin URI:  https://wordpress.org/plugins/dear-survey
 * Description: An advanced, high-performance Survey Maker with a premium SaaS-style interface.
 * Version:     1.0.0
 * Author:      Your Name
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: dear-survey
 */



if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Global DB variable for usage in other classes
global $dearsurvey_db;

// Define constants
define( 'DEAR_SURVEY_VERSION', '1.0.0' );
define( 'DEAR_SURVEY_PATH', plugin_dir_path( __FILE__ ) );
define( 'DEAR_SURVEY_URL', plugin_dir_url( __FILE__ ) );
define( 'DEAR_SURVEY_DB_VERSION', '2.0' ); // Major version bump for CPT migration
define( 'DEAR_SURVEY_DEBUG', false ); // Set to false for production

// Include core classes
require_once DEAR_SURVEY_PATH . 'includes/class-dear-survey-db.php';
require_once DEAR_SURVEY_PATH . 'includes/class-dear-survey-activator.php';

// Activation hook
register_activation_hook( __FILE__, array( 'DearSurvey_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'DearSurvey_Activator', 'deactivate' ) );

// Main Plugin Class
class DearSurvey {
	protected $db;
	public function __construct() {
		global $dearsurvey_db;
		$this->db = new DearSurvey_DB();
		$dearsurvey_db = $this->db;
		
		// Register CPTs on init hook (required - cannot call register_post_type before init)
		add_action( 'init', array( $this->db, 'register_post_types' ), 5 );
		
		// Check version after init when CPTs are registered
		add_action( 'init', array( $this, 'check_version' ), 10 );
		
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	public function check_version() {
		if ( get_option( 'dearsurvey_db_version' ) != DEAR_SURVEY_DB_VERSION ) {
			DearSurvey_Activator::activate();
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
			$menu = new DearSurvey_Menu( $this->db );
			add_action( 'admin_menu', array( $menu, 'register_menus' ) );
			
			// AJAX for builder
			$builder = new DearSurvey_Builder( $this->db );
			add_action( 'wp_ajax_ds_save_survey', array( $builder, 'ajax_save_survey' ) );
			add_action( 'wp_ajax_ds_send_outreach', array( $builder, 'ajax_send_outreach' ) );
			
			// Enqueue Admin CSS & JS
			add_action( 'admin_enqueue_scripts', function() {
				wp_enqueue_style( 'dear-survey-admin', DEAR_SURVEY_URL . 'admin/css/dear-survey-admin.css', array(), DEAR_SURVEY_VERSION );
				wp_enqueue_script( 'dear-survey-admin-js', DEAR_SURVEY_URL . 'admin/js/dear-survey-admin.js', array( 'jquery' ), DEAR_SURVEY_VERSION, true );
			} );
		}
	}

	private function define_public_hooks() {
		$shortcode = new DearSurvey_Shortcode( $this->db );
		add_shortcode( 'dearsurvey', array( $shortcode, 'render' ) );

		$submit = new DearSurvey_Submit( $this->db );
		add_action( 'wp_ajax_ds_submit_survey', array( $submit, 'ajax_handle_submit' ) );
		add_action( 'wp_ajax_nopriv_ds_submit_survey', array( $submit, 'ajax_handle_submit' ) );

		add_action( 'wp_enqueue_scripts', function() {
			wp_enqueue_style( 'dear-survey-front-css', DEAR_SURVEY_URL . 'public/css/dear-survey-public.css', array(), DEAR_SURVEY_VERSION );
			wp_enqueue_script( 'dear-survey-front', DEAR_SURVEY_URL . 'public/js/dear-survey-public.js', array( 'jquery' ), DEAR_SURVEY_VERSION, true );
			wp_localize_script( 'dear-survey-front', 'dearsurvey_obj', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'ds_submit_survey_nonce' ),
			) );
		} );
	}
}

// Run the plugin
function dearsurvey_run() {
	$plugin = new DearSurvey();
}
dearsurvey_run();
