<?php
DEFINE('TOKEN_URL','https://api.twitter.com/oauth2/token');
DEFINE('TIMELINE_URL','https://api.twitter.com/1.1/statuses/user_timeline.json');

class TwitterConnect {
  
  var $consumer;
  var $secret;
  var $response;
  var $ch;
  var $bearerToken;

  function __construct($consumer, $secret) {
    $this->consumer    = $consumer;
    $this->secret      = $secret;
    $this->response    = new stdClass();
    $this->_bearerToken();
  }

  function userTimeLine($screen_name) {
    $options = array(
      CURLOPT_PROXY => 'gc-proxy001',
      CURLOPT_PROXYPORT => 3128,
      CURLOPT_HTTPGET => true,
      CURLOPT_RETURNTRANSFER => true,       
      CURLOPT_FOLLOWLOCATION => true,         
      CURLOPT_HTTPHEADER => array("Authorization: Bearer {$this->bearerToken}"),
    );
    $this->_request(TIMELINE_URL . "?count=1&screen_name=" . $screen_name, $options);
    return $this->response->body;
  }

  private function _bearerToken() {
    $auth_string = base64_encode($this->consumer.":".$this->secret);
    $options = array(
      CURLOPT_PROXY => 'gc-proxy001',
      CURLOPT_PROXYPORT => 3128,
      CURLOPT_POST => true,
      CURLOPT_RETURNTRANSFER => true,       
      CURLOPT_USERAGENT      => "Ebay Classifieds Group", 
      CURLOPT_HTTPHEADER => array("Authorization: Basic {$auth_string}"),
      CURLOPT_POSTFIELDS => array("grant_type" => "client_credentials"),
    );
    $this->_request(TOKEN_URL, $options);
    $this->bearerToken = $this->response->body->access_token;
  }

  private function _request($url, $options = false) {
    $this->ch = curl_init($url);
    if($options) {
      foreach($options as $key=>$value) {
        curl_setopt($this->ch, $key, $value);
      }
    }
    $this->response->body     = curl_exec($this->ch);
    $this->response->body     = json_decode($this->response->body);
    $this->response->err      = curl_errno($this->ch); 
    $this->response->errmsg   = curl_error($this->ch) ; 
    $this->response->header   = curl_getinfo($this->ch); 
    curl_close($this->ch);
  }

  private function _curl_init($url) {
    /*$options = array( 
      CURLOPT_RETURNTRANSFER => true,       
      CURLOPT_HEADER         => false,        
      CURLOPT_FOLLOWLOCATION => true,         
      CURLOPT_ENCODING       => "",           
      CURLOPT_USERAGENT      => "Ebay Classifieds Group", 
      CURLOPT_AUTOREFERER    => true,         
      CURLOPT_CONNECTTIMEOUT => 120,          
      CURLOPT_TIMEOUT        => 120,          
      CURLOPT_MAXREDIRS      => 10,           
      #CURLOPT_SSL_VERIFYHOST => false,      
      #CURLOPT_SSL_VERIFYPEER => false,         
      #CURLOPT_VERBOSE        => true           
    );
 
    curl_setopt_array($this->ch,$options); 
    */
  }

}

