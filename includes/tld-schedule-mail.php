<?php

function tld_wcdpue_activate_schedule(){

  $tld_wcdpue_cur_recurrence = get_option('tld-wcdpue-schedule-setting-value');

  if ( !empty( $tld_wcdpue_cur_recurrence ) ){

    $active_cron_schedules = wp_get_schedules();

    foreach ( $active_cron_schedules as $key => $value ) {

      if ( $key == $tld_wcdpue_cur_recurrence ){

        $tld_wcdpue_wait_time = $value['interval'];

      }

    }

    wp_schedule_event( time() + $tld_wcdpue_wait_time, $tld_wcdpue_cur_recurrence, 'tld_wcdpue_email_burst' );

  }else{

    wp_schedule_event( time(), 'daily', 'tld_wcdpue_email_burst' );

  }

}
add_action( 'tld_wcdpue_email_burst', 'tld_wcdpue_send_schedule_mail' );

function tld_wcdpue_send_schedule_mail(){

  //get our options

  $tld_wcdpue_email_subject = esc_attr( get_option( 'tld-wcdpue-email-subject' ) );
  $tld_wcdpue_email_body = esc_attr( get_option( 'tld-wcdpue-email-body' ) );
  $tld_wcdpue_email_footer = esc_attr( get_option( 'tld-wcdpue-email-footer' ) );
  $tld_wcdpue_email_bursts_count = esc_attr( get_option( 'tld-wcdpue-email-bursts-count' ) );

  if ( empty( $tld_wcdpue_email_subject ) ){
    $tld_wcdpue_email_subject = 'A product you bought has been updated!';
  }

  if ( empty( $tld_wcdpue_email_body ) ){
    $tld_wcdpue_email_body = 'There is a new update for your product:';
  }

  if ( empty( $tld_wcdpue_email_footer ) ){
    $tld_wcdpue_email_footer = 'Log in to download it from your account now:';
  }

  if ( empty( $tld_wcdpue_email_bursts_count ) ){
    $tld_wcdpue_email_bursts_count = 5; //limit number of emails sent per schedule hit to 5
  }

  $tld_account_url = esc_url ( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) );

  global $wpdb;
  $tld_tbl_prefix = $wpdb->prefix;
  $tld_the_table = $tld_tbl_prefix . 'woocommerce_downloadable_product_emails_tld';
  $query_result = $wpdb->get_results( "SELECT * FROM $tld_the_table ORDER BY id ASC LIMIT $tld_wcdpue_email_bursts_count" );

  foreach ( $query_result as $result ){

    $tld_prod_id = $result->product_id;
    $tld_post_title = get_the_title( $tld_prod_id );
    $tld_prod_url = esc_url( get_permalink( $tld_prod_id ) );
    $tld_home_url = esc_url( home_url() );
    $tld_the_email = $result->user_email;
    $subject = $tld_wcdpue_email_subject;
    $message = $tld_wcdpue_email_body . "\n\n";
    $message .= $tld_post_title . ": " . $tld_prod_url . "\n\n" . $tld_wcdpue_email_footer . "\n\n" . $tld_account_url;
    wp_mail( $tld_the_email, $subject, $message );
    $wpdb->delete( $tld_the_table, array( 'id' => $result->id ) );   //delete the current row in loop after mail sent
    sleep(2); //short breath, no rush.

  }

}
?>
