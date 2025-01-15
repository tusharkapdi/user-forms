/*------------------------- 
Frontend javascript
-------------------------*/

/**
 * 
 * This file contains all of the frontend javascript. 
 * 
 * Here's some jQuery example code you can use to fire code once the page is loaded: $(document).ready( function(){} );
 * 
 * Action USERFORMS_login_ajax_call, USERFORMS_register_ajax_call, USERFORMS_forgot_password_ajax_call, USERFORMS_forgot_retrive_password_ajax_call which was added in the User_Forms_Shortcodes class.
 * 
 */

(function( $ ) {

	"use strict";

    $(document).ready( function() {
        
        /*Forgot Password start*/
        $('.userforms-retrive-password form').on('submit', function(e){

            $.ajax({
                type : "post",
                dataType : "json",
                url : userforms.ajaxurl,
                data : {
                    action: "USERFORMS_forgot_retrive_password_ajax_call", 
                    forgot_data : $(this).serialize(), 
                    ajax_nonce_parameter: userforms.security_nonce
                },
                success: function(response) {
                    if ( response.success == true ) {
                        $('.userforms-retrive-password .status').html( '<div class="success">'+response.data['msg']+'</div>' );

                        if(response.data['redirect_to'] != ''){
                            window.location.href = response.data['redirect_to'];
                        }
                        $("#pass1, #pass2").val("");
                    } else {
                        $('.userforms-retrive-password .status').html( '<div class="error">'+response.data['msg']+'</div>' );
                    }
                }
            });

            e.preventDefault();

        });
        /*Forgot Password end*/
        
        /*Forgot Password start*/
        $('.userforms-forgot-password form').on('submit', function(e){

            $.ajax({
                type : "post",
                dataType : "json",
                url : userforms.ajaxurl,
                data : {
                    action: "USERFORMS_forgot_password_ajax_call", 
                    forgot_data : $(this).serialize(), 
                    ajax_nonce_parameter: userforms.security_nonce
                },
                success: function(response) {
                    if ( response.success == true ) {
                        $('.userforms-forgot-password .status').html( '<div class="success">'+response.data['msg']+'</div>' );

                        if(response.data['redirect_to'] != ''){
                            window.location.href = response.data['redirect_to'];
                        }
                        $("#user_login").val("");
                    } else {
                        $('.userforms-forgot-password .status').html( '<div class="error">'+response.data['msg']+'</div>' );
                    }
                }
            });

            e.preventDefault();

        });
        /*Forgot Password end*/

        /*Login start*/
        $('.userforms-login form').on('submit', function(e){

            $.ajax({
                type : "post",
                dataType : "json",
                url : userforms.ajaxurl,
                data : {
                    action: "USERFORMS_login_ajax_call", 
                    login_data : $(this).serialize(), 
                    ajax_nonce_parameter: userforms.security_nonce
                },
                success: function(response) {
                    if ( response.success == true ) {
                        $('.userforms-login form .status').html( '<div class="success">'+response.data['msg']+'</div>' );

                        window.location.href = response.data['redirect_to'];
                    } else {
                        $('.userforms-login form .status').html( '<div class="error">'+response.data['msg']+'</div>' );
                    }
                }
            });

            e.preventDefault();

        });
        /*Login end*/

        /*Registration start*/
        function UF_Reg_validate(){ 
            
            var isvalid = true;
            jQuery(".userforms-register .status").html( "<div class=\"error\"></div>" );


            jQuery( ".userforms-register .uf-field .mandatory" ).each(function() {
                if( jQuery(this).val() == "" ){
                    var input_title = jQuery(this).parent('div.uf-field').find('label').html().replace("*", "");
                    jQuery(".userforms-register .status .error").append( input_title + " is required field<br>" ); 
                    isvalid = false;    
                }
            });

            jQuery( ".userforms-register .uf-field-checkbox.checkboxmandatory" ).each(function() {
                if( !jQuery(this).find("input").is(":checked") ){
                    var input_title = jQuery(this).find('label').html().replace("*", "");
                    jQuery(".userforms-register .status .error").append( input_title + " is required field<br>" ); 
                    isvalid = false;    
                }
            });

            jQuery( ".userforms-register .uf-field-radio.radiomandatory" ).each(function() {
                if( !jQuery(this).find("input").is(":checked") ){
                    var input_title = jQuery(this).find('label').html().replace("*", "");
                    jQuery(".userforms-register .status .error").append( input_title + " is required field<br>" ); 
                    isvalid = false;    
                }
            });

            if (jQuery(".userforms-register .uf-field input").hasClass("matchpwd")) {

                if( jQuery(".userforms-register .uf-field .matchpwd").val() != jQuery(".userforms-register .uf-field .input.pwd").val() ) { 

                    var input_title = jQuery(".userforms-register .uf-field .matchpwd").parent('div.uf-field').find('label').html().replace("*", "");
                    jQuery(".userforms-register .status .error").append( input_title + " is not matched<br>" );
                    isvalid = false;
                }
            }

            var regEx = /\S+@\S+\.\S+/;
            var validEmail = regEx.test(jQuery(".userforms-register .input.email").val());
            if( !validEmail ) { 
                jQuery(".userforms-register .status .error").append( "Email Address is email field<br>" ); 
                isvalid = false;
            }
            
            if(isvalid){
                return true;
            }else{
                return false;
            }
        }
        $('.userforms-register form').on('submit', function(e){

            var isdataok = UF_Reg_validate();

            if(isdataok){
                $('.userforms-register .status').html( '<div class="success">Processing...</div>' );

                $.ajax({
                    type : "post",
                    dataType : "json",
                    url : userforms.ajaxurl,
                    data : {
                        action: "USERFORMS_register_ajax_call", 
                        register_data : $(this).serialize(), 
                        ajax_nonce_parameter: userforms.security_nonce
                    },
                    success: function(response) {
                        if ( response.success == true ) {
                            $('.userforms-register .status').html( '<div class="success">'+response.data['msg']+'</div>' );

                            window.location.href = response.data['redirect_to'];
                        } else {
                            $('.userforms-register .status').html( '<div class="error">'+response.data['msg']+'</div>' );
                        }
                    }
                });
            }else{
                
                $([document.documentElement, document.body]).animate({
                    scrollTop: $(".userforms-register .status").offset().top
                }, 2000);
            }

            e.preventDefault();

        });
        /*Registration end*/
    });

})( jQuery );
