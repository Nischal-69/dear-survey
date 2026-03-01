<?php
/**
 * Plugin Name: Formera
 * Plugin URI:  https://wordpress.org/plugins/formera
 * Description: An advanced, high-performance Form Maker with a premium SaaS-style interface.
 * Version:     1.0.1
 * Author:      nischal01
 * Author URI: https://profiles.wordpress.org/nischal01/
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: formera
 */



if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Global DB variable for usage in other classes
global $formera_db;

// Define constants
define( 'FORMERA_VERSION', '1.0.1' );
define( 'FORMERA_PATH', plugin_dir_path( __FILE__ ) );
define( 'FORMERA_URL', plugin_dir_url( __FILE__ ) );
define( 'FORMERA_DB_VERSION', '2.0' ); // Major version bump for CPT migration
define( 'FORMERA_DEBUG', false ); // Set to false for production

// Include core classes
require_once FORMERA_PATH . 'includes/class-formera-db.php';
require_once FORMERA_PATH . 'includes/class-formera-activator.php';

// Activation hook
register_activation_hook( __FILE__, array( 'Formera_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Formera_Activator', 'deactivate' ) );

// Main Plugin Class
class Formera {
	protected $db;
	public function __construct() {
		global $formera_db;
		$this->db = new Formera_DB();
		$formera_db = $this->db;
		
		// Register CPTs on init hook (required - cannot call register_post_type before init)
		add_action( 'init', array( $this->db, 'register_post_types' ), 5 );
		
		// Check version after init when CPTs are registered
		add_action( 'init', array( $this, 'check_version' ), 10 );
		
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		
		// Register AJAX handlers on init to ensure they're available for AJAX requests
		add_action( 'init', array( $this, 'register_ajax_handlers' ) );
		
		// Register Gutenberg block
		add_action( 'init', array( $this, 'register_gutenberg_block' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
		add_filter( 'block_categories_all', array( $this, 'add_custom_block_category' ), 10, 2 );
	}
	
	public function register_ajax_handlers() {
		// Public AJAX handlers
		$submit = new Formera_Submit( $this->db );
		add_action( 'wp_ajax_formera_submit_survey', array( $submit, 'ajax_handle_submit' ) );
		add_action( 'wp_ajax_nopriv_formera_submit_survey', array( $submit, 'ajax_handle_submit' ) );
		
		// Admin AJAX handlers
		if ( is_admin() ) {
			$builder = new Formera_Builder( $this->db );
			add_action( 'wp_ajax_formera_save_survey', array( $builder, 'ajax_save_survey' ) );
			add_action( 'wp_ajax_formera_send_outreach', array( $builder, 'ajax_send_outreach' ) );

			$templates = new Formera_Templates( $this->db );
			add_action( 'wp_ajax_formera_use_template', array( $templates, 'ajax_use_template' ) );
		}
	}

	public function check_version() {
		if ( get_option( 'formera_db_version' ) != FORMERA_DB_VERSION ) {
			Formera_Activator::activate();
		}
	}

	private function load_dependencies() {
		// Admin
		if ( is_admin() ) {
			require_once FORMERA_PATH . 'admin/menu.php';
			require_once FORMERA_PATH . 'admin/survey-builder.php';
			require_once FORMERA_PATH . 'admin/results.php';
			require_once FORMERA_PATH . 'admin/settings.php';
			require_once FORMERA_PATH . 'admin/templates.php';
		}

		// Public
		require_once FORMERA_PATH . 'public/shortcode.php';
		require_once FORMERA_PATH . 'public/submit.php';
	}

	private function define_admin_hooks() {
		if ( is_admin() ) {
			$menu = new Formera_Menu( $this->db );
			add_action( 'admin_menu', array( $menu, 'register_menus' ) );
			
			// Enqueue Admin CSS & JS only on Formera plugin pages
			add_action( 'admin_enqueue_scripts', function( $hook ) {
				// Only load on Formera admin pages
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only check of admin page slug, no data processing.
				if ( ! isset( $_GET['page'] ) || strpos( sanitize_text_field( wp_unslash( $_GET['page'] ) ), 'formera' ) !== 0 ) {
					return;
				}
				wp_enqueue_style( 'formera-admin', FORMERA_URL . 'admin/css/formera-admin.css', array(), FORMERA_VERSION );
				wp_enqueue_script( 'formera-admin-js', FORMERA_URL . 'admin/js/formera-admin.js', array( 'jquery' ), FORMERA_VERSION, true );
				wp_localize_script( 'formera-admin-js', 'formera_admin', array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => wp_create_nonce( 'formera_admin_nonce' ),
				) );
			} );
		}
	}

	private function define_public_hooks() {
		$shortcode = new Formera_Shortcode( $this->db );
		add_shortcode( 'formera', array( $shortcode, 'render' ) );

		add_action( 'wp_enqueue_scripts', function() {
			wp_enqueue_style( 'formera-front-css', FORMERA_URL . 'public/css/formera-public.css', array(), FORMERA_VERSION );
			wp_enqueue_script( 'formera-front', FORMERA_URL . 'public/js/formera-public.js', array( 'jquery' ), FORMERA_VERSION, true );
			wp_localize_script( 'formera-front', 'formera_obj', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'formera_submit_survey_nonce' ),
			) );
		} );
	}
	
	/**
	 * Register Gutenberg block
	 */
	public function register_gutenberg_block() {
		// Register the block server-side
		register_block_type( 'formera/survey', array(
			'attributes' => array(
				'surveyId' => array(
					'type' => 'string',
					'default' => '',
				),
				'align' => array(
					'type' => 'string',
					'default' => '',
				),
			),
			'render_callback' => array( $this, 'render_survey_block' ),
		) );
	}
	
	/**
	 * Add custom block category for Formera
	 */
	public function add_custom_block_category( $categories, $post ) {
		return array_merge(
			array(
				array(
					'slug'  => 'formera',
					'title' => __( 'Formera', 'formera' ),
					'icon'  => 'feedback',
				),
			),
			$categories
		);
	}
	
	/**
	 * Enqueue block editor assets
	 */
	public function enqueue_block_editor_assets() {
		wp_enqueue_script(
			'formera-block',
			FORMERA_URL . 'admin/js/formera-block.js',
			array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-data', 'wp-block-editor' ),
			FORMERA_VERSION,
			true
		);
		
		wp_enqueue_style(
			'formera-block-editor',
			FORMERA_URL . 'admin/css/formera-block.css',
			array(),
			FORMERA_VERSION
		);
	}
	
	/**
	 * Render survey block on frontend
	 */
	public function render_survey_block( $attributes ) {
		if ( empty( $attributes['surveyId'] ) ) {
			return '<div class="formera-block-error"><p>' . __( 'Please select a form to display.', 'formera' ) . '</p></div>';
		}
		
		// Build wrapper classes
		$classes = array( 'wp-block-formera-survey' );
		if ( ! empty( $attributes['align'] ) ) {
			$classes[] = 'align' . $attributes['align'];
		}
		
		// Use existing shortcode functionality
		$shortcode = new Formera_Shortcode( $this->db );
		$content = $shortcode->render( array( 'id' => intval( $attributes['surveyId'] ) ) );
		
		return sprintf(
			'<div class="%s">%s</div>',
			esc_attr( implode( ' ', $classes ) ),
			$content
		);
	}
	
}

// Run the plugin
function formera_run() {
	$plugin = new Formera();
}
formera_run();
