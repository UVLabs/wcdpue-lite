<?php
/*
Plugin Name: TLD WC Downloadable Product Update Emails
Description: Inform customers when there is an update to products
Author: Uriahs Victor
Author URI: http://soaringleads.com
*/

include_once('fields.php');

function tld_get_product_screen( $current_screen ) {
	if ( 'product' == $current_screen->post_type && 'post' == $current_screen->base ) {
		//echo '<script>console.log("product edit screen")</script>';
	}
}
add_action( 'current_screen', 'tld_get_product_screen' );




function tld_post_saved( $post_id ) {

	$tld_email_on_update = get_field('send_email_on_update', $post_id);
	$tld_the_prod_ID = get_field('product_id', $post_id);
	$tld_file_updated = get_field('updated_the_download_file', $post_id);
	//	echo '<script>console.log("'.$tld_email_on_update.'")</script>';
	//	echo '<script>console.log("'.$tld_the_prod_ID.'")</script>';
	//	echo '<script>console.log("'.$tld_file_updated.'")</script>';

	if ( $tld_email_on_update == 'yes' && $tld_file_updated == 'yes'){
		global $wpdb;
		$tld_tbl_prefix = $wpdb->prefix;
		$tld_the_table = $tld_tbl_prefix . 'woocommerce_downloadable_product_permissions';
		$query_result = $wpdb->get_results("SELECT * FROM $tld_the_table WHERE product_id=$tld_the_prod_ID");


		// bail early if no ACF data
		//Look more into this
	/*	if( empty($_POST['acf']) ) {

			echo '<script>alert("'.get_the_title($post_id).'");</script>';
		}else{
			echo '<script>console.log("data");</script>';
		}*/

		foreach ( $query_result as $tld_email_address )
		{
			$tld_the_email = $tld_email_address->user_email;
			$post_title = get_the_title( $post_id );
			$post_url = get_permalink( $post_id );
			$subject = 'Your download has been updated!';
			$message = "There is an update for your product:\n\n";
			$tld_home_url = esc_url(home_url());
			$message .= $post_title . "\n\nDownload it from your account -> " . $tld_home_url . "/my-account";
			wp_mail( $tld_the_email, $subject, $message );

		}
		$value = 'no';
		$field_key = 'field_56fe9d96eaca6';
		update_field( $field_key, $value, $post_id );
		//echo '<script>console.log("'.$tld_file_updated.'");</script>';

	}

}


// run after ACF saves the $_POST['acf'] data
add_action('acf/save_post', 'tld_post_saved', 20);

function tld_auto_ID($post_id){

	$tld_email_on_update = get_field('send_email_on_update', $post_id);
	$tld_the_prod_ID = get_field('product_id', $post_id);

	if ( $tld_email_on_update == 'yes' && empty($tld_the_prod_ID)){

		$tld_post_title = get_the_title($post_id);
		$field_key = 'field_56fe9cd6eaca4';

		update_field( $field_key, $post_id, $post_id );
		//echo '<script>console.log("'.$tld_post_title.'")</script>';

	}
}
add_action('acf/save_post', 'tld_auto_ID', 20);
?>
