=== User Forms ===
Contributors: tusharkapdi
Donate link: https://amplebrain.com/donate/
Tags: login form, register form, forgot password form, form builder, ajax login
Tested up to: 6.7
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Basic WordPress user login, register, forgot password forms plugin. Build custom registration & login and reset password forms with ajax effect & more

== Description ==

Designing WordPress custom registration forms with simply drag and drop fields and visually create user register forms. Login form, Forgot Password Form with global setting menagement.

User Forms gives you modern frontend user capture form that are fully customizable. User Forms provides a range of field types that will allow you to collect a wide range of data from users. User Forms also has a common pre-defined fields that come automatically installed with the plugin to allow you to create forms faster.

AJAX Login, Register and Forgot Password form with AJAX effects, No Screen Refreshes!

Login with AJAX, Register with AJAX, Retrive Password Link with AJAX, Reset Password with AJAX. Error Message Box with AJAX Field Validation Effects.

= Popup Setting =
* Install any Popup/Modal plugin from WordPress repositoory and folow below steps
* For Popup Login : Add shortcode [USERFORMS_LOGIN] in login popup content
* For Popup Register : Add shortcode [USERFORMS_REGISTER] in register popup content

=== User Forms Add-ons ===
* [Menu Option](https://wordpress.org/plugins/menu-option/)
You can use my Menu Option plugin for Logout Link and manage Login, Register, Forgot Password pages links in navigation menu

=== Shortcodes: ===

= Login Form Shortcode [USERFORMS_LOGIN] Parameters =

* "echo" Whether to display the login form or return the form HTML code. Default **false**
* "redirect" URL to redirect to. Default is to redirect back to the **request URI**
* "form_id" ID attribute value for the form. Default **loginform**
* "label_username" Label for the username or email address field. Default **Username or Email Address**
* "label_password" Label for the password field. Default **Password**
* "label_remember" Label for the remember field. Default **Remember Me**
* "label_log_in" Label for the submit button. Default **Log In**
* "label_register" Label for register link at bottom of form. Default **Create an account**
* "label_forgot_password" Label for the forgot password link at bottom of form. Default **Lost password?**
* "id_username" ID attribute value for the username field. Default **user_login**
* "id_password" ID attribute value for the password field. Default **user_pass**
* "id_remember" ID attribute value for the remember field. Default **rememberme**
* "id_submit" ID attribute value for the submit button. Default **wp-submit**
* "remember" Whether to display the "rememberme" checkbox in the form. Default **true**
* "bottom_links" Whether to display the links at bottom in the form. Default **true**
* "value_username" Default value for the username field
* "value_remember" Whether the "Remember Me" checkbox should be checked by default. Default **false**
* "required_username" Whether the username field has the 'required' attribute. Default **false**
* "required_password" Whether the password field has the 'required' attribute. Default **false**

= Register Form Shortcode [USERFORMS_REGISTER] Parameters =

* "column" Display Column Layout (Accepts '1' or '2'). Default **1**
* "redirect" URL to redirect to. Default is to redirect back to the **request URI**
* "form_id" ID attribute value for the form. Default **registerform**
* "label_username" Label for the username field. Default **Username**
* "label_useroremail" Label for the username or email address field. Default **Username or Email Address**
* "label_password" Label for the password field. Default **Password**
* "label_email" Label for the email field. Default **Email Address**
* "label_first" Label for the first name field. Default **First Name**
* "label_last" Label for the last name field. Default **Last Name**
* "label_confirm" Label for the confirm password field. Default **Confirm Password**
* "label_forgot_password" Label for forgot password link at bottom of form. Default **Forgot your password?**
* "label_login" Label for login link at bottom of form. Default **Alredy register? Login**
* "bottom_links" Whether to display the links at bottom in the form. Default **true**
* "useroremail" Whether to display the combained field Username or Email Address. Default **true**
* "first" Whether to display the First Name field. Default **flase**
* "last" Whether to display the Last Name field. Default **flase**
* "confirm" Whether to display the Confirm Password field. Default **flase**
* "useroremail" Whether to display the combained field Username or Email Address. Default **true**
* "id_submit" ID attribute value for the submit button. Default **wp-submit**
* "register_button" Text on the submit button. Default **Register**
* "color" Background Color of submit button. Default **#000000**

= Forgot Password Form Shortcode [USERFORMS_FORGOT_PASSWORD] Parameters =

* "redirect" URL to redirect to. Default is to redirect back to the **request URI**
* "id_submit" ID attribute value for the submit button. Default **wp-submit**
* "forgot_button" Label for the submit button in Forgot Password Form. Default **Get New Password**
* "retrieve_button" Label for the submit button in Reset Form. Default **Save Password**
* "label_username" Label for the username field in Forgot Password Form. Default **Username or Email Address**
* "label_register" Label for register link at bottom of form. Default **Create an account**
* "label_log_in" Label for login link at bottom of form. Default **Log In**
* "bottom_links" Whether to display the links at bottom in the form. Default **true**
* "color" Background Color of submit button. Default **#000000**

Note that the `Shortcode Parameters` also manageble at setting page from admin.

After the plugin is activated, you may configure the General Settings. Access your general settings by going to User Forms → General Settings.

= Features =

* **Drag-and-drop Form Builder** Create custom user registration forms visually with the drag and drop form builder. No coding skills needed.
* **Unlimited Custom Fields** Add custom form fields in your registration forms. Collect a wide range of data from users.
* **Ready-to-use Form** Save your time and effort with User Forms pre-designed form. No extra customization is needed.
* **Built-in Login Form** Add built-in login form to your website and let users securely log into their account from the front-end.
* **Built-in Forgot Password Form** Add built-in forgot password form to your website and let users securely retrive their account password from the front-end.
* **Assign User Roles During Registration** Control the access to the admin dashboard with the help of default user roles.
* **Mobile Responsive Forms** Display pixel-perfect forms across all device sizes, including mobile screens.
* **Password reset link** The login form provides a forgot password link which will take users to the password reset form if they have forgotten their account password.
* **Show/hide System Field** You can choose to show/hide system/default field from showing on the login and register forms.
* **Custom Fields** User Forms provides a range of form field types that will allow you to collect a wide range of data from users. The plugin also has a collection of pre-defined fields that come automatically installed with the plugin to allow you to create forms faster.
* **Multi-column layout** The User Forms allows you to create a one or two column layout option.
* **Re-order Form Fields** The User Forms builder allows you to move and re-order any fields by simply dragging and dropping it where you would like it to go.
* **AJAX Effects** The User Forms with AJAX Login, AJAX Register and AJAX Forgot Password forms, No Screen Refresh!.

= More Information =

* For help use [wordpress.org](http://wordpress.org/support/plugin/user-forms/)
* Fork or contribute on [Github](https://github.com/tusharkapdi/user-forms/)
* Visit [our website](https://amplebrain.com/plugins/user-forms/) for more
* Follow me on [Twitter](http://twitter.com/tusharkapdi/)
* View my other [WordPress Plugins](https://profiles.wordpress.org/tusharkapdi/)

= Support =

Did you enjoy this plugin? Please [donate to support ongoing development](https://amplebrain.com/donate/). Your contribution would be greatly appreciated.

== Installation ==

1. Download and extract the zip archive
2. Upload 'user-form' folder to '/wp-content/plugins/'
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Add the shortcodes to pages and configure the options as desired

== Frequently Asked Questions ==

= Is User Form mobile responsive? =

Yes. User Forms is designed to adapt nicely to any screen resolution. It includes specific designs for phones and desktops.

= Does the User Forms work with any WordPress theme? =

Yes. User Form will work with any theme. If you find a styling issue with your theme please create a post in the community forum.

= Can I add custom fields to my registration forms? =

Yes, Form builder allows you to add and arrange custom fields in your registration forms.

= Does the User Forms have Built-in Login Form? =

Yes. Use shortcode [USERFORMS_LOGIN] in any page and user can securely login to their account from the front-end.

= Does the User Forms have Built-in Register Form? =

Yes. Use shortcode [USERFORMS_REGISTER] in any page and user can securely register their account from the front-end.

= Does the User Forms have Built-in Forgot Password Form? =

Yes. Use shortcode [USERFORMS_FORGOT_PASSWORD] in any page and user can securely retrive their account password from the front-end.

= Where does the extra custom field of registration will display? =

Find section "User Forms : Registration Custom Fields" by Edit User Screen from the back-end.

= Can we assign any user role to new users sign up? =

Yes. You can assign a any user role to new register user. The default user role selector setting will automatically be assigned that specific role to the users when sign up on your site. Default role is `Subscriber`

== Screenshots ==

1. Login
2. Register
3. Forgot Password
4. Retrive Password
5. Settings

== What Next? ==

= New Features =
* Logout
* Conditional logic within the form builder
* Profile Page
* Edit Profile
* Change Password
* Delete Account
* Email Template
* Social Login/Register
* Captcha Integration
* User Dashboard
* Member Directory
* Popup Modal

== Changelog ==

= 1.0.0: December 06, 2024 =
* Initial release of User Forms

== Upgrade Notice ==

= 1.0.0 =
This is the initial release.