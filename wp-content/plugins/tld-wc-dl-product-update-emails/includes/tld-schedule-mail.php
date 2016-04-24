<?php
function tld_wcdpue_activate_schedule(){

  //  wp_schedule_event( time(), 'hourly', 'tld_wcdpue_email_burst' );
  wp_schedule_event( time(), 'tld_quick_cron', 'tld_wcdpue_email_burst' );

}

add_action( 'tld_wcdpue_email_burst', 'tld_wcdpue_send_schedule_mail' );

function tld_wcdpue_send_schedule_mail(){

  //get our options

  $tld_wcdpue_email_subject = esc_attr( get_option( 'tld-wcdpue-email-subject' ) );
  $tld_wcdpue_email_body = esc_attr( get_option( 'tld-wcdpue-email-body' ) );
  $tld_wcdpue_email_bursts_count = esc_attr( get_option( 'tld-wcdpue-email-bursts-count' ) );

  if ( empty( $tld_wcdpue_email_subject ) ){
    $tld_wcdpue_email_subject = 'Your Downloadable Product has been updated!';
  }

  if ( empty( $tld_wcdpue_email_body ) ){
    $tld_wcdpue_email_body = 'There is a new update for your product:';
  }

  if ( empty( $tld_wcdpue_email_bursts_count ) ){
    $tld_wcdpue_email_bursts_count = 10; //limit to 10 to help avoid blacklisting?
  }

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
    $message .= $tld_post_title . ": " . $tld_prod_url . "\n\nLog in to download it from your account now -> " . $tld_home_url;
    wp_mail( $tld_the_email, $subject, $message );
    //delete the current row in loop after mail sent
    $wpdb->delete( $tld_the_table, array( 'id' => $result->id ) );
    sleep(1); //short breath, no rush.

  }

}
?>
