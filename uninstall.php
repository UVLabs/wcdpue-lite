<?php

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
  exit();
}

$tld_wcdpue_byebye = get_option('tld-wcdpue-delete-db-settings');

if ( $tld_wcdpue_byebye == 'on' ){

  delete_usermeta( $user_id, 'tld_wcdpue_review_dismiss' );

  delete_option('tld-wcdpue-schedule-setting-value');
  delete_option('tld-wcdpue-email-subject');
  delete_option('tld-wcdpue-email-bursts-count');
  delete_option('tld-wcdpue-email-body');
  delete_option('tld-wcdpue-email-footer');
  delete_option('tld_default_cron');
  delete_option('tld-wcdpue-delete-db-settings');
  delete_option( 'tld_wcdpue_activation_date' );
  delete_option('tld_table_version');

  global $wpdb;
  $tld_wcdpue_tbl = $wpdb->prefix."woocommerce_downloadable_product_emails_tld";
  $wpdb->query("DROP TABLE IF EXISTS $tld_wcdpue_tbl");

}

?>
