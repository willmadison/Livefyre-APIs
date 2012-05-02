<?php
require_once('JWT.php');

class Livefyre_Conversation {
    private $id;
    private $article;
    private $delegates;
    
    public function __construct( $conv_id = null, $article = null ) {
        $this->id = $conv_id;
        $this->article = $article;
        $this->delegates = array();
    }
    
    public function add_js_delegate( $delegate_name, $code ) {
        $this->delegates[ $delegate_name ] = $code;
    }
    
    public function render_js_delegates( ) {
        $str_out = '';
        if ( $this->delegates ) {
            $str_out = "var livefyreConvDelegates = {\n";
            foreach ($this->delegates as $handler => $code) {
                $str_out .= "    handle_$handler: " . $code . ", \n";
            }
            $str_out .= "}\nLF.ready( function() { LF.Dispatcher.addListener(livefyreConvDelegates); } )";
        }
        return $str_out;
    }

    public function to_initjs( $user = null, $display_name = null, $backplane = false, $jquery_ready = false ) {
        // When called, this will render all delegates added thru add_js_delegate
        $article = $this->article;
        $site = $article->get_site();
        $profile_domain = $site->get_domain()->get_host();
        $site_key = $site->get_key();
        $config = array(
            'site_id' => $site->get_id(),
            'article_id' => $article->get_id()
        );
        $builds_token = true;
        if ( $profile_domain != LF_DEFAULT_PROFILE_DOMAIN ) {
            $config[ 'domain' ] = $profile_domain;
        } else {
            // nobody but Livefyre can build tokens for livefyre.com profiles
            $builds_token = false;
        }
        if ( !empty($site_key) && !empty($article->get_url()) && !empty($article->get_title()) ) {
            // Produce a conv meta checksum if we have enough data
            $sig_fields = array($config['article_id'], $article->get_url(), $article->get_title(), $site_key);
            $config['conv_meta'] = array(
                'article_url' => $article->get_url(),
                'title' => $article->get_title(),
                'sig' => md5(implode(',', $sig_fields))
            );
        }
        if ( $backplane ) {
            $add_backplane = 'if ( typeof(Backplane) != \'undefined\' ) { lf_config.backplane = Backplane; };';
        } else {
            $add_backplane = '';
        }
        $login_js = '';
        if ( $user && $builds_token ) {
            $login_json = array( 'token' => $user->token( ), 'profile' => array('display_name' => $display_name) );
            $login_json_str = json_encode( $login_json );
            $login_js = "LF.ready( function() {LF.login($login_json_str);} );";
        }
        return '<script type="text/javascript" src="http://zor.' . LF_DEFAULT_TLD . '/wjs/v1.0/javascripts/livefyre_init.js"></script>
        <script type="text/javascript">
            ' . ($jquery_ready ? 'jQuery(function(){' : '') . '
            var lf_config = ' . json_encode( $config ) . ';
            ' . $add_backplane . '
            var conv = LF(lf_config);
            ' . $login_js . '
            ' . $this->render_js_delegates() . '
            ' . ($jquery_ready ? '});' : '') . '
        </script>';
    }
    
    public function to_initjs_v1( $user = null, $display_name = null, $backplane = false ) {
         $this->to_initjs( $user, $display_name, $backplane );
    }
    
    public function to_initjs_v2( $user = null, $display_name = null, $backplane = false ) {
        // When called, this will render all delegates added thru add_js_delegate
        $profile_domain = $this->article->get_site()->get_domain()->get_host();
        $meta = array("title" => $this->article->get_title(),
                "url" => $this->article->get_url(),
                "tags" => $this->article->get_tags());
        $checksum = md5(json_encode($meta));
        $collectionMeta = array("meta" => $meta,
                "checksum" => $checksum);
        $jwtString = JWT::encode($collectionMeta, $this->article->get_site()->get_key());
        $newConfig = array("collectionMeta" => $jwtString,
                "checksum" => $checksum,
                "siteId" =>  $this->article->get_site()->get_id(),
                "articleId" => $this->article->get_id());
        
        $builds_token = true;
        if ( $profile_domain != LF_DEFAULT_PROFILE_DOMAIN ) {
            $newConfig[ 'network' ] = $profile_domain;
        } else {
            // nobody but Livefyre can build tokens for livefyre.com profiles
            $builds_token = false;
        }
        if ( $backplane ) {
            $add_backplane = 'if ( typeof(Backplane) != \'undefined\' ) { lf_config.backplane = Backplane; };';
        } else {
            $add_backplane = '';
        }
        $login_js = '';
        if ( $user && $builds_token ) {
            $login_json = array( 'token' => $user->token( ), 'profile' => array('display_name' => $display_name) );
            $login_json_str = json_encode( $login_json );
            $login_js = "LF.ready( function() {LF.login($login_json_str);} );";
        }
        
        return '<script type="text/javascript" src="http://zor.' . LF_DEFAULT_TLD . '/wjs/v1.0/javascripts/livefyre_init.js"></script>
        <script type="text/javascript">
        var lf_config = ' . json_encode( $newConfig ) . ';
        ' . $add_backplane . '
        var conv = LF(lf_config);
        ' . '' /* $login_js */ . '
        ' . '' /* $this->render_js_delegates() */ . '
        </script>';
    }

    public function to_html( ) {
        assert('$this->article != null /* Article is necessary to get HTML */');
        $site_id = $this->article->get_site()->get_id();
        $article_id = $this->article->get_id();
        $site = $this->article->get_site();
        $domain = $site->get_domain();
        $dhost = $domain->get_host();
        $article_id_b64 = urlencode(base64_encode($article_id));
        $url = "http://bootstrap.$dhost/api/v1.1/public/bootstrap/html/$site_id/$article_id_b64.html";
        $result = $domain->http->request($url, array('method' => 'GET'));
        if (is_array( $result ) && isset($result['response']) && $result['response']['code'] == 200) {
            return $result['body'];
        } else {
            return false;
        }
    }
}

?>
