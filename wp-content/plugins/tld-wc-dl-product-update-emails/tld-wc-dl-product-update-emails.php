<?php
/*
Plugin Name: TLD WC Downloadable Product Update Emails
Plugin URI: http://soaringleads.com
Description: Inform customers when there is an update to their downloadable product.
Version: 1.0.0-beta
Author: Uriahs Victor
Author URI: http://soaringleads.com
License: GPL2
*/

defined( 'ABSPATH' ) or die( 'Time for a U turn!' );

//create db table
require_once dirname( __FILE__ ) . '/includes/tld-table-setup.php';
register_activation_hook( __FILE__, 'tld_wcdlprodmails_setup_table' );

//setup schedule
include dirname( __FILE__ ) . '/includes/tld-schedule-mail.php';
register_activation_hook( __FILE__, 'tld_activate_schedule' );
register_deactivation_hook(__FILE__, 'tld_deactivate_schedule');

function tld_deactivate_schedule() {
	wp_clear_scheduled_hook('tld_schedule_mail');
}

//register assets
function tld_load_assets() {

	wp_enqueue_script( 'tld_uilang', plugin_dir_url( __FILE__ ) . 'assets/js/uilang.js' );
	wp_enqueue_script( 'tld_scripts', plugin_dir_url( __FILE__ ) . 'assets/js/tld-scripts.js?v1.0.0' );
	wp_enqueue_style( 'tld_styles', plugin_dir_url( __FILE__ ) . 'assets/css/style.css?v1.0.1' );

}
add_action( 'admin_enqueue_scripts', 'tld_load_assets' );

//create 1.4 hour schedule

function tld_cron_quarter_hour($schedules){
	$schedules['quaterly'] = array(

		'interval' => 100,
		'display' => __( 'Once Quater Hourly' )

	);
	return $schedules;
}
add_filter( 'cron_schedules', 'tld_cron_quarter_hour' );


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

function tld_get_product_owners(){

	global $wpdb;
	$product_id = $_GET['post'];
	$tld_tbl_prefix = $wpdb->prefix;
	$tld_the_table = $tld_tbl_prefix . 'woocommerce_downloadable_product_permissions';
	$query_result = $wpdb->get_var(
	"SELECT COUNT(*)
	FROM $tld_the_table
	WHERE ( product_id=$product_id )
	AND (access_expires > NOW() OR access_expires IS NULL )
	");
	echo $query_result;
}

function tld_metabox_fields(){
	//ADD NOUNCE FIELD
	?>


		<div class="tld-wcdpue-center-text">

			<div>
				<p>Buyers with download access: <?php tld_get_product_owners() ?></p>
			</div>

			<div>
				<label for="tld-option-selected" id="meta-switch-label">Send product update email?</label>
			</div>
			<!-- /.tld-meta-head -->

			<!--Script was here -->

			<div id='tld-switch' onclick="tld_cookie_business()">
				<div id='circle'></div>
			</div>

			<div class="tld-wcdpue-top-margin">
				<input type="radio" name="tld-option-selected" value="immediately"><span style="margin-right: 10px;">Immediately</span>
				<input type="radio" name="tld-option-selected" value="schedule" checked><span>Schedule</span>
			</div>

			<?php //switch magic happens below ?>

			<code style="display: none;">
				clicking on "#tld-switch" toggles class "active" on "#tld-switch"
			</code>

			<?php // end magic ?>

		</div>

	<?php
}


function tld_post_saved( $post_id ) {

	if( isset( $_COOKIE['tld-cookie'] ) ) {

		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) )
		return;

		global $wpdb;
		$tld_tbl_prefix = $wpdb->prefix;
		$tld_the_table = $tld_tbl_prefix . 'woocommerce_downloadable_product_permissions';
		$query_result = $wpdb->get_results(
		"SELECT *
		FROM $tld_the_table
		WHERE ( product_id=$post_id )
		AND (access_expires > NOW() OR access_expires IS NULL )
		"
	);

	$tld_option_selected = $_POST['tld-option-selected'];

	if ( $tld_option_selected == 'immediately' ){

		foreach ( $query_result as $tld_email_address ){

			$tld_the_email = $tld_email_address->user_email;
			$post_title = get_the_title( $post_id );
			$post_url = esc_url( get_permalink( $post_id ) );
			$subject = 'Your download has been updated!';
			$message = "There is a new update to your awesome product:\n\n";
			$tld_home_url = esc_url( home_url() );
			$message .= $post_title . "\n\nLog in to get it from your account now -> " . $tld_home_url;
			wp_mail( $tld_the_email, $subject, $message );
			//usleep(250000); //sleep for 1/4 a second
			//echo '<script>console.log("'.$tld_email_address->user_email.'")</script>';
		}

	}else{

		foreach ( $query_result as $tld_email_address ){

			$tld_the_email = $tld_email_address->user_email;
			$post_title = get_the_title( $post_id );
			$post_url = esc_url( get_permalink( $post_id ) );
			$subject = 'Your download has been updated!';
			$message = "There is a new update to your awesome product:\n\n";
			$tld_home_url = esc_url( home_url() );
			$message .= $post_title . "\n\nLog in to get it from your account now -> " . $tld_home_url;

			$tld_the_schedule_table = $tld_tbl_prefix . 'woocommerce_downloadable_product_emails_tld';
			$wpdb->insert(
			$tld_the_schedule_table,
			array(
				'id' => '',
				'user_email' => $tld_the_email,
				'product_id' => $post_id,
			)
		);

	}

}
}
//delete our cookie since we're done with it
setcookie("tld-cookie", "tld-switch-cookie", time() - 3600);
}

add_action('save_post', 'tld_post_saved');
