<?php
namespace Livefyre;

include("Token.php");

class User {
    private $uid;
    private $domain;
    
    public function __construct($uid, $domain) {
        $this->uid = $uid;
        $this->domain = $domain;
    }
    
    public function get_uid() { return $this->uid; }
    public function get_domain() { return $this->domain; }
    
    public function jid() {
        return $this->$uid.'@'.$this->domain->get_host();
    }
    
    public function token($age=86400) {
        $domain_key = $this->domain->get_key();
        assert('$domain_key != null /* Domain key is necessary to generate token */');
        return Token::from_user($this);
    }
}

?>