<?php

include("Token.php");

class Livefyre_User {
    private $uid;
    private $domain;
    private $token;
    
    public function __construct($uid, $domain) {
        $this->uid = $uid;
        $this->domain = $domain;
        $this->token(); //create token for use with push/pull to the API
    }
    
    public function get_uid() { return $this->uid; }
    public function get_domain() { return $this->domain; }
    
    public function jid() {
        return $this->$uid.'@'.$this->domain->get_host();
    }
    
    public function token($age=86400) {
        $domain_key = $this->domain->get_key();
        assert('$domain_key != null /* Domain key is necessary to generate token */');
        $this->token = Livefyre_Token::from_user($this);
    }
    public function get_token() {
        return $this->token;
    }

    public function push( $user_data ) {
        //JSON encode $userdata and create $_POST array
        $user_data_in_json_format = json_encode($user_data);
        $post_data = array('json_object' => $user_data_in_json_format);

        //build remote url to livefyre api
        $token_base64 = base64_encode( $this->token() );
        $domain = $this->get_domain( )->get_host( );
        $remote_url = "http://{$domain}/profiles/?actor_token={$token_base64}&id={$user_data['id']}";
        $remote_url = "http://ssosandbox.livefyre.com/wds/push.php"; //TEST REMOTE URL
        
        //create curl object; issue http request
        $ch=curl_init($remote_url);

        $curl_options = array(
            CURLOPT_POST                => 1,
            CURLOPT_POSTFIELDS          => $post_data,
            CURLOPT_RETURNTRANSFER      => true,
            CURLOPT_HTTPHEADER          => array('Content-Type: application/json' )
        );

        curl_setopt_array( $ch, $curl_options );

        $response = curl_exec( $ch );
        $info = curl_getinfo( $ch );

        //get http code; if 201 with no data request suceeded; 

        if ( $info['http_code'] == 200 ) //change back to 201 in production, 200 for testing *************************
            $success = true;
        else
            $success = false;
            
        curl_close( $ch );
        return $success;
    }
}


?>