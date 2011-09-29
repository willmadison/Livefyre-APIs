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
    
    public function pull( $user_data ) {
        extract( $user_data );

        $token_base64 = base64( $this->token() );
        $domain = $this->get_domain( );
        $pull_profile_url = "http://{$domain}/users/get_remote_profile?id={$user_id}";
        $pull_profile_url_urlencoded = urlencode( $pull_profile_url );
        $remote_url = "http://{$domain}/?actor_token={$token_base64}&pull_profile_url={$pull_profile_url_urlencoded}";

        $ch=curl_init($remote_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public function push( $user_data ) {
        $user_data_in_json_format = json_encode($user_data);
        $token_base64 = base64( $this->token() );
        $domain = $this->get_domain( );
        $remote_url = "http://{$domain}/profiles/?actor_token={$token_base64}&id={$user_data['user_id']}";

        $ch=curl_init($remote_url);
        
        $curl_options = array(
            'CURLOPT_POST'              => true,
            'CURLOPT_POSTFIELDS'        => $user_data_in_json_format,
            'CURLOPT_RETURNTRANSFER'    => true,
            'CURLOPT_HTTPHEADER'        => array('Content-Type: application/json')
        );

        curl_setopt_array( $ch, $curl_options );
        $response = curl_exec( $ch );
        curl_close( $ch );

        return $response;
    }
}

?>