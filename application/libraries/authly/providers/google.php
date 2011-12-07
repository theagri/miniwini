<?php namespace mywizz\Authly;

use System\Config;

class Google {
	// ---------------------------------------------------------------------
	
	public static function make()
	{
		return new static();
	}
	// --------------------------------------------------------------------

	/**
	 * Sign in
	 *
	 * @access	public
	 * @return	void
	 */
	public function redirect_url()
	{
		$params = array(
		  'openid.ns'                	=> 'http://specs.openid.net/auth/2.0',
		  'openid.claimed_id'        	=> 'http://specs.openid.net/auth/2.0/identifier_select',
		  'openid.identity'          	=> 'http://specs.openid.net/auth/2.0/identifier_select',
		  'openid.return_to'         	=> Config::get('authly.connections.google.return_to'),
		  'openid.mode'              	=> "checkid_setup",
		  'openid.ns.ui'             	=> 'http://specs.openid.net/extensions/ui/1.0',
		  'openid.ns.ax'				=> 'http://openid.net/srv/ax/1.0',
		  'openid.ax.mode'         		=> 'fetch_request',
		  'openid.ax.type.email'   		=> 'http://axschema.org/contact/email',
		  'openid.ax.type.firstname'   	=> 'http://axschema.org/namePerson/first',
		  'openid.ax.type.lastname'    	=> 'http://axschema.org/namePerson/last',
		  'openid.ax.required'     		=> 'email,firstname,lastname'
		);
		
		return 'https://www.google.com/accounts/o8/ud?'.http_build_query($params);
	}
	

	
	// --------------------------------------------------------------------

	/**
	 * Finalize sign in and return user credential
	 *
	 * @access	public
	 * @param	string			$code
	 * @return	mixed|FALSE		user credentials on success, FALSE on failure
	 */
	public function access($data)
	{
		return array(
			'provider' 		=> 'google',
			'auth_id' 		=> $data['userid'],
			'auth_userid' 	=> $data['userid'],
			'auth_name' 	=> $data['last'] . ' ' . $data['first']
		);
	}
} // END Authly_Google

/* End of file Authly_Google.php */
/* Location: ./mywizz/Authly/libraries/Authly_Google.php */