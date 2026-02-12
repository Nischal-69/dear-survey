<?php
/**
 * Plugin Name: Formera
 * Plugin URI:  https://wordpress.org/plugins/formera
 * Description: An advanced, high-performance Survey Maker with a premium SaaS-style interface.
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
		
		// Add to WordPress admin bar
		add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_menu' ), 100 );
		add_action( 'wp_head', array( $this, 'admin_bar_styles' ) );
		add_action( 'admin_head', array( $this, 'admin_bar_styles' ) );
		
		// Add dashboard widget
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
		add_action( 'wp_network_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
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
		}

		// Public
		require_once FORMERA_PATH . 'public/shortcode.php';
		require_once FORMERA_PATH . 'public/submit.php';
	}

	private function define_admin_hooks() {
		if ( is_admin() ) {
			$menu = new Formera_Menu( $this->db );
			add_action( 'admin_menu', array( $menu, 'register_menus' ) );
			
			// Enqueue Admin CSS & JS
			add_action( 'admin_enqueue_scripts', function() {
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
			FORMERA_VERSION
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
			return '<div class="formera-block-error"><p>' . __( 'Please select a survey to display.', 'formera' ) . '</p></div>';
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
	
	/**
	 * Add Formera to WordPress admin bar
	 */
	public function add_admin_bar_menu( $wp_admin_bar ) {
		// Only show to users who can manage options
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		
		// Get survey count for badge
		$surveys = $this->db->get_surveys();
		$survey_count = count( $surveys );
		
		// Main Formera menu item
		$wp_admin_bar->add_menu( array(
			'id'    => 'formera',
			'title' => '<span class="ab-icon dashicons-feedback" style="font-family: dashicons; font-size: 20px; line-height: 1; margin-right: 6px; color: #0d9488;"></span>Formera <span class="formera-count" style="background: #0d9488; color: white; border-radius: 10px; padding: 2px 6px; font-size: 11px; margin-left: 4px;">' . $survey_count . '</span>',
			'href'  => admin_url( 'admin.php?page=formera' ),
			'meta'  => array(
				'title' => __( 'Formera Surveys', 'formera' ),
			),
		) );
		
		// Submenu items
		$wp_admin_bar->add_menu( array(
			'id'     => 'formera-dashboard',
			'parent' => 'formera',
			'title'  => __( 'Dashboard', 'formera' ),
			'href'   => admin_url( 'admin.php?page=formera' ),
		) );
		
		$wp_admin_bar->add_menu( array(
			'id'     => 'formera-surveys',
			'parent' => 'formera',
			'title'  => __( 'All Surveys', 'formera' ),
			'href'   => admin_url( 'admin.php?page=formera-list' ),
		) );
		
		$wp_admin_bar->add_menu( array(
			'id'     => 'formera-new',
			'parent' => 'formera',
			'title'  => '<span style="color: #0d9488; font-weight: 600;">+ New Survey</span>',
			'href'   => admin_url( 'admin.php?page=formera-builder' ),
		) );
		
		$wp_admin_bar->add_menu( array(
			'id'     => 'formera-contacts',
			'parent' => 'formera',
			'title'  => __( 'Contact List', 'formera' ),
			'href'   => admin_url( 'admin.php?page=formera-contacts' ),
		) );
		
		// Add recent surveys if any exist
		if ( ! empty( $surveys ) ) {
			$wp_admin_bar->add_group( array(
				'id'     => 'formera-recent-group',
				'parent' => 'formera',
				'meta'   => array(
					'class' => 'ab-sub-secondary',
				),
			) );
			
			$wp_admin_bar->add_menu( array(
				'id'     => 'formera-recent-header',
				'parent' => 'formera-recent-group',
				'title'  => '<strong style="color: #5f6368; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Recent Surveys</strong>',
				'meta'   => array(
					'class' => 'ab-sub-secondary',
				),
			) );
			
			// Show last 3 surveys
			$recent_surveys = array_slice( $surveys, 0, 3 );
			foreach ( $recent_surveys as $survey ) {
				$responses = $this->db->get_responses( $survey['id'] );
				$response_count = count( $responses );
				
				$wp_admin_bar->add_menu( array(
					'id'     => 'formera-survey-' . $survey['id'],
					'parent' => 'formera-recent-group',
					'title'  => esc_html( $survey['title'] ) . ' <span style="color: #5f6368; font-size: 11px;">(' . $response_count . ')</span>',
					'href'   => admin_url( 'admin.php?page=formera-builder&id=' . $survey['id'] ),
				) );
			}
		}
	}
	
	/**
	 * Add styles for admin bar menu
	 */
	public function admin_bar_styles() {
		if ( ! is_admin_bar_showing() || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<style type="text/css">
			#wpadminbar #wp-admin-bar-formera .ab-icon:before {
				content: '\f175';
				top: 2px;
			}
			
			#wpadminbar #wp-admin-bar-formera .formera-count {
				background: #0d9488 !important;
				color: white !important;
				border-radius: 10px;
				padding: 2px 6px;
				font-size: 11px;
				margin-left: 4px;
				font-weight: 600;
			}
			
			#wpadminbar .formera-recent-group .ab-sub-secondary {
				border-top: 1px solid #464646;
				margin-top: 6px;
				padding-top: 6px;
			}
			
			#wpadminbar #wp-admin-bar-formera-recent-header .ab-item {
				padding-top: 8px !important;
				padding-bottom: 4px !important;
			}
			
			#wpadminbar #wp-admin-bar-formera-new .ab-item {
				color: #0d9488;
				font-weight: 600;
			}
			
			#wpadminbar #wp-admin-bar-formera-new:hover .ab-item {
				background-color: #0d9488 !important;
				color: white !important;
			}
		</style>
		<?php
	}
	
	/**
	 * Add Formera dashboard widget
	 */
	public function add_dashboard_widget() {
		// Only show to users who can manage options
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		
		wp_add_dashboard_widget(
			'formera_dashboard_widget',
			'🔹 Formera Surveys',
			array( $this, 'render_dashboard_widget' ),
			null,
			null,
			'side',
			'high'
		);
		
		// Also add a normal width widget for better visibility
		wp_add_dashboard_widget(
			'formera_dashboard_main',
			'📊 Formera Survey Manager',
			array( $this, 'render_main_dashboard_widget' ),
			null,
			null,
			'normal',
			'high'
		);
		
		// Move Formera widgets to top
		global $wp_meta_boxes;
		
		// Move side widget to top
		if ( isset( $wp_meta_boxes['dashboard']['side']['core']['formera_dashboard_widget'] ) ) {
			$side_dashboard = $wp_meta_boxes['dashboard']['side']['core'];
			$formera_widget_backup = array( 'formera_dashboard_widget' => $side_dashboard['formera_dashboard_widget'] );
			unset( $side_dashboard['formera_dashboard_widget'] );
			$side_dashboard = array_merge( $formera_widget_backup, $side_dashboard );
			$wp_meta_boxes['dashboard']['side']['core'] = $side_dashboard;
		}
		
		// Move main widget to top
		if ( isset( $wp_meta_boxes['dashboard']['normal']['core']['formera_dashboard_main'] ) ) {
			$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
			$formera_main_backup = array( 'formera_dashboard_main' => $normal_dashboard['formera_dashboard_main'] );
			unset( $normal_dashboard['formera_dashboard_main'] );
			$normal_dashboard = array_merge( $formera_main_backup, $normal_dashboard );
			$wp_meta_boxes['dashboard']['normal']['core'] = $normal_dashboard;
		}
	}
	
	/**
	 * Render dashboard widget content (side widget - compact)
	 */
	public function render_dashboard_widget() {
		$surveys = $this->db->get_surveys();
		$all_responses = $this->db->get_all_responses();
		$total_surveys = count( $surveys );
		$total_responses = count( $all_responses );
		
		?>
		<div class="formera-side-widget">
			<style>
				.formera-side-widget {
					font-family: -apple-system, BlinkMacSystemFont, sans-serif;
				}
				.formera-side-stats {
					display: grid;
					grid-template-columns: 1fr 1fr;
					gap: 10px;
					margin-bottom: 15px;
				}
				.formera-side-stat {
					background: #f8fafc;
					padding: 12px;
					border-radius: 6px;
					text-align: center;
					border: 1px solid #e2e8f0;
				}
				.formera-side-number {
					font-size: 20px;
					font-weight: 700;
					color: #0d9488;
					display: block;
				}
				.formera-side-label {
					font-size: 11px;
					color: #64748b;
					margin-top: 2px;
					text-transform: uppercase;
					letter-spacing: 0.5px;
				}
				.formera-side-actions {
					display: flex;
					flex-direction: column;
					gap: 8px;
				}
				.formera-side-btn {
					padding: 10px 16px;
					border-radius: 6px;
					text-decoration: none;
					font-size: 13px;
					font-weight: 600;
					text-align: center;
					display: flex;
					align-items: center;
					justify-content: center;
					gap: 6px;
					transition: all 0.2s ease;
				}
				.formera-side-btn-primary {
					background: #0d9488;
					color: white;
				}
				.formera-side-btn-primary:hover {
					background: #0f766e;
					color: white;
				}
				.formera-side-btn-secondary {
					background: #f8fafc;
					color: #475569;
					border: 1px solid #e2e8f0;
				}
				.formera-side-btn-secondary:hover {
					background: #e2e8f0;
					color: #334155;
				}
			</style>
			
			<div class="formera-side-stats">
				<div class="formera-side-stat">
					<span class="formera-side-number"><?php echo esc_html( $total_surveys ); ?></span>
					<div class="formera-side-label">Surveys</div>
				</div>
				<div class="formera-side-stat">
					<span class="formera-side-number"><?php echo esc_html( $total_responses ); ?></span>
					<div class="formera-side-label">Responses</div>
				</div>
			</div>
			
			<div class="formera-side-actions">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=formera-builder' ) ); ?>" class="formera-side-btn formera-side-btn-primary">
					<span class="dashicons dashicons-plus-alt" style="font-size: 14px; width: 14px; height: 14px;"></span>
					New Survey
				</a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=formera' ) ); ?>" class="formera-side-btn formera-side-btn-secondary">
					<span class="dashicons dashicons-dashboard" style="font-size: 14px; width: 14px; height: 14px;"></span>
					Open Formera
				</a>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Render main dashboard widget content (full width)
	 */
	public function render_main_dashboard_widget() {
		$surveys = $this->db->get_surveys();
		$all_responses = $this->db->get_all_responses();
		$total_surveys = count( $surveys );
		$total_responses = count( $all_responses );
		
		// Get recent activity (last 7 days)
		$recent_responses = array_filter( $all_responses, function( $response ) {
			return strtotime( $response['created_at'] ) > ( time() - 7 * 24 * 60 * 60 );
		});
		$recent_count = count( $recent_responses );
		
		?>
		<div class="formera-main-widget">
			<style>
				.formera-main-widget {
					font-family: -apple-system, BlinkMacSystemFont, sans-serif;
				}
				.formera-main-header {
					display: flex;
					align-items: center;
					justify-content: space-between;
					margin-bottom: 20px;
					padding-bottom: 15px;
					border-bottom: 2px solid #0d9488;
				}
				.formera-main-title {
					display: flex;
					align-items: center;
					gap: 12px;
				}
				.formera-main-logo {
					width: 40px;
					height: 40px;
					background: linear-gradient(135deg, #0d9488, #06b6d4);
					border-radius: 8px;
					display: flex;
					align-items: center;
					justify-content: center;
					color: white;
					font-size: 20px;
				}
				.formera-main-title-text {
					margin: 0;
					font-size: 18px;
					color: #0d9488;
					font-weight: 600;
				}
				.formera-main-subtitle {
					margin: 0;
					font-size: 12px;
					color: #64748b;
				}
				.formera-main-stats {
					display: grid;
					grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
					gap: 15px;
					margin-bottom: 20px;
				}
				.formera-main-stat {
					background: #f8fafc;
					padding: 15px;
					border-radius: 8px;
					text-align: center;
					border: 1px solid #e2e8f0;
				}
				.formera-main-stat-number {
					font-size: 24px;
					font-weight: 700;
					color: #0d9488;
					display: block;
				}
				.formera-main-stat-label {
					font-size: 12px;
					color: #64748b;
					margin-top: 4px;
					text-transform: uppercase;
					letter-spacing: 0.5px;
				}
				.formera-main-content {
					display: grid;
					grid-template-columns: 1fr auto;
					gap: 20px;
					align-items: start;
				}
				.formera-main-surveys {
					background: #ffffff;
					border: 1px solid #e2e8f0;
					border-radius: 8px;
					padding: 15px;
				}
				.formera-main-surveys-title {
					font-size: 14px;
					font-weight: 600;
					color: #334155;
					margin: 0 0 12px 0;
				}
				.formera-survey-row {
					display: flex;
					justify-content: space-between;
					align-items: center;
					padding: 8px 0;
					border-bottom: 1px solid #f1f5f9;
				}
				.formera-survey-row:last-child {
					border-bottom: none;
				}
				.formera-survey-link {
					color: #475569;
					text-decoration: none;
					font-weight: 500;
				}
				.formera-survey-link:hover {
					color: #0d9488;
				}
				.formera-survey-badge {
					background: #0d9488;
					color: white;
					padding: 2px 8px;
					border-radius: 12px;
					font-size: 11px;
					font-weight: 600;
				}
				.formera-main-actions {
					display: flex;
					flex-direction: column;
					gap: 10px;
					min-width: 140px;
				}
				.formera-main-btn {
					padding: 10px 16px;
					border-radius: 6px;
					text-decoration: none;
					font-size: 13px;
					font-weight: 500;
					display: inline-flex;
					align-items: center;
					justify-content: center;
					gap: 6px;
					transition: all 0.2s ease;
					text-align: center;
				}
				.formera-main-btn-primary {
					background: #0d9488;
					color: white;
				}
				.formera-main-btn-primary:hover {
					background: #0f766e;
					color: white;
				}
				.formera-main-btn-secondary {
					background: #f8fafc;
					color: #475569;
					border: 1px solid #e2e8f0;
				}
				.formera-main-btn-secondary:hover {
					background: #e2e8f0;
					color: #334155;
				}
				.formera-empty-state {
					text-align: center;
					padding: 30px 20px;
					color: #64748b;
					background: #ffffff;
					border: 1px solid #e2e8f0;
					border-radius: 8px;
				}
			</style>
			
			<div class="formera-main-header">
				<div class="formera-main-title">
					<div class="formera-main-logo">
						<span class="dashicons dashicons-feedback"></span>
					</div>
					<div>
						<h3 class="formera-main-title-text">Formera Surveys</h3>
						<p class="formera-main-subtitle">Complete survey management system</p>
					</div>
				</div>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=formera' ) ); ?>" class="formera-main-btn formera-main-btn-primary">
					Open Dashboard
				</a>
			</div>
			
			<div class="formera-main-stats">
				<div class="formera-main-stat">
					<span class="formera-main-stat-number"><?php echo esc_html( $total_surveys ); ?></span>
					<div class="formera-main-stat-label">Total Surveys</div>
				</div>
				<div class="formera-main-stat">
					<span class="formera-main-stat-number"><?php echo esc_html( $total_responses ); ?></span>
					<div class="formera-main-stat-label">Total Responses</div>
				</div>
				<div class="formera-main-stat">
					<span class="formera-main-stat-number"><?php echo esc_html( $recent_count ); ?></span>
					<div class="formera-main-stat-label">This Week</div>
				</div>
				<div class="formera-main-stat">
					<span class="formera-main-stat-number"><?php echo $total_responses > 0 ? esc_html( round( $total_responses / $total_surveys ) ) : '0'; ?></span>
					<div class="formera-main-stat-label">Avg. per Survey</div>
				</div>
			</div>
			
			<div class="formera-main-content">
				<div class="formera-main-surveys">
					<?php if ( ! empty( $surveys ) ) : ?>
						<h4 class="formera-main-surveys-title">Recent Surveys</h4>
						<?php 
						$recent_surveys = array_slice( $surveys, 0, 5 );
						foreach ( $recent_surveys as $survey ) :
							$responses = $this->db->get_responses( $survey['id'] );
							$response_count = count( $responses );
						?>
							<div class="formera-survey-row">
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=formera-builder&id=' . $survey['id'] ) ); ?>" class="formera-survey-link">
									<?php echo esc_html( $survey['title'] ); ?>
								</a>
								<span class="formera-survey-badge"><?php echo esc_html( $response_count ); ?></span>
							</div>
						<?php endforeach; ?>
						
						<?php if ( count( $surveys ) > 5 ) : ?>
							<div style="text-align: center; margin-top: 12px;">
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=formera-list' ) ); ?>" style="font-size: 12px; color: #0d9488; text-decoration: none;">
									View all <?php echo esc_html( count( $surveys ) ); ?> surveys →
								</a>
							</div>
						<?php endif; ?>
					<?php else : ?>
						<div class="formera-empty-state">
							<span class="dashicons dashicons-feedback" style="font-size: 48px; opacity: 0.3; margin-bottom: 12px;"></span>
							<p><strong>No surveys created yet!</strong></p>
							<p style="margin-bottom: 16px;">Create your first survey to start collecting responses.</p>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=formera-builder' ) ); ?>" class="formera-main-btn formera-main-btn-primary">
								Create Your First Survey
							</a>
						</div>
					<?php endif; ?>
				</div>
				
				<div class="formera-main-actions">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=formera-builder' ) ); ?>" class="formera-main-btn formera-main-btn-primary">
						<span class="dashicons dashicons-plus-alt" style="font-size: 14px; width: 14px; height: 14px;"></span>
						New Survey
					</a>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=formera-list' ) ); ?>" class="formera-main-btn formera-main-btn-secondary">
						<span class="dashicons dashicons-list-view" style="font-size: 14px; width: 14px; height: 14px;"></span>
						All Surveys
					</a>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=formera-contacts' ) ); ?>" class="formera-main-btn formera-main-btn-secondary">
						<span class="dashicons dashicons-groups" style="font-size: 14px; width: 14px; height: 14px;"></span>
						Contacts
					</a>
				</div>
			</div>
		</div>
		<?php
	}
}

// Run the plugin
function formera_run() {
	$plugin = new Formera();
}
formera_run();
