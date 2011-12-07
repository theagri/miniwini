<?php namespace Authly;

use Authly\OAuthClient;

class Twitter extends OAuthClient {

	/**
	 * Request Token URL
	 *
	 * @var string
	 */
	protected $request_token_url = 'https://api.twitter.com/oauth/request_token';
	
	/**
	 * Authorize URL
	 *
	 * @var string
	 */
	protected $authorize_url = 'https://twitter.com/oauth/authorize';
	
	/**
	 * Access Token URL
	 *
	 * @var string
	 */
	protected $access_token_url = 'https://api.twitter.com/oauth/access_token';
	
	/**
	 * Authenticate URL
	 *
	 * @var string
	 */
	protected $authenticate_url = 'https://twitter.com/oauth/authenticate';
	
	// ---------------------------------------------------------------------
	
	/**
	 * Create instance
	 *
	 * @param   array   $data
	 * @return  void
	 */
	public function __construct($data = NULL)
	{
		$conf = array(
			'consumer_key' 		=> \Config::get('authly.connections.twitter.consumer_key'),
			'consumer_secret' 	=> \Config::get('authly.connections.twitter.consumer_secret'),
			'oauth_callback' 	=> \Config::get('authly.connections.twitter.oauth_callback')
		);
		
		if ( ! is_null($data))
		{
			$conf = array_merge($conf, $data);
		}
		
		parent::__construct($conf);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns URL for redirection
	 *
	 * @access	public
	 * @return	string
	 */
	public function redirect_url()
	{
		$token = $this->get_request_token();
		

		if ( ! empty($token) AND ! empty($token['oauth_token']) AND ! empty($token['oauth_token_secret']))
		{
			\Session::put('authly_auth_twitter', array(
				'oauth_token' => $token['oauth_token'],
				'oauth_token_secret' => $token['oauth_token_secret']
			));

			return $this->get_authenticate_url($token['oauth_token']);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Finalize sign in and return user credentials
	 *
	 * @access	public
	 * @param	string			$oauth_token
	 * @param	string			$oauth_verifier
	 * @return 	mixed|FALSE		user credentials on success, FALSE on failure
	 */
	public function connect($data)
	{
		$oauth_token = $data['oauth_token'];
		$oauth_verifier = $data['oauth_verifier'];
		
		$token = \Session::get('authly_auth_twitter');

		if (empty($token) OR $token['oauth_token'] !== $oauth_token)
		{
			return FALSE;
		}
		
		$res = $this->get_access_token($oauth_verifier);

		if ( ! empty($res) AND ! empty($res['oauth_token']))
		{
			return array(
				'provider' => 'twitter',
				'auth_id' => $res['user_id'],
				'auth_userid' => $res['screen_name'],
				'auth_name' => $res['screen_name'],
				'auth_token' => $res['oauth_token'],
				'auth_token_secret' => $res['oauth_token_secret'],
				'auth_avatar_url' => 'https://api.twitter.com/1/users/profile_image?screen_name=' . $res['screen_name'] .'&size=bigger'
			);
		}
		
		return FALSE;
	}
}