<?php
/*
Plugin Name: TLD WooCommerce Downloadable Product Update Emails
Plugin URI: http://soaringleads.com
Description: Inform customers when there is an update to their downloadable product. Never let customers guess if an edition of the e-book (an example) they bought has been updated. No more need to manually email customers about downloadable product updates or creating a new product.
Version: 1.0.1
Author: Uriahs Victor
Author URI: http://soaringleads.com
License: GPL2
*/

defined( 'ABSPATH' ) or die( 'But why!?' );

//table setup
require_once dirname( __FILE__ ) . '/includes/tld-table-setup.php';

//schedule setup
include dirname( __FILE__ ) . '/includes/tld-schedule-mail.php';

//options page setup
include dirname( __FILE__ ) . '/includes/admin/tld-settings-page.php';

//activation/deactivation tasks
register_activation_hook( __FILE__, 'tld_wcdpue_setup_table' );
register_activation_hook( __FILE__, 'tld_wcdpue_activate_schedule' );
register_deactivation_hook( __FILE__, 'tld_wcdpue_deactivate_schedule');


//register assets
function tld_wcdpue_load_assets() {

	wp_enqueue_script( 'tld_wcdpue_uilang', plugin_dir_url( __FILE__ ) . 'assets/js/uilang.js' );
	wp_enqueue_script( 'tld_wcdpue_scripts', plugin_dir_url( __FILE__ ) . 'assets/js/tld-scripts.js?v1.0.0' );
	wp_enqueue_style( 'tld_wcdpue_styles', plugin_dir_url( __FILE__ ) . 'assets/css/style.css?v1.0.0' );

}
add_action( 'admin_enqueue_scripts', 'tld_wcdpue_load_assets' );

function tld_wcdpue_deactivate_schedule() {

	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';

	check_admin_referer( "deactivate-plugin_{$plugin}" );

	wp_clear_scheduled_hook('tld_wcdpue_email_burst');

}

//Quick cron job for user convience

function tld_wcdpue_cron_quarter_hour($schedules){

	$schedules['tld_quick_cron'] = array(

		'interval' => 900,
		'display' => __( 'Every 15 Minutes' )

	);
	return $schedules;

}
add_filter( 'cron_schedules', 'tld_wcdpue_cron_quarter_hour' );

function tld_wcdpue_metabox(){

	add_meta_box(
	'tld_wcdpue_metabox',
	'Downloadable Product Email Options',
	'tld_metabox_fields',
	'',
	'side',
	'high'
);

}
add_action('add_meta_boxes_product', 'tld_wcdpue_metabox', 10, 2);

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
	?>

	<div class="tld-wcdpue-center-text">

		<div>
			<p>Buyers with download access: <?php tld_get_product_owners() ?></p>
		</div>

		<div>
			<label for="tld-option-selected" id="meta-switch-label">Send product update email?</label>
		</div>

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


function tld_wcdpue_post_saved( $post_id ) {

	if( isset( $_COOKIE['tld-wcdpue-cookie'] ) ) {

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

	//get our options

	$tld_wcdpue_email_subject = esc_attr( get_option( 'tld-wcdpue-email-subject' ) );
	$tld_wcdpue_email_body = esc_attr( get_option( 'tld-wcdpue-email-body' ) );

	if ( empty( $tld_wcdpue_email_subject ) ){
		$tld_wcdpue_email_subject = 'Your Downloadable Product has been updated!';
	}

	if ( empty( $tld_wcdpue_email_body ) ){
		$tld_wcdpue_email_body = 'There is a new update for your product:';
	}

	$tld_option_selected = $_POST['tld-option-selected'];

	if ( $tld_option_selected == 'immediately' ){

		foreach ( $query_result as $tld_email_address ){

			$post_title = get_the_title( $post_id );
			$tld_prod_url = esc_url( get_permalink( $post_id ) );
			$tld_home_url = esc_url( home_url() );
			$tld_the_email = $tld_email_address->user_email;
			$subject = $tld_wcdpue_email_subject;
			$message = $tld_wcdpue_email_body . "\n\n";
			$message .= $post_title . ": " . $tld_prod_url . "\n\nLog in to download it from your account now -> " . $tld_home_url;
			wp_mail( $tld_the_email, $subject, $message );

		}

	}else{

		foreach ( $query_result as $tld_email_address ){

			$post_title = get_the_title( $post_id );
			$post_url = esc_url( get_permalink( $post_id ) );
			$tld_home_url = esc_url( home_url() );
			$tld_the_email = $tld_email_address->user_email;
			$message .= $post_title . "\n\nLog in to download it from your account now -> " . $tld_home_url;
			$tld_the_schedule_table = $tld_tbl_prefix . 'woocommerce_downloadable_product_emails_tld';
			$wpdb->insert(
			$tld_the_schedule_table,
			array(

				'id' => '',
				'product_id' => $post_id,
				'user_email' => $tld_the_email,

			)
		);

	}

}

}
//delete our cookie since we're done with it
setcookie("tld-wcdpue-cookie", "tld-switch-cookie", time() - 3600);

}

add_action('save_post', 'tld_wcdpue_post_saved');
