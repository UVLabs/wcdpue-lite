<?php
function tld_wcdpue_activate_schedule(){

  $tld_wcdpue_cur_recurrence = get_option('tld-wcdpue-schedule-setting-value');

  if ( !empty( $tld_wcdpue_cur_recurrence ) ){

    $tld_wcdpue_active_cron_schedules = wp_get_schedules();

    foreach ( $tld_wcdpue_active_cron_schedules as $key => $value ) {

      if ( $key == $tld_wcdpue_cur_recurrence ){

        $tld_wcdpue_wait_time = $value['interval']; //to prevent premature firing of email

      }

    }

    wp_schedule_event( time() + $tld_wcdpue_wait_time, $tld_wcdpue_cur_recurrence, 'tld_wcdpue_email_burst' );

  }else{

    wp_schedule_event( time(), 'daily', 'tld_wcdpue_email_burst' );
    update_option( 'tld-wcdpue-schedule-setting-value', 'daily' );
  }

}
add_action( 'tld_wcdpue_email_burst', 'tld_wcdpue_send_schedule_mail' );

function tld_wcdpue_send_schedule_mail(){

  //get our options

  $tld_wcdpue_email_subject = esc_attr( get_option( 'tld-wcdpue-email-subject' ) );
  $tld_wcdpue_email_body = esc_attr( get_option( 'tld-wcdpue-email-body' ) );
  $tld_wcdpue_email_bursts_count = esc_attr( get_option( 'tld-wcdpue-email-bursts-count' ) );

  if ( empty( $tld_wcdpue_email_subject ) ){
    $tld_wcdpue_email_subject = 'A product you bought has been updated!';
  }

  if ( empty( $tld_wcdpue_email_body ) ){
    $tld_wcdpue_email_body = 'There is a new update for your product:';
  }

  if ( empty( $tld_wcdpue_email_bursts_count ) ){
    $tld_wcdpue_email_bursts_count = 5; //limit number of emails sent per schedule hit to 5
  }

  $tld_wcdpue_account_url = esc_url ( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) );

  global $wpdb;
  $tld_wcdpue_tbl_prefix = $wpdb->prefix;
  $tld_wcdpue_the_scheduling_table = $tld_wcdpue_tbl_prefix . 'woocommerce_downloadable_product_emails_tld';
  $query_result = $wpdb->get_results( "SELECT * FROM $tld_wcdpue_the_scheduling_table ORDER BY id ASC LIMIT $tld_wcdpue_email_bursts_count" );

  foreach ( $query_result as $result ){

    $tld_wcdpue_product_id = $result->product_id;
    $tld_wcdpue_post_title = get_the_title( $tld_wcdpue_product_id );
    $tld_wcdpue_product_url = esc_url( get_permalink( $tld_wcdpue_product_id ) );
    $tld_wcdpue_home_url = esc_url( home_url() );
    $tld_wcdpue_buyer_email_address = $result->user_email;
    $tld_wcdpue_email_message = $tld_wcdpue_email_body . "\n\n";
    $tld_wcdpue_email_message .= $tld_wcdpue_post_title . ": " . $tld_wcdpue_product_url . "\n\n" . $tld_wcdpue_account_url /*. "\n\n\n" . apply_filters( 'tld_wcdpue_plugin_creds', tld_wcdpue_get_creds() )*/;
    wp_mail( $tld_wcdpue_buyer_email_address, $tld_wcdpue_email_subject, $tld_wcdpue_email_message );
    $wpdb->delete( $tld_wcdpue_the_scheduling_table, array( 'id' => $result->id ) );   //delete the current row in loop after mail sent
    sleep(2); //short breath, no rush.

  }

}
?>
