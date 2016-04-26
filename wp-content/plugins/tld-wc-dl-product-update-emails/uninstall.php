<?php

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
  exit();
}

$tld_wcdpue_byebye = get_option('tld-wcdpue-delete-db-settings');

if ( !empty( '$tld_wcdpue_byebye' ) ){

  delete_option('tld-wcdpue-schedule-setting-value');
  delete_option('tld-wcdpue-email-subject');
  delete_option('tld-wcdpue-email-bursts-count');
  delete_option('tld-wcdpue-email-body');
  delete_option('tld-wcdpue-delete-db-settings');

  global $wpdb;
  $tld_tbl_name = $wpdb->prefix."woocommerce_downloadable_product_emails_tld";
  $wpdb->query("DROP TABLE IF EXISTS $tld_tbl_name");

}

?>
