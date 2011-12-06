<?php
function Livefyre_validate_import_request($key, $signature, $blogID) {

	if ( 	
			( getHmacsha1Signature( base64_decode($key), $signature ) != $_POST['sig'] )
			|| 
			( abs($_POST['sig_created'] - time()) > 259200 )
	) {

		return 'sig-failure';

	} else {

        if ($blogID != '')
            return 'sig-validated';
        else
            return 'missing-blog-id';
    }

}
?>