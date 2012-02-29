<?php

if ( !defined( 'LF_DEFAULT_TLD' ) ) {
    define( 'LF_DEFAULT_TLD', 'livefyre.com' );
}
if ( !defined( 'LF_DEFAULT_TLD' ) ) {
    define( 'LF_DEFAULT_PROFILE_DOMAIN', 'livefyre.com' );
}
include("User.php");
include("Site.php");

class Livefyre_Domain {
    private $host;
    private $key;
    
    public function __construct($host, $key=null, $http_api=null) {
        $this->host = $host;
        $this->key = $key;
        if ($http_api != null) {
            $this->http_api = new $http_api;
        } else {
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
    
    public function user($uid, $display_name = null) {
        return new Livefyre_User($uid, $this, $display_name);
    }
    
    public function site($site_id) {
        return new Livefyre_Site($site_id, $this);
    }

    public function validate_server_token($token) {
        return lftokenValidateServerToken($token, $this->key);
    }
}

?>
