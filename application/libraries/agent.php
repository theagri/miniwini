<?php
class Agent {
	
	public static $user_agent;
	
	public static $mobiles = array(
						// legacy array, old values commented out
						'mobileexplorer'	=> 'Mobile Explorer',
	//					'openwave'			=> 'Open Wave',
	//					'opera mini'		=> 'Opera Mini',
	//					'operamini'			=> 'Opera Mini',
	//					'elaine'			=> 'Palm',
						'palmsource'		=> 'Palm',
	//					'digital paths'		=> 'Palm',
	//					'avantgo'			=> 'Avantgo',
	//					'xiino'				=> 'Xiino',
						'palmscape'			=> 'Palmscape',
	//					'nokia'				=> 'Nokia',
	//					'ericsson'			=> 'Ericsson',
	//					'blackberry'		=> 'BlackBerry',
	//					'motorola'			=> 'Motorola'

						// Phones and Manufacturers
						'motorola'			=> "Motorola",
						'nokia'				=> "Nokia",
						'palm'				=> "Palm",
						'iphone'			=> "Apple iPhone",
						'ipad'				=> "iPad",
						'ipod'				=> "Apple iPod Touch",
						'sony'				=> "Sony Ericsson",
						'ericsson'			=> "Sony Ericsson",
						'blackberry'		=> "BlackBerry",
						'cocoon'			=> "O2 Cocoon",
						'blazer'			=> "Treo",
						'lg'				=> "LG",
						'amoi'				=> "Amoi",
						'xda'				=> "XDA",
						'mda'				=> "MDA",
						'vario'				=> "Vario",
						'htc'				=> "HTC",
						'samsung'			=> "Samsung",
						'sharp'				=> "Sharp",
						'sie-'				=> "Siemens",
						'alcatel'			=> "Alcatel",
						'benq'				=> "BenQ",
						'ipaq'				=> "HP iPaq",
						'mot-'				=> "Motorola",
						'playstation portable'	=> "PlayStation Portable",
						'hiptop'			=> "Danger Hiptop",
						'nec-'				=> "NEC",
						'panasonic'			=> "Panasonic",
						'philips'			=> "Philips",
						'sagem'				=> "Sagem",
						'sanyo'				=> "Sanyo",
						'spv'				=> "SPV",
						'zte'				=> "ZTE",
						'sendo'				=> "Sendo",

						// Operating Systems
						'symbian'				=> "Symbian",
						'SymbianOS'				=> "SymbianOS",
						'elaine'				=> "Palm",
						'palm'					=> "Palm",
						'series60'				=> "Symbian S60",
						'windows ce'			=> "Windows CE",

						// Browsers
						'obigo'					=> "Obigo",
						'netfront'				=> "Netfront Browser",
						'openwave'				=> "Openwave Browser",
						'mobilexplorer'			=> "Mobile Explorer",
						'operamini'				=> "Opera Mini",
						'opera mini'			=> "Opera Mini",

						// Other
						'digital paths'			=> "Digital Paths",
						'avantgo'				=> "AvantGo",
						'xiino'					=> "Xiino",
						'novarra'				=> "Novarra Transcoder",
						'vodafone'				=> "Vodafone",
						'docomo'				=> "NTT DoCoMo",
						'o2'					=> "O2",

						// Fallback
						'mobile'				=> "Generic Mobile",
						'wireless'				=> "Generic Mobile",
						'j2me'					=> "Generic Mobile",
						'midp'					=> "Generic Mobile",
						'cldc'					=> "Generic Mobile",
						'up.link'				=> "Generic Mobile",
						'up.browser'			=> "Generic Mobile",
						'smartphone'			=> "Generic Mobile",
						'cellphone'				=> "Generic Mobile"
					);
	
	public function __constructor()
	{
		
	}
	
	public static function get_agent()
	{
		if (is_null(static::$user_agent))
		{
			if (isset($_SERVER['HTTP_USER_AGENT']))
			{
				static::$user_agent = trim($_SERVER['HTTP_USER_AGENT']);
			}
		}
		
		return static::$user_agent;
	}
	
	public static function is_mobile()
	{
		$agent = static::get_agent();
		if (is_array(static::$mobiles) AND count(static::$mobiles) > 0)
		{
			foreach (static::$mobiles as $key => $val)
			{
				if (FALSE !== (strpos(strtolower($agent), $key)))
				{
					return TRUE;
				}
			}
		}
		return FALSE;
	}
}