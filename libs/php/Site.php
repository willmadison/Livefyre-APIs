<?php

include('Article.php');

class Livefyre_Site {
    private $id;
    private $domain;
    
    public function __construct($id, $domain) {
        $this->id = $id;
        $this->domain = $domain;
    }
    
    public function article($article_id, $url, $title, $tags = "") {
        return new Livefyre_Article($article_id, $this, $url, $title, $tags);
    }
    
    public function get_domain() {
        return $this->domain;
    }
    
    public function get_id() {
        return $this->id;
    }
}

?>