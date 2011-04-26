<?php
namespace Livefyre;

include("Conversation.php");

class Article {
    private $id;
    private $site;
    
    public function __construct($id, $site) {
        $this->id = $id;
        $this->site = $site;
    }
    
    public function get_id() {
        return $this->id;
    }
    
    public function get_site() {
        return $this->site;
    }
    
    public function conversation() {
        return new Conversation(null, $this);
    }
}

?>