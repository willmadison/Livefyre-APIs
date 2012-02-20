<?php

class Livefyre_http {

    public function __construct() {
 //       echo "Constructor for HTTP\n";
    }

    public function request($url, $args = array()) {
   //     echo "request";
        //return $url;
        $ch=curl_init($url);
        return $ch;
    }

    public function post($url, $args = array()) {
     //   echo "<br>POSTPOSTPOST<br>";
	//FB::log('Log message');
        #$ch = $this->request($url);
        $ch = curl_init($url);
        $post_data = array( 'data' => json_encode( $args ) );
        $curl_options = array(
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS        => $post_data,
            CURLOPT_RETURNTRANSFER    => true,
        );

        curl_setopt_array($ch, $curl_options);
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        //echo "<br>RESPONSE:<br>";
       // echo "$info['total_time']";
        return $response;
    }

    public function get($url, $args = array()) {
        //echo "get\n";

        #$ch = $this->request($url);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = $curl_exec($ch);
        $curl_close($ch);
        return $response;
    }

}


?>
