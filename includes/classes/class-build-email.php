<?php

class buildEmail{

  // protected $type;
  // protected $email_subject;
  // protected $email_body;
  // protected $email_bursts_count;
  // protected $account_url;

  protected function get_email_subject(){
    $email_subject = strip_tags( get_option( 'tld-wcdpue-email-subject' ) );
    if ( ! empty( $email_subject ) ){
      return $email_subject;
    }else{
      return "A product you bought has been updated!";
    }
  }

  protected function get_email_body(){
    $email_body = strip_tags( get_option( 'tld-wcdpue-email-body' ) );
    if ( ! empty( $email_body ) ){
      return $email_body;
    }else{
      return "There is a new update for your product: ";
    }
  }

  protected function get_email_burst_count(){
    $email_bursts_count = esc_attr( get_option( 'tld-wcdpue-email-bursts-count' ) );
    if ( ! empty( $email_bursts_count ) ){
      return $email_bursts_count;
    }else{
      return 5;
    }
  }

  protected function get_account_url(){
    $account_url = esc_url ( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) );
    if ( ! empty( $account_url ) ){
      return $account_url;
    }else{
      return get_site_url();
    }
  }

  protected function get_product_title( $id ){
    $title = get_the_title( $id );
    return apply_filters( 'wcdpue_product_title', $title );
  }

  protected function get_product_url( $id ){
    $url = esc_url( get_permalink( $id ) );
    return $url;
  }

}
