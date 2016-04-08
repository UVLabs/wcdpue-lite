<?php
/*
Plugin Name: TLD WC Downloadable Product Update Emails
Description: Inform customers when there is an update to their downloadable product.
Version: 1.0.0-alpha
Author: Uriahs Victor
Author URI: http://soaringleads.com
License: GPL2
*/

include_once('fields.php');

//=================== BELOW ARE TESTS FOR BETTER VERIFICATION, NOT YET WORKING ==============//
//$screen = "Edit Screen";
function tld_get_product_screen( $current_screen ) {
	if ( 'product' == $current_screen->post_type && 'post' == $current_screen->base ) {
		global $my_tld_screen;
		$my_tld_screen = "edit";

		//echo '<script>console.log("'.$screen.'")</script>';
	}
}
add_action( 'current_screen', 'tld_get_product_screen' );

//echo '<script>console.log("'.$screen.'")</script>';
if (!empty($my_tld_screen)){
	//echo '<script>console.log("Not empty")</script>';
}else{
	//	echo '<script>console.log("empty")</script>';
}

//=================== END =========================//


function tld_post_saved( $post_id ) {

	$tld_email_on_update = get_field('send_email_on_update', $post_id);
	//$tld_the_prod_ID = get_field('product_id', $post_id);
	$tld_the_prod_ID = $post_id;
	$tld_file_updated = get_field('updated_the_download_file', $post_id);
	//echo '<script>console.log("'.$tld_email_on_update.'")</script>';
	//echo '<script>console.log("'.$tld_the_prod_ID.'")</script>';
	//echo '<script>console.log("'.$tld_file_updated.'")</script>';

	if ( $tld_email_on_update == 'yes' && $tld_file_updated == 'yes'){

		global $wpdb;
		$tld_tbl_prefix = $wpdb->prefix;
		$tld_the_table = $tld_tbl_prefix . 'woocommerce_downloadable_product_permissions';
		$query_result = $wpdb->get_results("SELECT * FROM $tld_the_table WHERE product_id=$tld_the_prod_ID");

		foreach ( $query_result as $tld_email_address ){

			$tld_the_email = $tld_email_address->user_email;
			$post_title = get_the_title( $post_id );
			$post_url = get_permalink( $post_id );
			$subject = 'Your download has been updated!';
			$message = "There is an update for your product:\n\n";
			$tld_home_url = esc_url(home_url());
			$message .= $post_title . "\n\nDownload it from your account -> " . $tld_home_url . "/my-account";
			wp_mail( $tld_the_email, $subject, $message );

		}

		$tld_field_key = 'field_56fe9d96eaca6';
		$tld_value = 'no';
		update_field( $tld_field_key, $tld_value, $post_id );

	}

}

// run after ACF saves the $_POST['acf'] data
//find out what happens if multiple acf are on same screen and post is saved
add_action('acf/save_post', 'tld_post_saved', 20);
