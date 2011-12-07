<?php namespace Authly;

use OAuthSignatureMethod_HMAC_SHA1;
use OAuthConsumer;
use OAuthRequest;
use OAuthUtil;

class OAuthClient {
	
	/**
	 * Request Token URL
	 *
	 * @var	string
	 */
	protected $request_token_url;
	
	/**
	 * Authorize URL
	 *
	 * @var	string
	 */
	protected $authorize_url;
	
	/**
	 * Access Token URL
	 *
	 * @var	string
	 */
	protected $access_token_url;
	
	/**
	 * Consumer Key
	 *
	 * @var	string
	 */
	protected $consumer_key;
	
	/**
	 * Consumer Secret
	 *
	 * @var	string
	 */
	protected $consumer_secret;
	
	/**
	 * OAuth Token
	 *
	 * @var	string
	 */
	protected $oauth_token;
	
	/**
	 * OAuth Token Secret
	 *
	 * @var	string
	 */
	protected $oauth_token_secret;
	
	/**
	 * OAuth Callback URL
	 *
	 * @var	string
	 */
	protected $oauth_callback;
	
	/**
	 * Consumer
	 *
	 * @var	OAuthConsumer
	 */
	protected $consumer;
	
	/**
	 * Verified Consumer
	 *
	 * @var	OAuthConsumer
	 */
	protected $verified_consumer;
	
	/**
	 * Signature method
	 *
	 * @var OAuthSignatureMethod
	 */
	protected $signature_method;
	
	/**
	 * Response format
	 *
	 * @var string
	 */
	protected $response_format = 'json';
	
	/**
	 * pre-defined cURL options
	 *
	 * @link	http://www.php.net/manual/en/function.curl-setopt.php
	 * @var		array
	 */
	protected $curl_option = array(
		CURLOPT_USERAGENT		=> '',
		CURLOPT_CONNECTTIMEOUT	=> 30,
		CURLOPT_TIMEOUT			=> 30,
		CURLOPT_RETURNTRANSFER	=> TRUE,
		CURLOPT_HTTPHEADER		=> array('Expect:'),
		CURLOPT_SSL_VERIFYPEER	=> FALSE,
		CURLOPT_HEADER			=> FALSE
	);

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	array	$params
	 * @return	void
	 */
	public function __construct($params = NULL)
	{
		if (empty($params['consumer_key']) OR empty($params['consumer_secret']))
		{
			throw new OAuthClientException('consumer_key/consumer_secret missing');
		}
		
		$this->signature_method = new OAuthSignatureMethod_HMAC_SHA1();
		$this->consumer_key = $params['consumer_key'];
		$this->consumer_secret = $params['consumer_secret'];
		
		if ( ! empty($params['oauth_token']))
		{
			$this->oauth_token = $params['oauth_token'];
		}
		
		if ( ! empty($params['oauth_token_secret']))
		{
			$this->oauth_token_secret = $params['oauth_token_secret'];
		}
		
		if ( ! empty($params['oauth_callback']))
		{
			$this->oauth_callback = $params['oauth_callback'];
		}
		
		// create basic consumer
		$this->consumer = new OAuthConsumer($this->consumer_key, $this->consumer_secret);
		
		// when access token given, create verified consumer
		if ( ! empty($this->oauth_token) AND ! empty($this->oauth_token_secret))
		{
			$this->verified_consumer = new OAuthConsumer($this->oauth_token, $this->oauth_token_secret);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Return Authorize URL
	 *
	 * @access	public
	 * @param	string	$token
	 * @return	string
	 */
	public function get_authorize_url($token, $params = array())
	{
		$params = array_merge(array('oauth_token' => $token), $params);
		return $this->authorize_url . '?' . http_build_query($params);
	}
	
	// ---------------------------------------------------------------------
	
	public function get_authenticate_url($token)
	{
		return $this->authenticate_url . '?' . http_build_query(array('oauth_token' => $token));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Return Request Token and Secret
	 *
	 * @access	private
	 * @param	string	$oauth_callback
	 * @return	array	array(
	 *						'oauth_token' => 'ad12raXa',
	 *						'oauth_token_secret' => '2fzw1ds'
	 *						)
	 */
	public function get_request_token($oauth_callback = NULL)
	{
		$params = array();
		if ( ! empty($oauth_callback))
		{
			$params['oauth_callback'] = $oauth_callback;
		}
		else if ( ! empty($this->oauth_callback))
		{
			$params['oauth_callback'] = $this->oauth_callback;
		}

	    $res = $this->send_signed_request($this->request_token_url, 'GET', $params);
	    $result = OAuthUtil::parse_parameters($res);

		if ( empty($result['oauth_token']) OR empty($result['oauth_token_secret']))
		{
			throw new OAuthClientException('failed to get request token: ' . $res);
		}
	    $this->verified_consumer = new OAuthConsumer($result['oauth_token'], $result['oauth_token_secret']);
	    return $result;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns Access Token
	 *
	 * @access	private
	 * @param	string			$oauth_verifier
	 * @return	array|FALSE		array(
	 *								'oauth_token' => '1dasZsd',
	 *								'oauth_token_secret' => 'cxe3dAs'
	 *							)
	 */
	public function get_access_token($oauth_verifier = NULL)
	{
		if (empty($oauth_verifier))
		{
			return FALSE;
		}
		
	    $req = $this->send_signed_request($this->access_token_url, 'GET', array('oauth_verifier' => $oauth_verifier));

	    $result = OAuthUtil::parse_parameters($req);
	
		if ( ! empty($result) AND ! empty($result['oauth_token']))
		{
			$this->verified_consumer = new OAuthConsumer($result['oauth_token'], $result['oauth_token_secret']);
		    return $result;
		}
		
		return FALSE;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Set OAuth token and secret
	 *
	 * @access	public
	 * @param	array	$token
	 * @return	void
	 */
	public function set_token($token)
	{
		$this->verified_consumer = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Set response format
	 *
	 * @access	public
	 * @param	string	$format
	 * @return	void
	 */
	public function set_response_format($format)
	{
		$this->response_format = $format;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Send signed request
	 *
	 * @access	public
	 * @param	string	$url
	 * @param	string	$method
	 * @param	array	$params
	 * @return	string	http response
	 */
	public function send_signed_request($url, $method = 'GET', $params = array())
	{
		$req = OAuthRequest::from_consumer_and_token($this->consumer, $this->verified_consumer, $method, $url, $params);
	    $req->sign_request($this->signature_method, $this->consumer, $this->verified_consumer);

		switch (strtoupper($method))
		{
			case 'GET':
				return $this->request($req->to_url(), $method, $params);
			
			default:
				return $this->request($req->get_normalized_http_url(), $method, $req->to_postdata());
		}
	}
	
	public function send_signed_request_with_file($url, $method = 'GET', $params = array(), $filedata = array())
	{
		$req = OAuthRequest::from_consumer_and_token($this->consumer, $this->verified_consumer, $method, $url, $params);
	    $req->sign_request($this->signature_method, $this->consumer, $this->verified_consumer);

		switch (strtoupper($method))
		{
			case 'GET':
				return $this->request($req->to_url(), $method, $params);
			
			default:
				$postdata = $req->to_postdata();
				$postdata = $req->get_parameters();
				if ($postdata)
				{
					$postdata = array_merge($postdata, $filedata);
				}
				else
				{
					$postdata = $filedata;
				}
				
				return $this->request($req->get_normalized_http_url(), $method, $postdata);
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Send request using cURL
	 *
	 * @access	private
	 * @param	string	$url
	 * @param	string	$method
	 * @param	array	$params
	 * @return	string	http response
	 */
	public function request($url, $method = 'GET', $params = array())
	{
	    $curl = curl_init();
	
		curl_setopt_array($curl, $this->curl_option);

	    switch ($method) 
		{
			case 'POST':
				curl_setopt($curl, CURLOPT_POST, TRUE);
				if ( ! empty($params)) 
				{
					curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
				}
				break;
				
			case 'DELETE':
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
				if ( ! empty($params)) 
				{
					if (strpos('?', $url) === FALSE)
					{
						$url = $url . '?' . $params;
					}
					else
					{
						$url = $url . '&' . $params;
					}
				}
				break;
		}

	    curl_setopt($curl, CURLOPT_URL, $url);
	    $res = curl_exec($curl);
	    curl_close($curl);
	    return $res;
	}
}

class OAuthClientException extends \Exception {}