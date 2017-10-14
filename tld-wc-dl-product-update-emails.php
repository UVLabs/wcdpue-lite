<?php
/*
Plugin Name: WCDPUE Lite
Plugin URI: http://uriahsvictor.com
Description: Inform customers when there is an update to their WooCommerce downloadable product via email.
Version: 1.1.7
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

//admin review notice
include( dirname( __FILE__ ) . '/includes/admin/tld-notice.php' );

//activation/deactivation tasks
register_activation_hook( __FILE__, 'tld_wcdpue_setup_table' );
register_activation_hook( __FILE__, 'tld_wcdpue_activate_schedule' );
register_deactivation_hook( __FILE__, 'tld_wcdpue_deactivate_schedule');


//register assets
function tld_wcdpue_load_assets() {

  wp_enqueue_script( 'tld_wcdpue_uilang', plugin_dir_url( __FILE__ ) . 'assets/js/uilang.js' );
  wp_enqueue_script( 'tld_wcdpue_scripts', plugin_dir_url( __FILE__ ) . 'assets/js/tld-scripts.js?v1.0.1' );
  wp_enqueue_style( 'tld_wcdpue_styles', plugin_dir_url( __FILE__ ) . 'assets/css/style.css?v1.1.8' );

}
add_action( 'admin_enqueue_scripts', 'tld_wcdpue_load_assets' );

// check if WooCommerce is activated
function tld_wcdpue_wc_check(){

  if ( class_exists( 'woocommerce' ) ) {

    global $tld_wcdpue_wc_active;
    $tld_wcdpue_wc_active = 'yes';

  } else {

    global  $tld_wcdpue_wc_active;
    $tld_wcdpue_wc_active = 'no';

  }

}
add_action( 'admin_init', 'tld_wcdpue_wc_check' );

// show admin notice if WooCommerce is not activated
function tld_wcdpue_wc_active(){

  global  $tld_wcdpue_wc_active;

  if ( $tld_wcdpue_wc_active == 'no' ){
    ?>

    <div class="notice notice-error is-dismissible">
      <p>WooCommerce is not activated, please activate it to use <b>WCDPUE Lite.</b></p>
    </div>
    <?php

  }

}
add_action('admin_notices', 'tld_wcdpue_wc_active');

//setup review timer
if ( function_exists( 'tld_wcdpue_review_notice' ) ) {

  register_activation_hook( __FILE__,  'tld_wcdpue_set_review_trigger_date' );

  /**
  * Set Trigger Date.
  *
  * @since  1.0.0
  */
  function tld_wcdpue_set_review_trigger_date() {

    // Number of days you want the notice delayed by.
    $tld_wcdpue_delayindays = 30;

    // Create timestamp for when plugin was activated.
    $tld_wcdpue_triggerdate = mktime( 0, 0, 0, date('m')  , date('d') + $tld_wcdpue_delayindays, date('Y') );

    // If our option doesn't exist already, we'll create it with today's timestamp.
    if ( ! get_option( 'tld_wcdpue_activation_date') ) {
      add_option( 'tld_wcdpue_activation_date', $tld_wcdpue_triggerdate, '', 'yes' );
    }

  }

}

// delete cron job on deactivation
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

    'interval' => 60,
    'display' => __( 'Every 1 Minute' )

  );
  return $schedules;

}
add_filter( 'cron_schedules', 'tld_wcdpue_cron_quarter_hour' );

function tld_wcdpue_metabox(){

  global $pagenow;
  $tld_wcdpue_the_product = wc_get_product( get_the_ID() );
  if ( $pagenow != 'post-new.php' && $tld_wcdpue_the_product->is_downloadable( 'yes' ) ){
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
  $tld_wcdpue_query_result = $wpdb->get_var(
    "SELECT COUNT( DISTINCT product_id, order_id, order_key, user_email )
    FROM $tld_wcdpue_dls_table
    WHERE ( product_id = $tld_wcdpue_product_id )
    AND (access_expires > NOW() OR access_expires IS NULL )
    ");
    echo $tld_wcdpue_query_result;

  }

  function tld_metabox_fields(){
    $tld_wcdpue_product_id = get_the_ID();
    $tld_wcdpue_product = wc_get_product( $tld_wcdpue_product_id );
    ?>

    <div class="tld-wcdpue-center-text">

      <?php

      if( $tld_wcdpue_product->is_type( 'variable' ) ){

        echo '<div id="tld-wcdpue-upgrade"><strong><a href="https://codecanyon.net/item/woocommerce-downloadable-product-update-emails/18908283?ref=TheLoneDev" target="_blank">Upgrade to Pro</a> for variable downloadable product support!</strong></div></div>';

      }else{
        ?>
        <div>
          <p>Unique download access count: <?php tld_get_product_owners(); ?></p>
        </div>

        <div>
          <label for="tld-option-selected" id="meta-switch-label">Deactivated</label>
        </div>

        <div id='tld-switch' onclick="tld_cookie_business()">
          <div id='circle'></div>
        </div>

        <div id="tld-wcdpue-email-status"></div>

        <div id="tld-wcdpue-upgrade"><strong><a href="https://codecanyon.net/item/woocommerce-downloadable-product-update-emails/18908283?ref=TheLoneDev" target="_blank">Upgrade to Pro</a></strong></div>
        <!-- switch magic happens below -->

        <code style="display: none;">
          clicking on "#tld-switch" toggles class "active" on "#tld-switch"
        </code>

        <!-- end magic -->

      </div>

      <?php
    }
  }


  function tld_wcdpue_post_saved( $post_id ) {

    if( isset( $_COOKIE['tld-wcdpue-cookie'] ) ) {

      if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) )
      return;

      global $wpdb;
      $tld_wcdpue_tbl_prefix = $wpdb->prefix;
      $tld_wcdpue_dls_table = $tld_wcdpue_tbl_prefix . 'woocommerce_downloadable_product_permissions';
      $tld_wcdpue_query_result = $wpdb->get_results(
        "SELECT DISTINCT product_id, order_id, order_key, user_email
        FROM $tld_wcdpue_dls_table
        WHERE ( product_id = $post_id )
        AND (access_expires > NOW() OR access_expires IS NULL )
        "
      );

      //get our options

      $tld_wcdpue_email_subject = esc_attr( get_option( 'tld-wcdpue-email-subject' ) );
      $tld_wcdpue_email_body = esc_attr( get_option( 'tld-wcdpue-email-body' ) );

      if ( empty( $tld_wcdpue_email_subject ) ){
        $tld_wcdpue_email_subject = 'A product you bought has been updated!';
      }

      if ( empty( $tld_wcdpue_email_body ) ){
        $tld_wcdpue_email_body = 'There is a new update for your product:';
      }


      $tld_wcdpue_account_url = esc_url ( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) );

      foreach ( $tld_wcdpue_query_result as $tld_wcdpue_email_address ){

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

        $tld_wcdpue_emails_scheduled++;

      }

    }
    //delete our cookie since we're done with it
    setcookie("tld-wcdpue-cookie", "tld-switch-cookie", time() - 3600);
  }

  add_action('save_post', 'tld_wcdpue_post_saved');
