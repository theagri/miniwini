<?php namespace mywizz\Authly;

use System\Config;
use mywizz\Authly\OAuthClient2;
use OAuthUtil;

class Foursquare extends OAuthClient2 {
	
	/**
	 * Access token alias
	 *
	 * @var string
	 */
	protected $access_token_alias = 'oauth_token';
	
	/**
	 * Authorize URL
	 *
	 * @var string
	 */
	protected $authorize_url = 'https://foursquare.com/oauth2/authenticate?client_id={:client_id}&response_type=code&redirect_uri={:redirect_uri}';
	
	/**
	 * Access token URL
	 *
	 * @var string
	 */
	protected $access_token_url = 'https://foursquare.com/oauth2/access_token?client_id={:client_id}&client_secret={:client_secret}&grant_type=authorization_code&redirect_uri={:redirect_uri}&code={:code}';

	/**
	 * Response format
	 *
	 * @var string
	 */
	protected $response_format = 'json';
	
	// --------------------------------------------------------------------
	
	/**
	 * Create instance
	 *
	 * @param  array   $data
	 * @return string
	 */
	public static function __construct($data = NULL)
	{
		$conf = array(
			'client_id'		=> Config::get('authly.connections.foursquare.client_id'),
			'redirect_uri'	=> Config::get('authly.connections.foursquare.redirect_uri')
		);
		
		if ( ! is_null($data))
		{
			$conf = array_merge($conf, $data);
		}
		
		parent::__construct($conf);
	}

	// --------------------------------------------------------------------

	/**
	 * Finalize sign in and return user credential
	 *
	 * @access	public
	 * @param	string			$code
	 * @return	mixed|FALSE		user credentials on success, FALSE on failure
	 */
	public function connect($data)
	{
		$this->params = array(
			'client_id' 		=> Config::get('authly.connections.foursquare.client_id'),
			'client_secret' 	=> Config::get('authly.connections.foursquare.client_secret'),
			'redirect_uri' 		=> Config::get('authly.connections.foursquare.redirect_uri'),
			'code'				=> $data['code']
		);
		
		
		$token = json_decode($this->get_access_token());
		
		if ( ! empty($token) AND ! empty($token->access_token))
		{
			$this->set_access_token($token->access_token);
			$result = $this->send_signed_request('https://api.foursquare.com/v2/users/self');

			if ( ! empty($result))
			{
				return array(
					'provider' 		=> 'foursquare',
					'auth_id' 		=> $result->response->user->id,
					'auth_userid' 	=> $result->response->user->lastName . '_' . $result->response->user->firstName,
					'auth_name' 	=> $result->response->user->lastName . ' ' . $result->response->user->firstName,
					'auth_token' 	=> $token->access_token
				);
			}
		}
		return FALSE;
	}
}