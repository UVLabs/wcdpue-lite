<?php
// create custom plugin settings menu
add_action('admin_menu', 'tld_wcdpue_add_admin_menu');

function tld_wcdpue_add_admin_menu() {

  //create new settings menu item
  add_options_page('TLD Product Update Emails', 'TLD Product Update Emails', 'administrator', 'tld_product_update_emails', 'tld_wcdpue_settings_page' );

  //call register settings function
  add_action( 'admin_init', 'tld_wcdpue_settings' );
}

function tld_wcdpue_settings_page() {
  //page mark up
  ?>

  <div class="wrap">
    <h2>TLD WC Downloadable Product Update Emails</h2>

    <form method="post" action="options.php">

      <?php settings_fields( 'tld_wcdpue-settings-group' ); ?>
      <?php do_settings_sections( 'tld_wcdpue-settings-group' ); ?>

      <table class="form-table">

        <tr valign="top">
          <td><h3>Email Options</h3></td>
        </tr>

        <tr valign="top">
          <th scope="row">Email Subject</th>
          <td><input type="text" name="tld-wcdpue-email-subject" value="<?php echo esc_attr( get_option('tld-wcdpue-email-subject') ); ?>" placeholder="Your Downloadable Product has been updated!" size="70"/></td>
        </tr>

        <tr valign="top">
          <th scope="row">Message</th>
          <td>
            <textarea name="tld-wcdpue-email-body" placeholder="Enter optional text for the email body." rows="15" cols="70"/><?php echo esc_attr( get_option('tld-wcdpue-email-body') ); ?>
          </textarea>
        </td>
      </tr>

      <tr valign="top">
        <td><h3>Email Schedule Options</h3></td>
      </tr>

      <tr valign="top">
        <th scope="row">Send e-mails in bursts of <small>(recommend you keep this value below 10)</small>:</th>
        <td>
          <input type="number" name="tld-wcdpue-email-bursts-count" value="<?php echo esc_attr( get_option('tld-wcdpue-email-bursts-count') ); ?>" min="1" size="4"/>
        </td>
      </tr>

      <tr valign="top">
        <th scope="row">Select Schedule:</th>
        <td>

          <select name="tld-wcdpue-schedule-setting-value">
            <?php

            $active_cron_schedules = wp_get_schedules();
            foreach ( $active_cron_schedules as $key => $value ) {

              echo '<option value="' . $key . '">' . $value['display'] . '</option>';

            }

            ?>
          </select>
        </td>

      </tr>

      <tr valign="top">
        <th>Current Schedule:</th>
        <td>
          <?php

          foreach ( $active_cron_schedules as $key => $value ) {

            $tld_wcdpue_current_schedule = esc_attr( get_option( 'tld-wcdpue-schedule-setting-value' ) );
            if( $key == $tld_wcdpue_current_schedule ){

              echo  $value['display'];
              
            }
          }

          ?>
        </td>
      </tr>
    </table>

    <?php submit_button(); ?>

  </form>
</div>


<?php }

function tld_wcdpue_settings() {

  //register our settings
  register_setting( 'tld_wcdpue-settings-group', 'tld-wcdpue-email-subject' );
  register_setting( 'tld_wcdpue-settings-group', 'tld-wcdpue-email-body' );
  register_setting( 'tld_wcdpue-settings-group', 'tld-wcdpue-email-bursts-count' );
  register_setting( 'tld_wcdpue-settings-group', 'tld-wcdpue-schedule-setting-value' );

}

function tld_wcdpue_update_schedule() {

  $tld_wcdpue_set_cron_interval = get_option( 'tld-wcdpue-schedule-setting-value' ); //get interval set by user
  wp_clear_scheduled_hook('tld_wcdpue_email_burst'); //remove previous scheduled time
  wp_schedule_event( time(), $tld_wcdpue_set_cron_interval, 'tld_wcdpue_email_burst' ); //add new scheduled time

}
add_action( 'update_option_tld-wcdpue-schedule-setting-value', 'tld_wcdpue_update_schedule');

?>
