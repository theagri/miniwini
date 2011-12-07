<?php namespace Authly;

use Authly\OAuthClient2;
use OAuthUtil;

class Facebook extends OAuthClient2 {

	/**
	 * Authorize URL
	 *
	 * @var string
	 */
	protected $authorize_url = 'https://graph.facebook.com/oauth/authorize?response_type={:response_type}&client_id={:client_id}&redirect_uri={:redirect_uri}&scope={:scope}&display={:display}';
	
	/**
	 * Access token URL
	 *
	 * @var string
	 */
	protected $access_token_url = 'https://graph.facebook.com/oauth/access_token?client_id={:client_id}&redirect_uri={:redirect_uri}&client_secret={:client_secret}&code={:code}';

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
	 * @param   array   $data
	 * @return  string
	 */
	public function __construct($data = NULL)
	{
		$conf = array(
			'response_type'		=> 'code',
			'client_id' 		=> \Config::get('authly.connections.facebook.client_id'),
			'redirect_uri' 		=> \Config::get('authly.connections.facebook.redirect_uri'),
			'scope' 			=> \Config::get('authly.connections.facebook.scope'),
			'display'			=> 'page'
		);
		
		if ( ! is_null($data))
		{
			$conf = array_merge($conf, $data);
		}
		
		parent::__construct($conf);
	}

	// --------------------------------------------------------------------

	/**
	 * Finalize connection and return user credential
	 *
	 * @access  public
	 * @param   string       $data
	 * @return  mixed|FALSE  user credentials on success, FALSE on failure
	 */
	public function connect($data)
	{
		$this->params = array(
			'response_type'		=> 'code',
			'client_id' 		=> \Config::get('authly.connections.facebook.client_id'),
			'client_secret' 	=> \Config::get('authly.connections.facebook.client_secret'),
			'redirect_uri' 		=> \Config::get('authly.connections.facebook.redirect_uri'),
			'scope' 			=> \Config::get('authly.connections.facebook.scope'),
			'display'			=> 'page',
			'code'				=> $data['code']
		);
		
		$res = $this->get_access_token();
		$token = OAuthUtil::parse_parameters($res);
		
		if ( ! empty($token) AND ! empty($token['access_token']))
		{
			$this->set_access_token($token['access_token']);
			$result = $this->send_signed_request('https://graph.facebook.com/me');

			if ( ! empty($result))
			{
				return array(
					'provider' 		=> 'facebook',
					'auth_id' 		=> $result->id,
					'auth_userid' 	=> $result->name,
					'auth_name' 	=> $result->name,
					'auth_token' 	=> $token['access_token'],
					'auth_avatar_url' => 'https://graph.facebook.com/' . $result->id . '/picture'
				);
			}
		}
		return FALSE;
	}
}