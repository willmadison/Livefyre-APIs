<?php
namespace Livefyre;

include("User.php");
include("Site.php");

class Domain {
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
        return new User($uid, $this);
    }
    
    public function site($site_id) {
        return new Site($site_id, $this);
    }
}

?>