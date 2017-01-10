<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! function_exists( 'tld_wcdpue_review_notice' ) ) {
  // Add an admin notice.
  add_action('admin_notices', 'tld_wcdpue_review_notice');
  /**
  *  Admin Notice to Encourage a Review or Donation.
  *
  *  @author Matt Cromwell
  *  @version 1.0.0
  */
  function tld_wcdpue_review_notice() {
    // Define your Plugin name, review url, and donation url.
    $plugin_name = 'WCDPUE Lite';
    $review_url = 'https://wordpress.org/support/plugin/tld-woocommerce-downloadable-product-update-emails/reviews#new-post';

    // Get current user.
    global $current_user, $pagenow ;
    $user_id = $current_user->ID;
    // Get today's timestamp.
    $today = mktime( 0, 0, 0, date('m')  , date('d'), date('Y') );
    // Get the trigger date
    $tld_wcdpue_triggerdate = get_option( 'tld_wcdpue_activation_date', false );
    $installed = ( ! empty( $tld_wcdpue_triggerdate ) ? $tld_wcdpue_triggerdate : '999999999999999' );
    // First check whether today's date is greater than the install date plus the delay
    // Then check whether the use is a Super Admin or Admin on a non-Multisite Network
    // For testing live, remove `$installed <= $today &&` from this conditional

    if ( $installed <= $today && danp_is_super_admin_admin( $current_user = $current_user ) == true ) {
      // Make sure we're on the plugins page.
      if ( 'plugins.php' == $pagenow ) {
        // If the user hasn't already dismissed our alert,
        // Output the activation banner.
        $nag_admin_dismiss_url = 'plugins.php?tld_wcdpue_review_dismiss=0';
        $user_meta             = get_user_meta( $user_id, 'tld_wcdpue_review_dismiss' );

        if ( empty( $user_meta ) ) {
          ?>
          <div class="notice notice-success">

          <style>
          p.review {
            position: relative;
            margin-left: 35px;
          }
          p.review span.dashicons-heart {
            color: white;
            background: #66BB6A;
            position: absolute;
            left: -50px;
            padding: 9px;
            top: -8px;
          }
          p.review strong {
            color: #66BB6A;
          }
          p.review a.dismiss {
            float: right;
            text-decoration: none;
            color: #66BB6A;
          }
          </style>

          <p class="review"><span class="dashicons dashicons-heart"></span><?php echo wp_kses( sprintf( __( 'Hey :) hope <strong>' . $plugin_name . '</strong> is helping you better manage your store. Would you please consider <a href="%1$s" target="_blank">rating this plugin?</a> 5 stars would be nice :)', 'wcdpue-pro' ), esc_url( $review_url ) ), array( 'strong' => array(), 'a' => array( 'href' => array(), 'target' => array() ) ) ); ?><a href="<?php echo admin_url( $nag_admin_dismiss_url ); ?>" class="dismiss"><span class="dashicons dashicons-dismiss"></span></a>
          </div>

          <?php }
        }
      }
    }
  }

  if ( ! function_exists( 'tld_wcdpue_ignore_review_notice' ) ) {
    // Function to force the Review Admin Notice to stay dismissed correctly.
    add_action('admin_init', 'tld_wcdpue_ignore_review_notice');
    /**
    * Ignore review notice.
    *
    * @since  1.0.0
    */
    function tld_wcdpue_ignore_review_notice() {

      if ( isset( $_GET[ 'tld_wcdpue_review_dismiss' ] ) && '0' == $_GET[ 'tld_wcdpue_review_dismiss' ] ) {
        // Get the global user.
        global $current_user;
        $user_id = $current_user->ID;
        add_user_meta( $user_id, 'tld_wcdpue_review_dismiss', 'true', true );
      }

    }

  }

  if ( ! function_exists( 'danp_is_super_admin_admin' ) ) {
    // Helper function to determine whether the current
    // use is a Super Admin or Admin on a non-Network environment
    function danp_is_super_admin_admin($current_user){

      global $current_user;
      $shownotice = false;
      if (is_multisite() && current_user_can('create_sites')) {
        $shownotice = true;
      } elseif (is_multisite() == false && current_user_can('install_plugins')) {
        $shownotice = true;
      } else {
        $shownotice = false;
      }
      return $shownotice;

    }

  }
