<?php

function tld_activate_schedule(){

  wp_schedule_event( time(), 'hourly', 'tld_send_product_email_burst');

}

add_action( 'tld_send_product_email_burst', 'tld_send_schedule_mail' );

function tld_send_schedule_mail(){

  global $wpdb;
  $tld_tbl_prefix = $wpdb->prefix;
  $tld_the_table = $tld_tbl_prefix . 'woocommerce_downloadable_product_emails_tld';
  $query_result = $wpdb->get_results( "SELECT * FROM $tld_the_table LIMIT 10" ); //limit to 10 to help avoid blacklisting?

foreach ( $query_result as $result ){

  $tld_prod_id = $result->product_id;
  $tld_post_title = get_the_title( $tld_prod_id );
  $tld_prod_url = esc_url( get_permalink( $tld_prod_id ) );
  $tld_home_url = esc_url( home_url() );
  $tld_the_email = $result->user_email;
  $subject = 'Your download has been updated!';
  $message = "There is a new update to your product:\n\n";
  $message .= $tld_post_title . ": " . $tld_prod_url . "\n\nLog in to get it from your account now -> " . $tld_home_url;
  wp_mail( $tld_the_email, $subject, $message );
  //delete the current row in loop after mail sent
  $wpdb->delete( $tld_the_table, array( 'id' => $result->id ) );
}

}
?>
