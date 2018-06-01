<?php

// require_once dirname( __FILE__ ) . '/class-build-email.php';

class sendEmail extends buildEmail{

  // public function __construct( $type ){
  //   // $this->type = $type;
  //
  // if ($type === "schedule" ){
  //   $this->schedule_mail_instead();
  // }elseif ($type === "immediate") {
  //   $this->send_email_immediately();
  // }
  // }


  public function send_schedule_mail($which){

    //get our options

    $email_subject = $this->get_email_subject();
    $email_body = $this->get_email_body();
    $email_bursts_count = $this->get_email_burst_count();

    global $wpdb;
    $tld_wcdpue_tbl_prefix = $wpdb->prefix;
    $tld_wcdpue_the_scheduling_table = $tld_wcdpue_tbl_prefix . 'woocommerce_downloadable_product_emails_tld';
    $query_result = $wpdb->get_results( "SELECT * FROM $tld_wcdpue_the_scheduling_table ORDER BY id ASC LIMIT $email_bursts_count" );

    foreach ( $query_result as $result ){

      $tld_wcdpue_product_id = $result->product_id;
      $tld_wcdpue_post_title = $this->get_product_title( $tld_wcdpue_product_id );
      $tld_wcdpue_product_url = $this->get_product_url( $tld_wcdpue_product_id );
      $tld_wcdpue_home_url = esc_url( home_url() );
      $tld_wcdpue_buyer_email_address = $result->user_email;
      $tld_wcdpue_email_message = $email_body . "\n\n";
      $tld_wcdpue_email_message .= "\n\n" . $tld_wcdpue_post_title . ": " . "\n\n" . $tld_wcdpue_product_url . "\n\n" . "\n\n".  $tld_wcdpue_account_url;
      wp_mail( $tld_wcdpue_buyer_email_address, $email_subject, $tld_wcdpue_email_message );
      $wpdb->delete( $tld_wcdpue_the_scheduling_table, array( 'id' => $result->id ) );   //delete the current row in loop after mail sent
      sleep(2); //short breath, no rush.

    }
  }

  public function email_body( $post_id ){

    $email_body = $this->get_email_body() . "\n\n";
    $email_body .= $this->get_product_title($post_id) . "— " . $this->get_product_url($post_id) . "\n\n" . $this->get_account_url();
    return $email_body;

  }

  public function insert_schedule_mail( $post_id, $customer_email ){

    global $wpdb;
    $tld_wcdpue_the_scheduling_table = TLD_WCDPUE_SCHEDULED_TABLE;
    $wpdb->insert(
      $tld_wcdpue_the_scheduling_table,
      array(

        'id' => '',
        'product_id' => $post_id,
        'user_email' => $customer_email,

      )
    );

  }

  public function send_email_immediately( $post_id ){

    $customer2  = new buildCustomer();

    foreach ( $customer2->grab_customers( $post_id ) as $customer ){

      if( ! in_array( $customer->user_email, $tld_wcdpue_no_spam ) ){

        wp_mail( $customer->user_email, $this->get_email_subject(), self::email_body( $post_id ) );
        $tld_wcdpue_emails_sent_count++;

      }

      $tld_wcdpue_no_spam[] = $customer->user_email;

    }

    setcookie( "tld-wcdpue-emails-sent-count", $tld_wcdpue_emails_sent_count );

  }

  public function schedule_mail_instead( $post_id ){

    $customers = new buildCustomer();

    foreach ( $customers->grab_customers( $post_id ) as $customer ){

      if( ! in_array( $customer->user_email, $tld_wcdpue_no_spam ) ){

        $this->insert_schedule_mail( $post_id, $customer->user_email );

        $tld_wcdpue_emails_scheduled_count++;

      }

      $tld_wcdpue_no_spam[] = $customer->user_email;

    }

    //create amount of emails scheduled cookie for JS
    setcookie( "tld-wcdpue-emails-scheduled-count", $tld_wcdpue_emails_scheduled_count );

  }


}
