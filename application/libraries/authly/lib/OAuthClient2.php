<?php namespace Authly;
class OAuthClient2 {

	/**
	 * Access token alias
	 * 
	 * Some provider (like Foursquare) uses different parameter name for access token
	 * other than "access_token"
	 *
	 * @var string
	 */
	protected $access_token_alias = 'access_token';
	
	/**
	 * Authorize URL
	 *
	 * @var string
	 */
	protected $authorize_url = NULL;
	
	/**
	 * Access token URL
	 *
	 * @var string
	 */
	protected $access_token_url = NULL;

	/**
	 * Response format
	 *
	 * @var string
	 */
	protected $response_format = NULL;
	
	/**
	 * Parameters
	 *
	 * @var array
	 */
	protected $params = array();
	
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
	 * @return	void
	 */
	public function __construct($params = NULL)
	{
		if ( ! is_null($params))
		{
			$this->params = $params;
		}
	}
	
	
	// --------------------------------------------------------------------

	/**
	 * Authorize
	 *
	 * @access	public
	 * @return	void
	 */
	public function authorize()
	{
		$url = $this->authorize_url;
		if ( ! empty($this->params))
		{
			foreach ($this->params as $k => $v)
			{
				$url = preg_replace('/\{:'.$k.'\}/', urlencode($v), $url);
			}
		}
		
		header('Location: ' . $url);
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
		$url = $this->authorize_url;
		if ( ! empty($this->params))
		{
			foreach ($this->params as $k => $v)
			{
				$url = preg_replace('/\{:'.$k.'\}/', urlencode($v), $url);
			}
		}
		
		return $url;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Get access token
	 *
	 * @access	public
	 * @return	array
	 */
	public function get_access_token()
	{
		$url = $this->access_token_url;
		if ( ! empty($this->params))
		{
			foreach ($this->params as $k => $v)
			{
				$url = preg_replace('/\{:'.$k.'\}/', urlencode($v), $url);
			}
		}
		
		$curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt_array($curl, $this->curl_option);
	    $res = curl_exec($curl);
	    curl_close ($curl);
	
		return $res;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Set access token
	 *
	 * @access	public
	 * @param	string	$access_token
	 * @return	void
	 */
	public function set_access_token($access_token)
	{
		$this->params['access_token'] = $access_token;
	}
	
	
	
	// --------------------------------------------------------------------

	/**
	 * API call
	 *
	 * @access	public
	 * @param	string	$url
	 * @param	string	$method
	 * @param	array	$params
	 * @return	string	HTTP response
	 */
	public function send_signed_request($url, $method = 'GET', $params = array())
	{
		$url = preg_replace('/^\/+/', '', $url);
		
		if (strpos($url, '?') === FALSE)
		{
			$url = $url . '?' . $this->access_token_alias . '=' . $this->params['access_token'];
		}
		else
		{
			$url = $url . '&' . $this->access_token_alias . '=' . $this->params['access_token'];
		}
		
		
		$curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt_array($curl, $this->curl_option);
	    $res = curl_exec($curl);
	    curl_close ($curl);
		
		if ($this->response_format === 'json' AND function_exists('json_decode'))
		{
			return json_decode($res);
		}
		
		return $res;
	}
}