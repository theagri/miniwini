<?php namespace Authly;

use Authly\OAuthClient;

class Flickr extends OAuthClient {

	/**
	 * Request Token URL
	 *
	 * @var string
	 */
	protected $request_token_url = 'http://www.flickr.com/services/oauth/request_token';
	
	/**
	 * Authorize URL
	 *
	 * @var string
	 */
	protected $authorize_url = 'http://www.flickr.com/services/oauth/authorize';
	
	/**
	 * Access Token URL
	 *
	 * @var string
	 */
	protected $access_token_url = 'http://www.flickr.com/services/oauth/access_token';
	
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
			'consumer_key' 		=> \Config::get('authly.connections.flickr.consumer_key'),
			'consumer_secret' 	=> \Config::get('authly.connections.flickr.consumer_secret'),
			'oauth_callback' 	=> \Config::get('authly.connections.flickr.oauth_callback')
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
			\Session::put('authly_auth_flickr', array(
				'oauth_token' => $token['oauth_token'],
				'oauth_token_secret' => $token['oauth_token_secret']
			));

			return $this->get_authorize_url($token['oauth_token'], array('perms' => 'write'));
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
		
		$token = \Session::get('authly_auth_flickr');

		if (empty($token) OR $token['oauth_token'] !== $oauth_token)
		{
			return FALSE;
		}
		
		$res = $this->get_access_token($oauth_verifier);


		if ( ! empty($res) AND ! empty($res['oauth_token']))
		{
			return array(
				'provider' => 'flickr',
				'auth_id' => $res['user_nsid'],
				'auth_userid' => $res['user_nsid'],
				'auth_name' => $res['username'],
				'auth_token' => $res['oauth_token'],
				'auth_token_secret' => $res['oauth_token_secret'],
			);
		}
		
		return FALSE;
	}
	
	private function generate_api_sig($params)
	{
		ksort($params);
		$sig = $this->consumer_secret;

		foreach ($params as $k => $v)
		{
			$sig .= $k.$v;
		}
		//trace($sig);
		//ba29bb85a2199c5eapi_key7d85911611721560b3037f95eb6809d6auth_token72157628292590019-4f4568651a1b70c9"

		return md5($sig);
	}
	
	public function upload($file, $params = array())
	{
		$sig = $this->generate_api_sig($params);
		
		//http://farm8.staticflickr.com/7008/6464771213_d0ce4d6e2d_m.jpg
		
		//723f2d4c7730f15c34a33fb9bc968d43
		
		//723f2d4c7730f15c34a33fb9bc968d43
		
		$result = $this->send_signed_request_with_file('http://api.flickr.com/services/upload', 'POST', $params, array('photo' => $file));
		if (preg_match('/<rsp stat="ok">/', $result) and preg_match('/<photoid>(.+)<\/photoid>/', $result, $match))
		{
			return $match[1];
		}
		
		return FALSE;
	}
}