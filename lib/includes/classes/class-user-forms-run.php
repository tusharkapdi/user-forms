<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 
 * All the other registered classed which are controlled and managed by this class.
 * 
 */

/**
 * Class User_Forms_Run
 *
 * @package		USERFORMS
 * @subpackage	Classes/User_Forms_Run
 * @author		Tushar Kapdi
 * @since		1.0.0
 */
class User_Forms_Run{

	/**
	 * User_Forms_Run constructor 
	 *
	 * @since 1.0.0
	 */
	function __construct(){
		$this->add_hooks();
	}

	/**
	 * WordPress and plugin related hooks
	 *
	 * @access	private
	 * @since	1.0.0
	 * @return	void
	 */
	private function add_hooks(){
	
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_backend_scripts_and_styles' ), 20 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts_and_styles' ), 20 );	
	}

	/**
	 * Enqueue the admin scripts and styles for this plugin.
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function enqueue_backend_scripts_and_styles($hook) {

		//if($hook == 'settings_page_user-forms'){
		//if($hook == 'toplevel_page_user-forms'){
	   if($hook == 'user-forms_page_user-forms-settings'){

			wp_register_script( 'userforms-backend-scripts', USERFORMS_PLUGIN_URL . 'lib/includes/assets/js/admin-min.js', array( 'jquery' ), USERFORMS_VERSION, true );
			wp_enqueue_script('userforms-backend-scripts');

			wp_enqueue_style( 'wp-color-picker' );
	        wp_enqueue_script( 'wp-color-picker');

	        wp_enqueue_script( 'jquery-ui-sortable' );
	    }
	}


	/**
	 * Enqueue the frontend scripts and styles for this plugin.
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function enqueue_frontend_scripts_and_styles() {
		wp_enqueue_style( 'userforms-frontend-styles', USERFORMS_PLUGIN_URL . 'lib/includes/assets/css/style.css', array(), USERFORMS_VERSION, 'all' );
		wp_enqueue_script( 'userforms-frontend-scripts', USERFORMS_PLUGIN_URL . 'lib/includes/assets/js/script-min.js', array( 'jquery' ), USERFORMS_VERSION, true );
		wp_localize_script( 'userforms-frontend-scripts', 'userforms', array(
			'ajaxurl' 			=> admin_url( 'admin-ajax.php' ),
			'security_nonce'	=> wp_create_nonce( "USERFORMS-nonce" ),
		));
	}
}