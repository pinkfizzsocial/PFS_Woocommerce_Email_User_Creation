<?php
/*
* Plugin Name: PFS-Woocommerce Email User Creation
* Description: Force Woocommerce to use the email address for the user when creating a user account
* Version: 1.0.0
* Author: Pink Fizz Social
* Author URI: http://pinkfizz.social
* License: GPL2
*/

add_filter( 'woocommerce_new_customer_data', function( $data ) {
	$data['user_login'] = $data['user_email'];

	return $data;
} );