<?php
/*
* Plugin Name: PFS-Woocommerce Email User Creation
* Description: Force Woocommerce to use the email address for the user when creating a user account, also when updating the email address on a user profile all billing and orders are updated with the same email address.
* Version: 1.0.1
* Author: Pink Fizz Social
* Author URI: http://pinkfizz.social
* License: GPL2
*/

add_filter( 'woocommerce_new_customer_data', function( $data ) {
	$data['user_login'] = $data['user_email'];

	return $data;
} );

//Update billing and subscription email address when a user changes their main email address
add_action( 'profile_update', function( $user_id, $old_user_data ){
    $old_user_email = $old_user_data->data->user_email;
    $user = get_userdata( $user_id );
    $new_user_email = $user->user_email;

    // check if old and new email are not the same
    if ( $new_user_email !== $old_user_email ) {
        // update woocommerce billing email address to match user's wordpress email address
        update_user_meta( $user_id, 'billing_email', $new_user_email );

        // now loop through the user's woocommerce subscriptions to find the active subscription(s)
        $users_subscriptions = wcs_get_users_subscriptions( $user_id );
        foreach ($users_subscriptions as $subscription){
            if ( $subscription->has_status(array('active')) ) {
                // update the billing email for the active subscription as this is where the email address will be taken for future renewal orders
                update_post_meta( $subscription->get_id(), '_billing_email', $new_user_email );

                // per WooCommerce support, it is not necessary to update the billing email for the parent order of the active subscription, but here is how it would be done if desired
                $active_sub = wcs_get_subscription( $subscription->get_id() );
                update_post_meta( $active_sub->get_parent_id(), '_billing_email', $new_user_email );
            }
        }
    }
}, 10, 2 );