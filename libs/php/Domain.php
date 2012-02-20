<?php

include("User.php");
include("Site.php");
//include("Http.php");

class Livefyre_Domain {
    private $host;
    private $key;
    
    public function __construct($host, $key=null, $http_api=null) {
        $this->host = $host;
        $this->key = $key;
        if ($http_api != null) {
            $this->http_api = $http_api;
        } else {
            //or i can include it here
            echo "instantiatesssss lf http<br>";
            include_once("Http.php");
            $this->http_api = new Livefyre_http;  
        }
    }
    public function get_http() {
        return $this->http_api;
    }

    public function get_host() {
        return $this->host;
    }
    
    public function get_key() {
        return $this->key;
    }
    
    public function user($uid) {
        return new Livefyre_User($uid, $this);
    }
    
    public function site($site_id) {
        return new Livefyre_Site($site_id, $this);
    }

    public function validate_server_token($token) {
        return lftokenValidateServerToken($token, $this->key);
    }
}

?>
