<?php
/**
 * User Forms
 *
 * @package       USERFORMS
 * @author        Tushar Kapdi
 * @license       gplv2
 * @version       1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:   User Forms
 * Plugin URI:    http://amplebrain.com/plugins/user-forms
 * Description:   WordPress user management including login, register, forgot password forms. Build custom registration & login and forgot password forms with ajax effect.
 * Version:       1.0.0
 * Requires at least: 4.9
 * Requires PHP:  7.2
 * Author:        Tushar Kapdi
 * Author URI:    http://amplebrain.com/
 * Text Domain:   user-forms
 * Domain Path:   /languages
 * License:       GPLv2
 * License URI:   https://www.gnu.org/licenses/gpl-2.0.html
 *
 * You should have received a copy of the GNU General Public License
 * along with User Forms. If not, see <https://www.gnu.org/licenses/gpl-2.0.html/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 
 * This file contains the main information about the plugin.
 * It is used to register all components necessary to run the plugin.
 * 
 * The function USERFORMS() is the main function.
 * 
 */

// Plugin name
define( 'USERFORMS_NAME',			'User Forms' );

// Plugin version
define( 'USERFORMS_VERSION',		'1.0.0' );

// Plugin Root File
define( 'USERFORMS_PLUGIN_FILE',	__FILE__ );

// Plugin base
define( 'USERFORMS_PLUGIN_BASE',	plugin_basename( USERFORMS_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'USERFORMS_PLUGIN_DIR',		plugin_dir_path( USERFORMS_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'USERFORMS_PLUGIN_URL',		plugin_dir_url( USERFORMS_PLUGIN_FILE ) );

/**
 * Load the main class for the basic functionality
 */
require_once USERFORMS_PLUGIN_DIR . 'lib/class-user-forms.php';

/**
 * The code that runs during plugin activation.
 *
 * @author  Tushar Kapdi
 * @since   1.0.0
 * @return  object|User_Forms
 */
function user_forms_activate() {

	if ( 'not-exists' === get_option( 'user_forms_opt', 'not-exists' ) ) {

		$default_fields = array('first_name'=>'First Name', 'last_name'=>'Last Name', 'log'=>'Username', 'email'=>'Email Address', 'pwd'=>'Password', 'cpwd'=>'Confirm Password');
	    foreach ($default_fields as $typ => $tit) {
	    	$options['fields']['type'][] = $typ;
	    	$options['fields']['slug'][] = $typ;
	    	$options['fields']['title'][] = $tit;
	    	$options['fields']['option'][] = '';
	    	$options['fields']['required'][] = '1';
	    }
		update_option( 'user_forms_opt', $options );
	}
}

register_activation_hook( __FILE__, 'user_forms_activate' );

/**
 * The main function to load the instance of our master class.
 *
 * @author  Tushar Kapdi
 * @since   1.0.0
 * @return  object|User_Forms
 */
function USERFORMS() {
	return User_Forms::instance();
}
$USERFORMS = USERFORMS();