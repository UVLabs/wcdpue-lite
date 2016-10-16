<?php
/*
Plugin Name: TLD WooCommerce Downloadable Product Update Emails
Plugin URI: http://uriahsvictor.com
Description: Inform customers when there is an update to their downloadable product via email.
Version: 1.1.2
Author: Uriahs Victor
Author URI: http://uriahsvictor.com
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
	wp_enqueue_style( 'tld_wcdpue_styles', plugin_dir_url( __FILE__ ) . 'assets/css/style.css?v1.0.2' );

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

	global $pagenow;
	$tld_wcdpue_the_product = wc_get_product( get_the_ID() );
	if ( $pagenow != 'post-new.php' && $tld_wcdpue_the_product->is_downloadable( 'yes' ) && ! $tld_wcdpue_the_product->is_type('variable') ){
		add_meta_box(
		'tld_wcdpue_metabox',
		'Email Options',
		'tld_metabox_fields',
		'',
		'side',
		'high'
	);

}

}
add_action('add_meta_boxes_product', 'tld_wcdpue_metabox', 10, 2);

function tld_get_product_owners(){

	global $wpdb;
	$tld_wcdpue_product_id = get_the_ID();
	$tld_wcdpue_tbl_prefix = $wpdb->prefix;
	$tld_wcdpue_dls_table = $tld_wcdpue_tbl_prefix . 'woocommerce_downloadable_product_permissions';
	// try making above global to use in save posts event
	$query_result = $wpdb->get_var(
	"SELECT COUNT(*)
	FROM $tld_wcdpue_dls_table
	WHERE ( product_id = $tld_wcdpue_product_id )
	AND (access_expires > NOW() OR access_expires IS NULL )
	");
	echo $query_result;

}

function tld_metabox_fields(){

	?>

	<div class="tld-wcdpue-center-text">

		<div>
			<p>Buyers with download access: <?php tld_get_product_owners(); ?></p>
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

		<!-- switch magic happens below -->

		<code style="display: none;">
			clicking on "#tld-switch" toggles class "active" on "#tld-switch"
		</code>

		<!-- end magic -->

	</div>

	<?php
}


function tld_wcdpue_post_saved( $post_id ) {

	if( isset( $_COOKIE['tld-wcdpue-cookie'] ) ) {

		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) )
		return;

		global $wpdb;
		$tld_wcdpue_tbl_prefix = $wpdb->prefix;
		$tld_wcdpue_dls_table = $tld_wcdpue_tbl_prefix . 'woocommerce_downloadable_product_permissions';
		$query_result = $wpdb->get_results(
		"SELECT *
		FROM $tld_wcdpue_dls_table
		WHERE ( product_id = $post_id )
		AND (access_expires > NOW() OR access_expires IS NULL )
		"
	);

	//get our options

	$tld_wcdpue_email_subject = esc_attr( get_option( 'tld-wcdpue-email-subject' ) );
	$tld_wcdpue_email_body = esc_attr( get_option( 'tld-wcdpue-email-body' ) );
	$tld_wcdpue_email_footer = esc_attr( get_option( 'tld-wcdpue-email-footer' ) );

	if ( empty( $tld_wcdpue_email_subject ) ){
		$tld_wcdpue_email_subject = 'A product you bought has been updated!';
	}

	if ( empty( $tld_wcdpue_email_body ) ){
		$tld_wcdpue_email_body = 'There is a new update for your product:';
	}

	if ( empty( $tld_wcdpue_email_footer ) ){
		$tld_wcdpue_email_footer = 'Log in to download it from your account now:';
	}

	$tld_wcdpue_account_url = esc_url ( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) );

	$tld_wcdpue_option_selected = $_POST['tld-option-selected'];

	if ( $tld_wcdpue_option_selected == 'immediately' ){

		foreach ( $query_result as $tld_wcdpue_email_address ){

			$tld_wcdpue_post_title = get_the_title( $post_id );
			$tld_wcdpue_product_url = esc_url( get_permalink( $post_id ) );
			$tld_wcdpue_buyer_email_address = $tld_wcdpue_email_address->user_email;
			$tld_wcdpue_email_subject = $tld_wcdpue_email_subject;
			$tld_wcdpue_email_message = $tld_wcdpue_email_body . "\n\n";
			$tld_wcdpue_email_message .= $tld_wcdpue_post_title . ": " . $tld_wcdpue_product_url . "\n\n" . $tld_wcdpue_email_footer . "\n\n" . $tld_wcdpue_account_url;
			wp_mail( $tld_wcdpue_buyer_email_address, $tld_wcdpue_email_subject, $tld_wcdpue_email_message );

		}

	}else{

		foreach ( $query_result as $tld_wcdpue_email_address ){

			/*$tld_wcdpue_post_title = get_the_title( $post_id );
			$post_url = esc_url( get_permalink( $post_id ) );
			$tld_home_url = esc_url( home_url() );*/
			$tld_wcdpue_buyer_email_address = $tld_wcdpue_email_address->user_email;
			$tld_wcdpue_the_scheduling_table = $tld_wcdpue_tbl_prefix . 'woocommerce_downloadable_product_emails_tld';
			$wpdb->insert(
			$tld_wcdpue_the_scheduling_table ,
			array(

				'id' => '',
				'product_id' => $post_id,
				'user_email' => $tld_wcdpue_buyer_email_address,

			)
		);

	}

}

}
//delete our cookie since we're done with it
setcookie("tld-wcdpue-cookie", "tld-switch-cookie", time() - 3600);
}

add_action('save_post', 'tld_wcdpue_post_saved');
