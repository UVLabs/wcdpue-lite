jQuery( function($){

  $( document ).ready( function() {


  });

  $(window).load(function() {

    var tldOphanCookie = Cookies.get( 'tld-wcdpue-cookie' );
    var tld_wcdpue_emails_scheduled_count =  Cookies.get( 'tld-wcdpue-emails-scheduled-count' );
    var tld_wcdpue_emails_sent_count = Cookies.get( 'tld-wcdpue-emails-sent-count' );

    //add and statement to check which notification option user picked
    if ( tld_wcdpue_emails_scheduled_count !== undefined ){

      document.getElementById( "tld-wcdpue-email-status" ).style.cssText="margin-top: 15px; font-weight: bold; text-decoration: underline";

      if ( tld_wcdpue_emails_scheduled_count > 1 ){
        document.getElementById( "tld-wcdpue-email-status" ).innerHTML = tld_wcdpue_emails_scheduled_count + " emails scheduled";
      }else{
        document.getElementById( "tld-wcdpue-email-status" ).innerHTML = tld_wcdpue_emails_scheduled_count + " email scheduled";
      }

      document.cookie = "tld-wcdpue-emails-scheduled-count = tld-wcdpue-emails-scheduled-count; expires=Thu, 01 Jan 1970 00:00:00 UTC";

    }

    if ( tld_wcdpue_emails_sent_count !== undefined ){

      document.getElementById( "tld-wcdpue-email-status" ).style.cssText="margin-top: 15px; font-weight: bold; text-decoration: underline;";

      if ( tld_wcdpue_emails_sent_count > 1 ){
        document.getElementById( "tld-wcdpue-email-status" ).innerHTML = tld_wcdpue_emails_sent_count + " emails sent";
      }else{
        document.getElementById( "tld-wcdpue-email-status" ).innerHTML = tld_wcdpue_emails_sent_count + " email sent";

      }

      document.cookie = "tld-wcdpue-emails-sent-count = tld_wcdpue_emails_sent; expires=Thu, 01 Jan 1970 00:00:00 UTC";


    }

    //delete cookie if it already existed, in case user sets switch to active but refreshes page

    if ( tldOphanCookie !== null ) {

      Cookies.remove( 'tld-wcdpue-cookie' );

    }

  });

});


function tld_cookie_business() {

  var tldCookie = Cookies.get( 'tld-wcdpue-cookie' );

  if (tldCookie == null) {

    Cookies.set( 'tld-wcdpue-cookie', '1' );
    document.getElementById( "meta-switch-label" ).style.cssText="color: #228B22; font-weight: bold; ";
    document.getElementById( "meta-switch-label" ).innerHTML = "Activated"

  } else {

    Cookies.remove( 'tld-wcdpue-cookie' );
    document.getElementById( "meta-switch-label" ).style.cssText="color: inherit; font-weight: normal; ";
    document.getElementById( "meta-switch-label" ).innerHTML = "Deactivated"

  }
}
