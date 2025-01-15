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
		add_action( 'admin_print_styles', array( $this, 'backend_inline_styles' ) );
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

	        wp_add_inline_script( 'wp-color-picker', 'jQuery(document).ready(function($){$(".color-picker").wpColorPicker();$("table.form-table-tk tbody").sortable();});');
	    }
	}

	/**
	 * Add admin inline styles for this plugin.
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function backend_inline_styles() {

		$screen = get_current_screen();
		if($screen->id == 'user-forms_page_user-forms-settings'){

			echo "<style>";
		  	echo '.form-table th{font-weight:400;}.form-table i{font-size:12px;}.form-table-tk thead th{text-align: center;font-weight: 600;padding-right: 0}.form-table-tk tbody tr td{text-align: center;}.form-table-tk select, .form-table-tk input[type="text"], .form-table-tk textarea{width: 100%}.form-table-tk textarea{display: none;}.removefield{cursor: pointer;}.movefield{cursor: move;}.custom-fields-tr > th{display:none}.custom-fields-tr > td{padding: 0}.bluebg{background:royalblue;color:white}.bluebg th{color:white}'; 
		   	echo "</style>"; 
	    }
	    if($screen->id == 'toplevel_page_user-forms'){

			echo "<style>";
		  	echo '.bluebg{background:royalblue;color:white}.bluebg th{color:white}.unorderlist {margin: 1em 0 1em 1em ;}.unorderlist li{list-style-type: disc;}'; 
		   	echo "</style>"; 
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

		$options = get_option( 'user_forms_opt' );
		if( !empty($options['color']) ){
			$custom_css = "
			.userforms-login #wp-submit{ background: {$options['color']};}
			";
			wp_add_inline_style( 'userforms-frontend-styles', $custom_css );
		}
	}
}