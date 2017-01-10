<?php
// create custom plugin settings menu
add_action('admin_menu', 'tld_wcdpue_add_admin_menu');

function tld_wcdpue_add_admin_menu() {

  //create new settings menu item
  add_options_page('WooCommerce Downloadable Product Update Emails', 'WCDPUE Lite', 'administrator', 'wcdpue-lite', 'tld_wcdpue_settings_page' );

  //call register settings function
  add_action( 'admin_init', 'tld_wcdpue_settings' );

}

function tld_wcdpue_get_queue(){

  global $wpdb;
  $tld_wcdpue_tbl_prefix = $wpdb->prefix;
  $tld_wcdpue_the_schedule_table = $tld_wcdpue_tbl_prefix . 'woocommerce_downloadable_product_emails_tld';

  $tld_wcdpue_queue_count = $wpdb->get_var(
    "SELECT COUNT(*)
    FROM $tld_wcdpue_the_schedule_table
    ");
    echo $tld_wcdpue_queue_count;

  }

  function tld_wcdpue_get_email_body(){

    $tld_wcdpue_email_body = esc_attr( get_option( 'tld-wcdpue-email-body' ) );

    if( !empty( $tld_wcdpue_email_body ) ){

      echo $tld_wcdpue_email_body;

    }

  }

  function tld_wcdpue_get_email_footer(){

    $tld_wcdpue_email_footer = esc_attr( get_option('tld-wcdpue-email-footer') );

    if( !empty( $tld_wcdpue_email_footer ) ){

      echo $tld_wcdpue_email_footer;

    }

  }

  function tld_wcdpue_settings_page() {
    //page html
    ?>

    <div class="wrap">
      <h2>Plugin Options</h2>
      <div class="left">
        <form method="post" action="options.php">

          <?php settings_fields( 'tld-wcdpue-settings-group' ); ?>
          <?php do_settings_sections( 'tld-wcdpue-settings-group' ); ?>

          <table class="form-table">

            <tr valign="top">
              <th><h3>E-mail options</h3></th>
            </tr>

            <tr valign="top">
              <th scope="row">E-mail subject</th>
              <td><input type="text" name="tld-wcdpue-email-subject" value="<?php echo esc_attr( get_option('tld-wcdpue-email-subject') ); ?>" placeholder="A product you bought has been updated!" size="70"/></td>
            </tr>

            <tr valign="top">
              <th scope="row">E-mail Body</th>
              <td>
                <textarea name="tld-wcdpue-email-body" placeholder="There is a new update for your product" rows="8" cols="70"/><?php tld_wcdpue_get_email_body(); ?></textarea>
              </td>
            </tr>

            <tr valign="top">
              <th scope="row">E-mail Footer</th>
              <td>
                <textarea name="tld-wcdpue-email-footer" placeholder="Log in to download it from your account now" rows="8" cols="70"/><?php tld_wcdpue_get_email_footer(); ?></textarea>
              </td>
            </tr>

            <tr valign="top">
              <th><h3>E-mail schedule options</h3></th>
            </tr>

            <tr valign="top">
              <th scope="row">Send e-mails in bursts of <small>( Keep this value below 5 to ensure deliverability. )</small>:</th>
              <td>
                <input type="number" name="tld-wcdpue-email-bursts-count" value="<?php echo esc_attr( get_option('tld-wcdpue-email-bursts-count') ); ?>" min="1" size="4"/>
              </td>
            </tr>

            <tr valign="top">
              <th scope="row">Schedule:</th>
              <td>
                <select name="tld-wcdpue-schedule-setting-value">
                  <?php

                  $tld_wcdpue_active_cron_schedules = wp_get_schedules();

                  $tld_wcdpue_current_schedule = esc_attr( get_option( 'tld-wcdpue-schedule-setting-value' ) );

                  foreach ( $tld_wcdpue_active_cron_schedules as $key => $value ) {

                    if ( empty( $tld_wcdpue_current_schedule ) && $key == "daily" ){
                      echo '<option value="' . $key . '" selected>' . $value['display'] . '</option>';
                    }
                    elseif ( $key == $tld_wcdpue_current_schedule ){
                      echo '<option value="' . $key . '" selected>' . $value['display'] . '</option>';
                    }else{
                      echo '<option value="' . $key . '">' . $value['display'] . '</option>';
                    }
                  }
                  ?>
                </select>
              </td>
            </tr>
            <tr>
              <th>Emails in Queue:</th>
              <td><?php tld_wcdpue_get_queue(); ?></td>
            </tr>
            <tr valign="top">
              <th><h3>Housekeeping</h3></th>
            </tr>

            <tr valign="top">
              <th>Delete all plugin settings on uninstall?</th>
              <?php
              $tld_wcdpue_checked = get_option( 'tld-wcdpue-delete-db-settings' );
              if ( !empty( $tld_wcdpue_checked ) ){
                $tld_wcdpue_checked = "checked";
              }
              ?>
              <td><input type="checkbox" name="tld-wcdpue-delete-db-settings" <?php echo $tld_wcdpue_checked ?>></td>
            </tr>
          </table>

          <?php submit_button(); ?>

        </form>
      </div>

      <div id="tld-donation-wrap">

        <div id="tld-donation-container">
          <h1 id="tld-donation-header">Did my plugin help?</h1>
          <div id="tld-donation-body">

            <p>If this plugin genuinely helped you with managing your store then maybe you might like to donate?</p>
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
              <input type="hidden" name="cmd" value="_donations">
              <input type="hidden" name="business" value="me@uriahsvictor.com">
              <input type="hidden" name="lc" value="LC">
              <input type="hidden" name="item_name" value="Support further development with your kind donation.">
              <input type="hidden" name="item_number" value="WC Product Update Emails Plugin">
              <input type="hidden" name="no_note" value="0">
              <input type="hidden" name="currency_code" value="USD">
              <input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHostedGuest">
              <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
              <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
            </form>

          </div>

        </div>

      </div>
    </div>


    <?php }

    function tld_wcdpue_settings() {

      //register our settings
      register_setting( 'tld-wcdpue-settings-group', 'tld-wcdpue-email-subject' );
      register_setting( 'tld-wcdpue-settings-group', 'tld-wcdpue-email-body' );
      register_setting( 'tld-wcdpue-settings-group', 'tld-wcdpue-email-footer' );
      register_setting( 'tld-wcdpue-settings-group', 'tld-wcdpue-email-bursts-count' );
      register_setting( 'tld-wcdpue-settings-group', 'tld-wcdpue-schedule-setting-value' );
      register_setting( 'tld-wcdpue-settings-group', 'tld-wcdpue-delete-db-settings' );

    }

    function tld_wcdpue_update_schedule() {

      $tld_wcdpue_cur_recurrence = get_option( 'tld-wcdpue-schedule-setting-value' ); //get interval set by user
      $tld_wcdpue_active_cron_schedules = wp_get_schedules();

      foreach ( $tld_wcdpue_active_cron_schedules as $key => $value ) {

        if ( $key == $tld_wcdpue_cur_recurrence ){

          $tld_wcdpue_wait_time = $value['interval'];

        }

      }
      wp_clear_scheduled_hook('tld_wcdpue_email_burst'); //remove previous scheduled time
      wp_schedule_event( time() + $tld_wcdpue_wait_time , $tld_wcdpue_cur_recurrence, 'tld_wcdpue_email_burst' ); //add new scheduled time

    }
    add_action( 'update_option_tld-wcdpue-schedule-setting-value', 'tld_wcdpue_update_schedule');

    ?>
