<?php

class Livefyre_Conversation {
	
	private $id;
	private $article;
	
	public function __construct( $conv_id = null, $article = null ) {
		
		$this->id = $conv_id;
		$this->article = $article;
		
	}

	public function to_initjs( $user, $login_url = null, $logout_url = null ) {
		
		$domainname = $this->article->get_site()->get_domain()->get_host();
		return '<script type="text/javascript" src="http://zor.' . $domainname . '/wjs/v1.0/javascripts/livefyre_init.js"></script>
		<script type="text/javascript">
			var conv = LF({
				domain: \'' .  $domainname . '\',
				site_id: \'' .  $this->article->get_site()->get_id() . '\', 
				article_id: \'' .  $this->article->get_id() . '\'
			});
			var customLivefyreDelegates = {
				handle_auth_login: function(data) {
					document.location.href = \'' . $login_url . '\';
				},
				handle_auth_logout: function(data) {
					document.location.href = \'' . $logout_url . '\';
				}
			};
			LF.ready( function() {
					LF.Dispatcher.addListener(customLivefyreDelegates);
					LF.login(' . $user->auth_json( ) . ');
			});
		</script>';
		
	}

	public function to_html( ) {
		
		assert( '$this->article != null /* Article is necessary to get HTML */' );
		$site_id = $this->article->get_site()->get_id();
		$article_id = $this->article->get_id();
		$domain = $this->article->get_site()->get_domain()->get_host();
		return file_get_contents( "http://bootstrap.$domain/api/v1.1/public/bootstrap/html/$site_id/" . urlencode( base64_encode( $article_id ) ) . ".html" );
		
	}
}

?>
