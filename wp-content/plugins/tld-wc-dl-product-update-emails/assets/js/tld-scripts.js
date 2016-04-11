function getCookie(name) {
  var dc = document.cookie;
  var prefix = name + "=";
  var begin = dc.indexOf("; " + prefix);
  if (begin == -1) {
    begin = dc.indexOf(prefix);
    if (begin != 0) return null;
  }
  else
  {
    begin += 2;
    var end = document.cookie.indexOf(";", begin);
    if (end == -1) {
      end = dc.length;
    }
  }
  return unescape(dc.substring(begin + prefix.length, end));
}


jQuery( function($){

  $( document ).ready( function() {


  });

  $(window).load(function() {

    var tldOphanCookie = getCookie("tld-cookie");
    //delete cookie if it already existed, in case user sets switch to active but refreshes page
    if (tldOphanCookie !== null) {

      document.cookie = "tld-cookie = tld-switch-cookie; expires=Thu, 01 Jan 1970 00:00:00 UTC"

    }

  });

});


function tld_cookie_business() {

  var tldCookie = getCookie("tld-cookie");
  if (tldCookie == null) {

    document.cookie = "tld-cookie = tld-switch-cookie"
    //console.log('cookie created')

  } else {

    //console.log('cookie already exists')
    document.cookie = "tld-cookie = tld-switch-cookie; expires=Thu, 01 Jan 1970 00:00:00 UTC"
    //console.log('cookie deleted')
  }
}
