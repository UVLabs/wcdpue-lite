<?php
global $tld_tbl_ver;
$tld_tbl_ver = '1.1.2';

function tld_wcdpue_setup_table(){

  global $wpdb;
  global $tld_tbl_ver;
  $tld_tbl_name = $wpdb->prefix . 'woocommerce_downloadable_product_emails_tld';
  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE $tld_tbl_name(
  id bigint(20) NOT NULL AUTO_INCREMENT,
  user_email varchar(200) DEFAULT '',
  product_id varchar(100) DEFAULT 'Downloadable Product',
  UNIQUE KEY id  (id)
  ) $charset_collate;";

  require_once ( ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta ( $sql );
  add_option ('tld_table_version', $tld_tbl_ver);

//below doesnt update database for some reason :3

  $installed_ver = get_option('tld_table_version');

  if ( $installed_ver != $tld_tbl_ver ){

    $tld_tbl_name = $wpdb->prefix . 'woocommerce_downloadable_product_emails_tld';

    $sql = "CREATE TABLE $tld_tbl_name(
    id bigint(20) NOT NULL AUTO_INCREMENT,
    user_email varchar(50) DEFAULT '',
    product_id varchar(100) DEFAULT '',
    UNIQUE KEY id  (id)
  );";

  require_once ( ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta ( $sql );
  update_option ('tld_table_version', $tld_tbl_ver); //code reaches here

}

}

function tld_update_tbl_check() {
  global $tld_tbl_ver;
  if ( get_site_option( 'tld_table_version' ) != $tld_tbl_ver ) {
    tld_wcdlprodmails_setup_table();
  }
}
add_action( 'plugins_loaded', 'tld_update_tbl_check' );
?>
