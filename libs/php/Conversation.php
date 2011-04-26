<?php
namespace Livefyre;

class Conversation {
    private $id;
    private $article;
    
    public function __construct($conv_id, $article=null) {
        $this->id = $conv_id;
        $this->article = $article;
    }

    public function to_html() {
        assert('$this->article != null /* Article is necessary to get HTML */');
        $site_id = $this->article->get_site()->get_id();
        $article_id = $this->article->get_id();
        return file_get_contents("http://bootstrap.livefyre.com/bootstrap/fyres/show_partial/0/$site_id/$article_id/");
    }
}

?>