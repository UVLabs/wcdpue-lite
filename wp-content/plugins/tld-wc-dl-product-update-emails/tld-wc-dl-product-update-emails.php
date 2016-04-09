<?php
/*
Plugin Name: TLD WC Downloadable Product Update Emails
Plugin URI: http://soaringleads.com
Description: Inform customers when there is an update to their downloadable product.
Version: 2.0.0-alpha
Author: Uriahs Victor
Author URI: http://soaringleads.com
License: GPL2
*/

//include_once('fields.php');

defined( 'ABSPATH' ) or die( 'Time for a U turn!' );

//add_action('save_post', 'dc_prepend_save_display_metabox');

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

function tld_dl_product_emails_metaboxes(){
	add_meta_box(
	'tld_dl_product_emails_metabox',
	'Product Email Options',
	'tld_metabox_fields',
	'',
	'side',
	'high'
);
}
add_action('add_meta_boxes_product', 'tld_dl_product_emails_metaboxes', 10, 2);

function tld_metabox_fields(){
//way to little options to create a stylesheet imo
	?>

	<div>

		<div>

			<div>
				<label for="tld-option-selected" style="font-size:12px; margin-bottom: 15px">Select whether you updated the download file.</label>
			</div>
			<!-- /.tld-meta-head -->

			<div style="margin-top: 18px;">
				<input type="radio" name="tld-option-selected" value="yes"><span style="margin-right: 10px;">Yes</span>
				<input type="radio" name="tld-option-selected" value="no" checked><span>No</span>
			</div>
			<!-- /.tld-meta-options -->

		</div>
		<!-- /.meta-row -->
	</div>
	<?php
}


function tld_post_saved( $post_id ) {

	//only run on product screen
	$tld_post_type = get_post_type();
	if ($tld_post_type == 'product'){

		//echo '<script>console.log("'.get_post_type().'")</script>';
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) )
		return;

		$tld_selected_option = $_POST['tld-option-selected'];

		if ( $tld_selected_option == 'yes'){

			global $wpdb;
			$tld_tbl_prefix = $wpdb->prefix;
			$tld_the_table = $tld_tbl_prefix . 'woocommerce_downloadable_product_permissions';
			$query_result = $wpdb->get_results("SELECT * FROM $tld_the_table WHERE product_id=$post_id");

			foreach ( $query_result as $tld_email_address ){

				$tld_the_email = $tld_email_address->user_email;
				$post_title = get_the_title( $post_id );
				$post_url = esc_url( get_permalink( $post_id ) );
				$subject = 'Your download has been updated!';
				$message = "There is an update for your product:\n\n";
				$tld_home_url = esc_url( home_url() );
				$message .= $post_title . "\n\nDownload it from your account -> " . $tld_home_url . "/my-account";
				wp_mail( $tld_the_email, $subject, $message );

			}
		}
	}
}

add_action('save_post', 'tld_post_saved');
