<?php
namespace Livefyre;

include('JWT.php');

define('TOKEN_EXPIRATION', time() + 86400);

class Token {
    static function from_user($user, $token_expiration=TOKEN_EXPIRATION) {
        $key = $user->get_domain()->get_key();
        $token = array('domain'=>$user->get_domain()->get_host(), 'user_id'=>$user->get_uid(), 'expires'=>$token_expiration);
        return JWT.decode($token, $key);
    }
}

?>
