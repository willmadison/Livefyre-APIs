<?php

class Livefyre_Conversation {
    private $id;
    private $article;
    
    public function __construct( $conv_id = null, $article = null ) {
        $this->id = $conv_id;
        $this->article = $article;
    }

    public function to_initjs( $user = null, $display_name = null, $backplane = false ) {
        $domainname = $this->article->get_site()->get_domain()->get_host();
        $config = array(
            'domain' => $domainname,
            'site_id' => $this->article->get_site()->get_id(),
            'article_id' => $this->article->get_id()
        );
        if ( $backplane ) {
            $add_backplane = 'if ( typeof(Backplane) != \'undefined\' ) { lf_config.backplane = Backplane; };';
        } else {
            $add_backplane = '';
        }
        if ( $user ) {
            $login_json = array( 
                'token' => $user->token( ), 
                'profile' => array( 
                    'display_name' => $display_name
                )
            );
            $login_json_str = json_encode( $login_json );
            $login_js = "LF.ready( function() {LF.login($login_json_str);} );";
        } else {
            $login_js = '';
        }
        return '<script type="text/javascript" src="http://zor.' . $domainname . '/wjs/v1.0/javascripts/livefyre_init.js"></script>
        <script type="text/javascript">
            var lf_config = ' . json_encode( $config ) . ';
            ' . $add_backplane . '
            var conv = LF(lf_config);
            ' . $login_js . '
        </script>';
    }

    public function to_html( ) {
        assert('$this->article != null /* Article is necessary to get HTML */');
        $site_id = $this->article->get_site()->get_id();
        $article_id = $this->article->get_id();
        $domain = $this->article->get_site()->get_domain()->get_host();
        return file_get_contents("http://bootstrap.$domain/api/v1.1/public/bootstrap/html/$site_id/".urlencode(base64_encode($article_id)).".html");
    }
}

?>
