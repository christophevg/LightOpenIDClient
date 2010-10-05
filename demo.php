<?php

/**
 * Demo page for the LightOpenIDClient.php
 */

include_once 'LightOpenIDClient.php'; // this will also include LightOpenID

// get an instance of the client and set some requirements
$openid = LightOpenIDClient::getInstance()
            ->withRequired( 'namePerson/friendly', 'contact/email' )
            ->withOptional( 'namePerson/first' )

// try to retrieve an authenticated user              
if( $user = $openid->getUser() ) { 

  print "<pre>"; 
  print_r( $user ); 
  print "</pre>";
  print '<a href="demo.php">login</a>';

} else { // present a login form

  print <<<EOT
    <script>
    var openid = {
      config : { 
        google   : 'https://www.google.com/accounts/o8/id',
        myopenid : 'http://myopenid.com'
      },
      signin : function(provider) {
        document.getElementById('openid').value = this.config[provider];
        document.getElementById('openidForm').submit();
        return false;
      }
    };
    </script>
    <h3>Login using one of these OpenID Providers:</h3>
    <a href="#" onclick="openid.signin('google');">Google</a>
    <a href="#" onclick="openid.signin('myopenid');">MyOpenID</a>

    <h3>or enter your OpenID manually...</h3>
    <form id="openidForm" action="" method="post">
    OpenID: <input id="openid" type="text" name="openid_identifier" /> 
    <button>Submit</button>
    </form>
EOT;

} 
