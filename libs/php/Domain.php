<?php

if ( !defined( 'LF_DEFAULT_TLD' ) ) {
    define( 'LF_DEFAULT_TLD', 'livefyre.com' );
}
if ( !defined( 'LF_DEFAULT_PROFILE_DOMAIN' ) ) {
    define( 'LF_DEFAULT_PROFILE_DOMAIN', 'livefyre.com' );
}
define( 'LF_COOKIE_PREFIX', 'livefyre_' );
include("User.php");
include("Site.php");

class Livefyre_Domain {
    private $host;
    private $key;
    
    public function __construct($network, $key=null, $http_api=null) {
        $this->host = $network;
        $this->key = $key;
        if ( defined('LF_DEFAULT_HTTP_LIBRARY') ) {
            $httplib = LF_DEFAULT_HTTP_LIBRARY;
            $this->http = new $httplib;
        } else {
            include_once("Http.php");
            $this->http = new Livefyre_http; 
        }
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
    
    public function token_cookie_name() {
        return LF_COOKIE_PREFIX . 'token_' . $this->get_host();
    }
    
    public function dname_cookie_name() {
        return LF_COOKIE_PREFIX . 'display_name_' . $this->get_host();
    }
    
    public function set_token_cookie( $token, $cookie_path, $cookie_domain, $expire = null, $secure = false ) {
        $this->set_cookie($this->token_cookie_name(), $token, $cookie_path, $cookie_domain, $expire, $secure = false);
    }
    
    public function set_display_name_cookie( $display_name, $cookie_path, $cookie_domain, $expire = null, $secure = false ) {
        if ($expire == null) {
            $expire = time() + 1210000;
        }
        $this->set_cookie($this->dname_cookie_name(), $display_name, $cookie_path, $cookie_domain, $expire, $secure = false);
    }
    
    public function set_cookie( $name, $value, $cookie_path, $cookie_domain, $expire = null, $secure = false ) {
        if ( $expire == null ) {
            $expire = time() + 86400;
        }
        setcookie( $name, $value, $expire, $cookie_path, $cookie_domain, $secure, false );
    }
    
    public function clear_cookies( $cookie_path, $cookie_domain ) {
        setcookie( $this->dname_cookie_name(), ' ', time() - 31536000, $cookie_path, $cookie_domain );
        setcookie( $this->token_cookie_name(), ' ', time() - 31536000, $cookie_path, $cookie_domain );
    }
    
    public function source_js_v1() {
        return '<script type="text/javascript" src="http://zor.' . LF_DEFAULT_TLD . '/wjs/v1.0/javascripts/livefyre_init.js"></script>';
    }
    
    public function authenticate_js( $token_url = '', $cookie_path = '/', $token_cookie = null, $dname_cookie = null  ) {
        
        /*
            This script should be rendered when it appears the user is logged in
            Now we attempt to fetch Livefyre credentials from a cookie,
            falling back to ajax as needed.
        */
        $token_cookie = $token_cookie ? $token_cookie : $this->token_cookie_name();
        $dname_cookie = $dname_cookie ? $dname_cookie : $this->dname_cookie_name();
        ?>
            <script type="text/javascript">
                LF.ready(function(){
                    var lfTokenCookie = '<?php echo $token_cookie; ?>';
                    var lfDnameCookie = '<?php echo $dname_cookie; ?>';
                    if (!$jl.cookie(lfTokenCookie)) {
                        <?php
                        if ( !empty($token_url) ) {
                            ?>
                            // fetch via ajax
                            $jl.ajax({
                                url: '<?php echo $token_url; ?>',
                                type: 'json',
                                success: function(json){
                                    LF.login(json);
                                    $jl.cookie(lfTokenCookie, json.token, {expires:1, path:'<?php echo $cookie_path ?>'});
                                    $jl.cookie(lfDnameCookie, json.profile.display_name, {expires:1, path:'<?php echo $cookie_path ?>'});
                                },
                                error: function(a, b){
                                    console.log("There was some problem fetching a livefyre token. ", a, b);
                                }
                            });
                            <?php
                        }
                        ?>
                    } else {
                        try {
                            LF.login({
                                token: $jl.cookie(lfTokenCookie),
                                profile: {display_name: $jl.cookie(lfDnameCookie).replace(/\+/g, ' ')},
                            });
                        } catch (e) {
                            console.log("Error attempting to login with ", lfTokenCookie, " cookie value: ", $jl.cookie(lfTokenCookie), " ", e);
                        }
                    }
                });
            </script>
        <?php
    
    }

    public function site($site_id, $key = null) {
        return new Livefyre_Site($site_id, $key, $this);
    }

    public function validate_server_token($token) {
        return lftokenValidateServerToken($token, $this->key);
    }
}

?>
