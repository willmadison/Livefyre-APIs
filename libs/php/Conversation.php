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
        $domain = $this->article->get_site()->get_domain()->get_host();
        return file_get_contents("http://bootstrap.$domain/api/v1.1/public/bootstrap/html/$site_id/".urlencode(base64_encode($article_id)).".html");
    }
}

?>
