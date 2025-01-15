<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 
 * All shortcodes registered here which are controlled and managed by this class.
 * 
 */

/**
 * Class User_Forms_Shortcodes
 *
 * @package		USERFORMS
 * @subpackage	Classes/User_Forms_Shortcodes
 * @author		Tushar Kapdi
 * @since		1.0.0
 */
class User_Forms_Shortcodes{

	/**
	 * User_Forms_Shortcodes constructor 
	 *
	 * @since 1.0.0
	 */
	function __construct(){
		$this->add_shortcodes();
	}

	/**
	 * WordPress and plugin related hooks
	 *
	 * @access	private
	 * @since	1.0.0
	 * @return	void
	 */
	private function add_shortcodes(){
	
		/*
		* Shortcodes: [USERFORMS_LOGIN]
		*/
		add_shortcode( 'USERFORMS_LOGIN', array($this, 'shortcode_login_form_call') );
		add_filter( 'login_form_top', array($this, 'add_error_message_tag_in_loginform'), 10, 2 );
		
		/*
		* Ajax actions for logged-out users
		*/
		add_action( 'wp_ajax_nopriv_USERFORMS_login_ajax_call', array( $this, 'login_ajax_call_callback' ), 20 );

		/*
		* Shortcodes: [USERFORMS_REGISTER]
		*/
		add_shortcode( 'USERFORMS_REGISTER', array($this, 'shortcode_register_form_call') );

		/*
		* Ajax actions for logged-out users
		*/
		add_action( 'wp_ajax_nopriv_USERFORMS_register_ajax_call', array( $this, 'register_ajax_call_callback' ), 20 );

		/*
		* Shortcodes: [USERFORMS_FORGOT_PASSWORD]
		*/
		add_shortcode( 'USERFORMS_FORGOT_PASSWORD', array($this, 'shortcode_forgot_password_form_call') );
		add_filter( 'retrieve_password_message', array($this, 'forgot_password_retrieve_password_message_change_link'), 10, 3 );

		/*
		* Ajax actions for logged-out users
		*/
		add_action( 'wp_ajax_nopriv_USERFORMS_forgot_password_ajax_call', array( $this, 'forgot_password_ajax_call_callback' ), 20 );
		add_action( 'wp_ajax_nopriv_USERFORMS_forgot_retrive_password_ajax_call', array( $this, 'forgot_retrive_password_ajax_call_callback' ), 20 );
		add_action( 'template_redirect', array( $this, 'retrive_password_check_reset_key_call_callback' ) );
	
	}

	/**
	 * Check reset password link
	 *
	 * @access	static
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public static function retrive_password_check_reset_key_call_callback(){

		if ( isset( $_GET['key'] ) && isset( $_GET['login'] ) ) {

			$nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'])) : '';

			if ( ! wp_verify_nonce( $nonce, 'USERFORMS_reset_password_nonce' ) ) {
				die( esc_html__('Nonce is invalid', 'user-forms') ); 
			}
			
			$options = get_option( 'user_forms_opt' );

			$rp_key = sanitize_text_field( wp_unslash( $_GET['key'] ) );
			$rp_login = sanitize_text_field( wp_unslash( $_GET['login'] ) );
			
			$user = check_password_reset_key( $rp_key, $rp_login );

			if ( ! $user || is_wp_error( $user ) ) {
			
				if ( $user && $user->get_error_code() === 'expired_key' ) {
					wp_redirect( get_permalink($options['forgot_page']).'?error=expiredkey' );
					exit;
				} else {
					wp_redirect( get_permalink($options['forgot_page']).'?error=invalidkey' );
					exit;
				}
			}
		}
	}

	/**
	 * Display login form.
	 *
	 * @access	static
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public static function shortcode_login_form_call( $atts, $content = "" ) {

		global $wp;
		
		$options = get_option( 'user_forms_opt' );

		if( !empty($options['login_redirect']) ){
			$redirect = $options['login_redirect'];
		}else{
			$redirect = home_url($wp->request);
		}

		$default = array(
			'echo'              => false,
			'redirect'          => $redirect,
			'form_id'           => 'loginform',
			'label_username'    => __( 'Username or Email Address', 'user-forms' ),
			'label_password'    => __( 'Password', 'user-forms' ),
			'label_remember'    => __( 'Remember Me', 'user-forms' ),
			'label_log_in'      => __( 'Log In', 'user-forms' ),
			'id_username'       => 'user_login',
			'id_password'       => 'user_pass',
			'id_remember'       => 'rememberme',
			'id_submit'         => 'wp-submit',
			'remember'          => ( !empty($options['show_remember']) ? false : true ),
			'value_username'    => '',
			'value_remember'    => false,
			'required_username' => false,
			'required_password' => false,
			'bottom_links' 		=> ( !empty($options['login_links']) ? $options['login_links'] : 0 ),
			'label_forgot_password'    => __( 'Lost password?', 'user-forms' ),
			'label_register'    => __( 'Create an account', 'user-forms' ),
			'color'    	=> ( !empty($options['color']) ? $options['color'] : '' ),
		);

		$args = shortcode_atts( $default, $atts );

		if( !is_user_logged_in() ) { 

			$links = "";
			if( !$args['bottom_links'] ) {
				$lost_password = (!empty($options['forgot_page'])) ? get_permalink($options['forgot_page']) : '';
				$register_link = (!empty($options['register_page'])) ? get_permalink($options['register_page']) : '';
				$links = sprintf(
					'<ul class="bottom-links"><li><a href="%1$s">%2$s</a></li><li><a href="%3$s">%4$s</a></li></ul>',
					esc_html( $lost_password ),
					esc_html( $args['label_forgot_password'] ),
					esc_url( $register_link ),
					esc_html( $args['label_register'] )
				);
			}
			
			return '<div class="userforms-login">'.wp_login_form( $args ).$links.'</div>';

		} else {
			
			$current_user = wp_get_current_user();
			$message = sprintf(
				'<div class="user-message">Howdy, %1$s</div><ul class="user-links"><li><a href="%2$s">%3$s</a></li></ul>',
				esc_html( $current_user->display_name ),
				esc_url( wp_logout_url( home_url()) ),
				__('Logout','user-forms')
			);

			return '<div class="userforms-login">'.$message.'</div>';
		}
	}

	/**
	 * Display register form.
	 *
	 * @access	static
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public static function shortcode_register_form_call( $atts, $content = "" ) {

		global $wp;

		$options = get_option( 'user_forms_opt' );

		if( !empty($options['register_redirect']) ){
			$redirect = $options['register_redirect'];
		}else{
			$redirect = home_url($wp->request);
		}
		
		$default = array(
			'column'            => ( !empty($options['column']) ? $options['column'] : 1 ),
			'redirect'          => $redirect,
			'form_id'           => 'registerform',
			'label_username'    => __( 'Username', 'user-forms' ),
			'label_password'    => __( 'Password', 'user-forms' ),
			'register_button'    => ( !empty($options['register_button']) ? $options['register_button'] : __( 'Register', 'user-forms' ) ),
			'id_submit'         => 'wp-submit',
			'bottom_links'		=> ( !empty($options['register_links']) ? $options['register_links'] : 0 ),
			'useroremail'		=> ( !empty($options['uore']) ? $options['uore'] : 0 ),
			'first' 			=> ( !empty($options['show_first']) ? $options['show_first'] : 0 ),
			'last' 				=> ( !empty($options['show_last']) ? $options['show_last'] : 0 ),
			'confirm' 			=> ( !empty($options['show_confirm']) ? $options['show_confirm'] : 0 ),
			'label_useroremail'	=> __( 'Username or Email Address', 'user-forms' ),
			'label_email'      	=> __( 'Email Address', 'user-forms' ),
			'label_first'      	=> __( 'First Name', 'user-forms' ),
			'label_last'      	=> __( 'Last Name', 'user-forms' ),
			'label_confirm'    	=> __( 'Confirm Password', 'user-forms' ),
			'label_forgot_password'    => __( 'Forgot your password?', 'user-forms' ),
			'label_login'    	=> __( 'Alredy register? Login', 'user-forms' ),
			'color'    	=> ( !empty($options['color']) ? $options['color'] : '' ),
		);

		$args = shortcode_atts( $default, $atts );

		if( !is_user_logged_in() ) { 

			$links = "";
			if( !$args['bottom_links'] ){
				$lost_password = (!empty($options['forgot_page'])) ? get_permalink($options['forgot_page']) : '';
				$login_link = (!empty($options['login_page'])) ? get_permalink($options['login_page']) : '';
				$links = sprintf( '<ul class="bottom-links"><li><a href="%1$s">%2$s</a></li><li><a href="%3$s">%4$s</a></li></ul>',
					esc_url( $lost_password ),
					esc_html( $args['label_forgot_password'] ),
					esc_url( $login_link ),
					esc_html( $args['label_login'] )
				);
			}
			
			$form_html = self::register_form($args, $options);
			$status = "<div class='status'></div>";

			$column = ( isset($options['column']) ) ? $options['column'] : 1;
			return '<div class="userforms-register column-'.$column.'">'.$status.$form_html.$links.'</div>';

		} else {
			
			$current_user = wp_get_current_user();
			$message = sprintf(
				'<div class="user-message">Howdy, %1$s</div><ul class="user-links"><li><a href="%2$s">%3$s</a></li></ul>',
				esc_html( $current_user->display_name ),
				esc_url( wp_logout_url( home_url()) ),
				__('Logout','user-forms')
			);

			return '<div class="userforms-register">'.$message.'</div>';
		}
	}

	/**
	 * Display register form fields.
	 *
	 * @access	static
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	private static function register_form($args, $options){
		
		$register_form_top = apply_filters( 'userforms_register_form_top', '', $args );
		$register_form_bottom = apply_filters( 'userforms_register_form_bottom', '', $args );

		$required_attr = '';//'required';
		$required_class= 'mandatory';

		$option_types = array('checkbox', 'radio', 'select');
		$custom_fields = '';
		foreach ($options['fields']['type'] as $index => $type) {

			/*Field Name*/
			$slug = $options['fields']['slug'][$index];//sanitize_title( wp_trim_words( $options['fields']['title'][$index], 5 ) );

			/*Field Options*/
			$opt_arr = array();
			if( in_array($type, $option_types) ){
				$opt_arr = explode(PHP_EOL, $options['fields']['option'][$index] );
			}

			switch ($type) {

				case 'first_name':
					if( $args['first'] ) { 
				 		$custom_fields .= sprintf(
							'<div class="uf-field uf-field-text %1$s">
								<label for="%1$s">%2$s %4$s</label>
								<input type="text" name="%1$s" autocomplete="first_name" class="input %1$s %5$s" placeholder="%2$s" size="20" %3$s />
							</div>',
							$type,
							esc_html( $options['fields']['title'][$index] ),
							( ( $options['fields']['required'][$index] ) ? $required_attr : '' ),
							( ( $options['fields']['required'][$index] ) ? '<span class="required">*</span>' : '' ),
							( ( $options['fields']['required'][$index] ) ? $required_class : '' )
						);
					}
			 		break;

			 	case 'last_name':
			 		if( $args['last'] ) { 
				 		$custom_fields .= sprintf(
							'<div class="uf-field uf-field-text %1$s">
								<label for="%1$s">%2$s %4$s</label>
								<input type="text" name="%1$s" autocomplete="last_name" class="input %1$s %5$s" placeholder="%2$s" size="20" %3$s />
							</div>',
							$type,
							esc_html( $options['fields']['title'][$index] ),
							( ( $options['fields']['required'][$index] ) ? $required_attr : '' ),
							( ( $options['fields']['required'][$index] ) ? '<span class="required">*</span>' : '' ),
							( ( $options['fields']['required'][$index] ) ? $required_class : '' )
						);
					}
			 		break;

			 	case 'log':
			 		if( $args['useroremail'] ) {
				 		$custom_fields .= sprintf(
							'<div class="uf-field uf-field-text %1$s">
								<label for="%1$s">%2$s %4$s</label>
								<input type="text" name="%1$s" autocomplete="username" class="input %1$s %5$s" placeholder="%2$s" size="20" %3$s />
							</div>',
							$type,
							esc_html( $options['fields']['title'][$index] ),
							( ( $options['fields']['required'][$index] ) ? $required_attr : '' ),
							( ( $options['fields']['required'][$index] ) ? '<span class="required">*</span>' : '' ),
							( ( $options['fields']['required'][$index] ) ? $required_class : '' )
						);
				 	}
			 		break;

			 	case 'email':
			 		$custom_fields .= sprintf(
						'<div class="uf-field uf-field-text %1$s">
							<label for="%1$s">%2$s %4$s</label>
							<input type="email" name="%1$s" autocomplete="email" class="input %1$s %5$s" placeholder="%2$s" size="20" %3$s />
						</div>',
						$type,
						esc_html( $options['fields']['title'][$index] ),
						( ( $options['fields']['required'][$index] ) ? $required_attr : '' ),
						( ( $options['fields']['required'][$index] ) ? '<span class="required">*</span>' : '' ),
						( ( $options['fields']['required'][$index] ) ? $required_class : '' )
					);
			 		break;

			 	case 'pwd':
			 		$custom_fields .= sprintf(
						'<div class="uf-field uf-field-text %1$s">
							<label for="%1$s">%2$s %4$s</label>
							<input type="password" name="%1$s" autocomplete="current-password" placeholder="%2$s" minlength="5" spellcheck="false" class="input %1$s %5$s" size="20" %3$s />
						</div>',
						$type,
						esc_html( $options['fields']['title'][$index] ),
						( ( $options['fields']['required'][$index] ) ? $required_attr : '' ),
						( ( $options['fields']['required'][$index] ) ? '<span class="required">*</span>' : '' ),
						( ( $options['fields']['required'][$index] ) ? $required_class : '' )
					);
					break;

			 	case 'cpwd':
			 		if( $args['confirm'] ) {
				 		$custom_fields .= sprintf(
							'<div class="uf-field uf-field-text %1$s">
								<label for="%1$s">%2$s %4$s</label>
								<input type="password" name="%1$s" autocomplete="confirm-password" placeholder="%2$s" spellcheck="false" class="input %1$s %5$s" size="20" %3$s />
							</div>',
							$type,
							esc_html( $options['fields']['title'][$index] ),
							( ( $options['fields']['required'][$index] ) ? $required_attr : '' ),
							( ( $options['fields']['required'][$index] ) ? '<span class="required">*</span>' : '' ),
							( ( $options['fields']['required'][$index] ) ? 'matchpwd' : '' )
						);
				 	}
			 		break;


			 	case 'text':
			 		$custom_fields .= sprintf(
						'<div class="uf-field uf-field-text %1$s">
							<label for="%1$s">%2$s %4$s</label>
							<input type="text" name="%1$s" class="input %1$s %5$s" placeholder="%2$s" size="20" %3$s />
						</div>',
						$slug,
						esc_html( $options['fields']['title'][$index] ),
						( ( $options['fields']['required'][$index] ) ? $required_attr : '' ),
						( ( $options['fields']['required'][$index] ) ? '<span class="required">*</span>' : '' ),
						( ( $options['fields']['required'][$index] ) ? $required_class : '' )
					);
			 		break;
			 	
			 	case 'number':
			 		$custom_fields .= sprintf(
						'<div class="uf-field uf-field-number %1$s">
							<label for="%1$s">%2$s %4$s</label>
							<input type="number" name="%1$s" class="number %1$s %5$s" placeholder="%2$s" size="20" %3$s />
						</div>',
						$slug,
						esc_html( $options['fields']['title'][$index] ),
						( ( $options['fields']['required'][$index] ) ? $required_attr : '' ),
						( ( $options['fields']['required'][$index] ) ? '<span class="required">*</span>' : '' ),
						( ( $options['fields']['required'][$index] ) ? $required_class : '' )
					);
			 		break;
			 	
			 	case 'textarea':
			 		$custom_fields .= sprintf(
						'<div class="uf-field uf-field-textarea %1$s">
							<label for="%1$s">%2$s %4$s</label>
							<textarea name="%1$s" class="textarea %1$s %5$s" placeholder="%2$s" rows="4" cols="50" %3$s></textarea>
						</div>',
						$slug,
						esc_html( $options['fields']['title'][$index] ),
						( ( $options['fields']['required'][$index] ) ? $required_attr : '' ),
						( ( $options['fields']['required'][$index] ) ? '<span class="required">*</span>' : '' ),
						( ( $options['fields']['required'][$index] ) ? $required_class : '' )
					);
			 		break;
			 	
			 	case 'select':

			 		$opt_str = "<option value=''></option>";
			 		foreach ($opt_arr as $opt) {
			 			$opt_str .= '<option value="'.trim($opt).'">'.trim($opt).'</option>';
			 		}
			 		$custom_fields .= sprintf(
						'<div class="uf-field uf-field-select %1$s">
							<label for="%1$s">%2$s %5$s</label>
							<select name="%1$s" class="select %1$s %6$s" %4$s>%3$s</select>
						</div>',
						$slug,
						esc_html( $options['fields']['title'][$index] ),
						$opt_str,
						( ( $options['fields']['required'][$index] ) ? $required_attr : '' ),
						( ( $options['fields']['required'][$index] ) ? '<span class="required">*</span>' : '' ),
						( ( $options['fields']['required'][$index] ) ? $required_class : '' )
					);
			 		break;
			 	
			 	case 'checkbox':

			 		$opt_str = "";
			 		foreach ($opt_arr as $opt) {
			 			$opt_str .= '<div class="uf-checkbox"><input type="checkbox" name="'.$slug.'[]" value="'.trim($opt).'" class="checkbox" /> '.trim($opt).'</div>';
			 		}
			 		$custom_fields .= sprintf(
						'<div class="uf-field uf-field-checkbox %1$s checkbox%5$s">
							<label for="%1$s">%2$s %4$s</label>
							<div class="uf-checkboxs">%3$s</div>
						</div>',
						$slug,
						esc_html( $options['fields']['title'][$index] ),
						$opt_str,
						( ( $options['fields']['required'][$index] ) ? '<span class="required">*</span>' : '' ),
						( ( $options['fields']['required'][$index] ) ? $required_class : '' )
					);
			 		break;
			 	

			 	case 'radio':

			 		$opt_str = "";
			 		foreach ($opt_arr as $opt) {
			 			$opt_str .= '<div class="uf-radio"><input type="radio" name="'.$slug.'" value="'.trim($opt).'" class="radio" /> '.trim($opt).'</div>';
			 		}
			 		$custom_fields .= sprintf(
						'<div class="uf-field uf-field-radio %1$s radio%5$s">
							<label for="%1$s">%2$s %4$s</label>
							<div class="uf-radios">%3$s</div>
						</div>',
						$slug,
						esc_html( $options['fields']['title'][$index] ),
						$opt_str,
						( ( $options['fields']['required'][$index] ) ? '<span class="required">*</span>' : '' ),
						( ( $options['fields']['required'][$index] ) ? $required_class : '' )
					);
			 		break;
			 	
			 	case 'country':

			 		$json = '{"BD": "Bangladesh", "BE": "Belgium", "BF": "Burkina Faso", "BG": "Bulgaria", "BA": "Bosnia and Herzegovina", "BB": "Barbados", "WF": "Wallis and Futuna", "BL": "Saint Barthelemy", "BM": "Bermuda", "BN": "Brunei", "BO": "Bolivia", "BH": "Bahrain", "BI": "Burundi", "BJ": "Benin", "BT": "Bhutan", "JM": "Jamaica", "BV": "Bouvet Island", "BW": "Botswana", "WS": "Samoa", "BQ": "Bonaire, Saint Eustatius and Saba ", "BR": "Brazil", "BS": "Bahamas", "JE": "Jersey", "BY": "Belarus", "BZ": "Belize", "RU": "Russia", "RW": "Rwanda", "RS": "Serbia", "TL": "East Timor", "RE": "Reunion", "TM": "Turkmenistan", "TJ": "Tajikistan", "RO": "Romania", "TK": "Tokelau", "GW": "Guinea-Bissau", "GU": "Guam", "GT": "Guatemala", "GS": "South Georgia and the South Sandwich Islands", "GR": "Greece", "GQ": "Equatorial Guinea", "GP": "Guadeloupe", "JP": "Japan", "GY": "Guyana", "GG": "Guernsey", "GF": "French Guiana", "GE": "Georgia", "GD": "Grenada", "GB": "United Kingdom", "GA": "Gabon", "SV": "El Salvador", "GN": "Guinea", "GM": "Gambia", "GL": "Greenland", "GI": "Gibraltar", "GH": "Ghana", "OM": "Oman", "TN": "Tunisia", "JO": "Jordan", "HR": "Croatia", "HT": "Haiti", "HU": "Hungary", "HK": "Hong Kong", "HN": "Honduras", "HM": "Heard Island and McDonald Islands", "VE": "Venezuela", "PR": "Puerto Rico", "PS": "Palestinian Territory", "PW": "Palau", "PT": "Portugal", "SJ": "Svalbard and Jan Mayen", "PY": "Paraguay", "IQ": "Iraq", "PA": "Panama", "PF": "French Polynesia", "PG": "Papua New Guinea", "PE": "Peru", "PK": "Pakistan", "PH": "Philippines", "PN": "Pitcairn", "PL": "Poland", "PM": "Saint Pierre and Miquelon", "ZM": "Zambia", "EH": "Western Sahara", "EE": "Estonia", "EG": "Egypt", "ZA": "South Africa", "EC": "Ecuador", "IT": "Italy", "VN": "Vietnam", "SB": "Solomon Islands", "ET": "Ethiopia", "SO": "Somalia", "ZW": "Zimbabwe", "SA": "Saudi Arabia", "ES": "Spain", "ER": "Eritrea", "ME": "Montenegro", "MD": "Moldova", "MG": "Madagascar", "MF": "Saint Martin", "MA": "Morocco", "MC": "Monaco", "UZ": "Uzbekistan", "MM": "Myanmar", "ML": "Mali", "MO": "Macao", "MN": "Mongolia", "MH": "Marshall Islands", "MK": "Macedonia", "MU": "Mauritius", "MT": "Malta", "MW": "Malawi", "MV": "Maldives", "MQ": "Martinique", "MP": "Northern Mariana Islands", "MS": "Montserrat", "MR": "Mauritania", "IM": "Isle of Man", "UG": "Uganda", "TZ": "Tanzania", "MY": "Malaysia", "MX": "Mexico", "IL": "Israel", "FR": "France", "IO": "British Indian Ocean Territory", "SH": "Saint Helena", "FI": "Finland", "FJ": "Fiji", "FK": "Falkland Islands", "FM": "Micronesia", "FO": "Faroe Islands", "NI": "Nicaragua", "NL": "Netherlands", "NO": "Norway", "NA": "Namibia", "VU": "Vanuatu", "NC": "New Caledonia", "NE": "Niger", "NF": "Norfolk Island", "NG": "Nigeria", "NZ": "New Zealand", "NP": "Nepal", "NR": "Nauru", "NU": "Niue", "CK": "Cook Islands", "XK": "Kosovo", "CI": "Ivory Coast", "CH": "Switzerland", "CO": "Colombia", "CN": "China", "CM": "Cameroon", "CL": "Chile", "CC": "Cocos Islands", "CA": "Canada", "CG": "Republic of the Congo", "CF": "Central African Republic", "CD": "Democratic Republic of the Congo", "CZ": "Czech Republic", "CY": "Cyprus", "CX": "Christmas Island", "CR": "Costa Rica", "CW": "Curacao", "CV": "Cape Verde", "CU": "Cuba", "SZ": "Swaziland", "SY": "Syria", "SX": "Sint Maarten", "KG": "Kyrgyzstan", "KE": "Kenya", "SS": "South Sudan", "SR": "Suriname", "KI": "Kiribati", "KH": "Cambodia", "KN": "Saint Kitts and Nevis", "KM": "Comoros", "ST": "Sao Tome and Principe", "SK": "Slovakia", "KR": "South Korea", "SI": "Slovenia", "KP": "North Korea", "KW": "Kuwait", "SN": "Senegal", "SM": "San Marino", "SL": "Sierra Leone", "SC": "Seychelles", "KZ": "Kazakhstan", "KY": "Cayman Islands", "SG": "Singapore", "SE": "Sweden", "SD": "Sudan", "DO": "Dominican Republic", "DM": "Dominica", "DJ": "Djibouti", "DK": "Denmark", "VG": "British Virgin Islands", "DE": "Germany", "YE": "Yemen", "DZ": "Algeria", "US": "United States", "UY": "Uruguay", "YT": "Mayotte", "UM": "United States Minor Outlying Islands", "LB": "Lebanon", "LC": "Saint Lucia", "LA": "Laos", "TV": "Tuvalu", "TW": "Taiwan", "TT": "Trinidad and Tobago", "TR": "Turkey", "LK": "Sri Lanka", "LI": "Liechtenstein", "LV": "Latvia", "TO": "Tonga", "LT": "Lithuania", "LU": "Luxembourg", "LR": "Liberia", "LS": "Lesotho", "TH": "Thailand", "TF": "French Southern Territories", "TG": "Togo", "TD": "Chad", "TC": "Turks and Caicos Islands", "LY": "Libya", "VA": "Vatican", "VC": "Saint Vincent and the Grenadines", "AE": "United Arab Emirates", "AD": "Andorra", "AG": "Antigua and Barbuda", "AF": "Afghanistan", "AI": "Anguilla", "VI": "U.S. Virgin Islands", "IS": "Iceland", "IR": "Iran", "AM": "Armenia", "AL": "Albania", "AO": "Angola", "AQ": "Antarctica", "AS": "American Samoa", "AR": "Argentina", "AU": "Australia", "AT": "Austria", "AW": "Aruba", "IN": "India", "AX": "Aland Islands", "AZ": "Azerbaijan", "IE": "Ireland", "ID": "Indonesia", "UA": "Ukraine", "QA": "Qatar", "MZ": "Mozambique"}';

			        $countries = json_decode($json, true);

			        $opt_str = "<option value=''></option>";
		            foreach ($countries as $key => $value) {
		                $opt_str .= '<option value="'.$key.'">'.$key." - ".$value.'</option>';
		            }

			 		$custom_fields .= sprintf(
						'<div class="uf-field uf-field-select %1$s">
							<label for="%1$s">%2$s %5$s</label>
							<select name="%1$s" class="select %1$s %6$s" %4$s>%3$s</select>
						</div>',
						$slug,
						esc_html( $options['fields']['title'][$index] ),
						$opt_str,
						( ( $options['fields']['required'][$index] ) ? $required_attr : '' ),
						( ( $options['fields']['required'][$index] ) ? '<span class="required">*</span>' : '' ),
						( ( $options['fields']['required'][$index] ) ? $required_class : '' )
					);
			 		break;
			 	
			 	case 'heading':

			 		$custom_fields .= sprintf(
						'<div class="uf-field uf-field-heading  %1$s">
							<h3>%2$s</h3>
						</div>',
						$slug,
						esc_html( $options['fields']['title'][$index] )
					);
			 		break;
			 	
			 	default:
			 		// code...
			 		break;
			 } $type;
		}

		$form =
			sprintf(
				'<form name="%1$s" id="%1$s" action="" method="post">',
				esc_attr( $args['form_id'] )
			) .
			$register_form_top.
			( $custom_fields ? $custom_fields : '' ) .
			sprintf(
				'<div class="uf-field uf-field-button register-submit">
					<input type="submit" name="wp-submit" id="%1$s" class="button button-primary" value="%2$s" style="background:%4$s" />
					<input type="hidden" name="redirect_to" value="%3$s" />
				</div>',
				esc_attr( $args['id_submit'] ),
				esc_attr( $args['register_button'] ),
				esc_url( $args['redirect'] ),
				esc_html( $args['color'] )
			) .
			$register_form_bottom .
			'</form>';

		return $form;
	}

	/**
	 * Filter: Forgot password retrive password email message reset link replacement.
	 *
	 * @access	static
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public static function forgot_password_retrieve_password_message_change_link( $message, $key, $user_login ) {
		check_ajax_referer( 'USERFORMS-nonce', 'ajax_nonce_parameter' );

		$options = get_option( 'user_forms_opt' );
		if( isset($options['forgot_page']) ){
			$forgot_page = $options['forgot_page'];
			$slug = get_post_field( 'post_name', $forgot_page );
		
		
			//if ( is_page( $forgot_page ) ){
			if ( isset($_POST['action']) && $_POST['action'] == "USERFORMS_forgot_password_ajax_call" ){

				$nonce = wp_create_nonce( 'USERFORMS_reset_password_nonce' );

				$site_name  = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
				$reset_link = network_site_url( $slug."?action=rp&key=$key&login=" . rawurlencode( $user_login ).'&_wpnonce='.$nonce, 'login' );

				/* translators: email text message start*/
				$message = sprintf( esc_html__( 'Someone has requested a password reset for the following account: %s', 'user-forms' ), $user_login ) . "\n";
				/* translators: email sitename text */
				$message .= sprintf( esc_html__( 'Site Name: %s', 'user-forms' ), $site_name ) . "\n";
				/* translators: email siteurl text */
				$message .= sprintf( esc_html__( 'Site URL: %s', 'user-forms' ), network_home_url( '/' ) ) . "\n";
				/* translators: email username text */
				$message .= sprintf( esc_html__( 'Username: %s', 'user-forms' ), $user_login ) . "\n";
				$message .= __( 'If this was a mistake, just ignore this email and nothing will happen.', 'user-forms' ) . "\n";
				$message .= __( 'To reset your password, visit the following address:', 'user-forms' ) . "\n";
				$message .= $reset_link . "\n";
			}
		}

		return $message;
	}

	/**
	 * Display forgot password form.
	 *
	 * @access	static
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public static function shortcode_forgot_password_form_call( $atts, $content = "" ) {
		
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'rp' ){

			$nonce = isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field(wp_unslash($_REQUEST['_wpnonce'])) : '';

			if ( ! wp_verify_nonce( $nonce, 'USERFORMS_reset_password_nonce' ) ) {
				die( esc_html__('Nonce is invalid', 'user-forms') ); 
			}
		}

		$action = isset( $_REQUEST['action'] ) ? sanitize_text_field(wp_unslash($_REQUEST['action'])) : '';
		$error = isset( $_REQUEST['error'] ) ? sanitize_text_field(wp_unslash($_REQUEST['error'])) : '';
		
		$options = get_option( 'user_forms_opt' );

		if ( $action == 'rp' ){

			if( !empty($options['retrieve_redirect']) ){
				$redirect = $options['retrieve_redirect'];
			}else{
				$redirect = '';//( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			}
			
			$default = array(
				'redirect'          => $redirect,
				'form_id'           => 'resetpassform',
				'id_submit'         => 'wp-submit',
				'retrieve_button'    => ( !empty($options['retrieve_button']) ? $options['retrieve_button'] : __( 'Save Password', 'user-forms' ) ),
				'bottom_links'	=> ( !empty($options['retrieve_links']) ? $options['retrieve_links'] : 0 ),
				'label_log_in'    => __( 'Log In', 'user-forms' ),
				'label_register'    => __( 'Create an account', 'user-forms' ),
				'color'    	=> ( !empty($options['color']) ? $options['color'] : '' ),
			);
		}else{

			if( !empty($options['forgot_redirect']) ){
				$redirect = $options['forgot_redirect'];
			}else{
				$redirect = '';//( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			}
			$default = array(
				'redirect'          => $redirect,
				'form_id'           => 'lostpasswordform',
				'label_username'    => __( 'Username or Email Address', 'user-forms' ),
				'id_submit'         => 'wp-submit',
				'forgot_button'    => ( !empty($options['forgot_button']) ? $options['forgot_button'] : __( 'Get New Password', 'user-forms' ) ),
				'bottom_links'	=> ( !empty($options['forgot_links']) ? $options['forgot_links'] : 0 ),
				'label_log_in'    => __( 'Log In', 'user-forms' ),
				'label_register'    => __( 'Create an account', 'user-forms' ),
				'color'    	=> ( !empty($options['color']) ? $options['color'] : '' ),
			);
		}

		$args = shortcode_atts( $default, $atts );

		switch ( $action ) {

			case 'rp':

				$pageform = 'retrive';
				$status = "<div class='status'></div>";

				$keyerror = 0;

				$args['forgot_button'] = __('Save Password', 'user-forms');

				if ( isset( $_GET['key'] ) && isset( $_GET['login'] ) ) {
					
					$rp_key = sanitize_text_field( wp_unslash( $_GET['key'] ) );
					$rp_login = sanitize_text_field( wp_unslash( $_GET['login'] ) );
					
					$user = check_password_reset_key( $rp_key, $rp_login );

					if ( ! $user || is_wp_error( $user ) ) {
					
						if ( $user && $user->get_error_code() === 'expired_key' ) {
							//wp_redirect( get_permalink($options['forgot_page']).'?error=expiredkey' );
							$status = "<div class='status'><div class='error'>".__('Your password reset link has expired. Please request a new link below', 'user-forms')."</div></div>";
							$keyerror = get_permalink($options['forgot_page']).'?error=expiredkey';
						} else {
							//wp_redirect( get_permalink($options['forgot_page']).'?error=invalidkey' );
							$status = "<div class='status'><div class='error'>".__('Your password reset link appears to be invalid. Please request a new link below', 'user-forms')."</div></div>";
							$keyerror = get_permalink($options['forgot_page']).'?error=invalidkey';
						}
						//exit;
						
					}
					
					if( $keyerror != 0 ){

						$form = "";
					}else{
						$form =
							sprintf(
								'<form name="%1$s" id="%1$s" action="%2$s" method="post">',
								esc_attr( $args['form_id'] ),
								esc_url( network_site_url( 'wp-login.php?action=resetpass', 'login_post' ) )
							) .
							sprintf(
								'<div class="uf-field uf-field-text user-login">
									<label for="pass1">%1$s</label>
									<input type="password" id="pass1" name="pass1" autocomplete="new-password" class="input" size="20" required="required" />
								</div>',
								__( 'New password', 'user-forms' )
							) .
							sprintf(
								'<div class="uf-field uf-field-text user-login">
									<label for="pass2">%1$s</label>
									<input type="password" id="pass2" name="pass2" autocomplete="new-password" class="input" size="20" required="required" />
								</div>',
								__( 'Confirm new password', 'user-forms' )
							) .
							sprintf(
								'<div class="uf-field uf-field-button register-submit">
									<input type="submit" name="wp-submit" id="%1$s" class="button button-primary" value="%2$s" style="background:%4$s" />
									<input type="hidden" name="rp_key" value="%5$s" />
									<input type="hidden" name="rp_login" value="%6$s" />
									<input type="hidden" name="redirect_to" value="%3$s" />
								</div>',
								esc_attr( $args['id_submit'] ),
								esc_attr( $args['retrieve_button'] ),
								esc_url( $args['redirect'] ),
								esc_html( $args['color'] ),
								esc_attr( $rp_key ),
								esc_attr( $rp_login )
							) .
							'</form>';
					}
				}
				break;

			default:

				$pageform = 'forgot';
				$status = "<div class='status'></div>";

				if($error != ''){
					if ( $error == 'expiredkey' ) {
						$status = "<div class='status'><div class='error'>".__('Your password reset link has expired. Please request a new link below', 'user-forms')."</div></div>";
					} else if ( $error == 'invalidkey' ) {
						$status = "<div class='status'><div class='error'>".__('Your password reset link appears to be invalid. Please request a new link below', 'user-forms')."</div></div>";
					}
				}

				$form = '<div class="uf-notice uf-notice-info">'.__( 'Please enter your username or email address. You will receive an email message with instructions on how to reset your password.', 'user-forms' ).'</div>';
				$form .=
				sprintf(
					'<form name="%1$s" id="%1$s" action="%2$s" method="post">',
					esc_attr( $args['form_id'] ),
					esc_url( network_site_url( 'wp-login.php?action=lostpassword', 'login_post' ) )
				) .
				sprintf(
					'<div class="uf-field uf-field-text user-login">
						<label for="user_login">%1$s</label>
						<input type="text" id="user_login" name="user_login" autocomplete="username" class="input" size="20" required="required" />
					</div>',
					esc_html( $args['label_username'] )
				) .
				sprintf(
					'<div class="uf-field uf-field-button register-submit">
						<input type="submit" name="wp-submit" id="%1$s" class="button button-primary" value="%2$s" style="background:%4$s" />
						<input type="hidden" name="redirect_to" value="%3$s" />
					</div>',
					esc_attr( $args['id_submit'] ),
					esc_attr( $args['forgot_button'] ),
					esc_url( $args['redirect'] ),
					esc_html( $args['color'] )
				) .
				'</form>';

				break;
				
		}

		if( !is_user_logged_in() ) { 

			$links = "";
			if( !$args['bottom_links'] ) {
				$login_link = (!empty($options['login_page'])) ? get_permalink($options['login_page']) : '';
				$register_link = (!empty($options['register_page'])) ? get_permalink($options['register_page']) : '';
				$links = sprintf(
					'<ul class="bottom-links"><li><a href="%1$s">%2$s</a></li><li><a href="%3$s">%4$s</a></li></ul>',
					esc_html( $login_link ),
					esc_html( $args['label_log_in'] ),
					esc_url( $register_link ),
					esc_html( $args['label_register'] )
				);
			}

			return '<div class="userforms-'.$pageform.'-password">'.$status.$form.$links.'</div>';

		} else {
			
			$current_user = wp_get_current_user();
			$message = sprintf(
				'<div class="user-message">Howdy, %1$s</div><ul class="user-links"><li><a href="%2$s">%3$s</a></li></ul>',
				esc_html( $current_user->display_name ),
				esc_url( wp_logout_url( home_url()) ),
				__('Logout','user-forms')
			);

			return '<div class="userforms-'.$pageform.'-password">'.$message.'</div>';
		}
	}

	/**
	 * The callback function for register : Ajax actions for logged-out users
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function register_ajax_call_callback() {
		check_ajax_referer( 'USERFORMS-nonce', 'ajax_nonce_parameter' );

		$register_data = isset( $_REQUEST['register_data'] ) ? (wp_kses_post(wp_unslash($_REQUEST['register_data']))) : '';
		
		$response = array( 'success' => false );

		parse_str($register_data, $register_data_array2);

		foreach ($register_data_array2 as $key => $value) {
			$register_data_array[str_replace("amp;", "", $key)] = $value;
		}

		$redirect_to = esc_url($register_data_array['redirect_to']);

		$options = get_option( 'user_forms_opt' );
		//echo '<pre>';print_r($register_data_array);exit;
		
		$userdata = array(
		    'user_email' => sanitize_email($register_data_array['email']),
		    'user_login' => sanitize_user($register_data_array['email']),
		    'user_pass'  => trim($register_data_array['pwd']),
		    'role'       => sanitize_text_field($options['role'])
		);

		if(!empty($register_data_array['log'])){
			$userdata['user_login'] = sanitize_user($register_data_array['log']);
		}
		if(!empty($register_data_array['first_name'])){
			$userdata['first_name'] = sanitize_text_field($register_data_array['first_name']);
		}
		if(!empty($register_data_array['last_name'])){
			$userdata['last_name'] = sanitize_text_field($register_data_array['last_name']);
		}

		//print_r($userdata);exit;

		$new_user_id = wp_insert_user($userdata);

	    if ( ! is_wp_error( $new_user_id ) ) {
	    	
	    	$customfield = array();
	    	foreach ($register_data_array as $field => $fieldvalue) {
	    		
	    		if( !in_array($field, array('role', 'redirect_to', 'email', 'log', 'pwd', 'cpwd', 'first_name', 'last_name')) ){

	    			$customfield[] = sanitize_key($field);
	    			update_user_meta( $new_user_id, sanitize_key($field), wp_unslash($fieldvalue) );
	    		}
	    	}
	    	if(count($customfield)){
	    		update_user_meta( $new_user_id, 'user_forms_fields', $customfield );
	    	}

	    	$user_info = get_user_by( 'id', $new_user_id );

	    	wp_new_user_notification($new_user_id);
	    	wp_set_auth_cookie($user_info->ID);
			wp_set_current_user($new_user_id, $user_info->user_login);
			do_action('wp_login', $user_info->user_login, $user_info);
      	}

		if (  is_wp_error( $new_user_id ) ) {
			$response['msg'] = $new_user_id->get_error_message();
		} else {
			$response['success'] = true;
			$response['msg'] = __( 'Register successful, redirecting...', 'user-forms' );
			$response['redirect_to'] = $redirect_to;//$register_data_array['redirect_to'];
		}

		if( $response['success'] ){
			wp_send_json_success( $response );
		} else {
			wp_send_json_error( $response );
		}

		die();
	}

	/**
	 * Add status tag at top of the login form.
	 *
	 * @access	static
	 * @since	1.0.0
	 *
	 * @return	$content
	 */
	public static function add_error_message_tag_in_loginform($content, $args){
		
		return "<div class='status'></div>".$content;
	}

	/**
	 * The callback function for login : Ajax actions for logged-out users
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function login_ajax_call_callback() {
		check_ajax_referer( 'USERFORMS-nonce', 'ajax_nonce_parameter' );

		$login_data = isset( $_REQUEST['login_data'] ) ? wp_kses_post(wp_unslash($_REQUEST['login_data'])) : '';
		$response = array( 'success' => false );

		parse_str($login_data, $login_data_array2);
		
		foreach ($login_data_array2 as $key => $value) {
			$login_data_array[str_replace("amp;", "", $key)] = $value;
		}

		$redirect_to = esc_url($login_data_array['redirect_to']);
		
		$creds = array(
			'user_login'    => sanitize_user( $login_data_array['log'] ),
			'user_password' => trim($login_data_array['pwd']),
			'remember'      => isset( $login_data_array['rememberme']) ? true : false
		);
		
		$user_signon = wp_signon( $creds, true );

		if (  is_wp_error( $user_signon ) ) {
			$response['msg'] = $user_signon->get_error_message();
		} else {

			wp_set_auth_cookie($user_signon->ID);
			wp_set_current_user($user_signon->ID, $user_signon->user_login);
			do_action('wp_login', $user_signon->user_login, wp_get_current_user());
			
			$response['success'] = true;
			$response['msg'] = __( 'Login successful, redirecting...', 'user-forms' );
			$response['redirect_to'] = $redirect_to;//$login_data_array['redirect_to'];
		}

		if( $response['success'] ){
			wp_send_json_success( $response );
		} else {
			wp_send_json_error( $response );
		}

		die();
	}

	/**
	 * The callback function for forgot password : Ajax actions for logged-out users
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function forgot_password_ajax_call_callback() {
		check_ajax_referer( 'USERFORMS-nonce', 'ajax_nonce_parameter' );

		$forgot_data = isset( $_REQUEST['forgot_data'] ) ? wp_kses_post(wp_unslash($_REQUEST['forgot_data'])) : '';
		$response = array( 'success' => false );

		parse_str($forgot_data, $forgot_data_array2);

		foreach ($forgot_data_array2 as $key => $value) {
			$forgot_data_array[str_replace("amp;", "", $key)] = $value;
		}

		$redirect_to = esc_url($forgot_data_array['redirect_to']);
		
		$userdata = array(
		    'user_login' => sanitize_user($forgot_data_array['user_login']),
		);

		$retrieve_pwd = retrieve_password( $userdata['user_login'] );

		if (  is_wp_error( $retrieve_pwd ) ) {
			$response['msg'] = $retrieve_pwd->get_error_message();
		} else {
			$response['success'] = true;
			if($forgot_data_array['redirect_to'] == ''){
				$response['msg'] = __( 'We have received a request to reset the password for your account, Please check your email for the confirmation link and follow the instructions.', 'user-forms' );
			}else{
				$response['msg'] = __( 'We have received a request to reset the password for your account, Please check your email for the confirmation link and follow the instructions. redirecting...', 'user-forms' );
			}
			$response['redirect_to'] = $redirect_to;//$forgot_data_array['redirect_to'];
		}

		if( $response['success'] ){
			wp_send_json_success( $response );
		} else {
			wp_send_json_error( $response );
		}

		die();
	}

	/**
	 * The callback function for retrive password : Ajax actions for logged-out users
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function forgot_retrive_password_ajax_call_callback() {
		check_ajax_referer( 'USERFORMS-nonce', 'ajax_nonce_parameter' );

		$forgot_data = isset( $_REQUEST['forgot_data'] ) ? wp_kses_post(wp_unslash($_REQUEST['forgot_data'])) : '';
		$response = array( 'success' => false );

		parse_str($forgot_data, $forgot_data_array2);

		foreach ($forgot_data_array2 as $key => $value) {
			$forgot_data_array[str_replace("amp;", "", $key)] = $value;
		}

		$redirect_to = esc_url($forgot_data_array['redirect_to']);

		$userdata = array(
		    'login' => sanitize_user($forgot_data_array['rp_login']),
		    'pass1' => trim($forgot_data_array['pass1']),
		    'pass2' => trim($forgot_data_array['pass2']),
		);

		if ( empty( $userdata['pass1'] ) ) {
			
			$response['msg'] = __( 'The password cannot be a space or all spaces', 'user-forms' );
		}

		if ( ! empty( $userdata['pass1'] ) && trim( $userdata['pass2'] ) !== $userdata['pass1'] ) {
			
			$response['msg'] = __( '<strong>Error:</strong> The passwords do not match', 'user-forms' );
		}

		if (  !isset( $response['msg'] ) ) {

			$user = get_user_by('login', $userdata['login']);

			reset_password( $user, $userdata['pass1'] );

			$response['success'] = true;
			if($forgot_data_array['redirect_to'] == ''){
				$response['msg'] = __( 'Your password has been reset successful', 'user-forms' );
			}else{
				$response['msg'] = __( 'Your password has been reset successful, redirecting...', 'user-forms' );
			}
			$response['redirect_to'] = $redirect_to;//$forgot_data_array['redirect_to'];
			
		}
		
		if( $response['success'] ){
			wp_send_json_success( $response );
		} else {
			wp_send_json_error( $response );
		}

		die();
	}

}
