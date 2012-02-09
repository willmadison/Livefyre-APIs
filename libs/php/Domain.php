<?php

include("User.php");
include("Site.php");
include("Token.php");

class Livefyre_Domain {
    private $host;
    private $key;
    
    public function __construct($host, $key=null) {
        $this->host = $host;
        $this->key = $key;
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
}

?>
