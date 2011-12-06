<?php
function Livefyre_validate_import_request( $blogID, $key, $correct_signature, $given_signature, $given_signature_time_created ) {
	
	if ( 	
			( $correct_signature != $given_signature )
			|| 
			( abs($given_signature_time_created - time()) > 259200 )
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