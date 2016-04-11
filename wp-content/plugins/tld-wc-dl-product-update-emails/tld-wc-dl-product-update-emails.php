<?php
/*
Plugin Name: TLD WC Downloadable Product Update Emails
Plugin URI: http://soaringleads.com
Description: Inform customers when there is an update to their downloadable product.
Version: 3.0.0-alpha
Author: Uriahs Victor
Author URI: http://soaringleads.com
License: GPL2
*/

defined( 'ABSPATH' ) or die( 'Time for a U turn!' );
//register JS
function tld_load_assets($hook) {

	if ( 'post.php' != $hook ) {
		return;
	}
	wp_enqueue_script( 'tld_uilang', plugin_dir_url( __FILE__ ) . 'assets/js/uilang.js' );
	wp_enqueue_script( 'tld_scripts', plugin_dir_url( __FILE__ ) . 'assets/js/tld-scripts.js?v1.0.0' );
	wp_enqueue_style( 'tld_styles', plugin_dir_url( __FILE__ ) . 'assets/css/style.css?v1.0.0' );

}
add_action( 'admin_enqueue_scripts', 'tld_load_assets' );


//global $pagenow, $typenow;
//var_dump($pagenow);
//var_dump($typenow);
//echo '<script>console.log("'.$product->get_type.'")</script>';
//$bool = WC_Product::is_downloadable();
//echo '<script>console.log("'.$bool.'")</script>';

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
	//ADD NOUNCE FIELD
	?>

	<div>

		<div>

			<div>
				<label for="tld-option-selected" style="font-size:12px; margin-bottom: 15px">Send Email About Updated File?</label>
			</div>
			<!-- /.tld-meta-head -->

			<!--Script was here -->

			<div id='tld-switch' onclick="tld_cookie_business()">
				<div id='circle'></div>
			</div>

			<?php //switch magic happens below ?>

			<code style="display: none;">
				clicking on "#tld-switch" toggles class "active" on "#tld-switch"
			</code>

			<?php // end magic ?>

		</div>
		<!-- /.meta-row -->
	</div>
	<?php
}


function tld_post_saved( $post_id ) {

	if(isset($_COOKIE['tld-cookie'])) {

		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) )
		return;

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

			//echo '<script>console.log("'.$tld_email_address->user_email.'")</script>';
		}

	}
	//delete our cookie since we're done with it
	setcookie("tld-cookie", "tld-switch-cookie", time() - 3600);
}

add_action('save_post', 'tld_post_saved');
