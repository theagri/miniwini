<?php namespace mywizz\Authly;

use System\Config;
use System\Session;
use mywizz\Authly\OAuthClient;

class Linkedin extends OAuthClient {
	
	/**
	 * Request Token URL
	 *
	 * @var string
	 */
	protected $request_token_url = 'https://api.linkedin.com/uas/oauth/requestToken';
	
	/**
	 * Authorize URL
	 *
	 * @var string
	 */
	protected $authorize_url = 'https://www.linkedin.com/uas/oauth/authorize';
	
	/**
	 * Access Token URL
	 *
	 * @var string
	 */
	protected $access_token_url = 'https://api.linkedin.com/uas/oauth/accessToken';
	
	/**
	 * pre-defined cURL options
	 *
	 * To get json data with Linkedin, 
	 * we must add "x-li-format: json" to request header
	 * see http://developer.linkedin.com/docs/DOC-1203
	 *
	 * @link	http://www.php.net/manual/en/function.curl-setopt.php
	 * @var		array
	 */
	protected $curl_option = array(
		CURLOPT_USERAGENT		=> '',
		CURLOPT_CONNECTTIMEOUT	=> 30,
		CURLOPT_TIMEOUT			=> 30,
		CURLOPT_RETURNTRANSFER	=> TRUE,
		CURLOPT_HTTPHEADER		=> array('Expect:', 'x-li-format: json'),
		CURLOPT_SSL_VERIFYPEER	=> FALSE,
		CURLOPT_HEADER			=> FALSE
	);
	
	// ---------------------------------------------------------------------
	
	/**
	 * Create instance
	 *
	 * @param  array	$data
	 * @return string 
	 */
	public static function make($data = NULL)
	{
		$conf = array(
			'consumer_key' 		=> Config::get('authly.connections.linkedin.consumer_key'),
			'consumer_secret' 	=> Config::get('authly.connections.linkedin.consumer_secret'),
			'oauth_callback' 	=> Config::get('authly.connections.linkedin.oauth_callback')
		);
		
		if ( ! is_null($data))
		{
			$conf = array_merge($conf, $data);
		}
		
		return new static($conf);
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
			Session::put('linkedin_oauth', array(
				'oauth_token' => $token['oauth_token'],
				'oauth_token_secret' => $token['oauth_token_secret']
			));

			return $this->get_authorize_url($token['oauth_token']);
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
	public function access($data)
	{
		$oauth_token = $data['oauth_token'];
		$oauth_verifier = $data['oauth_verifier'];
		
		$token = Session::get('linkedin_oauth');

		if (empty($token) OR $token['oauth_token'] !== $oauth_token)
		{
			return FALSE;
		}
		
		$token = $this->get_access_token($oauth_verifier);

		if ($token AND $token['oauth_token'])
		{
			$this->set_token($token);
			$result = json_decode($this->send_signed_request('http://api.linkedin.com/v1/people/~:(id,first-name,last-name)'));
			if ( ! empty($result))
			{
				return array(
					'provider' 		=> 'linkedin',
					'auth_id' 		=> $result->id,
					'auth_userid' 	=> $result->id,
					'auth_name' 	=> $result->lastName . ' ' . $result->firstName,
					'auth_token' 	=> $token['oauth_token']
				);
			}
		}
		
		return FALSE;
	}
}