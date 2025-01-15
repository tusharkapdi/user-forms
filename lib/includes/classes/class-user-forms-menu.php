<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 
 * Admin side managements class which is controlled and managed by this class.
 * 
 */

/**
 * Class User_Forms_Menu
 *
 * @package		USERFORMS
 * @subpackage	Classes/User_Forms_Menu
 * @author		Tushar Kapdi
 * @since		1.0.0
 */
class User_Forms_Menu{

	/**
     * Holds the values to be used in the fields callbacks
     * 
     * @since 1.0.0
     */
    private $options;

	/**
	 * User_Forms_Menu constructor 
	 *
	 * @since 1.0.0
	 */
	function __construct(){

		//variables
        $options = get_option( 'user_forms_opt' );

        if(!$options){
        	$this->options['login_page']=NULL;
            $this->options['register_page']=NULL;
            $this->options['forgot_page']=NULL;
            $this->options['color']=NULL;

            $this->options['show_remember']=NULL;
            $this->options['login_button']=NULL;
            $this->options['login_links']=NULL;
            $this->options['login_redirect']=NULL;

            $this->options['forgot_button']=NULL;
            $this->options['forgot_links']=NULL;
            $this->options['forgot_redirect']=NULL;

            $this->options['column']=NULL;
            $this->options['role']=NULL;
            $this->options['uore']=NULL;
            $this->options['show_first']=NULL;
            $this->options['show_last']=NULL;
            $this->options['show_confirm']=NULL;
            $this->options['register_links']=NULL;
            $this->options['register_button']=NULL;
            $this->options['register_redirect']=NULL;

            $default_fields = array('first_name'=>__('First Name','user-forms'), 'last_name'=>__('Last Name','user-forms'), 'log'=>__('Username','user-forms'), 'email'=>__('Email Address','user-forms'), 'pwd'=>__('Password','user-forms'), 'cpwd'=>__('Confirm Password','user-forms'));
            foreach ($default_fields as $typ => $tit) {
            	$this->options['fields']['type'][] = $typ;
            	$this->options['fields']['slug'][] = $typ;
            	$this->options['fields']['title'][] = $tit;
            	$this->options['fields']['option'][] = '';
            	$this->options['fields']['required'][] = '1';
            }
        }else{
            $this->options = $options;
        }

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
	
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );

        /*
        * Show Custom Fields details on user page on admin
        */
        add_action( 'show_user_profile', array( $this, 'add_custom_fields_details_on_user_profile_page' ) );
		add_action( 'edit_user_profile', array( $this, 'add_custom_fields_details_on_user_profile_page' ) );	
	}

    /**
	 * Add user form options page
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        /*add_options_page(
            __('Settings Admin', 'user-forms'), 
            __('User Forms', 'user-forms'), 
            'manage_options', 
            'user-forms', 
            array( $this, 'create_settings_admin_page' )
        );*/

        $eventa_icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPCFET0NUWVBFIHN2ZyBQVUJMSUMgIi0vL1czQy8vRFREIFNWRyAxLjEvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkIj4KPHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZlcnNpb249IjEuMSIgd2lkdGg9IjExMHB4IiBoZWlnaHQ9IjgwcHgiIHN0eWxlPSJzaGFwZS1yZW5kZXJpbmc6Z2VvbWV0cmljUHJlY2lzaW9uOyB0ZXh0LXJlbmRlcmluZzpnZW9tZXRyaWNQcmVjaXNpb247IGltYWdlLXJlbmRlcmluZzpvcHRpbWl6ZVF1YWxpdHk7IGZpbGwtcnVsZTpldmVub2RkOyBjbGlwLXJ1bGU6ZXZlbm9kZCIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPgo8Zz48cGF0aCBzdHlsZT0ib3BhY2l0eTowLjg2NCIgZmlsbD0iIzAwMDAwMCIgZD0iTSAzNi41LDc5LjUgQyAzMi4xNjY3LDc5LjUgMjcuODMzMyw3OS41IDIzLjUsNzkuNUMgMTIuNjQ1Miw3Ny42MzM1IDUuODExODYsNzEuMzAwMiAzLDYwLjVDIDEuNTYxMjksNDAuNTQ2IDEuMDYxMjksMjAuNTQ2IDEuNSwwLjVDIDQuMTY2NjcsMC41IDYuODMzMzMsMC41IDkuNSwwLjVDIDkuMDYzNjEsMTkuODc5NyA5LjU2MzYxLDM5LjIxMzEgMTEsNTguNUMgMTQuMDc1Miw2Ny4wNTY1IDIwLjI0MTksNzEuNzIzMiAyOS41LDcyLjVDIDQwLjA4NzIsNzIuMDc0NSA0Ni41ODcyLDY2Ljc0MTEgNDksNTYuNUMgNDkuNDk5OSwzNy44MzYzIDQ5LjY2NjYsMTkuMTY5NiA0OS41LDAuNUMgNTIuMTY2NywwLjUgNTQuODMzMywwLjUgNTcuNSwwLjVDIDU3Ljk1MzMsMjEuMjIwNSA1Ny40NTMzLDQxLjg4NzIgNTYsNjIuNUMgNTIuNzA2Miw3MS45NjczIDQ2LjIwNjIsNzcuNjMzOSAzNi41LDc5LjUgWiIvPjwvZz4KPGc+PHBhdGggc3R5bGU9Im9wYWNpdHk6MC44MjYiIGZpbGw9IiMwMDAwMDAiIGQ9Ik0gNjMuNSwwLjUgQyA3OC41LDAuNSA5My41LDAuNSAxMDguNSwwLjVDIDEwOC41LDIuODMzMzMgMTA4LjUsNS4xNjY2NyAxMDguNSw3LjVDIDk2LjUsNy41IDg0LjUsNy41IDcyLjUsNy41QyA3Mi41LDE2LjUgNzIuNSwyNS41IDcyLjUsMzQuNUMgODIuODMzMywzNC41IDkzLjE2NjcsMzQuNSAxMDMuNSwzNC41QyAxMDMuNSwzNi44MzMzIDEwMy41LDM5LjE2NjcgMTAzLjUsNDEuNUMgOTMuMTY2Nyw0MS41IDgyLjgzMzMsNDEuNSA3Mi41LDQxLjVDIDcyLjUsNTMuNSA3Mi41LDY1LjUgNzIuNSw3Ny41QyA2OS41LDc3LjUgNjYuNSw3Ny41IDYzLjUsNzcuNUMgNjMuNSw1MS44MzMzIDYzLjUsMjYuMTY2NyA2My41LDAuNSBaIi8+PC9nPgo8L3N2Zz4K';

        // This page will be saparate top level menu "User Forms"
        add_menu_page( __( 'User Forms', 'user-forms' ), __( 'User Forms', 'user-forms' ), 'manage_options', 'user-forms', array( $this, 'create_dashboard_admin_page' ), $eventa_icon, '71' );
        add_submenu_page('user-forms', '', '', 'manage_options', 'user-forms');
        remove_submenu_page( 'user-forms', 'user-forms' );
        add_submenu_page( 'user-forms', __( 'Dashboard', 'user-forms' ), __( 'Dashboard', 'user-forms' ), 'manage_options', 'user-forms', array( $this, 'create_dashboard_admin_page' ) );
        add_submenu_page( 'user-forms', __( 'Settings', 'user-forms' ), __( 'Settings', 'user-forms' ), 'manage_options', 'user-forms-settings', array( $this, 'create_settings_admin_page' ) );
    }

    /**
     * Dashboard page callback function
     *
     * @access  public
     * @since   1.0.0
     *
     * @return  void
     */
    public function create_dashboard_admin_page()
    {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Dashboard', 'user-forms');?></h1>
            <table border="0" bordercolor="yellow" cellspacing="0" cellpadding="15" align="left" width="25%" style="text-align:center;border:1px solid yellow;">
            <tbody>
                <tr style="background: white;">
                    <td><img src="<?php echo esc_url(USERFORMS_PLUGIN_URL ."lib/includes/assets/img/uf-logo.jpg");?>" /></td>
                </tr>
            </tbody>
            </table>
            <table border="0" bordercolor="coral" cellspacing="0" cellpadding="15" align="right" width="25%" style="text-align:center;border:1px solid coral;">
            <thead>
                <tr style="background: coral;color: white;"><th><big>Need help!</big></th></tr>
            </thead>
            <tbody>
                <tr class="alternate">
                    <td>I specialize in providing website tweaks, updates, and ongoing maintenance.<br>
                        Please contact me to <a href="https://amplebrain.com/request-a-quote/" target="_blank">click here</a> or <br> email me at <code>amplebrain@gmail.com</code></td>
                </tr>
            </tbody>
            </table>
            <br class="clear" />
            <h2>Shortcodes:</h2>
            <table border="0" bordercolor="#dedede" cellspacing="0" cellpadding="15" align="left" width="100%" style="text-align:center;border:1px solid royalblue;">
                <thead>
                    <tr class="bluebg">
                        <th>Login Shortcode</th>
                        <th>Register Shortcode</th>
                        <th>Forgot Password Shortcode</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="alternate">
                        <td><code>[USERFORMS_LOGIN]</code></td>
                        <td><code>[USERFORMS_REGISTER]</code></td>
                        <td><code>[USERFORMS_FORGOT_PASSWORD]</code></td>
                    </tr>
                </tbody>
            </table>
            <br class="clear" />
            <h2>Steps to follow:</h2>
            <ol>
                <li>Create login page and add login shortcode <code>[USERFORMS_LOGIN]</code></li>
                <li>Create register page and add register shortcode <code>[USERFORMS_REGISTER]</code></li>
                <li>Create forgot password page and add forgot password shortcode <code>[USERFORMS_FORGOT_PASSWORD]</code></li>
                <li>Go to Settings page ( User Forms -> Settings ) to manage options</li>
                <li>Configure Setting Options:
                    <ul class="unorderlist">
                        <li><b>General Settings :</b> Set you created pages to link properly in front-end</li>
                        <li><b>Login Settings :</b> Set login page options as per your desire</li>
                        <li><b>Forgot Password Settings :</b> While user generate retrive password link for change their password from front-end</li>
                        <li><b>Retrieve Password Settings :</b> While user click on retrive password link, Set retrive password page options as per your desire
                            <ol>
                                <li style="color:red"><b>Reset Password Link</b> will be only generate when you set <b>Forgot Password Page</b> from <b>General Settings</b>. Otherwise, Reset Password Link will be default WordPress link</li>
                            </ol>
                        </li>
                        <li><b>Register Settings :</b> Set registration page options as per your desire</li>
                        <li><b>Registration Form Management :</b> Manage register fields which will display while user is registring</li>
                    </ul>
                </li>
                <li>Few notes to remember:
                    <ul class="unorderlist">
                        <li><b>Register Form Builder :</b> Your selected type is Dropdown Checkbox or Radio, the Option textarea field will shows. You have to enter value must be in new line</li>
                        <li><b>Register Form Builder :</b> To arrage the fields, Drag and Drop the field by picking the icon <span class="movefield dashicons dashicons-move"></span> from Order column</li>
                        <li>Click on button <span class="button button-primary">Save Changes</span> after add new field in Registration Form Management</li>
                    </ul>
                </li>
            </ol>

            <br class="clear" />
            <h2>Contact me:</h2>
            <table border="0" bordercolor="darkblue" cellspacing="0" cellpadding="15" align="center" width="100%" style="text-align:center;border:1px solid darkblue;">
            <thead>
                <tr style="background: darkblue;color: white;"><th><big>Contact me if any help is required</big></th></tr>
            </thead>
            <tbody>
                <tr class="alternate">
                    <td>I specialize in providing website tweaks, updates, and ongoing maintenance. Whether you’re looking for design adjustments, functionality improvements, or content updates, I am here to help. I work closely with businesses to make sure their websites remain fresh, user-friendly, and aligned with current trends and standards.<br>
                        Please feel free contact me by <a href="https://amplebrain.com/request-a-quote/" target="_blank">click here</a> or <br> email me at <code>amplebrain@gmail.com</code></td>
                </tr>
            </tbody>
            </table>
        </div>
        <?php
    }

    /**
	 * Options page callback function
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
    public function create_settings_admin_page()
    {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('User Forms', 'user-forms');?></h1>
            <table border="0" bordercolor="#dedede" cellspacing="0" cellpadding="15" align="left" width="73%" style="text-align:center;border:1px solid royalblue;">
				<thead>
					<tr class="bluebg">
						<th>Login Shortcode</th>
						<th>Register Shortcode</th>
						<th>Forgot Password Shortcode</th>
					</tr>
				</thead>
				<tbody>
					<tr class="alternate">
						<td><code>[USERFORMS_LOGIN]</code></td>
						<td><code>[USERFORMS_REGISTER]</code></td>
						<td><code>[USERFORMS_FORGOT_PASSWORD]</code></td>
					</tr>
				</tbody>
			</table>
            <table border="0" bordercolor="coral" cellspacing="0" cellpadding="15" align="right" width="25%" style="text-align:center;border:1px solid coral;">
            <thead>
                <tr style="background: coral;color: white;"><th><big>Need help!</big></th></tr>
            </thead>
            <tbody>
                <tr class="alternate">
                    <td>I specialize in providing website tweaks, updates, and ongoing maintenance.<br>
                        Please contact me to <a href="https://amplebrain.com/request-a-quote/" target="_blank">click here</a> or <br> email me at <code>amplebrain@gmail.com</code></td>
                </tr>
            </tbody>
            </table>
            <br class="clear" />
            <?php settings_errors(); ?>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'user_forms_group' );
                do_settings_sections( 'user_forms_opt' );
                submit_button();
            ?>
            </form>
            <table border="0" bordercolor="darkblue" cellspacing="0" cellpadding="15" align="center" width="100%" style="text-align:center;border:1px solid darkblue;">
            <thead>
                <tr style="background: darkblue;color: white;"><th><big>Contact me if any help is required</big></th></tr>
            </thead>
            <tbody>
                <tr class="alternate">
                    <td>I specialize in providing website tweaks, updates, and ongoing maintenance. Whether you’re looking for design adjustments, functionality improvements, or content updates, I am here to help. I work closely with businesses to make sure their websites remain fresh, user-friendly, and aligned with current trends and standards.<br>
                        Please feel free contact me by <a href="https://amplebrain.com/request-a-quote/" target="_blank">click here</a> or <br> email me at <code>amplebrain@gmail.com</code></td>
                </tr>
            </tbody>
            </table>
        </div>
        <?php
    }

    /**
	 * Register and add setting fields
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
    public function page_init()
    {        
        register_setting(
            'user_forms_group', // Option group
            'user_forms_opt', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_general', // ID
            __('General Settings', 'user-forms'), // Title
            array( $this, 'print_section_info' ), // Callback
            'user_forms_opt' // Page
        );

        add_settings_field(
            'login_page', // ID
            __('Login Page', 'user-forms'),
            array( $this, 'login_page_callback' ),
            'user_forms_opt', 
            'setting_section_general' 
        );

        add_settings_field(
            'register_page', // ID
            __('Register Page', 'user-forms'),
            array( $this, 'register_page_callback' ),
            'user_forms_opt', 
            'setting_section_general' 
        );

        add_settings_field(
            'forgot_page', // ID
            __('Forgot Password Page', 'user-forms'),
            array( $this, 'forgot_page_callback' ),
            'user_forms_opt', 
            'setting_section_general' 
        );

        add_settings_field(
            'color', // ID
            __('Button Color', 'user-forms'),
            array( $this, 'color_callback' ),
            'user_forms_opt', 
            'setting_section_general' 
        );

        add_settings_section(
            'setting_section_login', // ID
            __('Login Settings', 'user-forms'), // Title
            array( $this, 'print_section_info' ), // Callback
            'user_forms_opt' // Page
        );  

        add_settings_field(
            'show_remember', // ID
            __('Hide Remember', 'user-forms'),
            array( $this, 'show_remember_callback' ),
            'user_forms_opt', 
            'setting_section_login' 
        );

        add_settings_field(
            'show_login_links', // ID
            __('Hide Bottom Links', 'user-forms'),
            array( $this, 'show_login_links_callback' ),
            'user_forms_opt', 
            'setting_section_login' 
        );

        add_settings_field(
            'login_button', 
            __('Login Button Label', 'user-forms'), 
            array( $this, 'login_button_callback' ),
            'user_forms_opt', 
            'setting_section_login'       
        );

        add_settings_field(
            'login_redirect', 
            __('Redirect URL', 'user-forms'), 
            array( $this, 'login_redirect_callback' ),
            'user_forms_opt', 
            'setting_section_login'       
        );

        add_settings_section(
            'setting_section_forgot', // ID
            __('Forgot Password Settings', 'user-forms'), // Title
            array( $this, 'print_section_info' ), // Callback
            'user_forms_opt' // Page
        );  

        add_settings_field(
            'show_forgot_links', // ID
            __('Hide Bottom Links', 'user-forms'),
            array( $this, 'show_forgot_links_callback' ),
            'user_forms_opt', 
            'setting_section_forgot' 
        );

        add_settings_field(
            'forgot_button', 
            __('Forgot Password Button Label', 'user-forms'), 
            array( $this, 'forgot_button_callback' ),
            'user_forms_opt', 
            'setting_section_forgot'       
        );

        add_settings_field(
            'forgot_redirect', 
            __('Redirect URL', 'user-forms'), 
            array( $this, 'forgot_redirect_callback' ),
            'user_forms_opt', 
            'setting_section_forgot'       
        );

        add_settings_section(
            'setting_section_retrieve', // ID
            __('Retrieve Password Settings', 'user-forms'), // Title
            array( $this, 'print_section_info' ), // Callback
            'user_forms_opt' // Page
        );  

        add_settings_field(
            'show_retrieve_links', // ID
            __('Hide Bottom Links', 'user-forms'),
            array( $this, 'show_retrieve_links_callback' ),
            'user_forms_opt', 
            'setting_section_retrieve' 
        );

        add_settings_field(
            'forgot_button', 
            __('Retrieve Password Button Label', 'user-forms'), 
            array( $this, 'retrieve_button_callback' ),
            'user_forms_opt', 
            'setting_section_retrieve'       
        );

        add_settings_field(
            'retrieve_redirect', 
            __('Redirect URL', 'user-forms'), 
            array( $this, 'retrieve_redirect_callback' ),
            'user_forms_opt', 
            'setting_section_retrieve'       
        );

        add_settings_section(
            'setting_section_register', // ID
            __('Register Settings', 'user-forms'), // Title
            array( $this, 'print_section_info' ), // Callback
            'user_forms_opt' // Page
        ); 

        add_settings_field(
            'column', 
            __('Layout', 'user-forms'), 
            array( $this, 'column_callback' ),
            'user_forms_opt', 
            'setting_section_register'       
        );

        add_settings_field(
            'role', 
            __('New User Role', 'user-forms'), 
            array( $this, 'role_callback' ),
            'user_forms_opt', 
            'setting_section_register'       
        );

        add_settings_field(
            'uore', 
            __('Username or Email Address', 'user-forms'), 
            array( $this, 'uore_callback' ),
            'user_forms_opt', 
            'setting_section_register'       
        );

        add_settings_field(
            'show_first', // ID
            __('Show First Name', 'user-forms'),
            array( $this, 'show_first_callback' ),
            'user_forms_opt', 
            'setting_section_register' 
        );

        add_settings_field(
            'show_last', // ID
            __('Show Last Name', 'user-forms'),
            array( $this, 'show_last_callback' ),
            'user_forms_opt', 
            'setting_section_register' 
        );

        add_settings_field(
            'show_confirm', // ID
            __('Show Confirm Password', 'user-forms'),
            array( $this, 'show_confirm_callback' ),
            'user_forms_opt', 
            'setting_section_register' 
        );

        add_settings_field(
            'show_register_links', // ID
            __('Hide Bottom Links', 'user-forms'),
            array( $this, 'show_register_links_callback' ),
            'user_forms_opt', 
            'setting_section_register' 
        );

        add_settings_field(
            'register_button', 
            __('Register Button Label', 'user-forms'), 
            array( $this, 'register_button_callback' ),
            'user_forms_opt', 
            'setting_section_register'       
        );

        add_settings_field(
            'register_redirect', 
            __('Redirect URL', 'user-forms'), 
            array( $this, 'register_redirect_callback' ),
            'user_forms_opt', 
            'setting_section_register'
        );

        add_settings_section(
            'setting_section_fields', // ID
            __('Registration Form Management', 'user-forms'), // Title
            array( $this, 'print_section_fields' ), // Callback
            'user_forms_opt' // Page
        ); 

        add_settings_field(
            'register_button', 
            __('Register Fields', 'user-forms'), 
            array( $this, 'register_custom_fields_callback' ),
            'user_forms_opt', 
            'setting_section_fields',
            array( 'class' => 'custom-fields-tr' )
        );

    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();

        if( isset( $input['login_page'] ) )
            $new_input['login_page'] = sanitize_text_field( $input['login_page'] );

        if( isset( $input['register_page'] ) )
            $new_input['register_page'] = sanitize_text_field( $input['register_page'] );

        if( isset( $input['forgot_page'] ) )
            $new_input['forgot_page'] = sanitize_text_field( $input['forgot_page'] );

        if( isset( $input['color'] ) )
            $new_input['color'] = sanitize_text_field( $input['color'] );

        if( isset( $input['show_remember'] ) )
            $new_input['show_remember'] = sanitize_text_field( $input['show_remember'] );

        if( isset( $input['login_links'] ) )
            $new_input['login_links'] = sanitize_text_field( $input['login_links'] );

        if( isset( $input['login_button'] ) )
            $new_input['login_button'] = sanitize_text_field( $input['login_button'] );

        if( isset( $input['login_redirect'] ) )
            $new_input['login_redirect'] = sanitize_text_field( $input['login_redirect'] );

        if( isset( $input['forgot_links'] ) )
            $new_input['forgot_links'] = sanitize_text_field( $input['forgot_links'] );

        if( isset( $input['forgot_button'] ) )
            $new_input['forgot_button'] = sanitize_text_field( $input['forgot_button'] );

        if( isset( $input['forgot_redirect'] ) )
            $new_input['forgot_redirect'] = sanitize_text_field( $input['forgot_redirect'] );

        if( isset( $input['retrieve_links'] ) )
            $new_input['retrieve_links'] = sanitize_text_field( $input['retrieve_links'] );

        if( isset( $input['retrieve_button'] ) )
            $new_input['retrieve_button'] = sanitize_text_field( $input['retrieve_button'] );

        if( isset( $input['retrieve_redirect'] ) )
            $new_input['retrieve_redirect'] = sanitize_text_field( $input['retrieve_redirect'] );

        if( isset( $input['column'] ) )
            $new_input['column'] = sanitize_text_field( $input['column'] );

        if( isset( $input['role'] ) )
            $new_input['role'] = sanitize_text_field( $input['role'] );

        if( isset( $input['uore'] ) )
            $new_input['uore'] = sanitize_text_field( $input['uore'] );

        if( isset( $input['show_first'] ) )
            $new_input['show_first'] = sanitize_text_field( $input['show_first'] );

        if( isset( $input['show_last'] ) )
            $new_input['show_last'] = sanitize_text_field( $input['show_last'] );

        if( isset( $input['show_confirm'] ) )
            $new_input['show_confirm'] = sanitize_text_field( $input['show_confirm'] );

        if( isset( $input['register_links'] ) )
            $new_input['register_links'] = sanitize_text_field( $input['register_links'] );
        
        if( isset( $input['register_button'] ) )
            $new_input['register_button'] = sanitize_text_field( $input['register_button'] );

        if( isset( $input['register_redirect'] ) )
            $new_input['register_redirect'] = sanitize_text_field( $input['register_redirect'] );

        if( isset( $input['fields'] ) )
            $new_input['fields'] = ( $input['fields'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        
    }

    /** 
     * Print the Section text
     */
    public function print_section_fields()
    {
        printf(
            '<input type="button" class="addfield button button-small button-secondary" value="%s" />',
            'Add New Field'
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function login_page_callback()
    {
    	$dropdown_args = array(
            'post_type'        => 'page',
            'selected'         => ($this->options['login_page']),
            'id'             => 'login_page',
            'name'             => 'user_forms_opt[login_page]',
            'show_option_none' => esc_html__('Select page','user-forms'),
            'sort_column'      => 'menu_order, post_title',
            'echo'             => 0,
        );
        print( ( wp_dropdown_pages(  ($dropdown_args) ) ) );
        echo '<div><i>'.esc_html__('Select Login Page. Use shortcode [USERFORMS_LOGIN]','user-forms').'</i></div>';
    }

    public function register_page_callback()
    {
    	$dropdown_args = array(
            'post_type'        => 'page',
            'selected'         => ($this->options['register_page']),
            'id'             => 'register_page',
            'name'             => 'user_forms_opt[register_page]',
            'show_option_none' => esc_html__('Select page','user-forms'),
            'sort_column'      => 'menu_order, post_title',
            'echo'             => 0,
        );
        print( ( wp_dropdown_pages( ($dropdown_args) ) ) );
        echo '<div><i>'.esc_html__('Select Register Page. Use shortcode [USERFORMS_REGISTER]','user-forms').'</i></div>';
    }

    public function forgot_page_callback()
    {
    	$dropdown_args = array(
            'post_type'        => 'page',
            'selected'         => ($this->options['forgot_page']),
            'id'             => 'forgot_page',
            'name'             => 'user_forms_opt[forgot_page]',
            'show_option_none' => esc_html__('Select page','user-forms'),
            'sort_column'      => 'menu_order, post_title',
            'echo'             => 0,
        );
        print( ( wp_dropdown_pages( ($dropdown_args) ) ) );
        echo '<div><i>'.esc_html__('Select Forgot Password Page. Use shortcode [USERFORMS_FORGOT_PASSWORD]','user-forms').'</i></div>';
        echo '<div style="color:red"><i>'.esc_html__('Reset Password Link will be only generate when you set Forgot Password Page. Otherwise, Reset Password Link will be default WordPress link','user-forms').'</i></div>';
    }

    public function color_callback()
    {
        printf(
            '<input type="text" name="user_forms_opt[color]" id="color" class="user_forms_colorfield color-picker" data-rgba="true" value="%s" />',
            isset( $this->options['color'] ) ? ( esc_html( $this->options['color'] ) ) : ''
        );
        echo '<div><i>'.esc_html__('Choose Button Color','user-forms').'</i></div>';
    }

    public function show_remember_callback()
    {

        printf(
            '<input type="checkbox" id="show_remember" name="user_forms_opt[show_remember]" value="1" %s />',
            isset( $this->options['show_remember'] ) ? 'checked="checked"' : ''
        );
        echo '<div><i>'.esc_html__('Checked to hide Remember field.','user-forms').'</i></div>';
    }

    public function show_login_links_callback()
    {

        printf(
            '<input type="checkbox" id="login_links" name="user_forms_opt[login_links]" value="1" %s />',
            isset( $this->options['login_links'] ) ? 'checked="checked"' : ''
        );
        echo '<div><i>'.esc_html__('Checked to hide register form Bottom Links.','user-forms').'</i></div>';
    }

    public function login_button_callback()
    {
        printf(
            '<input type="text" id="login_button" name="user_forms_opt[login_button]" value="%s" placeholder="Log In" />',
            isset( $this->options['login_button'] ) ? esc_attr( $this->options['login_button']) : ''
        );
        echo '<div><i>'.esc_html__('Enter Login Button Label. Default is `Log In`','user-forms').'</i></div>';
    }

    public function login_redirect_callback()
    {
        printf(
            '<input type="text" id="login_redirect" name="user_forms_opt[login_redirect]" value="%s" size="50" />',
            isset( $this->options['login_redirect'] ) ? esc_attr( $this->options['login_redirect']) : ''
        );
        echo '<div><i>'.esc_html__('Enter redirect URL. Redirect to this URL after Login sucessful','user-forms').'</i></div>';
    }

    public function show_forgot_links_callback()
    {

        printf(
            '<input type="checkbox" id="forgot_links" name="user_forms_opt[forgot_links]" value="1" %s />',
            isset( $this->options['forgot_links'] ) ? 'checked="checked"' : ''
        );
        echo '<div><i>'.esc_html__('Checked to hide Forgot Password form Bottom Links.','user-forms').'</i></div>';
    }

    public function forgot_button_callback()
    {
        printf(
            '<input type="text" id="forgot_button" name="user_forms_opt[forgot_button]" value="%s" placeholder="Get New Password" />',
            isset( $this->options['forgot_button'] ) ? esc_attr( $this->options['forgot_button']) : ''
        );
        echo '<div><i>'.esc_html__('Enter Forgot Password Button Label. Default is `Get New Password`','user-forms').'</i></div>';
    }

    public function forgot_redirect_callback()
    {
        printf(
            '<input type="text" id="forgot_redirect" name="user_forms_opt[forgot_redirect]" value="%s" size="50" />',
            isset( $this->options['forgot_redirect'] ) ? esc_attr( $this->options['forgot_redirect']) : ''
        );
        echo '<div><i>'.esc_html__('Enter redirect URL. Redirect to this URL after forgot password sucessful','user-forms').'</i></div>';
    }

	public function show_retrieve_links_callback()
    {

        printf(
            '<input type="checkbox" id="retrieve_links" name="user_forms_opt[retrieve_links]" value="1" %s />',
            isset( $this->options['retrieve_links'] ) ? 'checked="checked"' : ''
        );
        echo '<div><i>'.esc_html__('Checked to hide Retrieve Password form Bottom Links.','user-forms').'</i></div>';
    }

    public function retrieve_button_callback()
    {
        printf(
            '<input type="text" id="retrieve_button" name="user_forms_opt[retrieve_button]" value="%s" placeholder="Save Password" />',
            isset( $this->options['retrieve_button'] ) ? esc_attr( $this->options['retrieve_button']) : ''
        );
        echo '<div><i>'.esc_html__('Enter Retrieve Password Button Label. Default is `Save Password`','user-forms').'</i></div>';
    }

    public function retrieve_redirect_callback()
    {
        printf(
            '<input type="text" id="retrieve_redirect" name="user_forms_opt[retrieve_redirect]" value="%s" size="50" />',
            isset( $this->options['retrieve_redirect'] ) ? esc_attr( $this->options['retrieve_redirect']) : ''
        );
        echo '<div><i>'.esc_html__('Enter redirect URL. Redirect to this URL after retrieve password sucessful','user-forms').'</i></div>';
    }

    public function column_callback()
    {
    	echo '<select id="column" name="user_forms_opt[column]">';
            echo '<option value="1" '.( ( $this->options['column'] == '1' ) ? 'selected="selected"' : '' ).'>1 Column</option>';
            echo '<option value="2" '.( ( $this->options['column'] == '2' ) ? 'selected="selected"' : '' ).'>2 Columns</option>';
        echo '</select>';  
        echo '<div><i>'.esc_html__('Select Column Layout. Default is `1 Column`','user-forms').'</i></div>';
    }

    public function role_callback()
    {
    	global $wp_roles;

    	$currentrole = ( !empty($this->options['role']) ? $this->options['role'] : 'subscriber' );

	    echo '<select id="role" name="user_forms_opt[role]">';
    	foreach ( $wp_roles->roles as $roleslug=>$role ) {
	       if ( ! in_array( $role['name'], [ 'Administrator', 'Contributor', ] ) ) {
	          echo '<option value="'.esc_attr($roleslug).'" '.( ( $currentrole == $roleslug ) ? 'selected="selected"' : '' ).'>'.esc_attr($role['name']).'</option>';
	       }
	    }
        echo '</select>';  
        echo '<div><i>'.esc_html__('Select New User Role. Default is `Subscriber`','user-forms').'</i></div>';
    }

    public function uore_callback()
    {

        printf(
            '<input type="checkbox" id="uore" name="user_forms_opt[uore]" value="1" %s />',
            isset( $this->options['uore'] ) ? 'checked="checked"' : ''
        );
        echo '<div><i>'.esc_html__('Checked to show Username and Email both fields and hide combained field Username or Email Address.','user-forms').'</i></div>';
    }

    public function show_first_callback()
    {

        printf(
            '<input type="checkbox" id="show_first" name="user_forms_opt[show_first]" value="1" %s />',
            isset( $this->options['show_first'] ) ? 'checked="checked"' : ''
        );
        echo '<div><i>'.esc_html__('Checked to show First Name field.','user-forms').'</i></div>';
    }

    public function show_last_callback()
    {

        printf(
            '<input type="checkbox" id="show_last" name="user_forms_opt[show_last]" value="1" %s />',
            isset( $this->options['show_last'] ) ? 'checked="checked"' : ''
        );
        echo '<div><i>'.esc_html__('Checked to show Last Name field.','user-forms').'</i></div>';
    }

    public function show_confirm_callback()
    {

        printf(
            '<input type="checkbox" id="show_confirm" name="user_forms_opt[show_confirm]" value="1" %s />',
            isset( $this->options['show_confirm'] ) ? 'checked="checked"' : ''
        );
        echo '<div><i>'.esc_html__('Checked to show Confirm Password field.','user-forms').'</i></div>';
    }

    public function show_register_links_callback()
    {

        printf(
            '<input type="checkbox" id="register_links" name="user_forms_opt[register_links]" value="1" %s />',
            isset( $this->options['register_links'] ) ? 'checked="checked"' : ''
        );
        echo '<div><i>'.esc_html__('Checked to hide register form Bottom Links.','user-forms').'</i></div>';
    }

    public function register_button_callback()
    {
        printf(
            '<input type="text" id="register_button" name="user_forms_opt[register_button]" value="%s" placeholder="Register" />',
            isset( $this->options['register_button'] ) ? esc_attr( $this->options['register_button']) : ''
        );
        echo '<div><i>'.esc_html__('Enter Register Button Label. Default is `Register`','user-forms').'</i></div>';
    }

    public function register_redirect_callback()
    {
        printf(
            '<input type="text" id="register_redirect" name="user_forms_opt[register_redirect]" value="%s" size="50" />',
            isset( $this->options['register_redirect'] ) ? esc_attr( $this->options['register_redirect']) : ''
        );
        echo '<div><i>'.esc_html__('Enter Redirect URL. Redirect to this URL after Registration is successful','user-forms').'</i></div>';
    }

    public function register_custom_fields_callback()
    {
    	$option_types = array('checkbox', 'radio', 'select');
    	$default_fields = array('first_name'=>'First Name', 'last_name'=>'Last Name', 'log'=>'Username', 'email'=>'Email Address', 'pwd'=>'Password', 'cpwd'=>'Confirm Password');
    	//$default_fields = array('first_name', 'last_name', 'log', 'email', 'pwd', 'cpwd');
    	$required_fields = array('log', 'email', 'pwd', 'cpwd');

    	echo '<table class="form-table form-table-tk" border="1" bordercolor="#dedede">
				<thead>
					<tr class="bluebg">
						<th style="width:2.2em">'.esc_html__('Order','user-forms').'</th>
						<th style="width:90px">'.esc_html__('Type','user-forms').'</th>
						<th>'.esc_html__('Label','user-forms').'</th>
						<th>'.esc_html__('Option','user-forms').'</th>
						<th style="width:2.2em">'.esc_html__('Required','user-forms').'</th>
						<th style="width:2.2em">'.esc_html__('Delete','user-forms').'</th>
					</tr>
				</thead>
				<tbody>';

		/*if( empty( $this->options['fields'] ) ) {
			echo '<tr class="initialrow"><td colspan="5" style="text-align:center">'.__('Click `Add New Field` button to add field','user-forms').'</td></tr>';
		}*/

		//echo '<pre>';print_r($this->options['fields']);exit;
		if ( !empty( $this->options['fields'] ) ){

			//echo '<pre>';var_dump($this->options['fields']);exit;
			
			$show_option_field = '';
			foreach ($this->options['fields']['type'] as $index => $type) {
			
				if( in_array($type, $option_types) ){
					$show_option_field = 'style="display:block"';
				}else{
					$show_option_field = '';
				}

			echo '<tr class="alternate">';
				echo '<td>';
	        		echo '<span title="'.esc_html__('Drag & Drop','user-forms').'" class="movefield dashicons dashicons-move"></span>';
	        	echo '</td>';
				echo '<td>';
					if( array_key_exists($type, $default_fields) ){
						echo '<input type="hidden" name="user_forms_opt[fields][type][]" value="'.esc_attr($type).'" />';
						echo esc_attr($default_fields[$type]);
					}else{
						echo '<select class="type" name="user_forms_opt[fields][type][]">';
							/*echo '<optgroup label="System Fields">';
				            echo '<option value="email" '.( ( $type == 'email' ) ? 'selected="selected"' : '' ).'>Email</option>';
				            echo '<option value="password" '.( ( $type == 'password' ) ? 'selected="selected"' : '' ).'>Password</option>';
				            echo '<option value="confirm" '.( ( $type == 'confirm' ) ? 'selected="selected"' : '' ).'>Confirm Password</option>';
				            echo '<option value="first_name" '.( ( $type == 'first_name' ) ? 'selected="selected"' : '' ).'>First Name</option>';
				            echo '<option value="last_name" '.( ( $type == 'last_name' ) ? 'selected="selected"' : '' ).'>Last Name</option>';
				            echo '</optgroup>';
				            echo '<optgroup label="Custom Fields">';*/
				            echo '<option value="system" '.( ( $type == 'system' ) ? 'selected="selected"' : '' ).'>Default</option>';
				            echo '<option value="text" '.( ( $type == 'text' ) ? 'selected="selected"' : '' ).'>Text</option>';
				            echo '<option value="number" '.( ( $type == 'number' ) ? 'selected="selected"' : '' ).'>Number</option>';
				            echo '<option value="textarea" '.( ( $type == 'textarea' ) ? 'selected="selected"' : '' ).'>Textarea</option>';
				            echo '<option value="select" '.( ( $type == 'select' ) ? 'selected="selected"' : '' ).'>Dropdown</option>';
				            echo '<option value="checkbox" '.( ( $type == 'checkbox' ) ? 'selected="selected"' : '' ).'>Checkbox</option>';
				            echo '<option value="radio" '.( ( $type == 'radio' ) ? 'selected="selected"' : '' ).'>Radio</option>';
				            echo '<option value="country" '.( ( $type == 'country' ) ? 'selected="selected"' : '' ).'>Country</option>';
				            echo '<option value="heading" '.( ( $type == 'heading' ) ? 'selected="selected"' : '' ).'>Heading</option>';
				            //echo '</optgroup>';
				        echo '</select>';
			    	}
	        	echo '</td>';
	        	echo '<td>';
			        printf(
			            '<input type="hidden" name="user_forms_opt[fields][slug][]" value="%s" />',
			            isset( $this->options['fields']['slug'][$index] ) ? esc_attr( $this->options['fields']['slug'][$index]) : ''
			        );
			        printf(
			            '<input type="text" name="user_forms_opt[fields][title][]" value="%s" />',
			            isset( $this->options['fields']['title'][$index] ) ? esc_attr( $this->options['fields']['title'][$index]) : ''
			        );
	        	echo '</td>';
	        	echo '<td>';
	        		echo sprintf( '<textarea name="user_forms_opt[fields][option][]" %2$s>%1$s</textarea>',
						isset( $this->options['fields']['option'][$index] ) && !empty($show_option_field) ? esc_attr( $this->options['fields']['option'][$index]) : '',
						wp_kses_data($show_option_field)
					);
	        	echo '</td>';
	        	echo '<td>';

	        		if( in_array($type, $required_fields) ){
	        			echo '<input type="hidden" name="user_forms_opt[fields][required][]" value="1" />';
	        		}else{
	        		echo '<select name="user_forms_opt[fields][required][]">';
			            echo '<option value="0" '.( ( $this->options['fields']['required'][$index] == '0' ) ? 'selected="selected"' : '' ).'>No</option>';
			            echo '<option value="1" '.( ( $this->options['fields']['required'][$index] == '1' ) ? 'selected="selected"' : '' ).'>Yes</option>';
			        echo '</select>';
			    	}
	        	echo '</td>';
	        	echo '<td>';
	        		if( !array_key_exists($type, $default_fields) ){
		        		echo '<span title="'.esc_html__('Delete','user-forms').'" class="removefield dashicons dashicons-remove"></span>';
		        	}
	        	echo '</td>';
	        echo '</tr>';

        	}
        }
		echo '</tbody>';

		echo '<tfoot>
				<tr>
					<th colspan="3"><span style="font-size:23px;margin:0 5px 0 10px" class="dashicons dashicons-text-page wp-ui-text-highlight"></span><small>';
					echo esc_html__('Option textarea field value must be in new line','user-forms');
				echo '</small></th>
					<th colspan="3" style="text-align:right">';
					printf(
			            '<input type="button" class="addfield button button-small button-primary" value="%s" />',
			            esc_html__('Add New Field','user-forms')
			        );
				echo '</th>
				</tr>
			</tfoot>';
		echo '</table>';

		echo '<table align="right"><tr>
			<td><span style="font-size:15px;" class="dashicons dashicons-move wp-ui-text-highlight"></span> '.esc_html__('Display Order','user-forms').'</td>
			<td><span style="font-size:15px;" class="dashicons dashicons-remove wp-ui-text-highlight"></span> '.esc_html__('Delete Field','user-forms').'</td>
		</tr><tr><td colspan="2" align="right"><small>*Save Changes after add new field</small></td></tr></table>';
    }

    /**
     * Show Custom Fields details on user page : Admin
     *
     * @access  public
     * @since   1.0.0
     *
     * @return  void
     */
    public function add_custom_fields_details_on_user_profile_page($user){
        
        $slug_title = array();
        foreach ($this->options['fields']['title'] as $k => $v) {
            $slug_title[ $this->options['fields']['slug'][$k] ] = $v;
        }

        if(is_object($user)){
            $user_forms_fields = get_the_author_meta( 'user_forms_fields', $user->ID );
        }else{
            $user_forms_fields = array();
        }

        if(count($user_forms_fields)){

            ?>
            <h2><?php esc_html_e('User Forms : Registration Custom Fields', 'user-forms')?></h2>
            <table class="form-table">
                <?php foreach ($user_forms_fields as $index => $fieldslug) { ?>
                    <tr>
                        <th>
                            <label>
                            <?php 
                            echo esc_attr($slug_title[$fieldslug]);
                            ?>
                            </label>
                        </th>
                        <td>
                            <?php 
                            $usermeta_data = get_the_author_meta( $fieldslug, $user->ID ); 
                            if( is_array($usermeta_data) ){
                                foreach ($usermeta_data as $val) {
                                    echo esc_attr($val);
                                }
                            }else{
                                echo esc_attr(get_the_author_meta( $fieldslug, $user->ID ));
                            }
                            ?>                              
                        </td>
                    </tr>
                <?php } ?>
            </table>
            <?php
        }
    }

}