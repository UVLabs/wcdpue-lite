<?php

class Activation{

  public function table_setup(){

    if ( ! current_user_can( 'activate_plugins' ) ) {

      return;

    }

    $tld_tbl_ver = '1.0.0';
    $tld_wcdpue_cur_version = get_option('tld_table_version');
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    if ( empty( $tld_wcdpue_cur_version ) ){

      $tld_tbl_name = $wpdb->prefix . 'woocommerce_downloadable_product_emails_tld';

      $sql = "CREATE TABLE $tld_tbl_name(
        id bigint(20) NOT NULL AUTO_INCREMENT,
        product_id bigint(20),
        user_email varchar(200) DEFAULT '',
        UNIQUE KEY id  (id)
      ) $charset_collate;";

      require_once ( ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta ( $sql );
      add_option ('tld_table_version', $tld_tbl_ver);

    }else{


      //check if current version in db is lower than new version of plugin
      if ( version_compare( $tld_wcdpue_cur_version, $tld_tbl_ver ) < 0 ){

        //dbDelta buggy, lets update this using ALTER TABLE

        $tld_tbl_name = $wpdb->prefix . 'woocommerce_downloadable_product_emails_tld';


        $wpdb->query(

          ""// ALTER TABLE code, futureproofness.

        );

        update_option ('tld_table_version', $tld_tbl_ver);

      }

    }

  }

  public function activate_schedule(){

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


}
