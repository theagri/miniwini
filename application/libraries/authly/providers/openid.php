<?php namespace mywizz\Authly;

use System\Config;
use LightOpenID;

class OpenID {
	
	protected static $openid;
	
	// --------------------------------------------------------------------

	/**
	 * Sign in
	 *
	 * @access	public
	 * @return	void
	 */
	public static function make($openid_identifier)
	{
		static::$openid = new LightOpenID('http://' . $_SERVER["HTTP_HOST"]);
		static::$openid->identity = $openid_identifier;
		static::$openid->required = Config::get('authly.connections.openid.required_fields');
		static::$openid->optional = Config::get('authly.connections.openid.optional_fields');
		
		return new static();
	}
	
	public function redirect_url()
	{
		return static::$openid->authUrl(); 
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Finalize sign in and return user credentials
	 *
	 * @access	public
	 * @param	mixed			$info
	 * @return 	mixed|FALSE		user credentials on success, FALSE on failure
	 */
	public function finalize_sign_in($info)
	{
		require_once(__DIR__ . '/../third_party/openid.php');
		
		if ($info['openid.mode'] === 'id_res')
		{
			$openid = new SimpleOpenID;
			$openid->SetIdentity($info['openid.identity']);
			$validated = $openid->ValidateWithServer($info);
			if ($validated === TRUE)
			{
				return array(
					'provider' 		=> 'openid',
					'auth_id' 		=> $info['openid.identity'],
					'auth_userid' 	=> $info['openid.identity'],
					'auth_name' 	=> empty($info['openid.sreg.fullname']) ? $info['openid.identity'] : $info['openid.sreg.fullname'],
					'email'			=> empty($info['openid.sreg.email']) ? NULL : $info['openid.sreg.email']
				);
			}
		}
		
		return FALSE;
	}
}