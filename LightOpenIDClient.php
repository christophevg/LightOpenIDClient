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
  
  public function withRequired( $requiredInfo ) {
    $this->openid->required = $requiredInfo;
    return $this;
  }
  
  public function withOptional( $optionalInfo ) {
    $this->openid->optional = $optionalInfo;      
    return $this;
  }

  public function getUser() {
    $this->handleLoginRequest();
    
    global $_GET;
    if( isset($_GET['openid_mode']) && $_GET['openid_mode'] != 'cancel' &&
      $this->openid->validate() ) 
    {
      return array( 
        'identity'   => $this->openid->identity,
        'attributes' => $this->openid->getAttributes() 
      );
    }
  }
}
