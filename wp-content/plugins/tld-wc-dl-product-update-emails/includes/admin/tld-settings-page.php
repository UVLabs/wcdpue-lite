<?php
// create custom plugin settings menu
add_action('admin_menu', 'tld_wcdpue_add_admin_menu');

function tld_wcdpue_add_admin_menu() {

  //create new settings menu item
  add_options_page('Product Update Emails', 'Product Update Emails', 'administrator', 'tld_product_update_emails', 'tld_wcdpue_settings_page' );

  //call register settings function
  add_action( 'admin_init', 'tld_wcdpue_settings' );
}

function tld_wcdpue_get_textarea_val(){

  $email_body = esc_attr( get_option('tld-wcdpue-email-body') );

  if( !empty( $email_body ) ){

    echo $email_body;

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
            <td><h3>Email options</h3></td>
          </tr>

          <tr valign="top">
            <th scope="row">Email subject</th>
            <td><input type="text" name="tld-wcdpue-email-subject" value="<?php echo esc_attr( get_option('tld-wcdpue-email-subject') ); ?>" placeholder="Your Downloadable Product has been updated!" size="70"/></td>
          </tr>

          <tr valign="top">
            <th scope="row">Message</th>
            <td>
              <textarea name="tld-wcdpue-email-body" placeholder="Enter optional text for the email body." rows="15" cols="70"/><?php tld_wcdpue_get_textarea_val(); ?></textarea>
            </td>
          </tr>

          <tr valign="top">
            <td><h3>Email schedule options</h3></td>
          </tr>

          <tr valign="top">
            <th scope="row">Send e-mails in bursts of <small>(recommend you keep this value below 10)</small>:</th>
            <td>
              <input type="number" name="tld-wcdpue-email-bursts-count" value="<?php echo esc_attr( get_option('tld-wcdpue-email-bursts-count') ); ?>" min="1" size="4"/>
            </td>
          </tr>

          <tr valign="top">
            <th>Current schedule:</th>
            <td>
              <?php
              $active_cron_schedules = wp_get_schedules();
              $tld_wcdpue_current_schedule = esc_attr( get_option( 'tld-wcdpue-schedule-setting-value' ) );

              if ( !empty( $tld_wcdpue_current_schedule ) ){

                foreach ( $active_cron_schedules as $key => $value ) {

                  if( $key == $tld_wcdpue_current_schedule ){

                    echo  $value['display'];

                  }

                }

              }else{

                echo "Once Daily";

              }
              ?>
            </td>
          </tr>

          <tr valign="top">
            <th scope="row">Select new schedule:</th>
            <td>
              <select name="tld-wcdpue-schedule-setting-value">
                <?php
                //$active_cron_schedules = wp_get_schedules();
                foreach ( $active_cron_schedules as $key => $value ) {

                  echo '<option value="' . $key . '">' . $value['display'] . '</option>';

                }
                ?>
              </select>
            </td>
          </tr>

          <tr valign="top">
            <th>Delete all plugin settings on uninstall?</th>
            <?php
            $checked = get_option( 'tld-wcdpue-delete-db-settings' );
            if ( !empty( $checked ) ){
              $checked = "checked";
            }
            ?>
            <td><input type="checkbox" name="tld-wcdpue-delete-db-settings" <?php echo $checked ?>></td>
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
            <input type="hidden" name="business" value="donation@tutbakery.com">
            <input type="hidden" name="lc" value="LC">
            <input type="hidden" name="item_name" value="Support TheLoneDeveloper with your kind donation.">
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
    register_setting( 'tld-wcdpue-settings-group', 'tld-wcdpue-email-bursts-count' );
    register_setting( 'tld-wcdpue-settings-group', 'tld-wcdpue-schedule-setting-value' );
    register_setting( 'tld-wcdpue-settings-group', 'tld-wcdpue-delete-db-settings' );

  }

  function tld_wcdpue_update_schedule() {

    $tld_wcdpue_cur_recurrence = get_option( 'tld-wcdpue-schedule-setting-value' ); //get interval set by user
    $active_cron_schedules = wp_get_schedules();

    foreach ( $active_cron_schedules as $key => $value ) {

      if ( $key == $tld_wcdpue_cur_recurrence ){

        $tld_wcdpue_wait_time = $value['interval'];

      }

    }
    wp_clear_scheduled_hook('tld_wcdpue_email_burst'); //remove previous scheduled time
    wp_schedule_event( time() + $tld_wcdpue_wait_time , $tld_wcdpue_cur_recurrence, 'tld_wcdpue_email_burst' ); //add new scheduled time

  }
  add_action( 'update_option_tld-wcdpue-schedule-setting-value', 'tld_wcdpue_update_schedule');

  ?>
