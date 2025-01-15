<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 
 * This is the main class that is responsible for registering the core functions, including the files and setting up all features.
 * 
 */

if ( ! class_exists( 'User_Forms' ) ) :

	/**
	 * Main User_Forms Class.
	 *
	 * @package		USERFORMS
	 * @subpackage	Classes/User_Forms
	 * @since		1.0.0
	 * @author		Tushar Kapdi
	 */
	final class User_Forms {

		/**
		 * The class instance
		 *
		 * @access	private
		 * @since	1.0.0
		 * @var		object|User_Forms
		 */
		private static $instance;

		/**
		 * Throw error on object clone.
		 *
		 * Cloning instances of the class is forbidden.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'You are not allowed to clone this class.', 'user-forms' ), '1.0.0' );
		}

		/**
		 * Disable unserializing of the class.
		 *
		 * @access	public
		 * @since	1.0.0
		 * @return	void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'You are not allowed to unserialize this class.', 'user-forms' ), '1.0.0' );
		}

		/**
		 * Main User_Forms Instance.
		 *
		 * @access		public
		 * @since		1.0.0
		 * @static
		 * @return		object|User_Forms
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof User_Forms ) ) {
				self::$instance					= new User_Forms;
				//self::$instance->base_hooks();
				self::$instance->includes();
				self::$instance->menu			= new User_Forms_Menu();
				self::$instance->shortcodes		= new User_Forms_Shortcodes();
				
				//Run the plugin base code
				new User_Forms_Run();

				/**
				 * Add a custom action to allow dependencies after the successful plugin setup
				 */
				do_action( 'USERFORMS_plugin_loaded' );
			}

			return self::$instance;
		}

		/**
		 * Include required files.
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function includes() {
			require_once USERFORMS_PLUGIN_DIR . 'lib/includes/classes/class-user-forms-menu.php';
			require_once USERFORMS_PLUGIN_DIR . 'lib/includes/classes/shortcodes/class-user-forms-shortcodes.php';

			require_once USERFORMS_PLUGIN_DIR . 'lib/includes/classes/class-user-forms-run.php';
		}

		/**
		 * Add base hooks for the core functionality
		 *
		 * @access  private
		 * @since   1.0.0
		 * @return  void
		 */
		private function base_hooks() {
			//add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
		}

	}

endif; // End if class_exists check.