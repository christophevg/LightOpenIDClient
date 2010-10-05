<?php

/**
 * LightOpenIDClient
 * 
 * A small Client wrapper around the very nice LightOpenID
 * See: http://gitorious.org/lightopenid
 * 
 * @author Christophe VG
 */

include_once dirname(__FILE__) . '/lightopenid/openid.php';

class LightOpenIDClient {
  private $openid;
  private static $instance;
  
  public static function getInstance() {
    if( ! self::$instance ) {
      self::$instance = new LightOpenIDClient();
    }
    return self::$instance;
  }

  private function __construct() {
    $this->openid = new LightOpenID();
  }

  private function handleLoginRequest() {
    global $_POST;
    if( isset($_POST['openid_identifier']) ) {
      try {
        $this->openid->identity = $_POST['openid_identifier'];
        header( 'Location: ' . $this->openid->authUrl() );
        exit();
      } catch(ErrorException $e) {
        echo "<h1>Whoops</h1>";
        echo $e->getMessage();
      }
      unset( $_POST['openid_identifier'] ); // only process this once
    }
  }
  
  public function withRequired() {
    $this->openid->required = func_get_args();
    return $this;
  }
  
  public function withOptional() {
    $this->openid->optional = func_get_args();
    return $this;
  }

  public function getUser() {
    $this->handleLoginRequest();
    
    global $_GET;
    if( isset($_GET['openid_mode']) && $_GET['openid_mode'] != 'cancel' &&
      $this->openid->validate() ) 
    {
      return new LightOpenIDUser( 
        array( 
          'identity'   => $this->openid->identity,
          'attributes' => $this->openid->getAttributes() 
        ) 
      );
    }
  }
}

class LightOpenIDUser {
  private $info;
  
  function __construct( $info ) {
    $this->info = $info;
  }
  
  function __get( $prop ) {
    switch( $prop ) {
      case 'identity':
        return isset($this->info['identity']) ? $this->info['identity'] : null;
      case 'firstName':
        return isset($this->info['attributes']) && 
          isset($this->info['attributes']['namePerson/first']) ?
            $this->info['attributes']['namePerson/first'] : null;
      case 'nick':
        return isset($this->info['attributes']) && 
          isset($this->info['attributes']['namePerson/friendly']) ?
            $this->info['attributes']['namePerson/friendly'] : null;
      case 'email':
        return isset($this->info['attributes']) && 
          isset($this->info['attributes']['contact/email']) ?
            $this->info['attributes']['contact/email'] : null;
    }
  } 
}
