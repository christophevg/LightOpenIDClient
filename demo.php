<?php

/**
 * Demo page for the LightOpenIDClient.php
 */

include_once 'LightOpenIDClient.php'; // this will also include LightOpenID

// start a session to cache user's info
session_start();

// get an instance of the client and configure it
$openid = LightOpenIDClient::getInstance()
            ->withRequired( 'namePerson/friendly', 'contact/email' )
            ->withOptional( 'namePerson/first' )
            ->cacheInSession();

// handle optionally a log off event and pass it on to the OpenID client
if( isset($_GET['action']) && $_GET['action'] == 'logoff' ) {
  $openid->logoff();
}

// try to retrieve an authenticated user              
if( $user = $openid->getUser() ) { 
  print <<<EOT
  identity: $user->identity<br> 
  email : $user->email<br>
  nickname : $user->nick<br>
  first name : $user->firstName<br>
  <br>
  <a href="demo.php">refresh</a> | 
  <a href="demo.php?action=logoff">log off</a>
EOT;

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
