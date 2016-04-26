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

}

?>
