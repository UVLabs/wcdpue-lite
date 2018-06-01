<?php

class buildCustomer{

  public function grab_customers( $post_id ){
    global $wpdb;
    $tld_wcdpue_tbl_prefix = $wpdb->prefix;
    $tld_wcdpue_dls_table = $tld_wcdpue_tbl_prefix . 'woocommerce_downloadable_product_permissions';
    $tld_wcdpue_query_result = $wpdb->get_results(
      "SELECT DISTINCT product_id, order_id, order_key, user_email
      FROM $tld_wcdpue_dls_table
      WHERE ( product_id = $post_id )
      AND (access_expires > NOW() OR access_expires IS NULL )
      "
    );

    return $tld_wcdpue_query_result;
  }

}
