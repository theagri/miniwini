<?php namespace Authly;


class Factory {
	
	public static function create($service, $data = NULL)
	{
		require_once __DIR__ . '/lib/OAuth.php';
		require_once __DIR__ . '/lib/OAuthClient.php';
		require_once __DIR__ . '/lib/OAuthClient2.php';
		require_once __DIR__ . '/lib/LightOpenID.php';
		require_once __DIR__ . '/providers/' . $service .'.php';

		switch ($service)
		{
			case 'twitter':
				return new Twitter($data);
				
			case 'flickr':
				return new Flickr($data);
				
			case 'facebook':
				return new Facebook($data);
				
			case 'linkedin':
				return new Linkedin($data);
				
			case 'foursquare':
				return new Foursquare($data);
				
			case 'openid':
				return new OpenID($data);
				
			case 'google':
				return new Google($data);
		}
	}
}
