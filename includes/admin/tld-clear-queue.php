<?php

require( $_SERVER['DOCUMENT_ROOT'].'/wp-load.php' );

global $wpdb;

$tld_wcdpue_the_scheduling_table = TLD_WCDPUE_SCHEDULED_TABLE;

$wpdb->query(
  "TRUNCATE $tld_wcdpue_the_scheduling_table"
);

wp_redirect( home_url("/wp-admin/options-general.php?page=wcdpue-lite") );
