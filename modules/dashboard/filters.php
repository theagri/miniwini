<?php
return array(
	'before' => function($method, $uri)
	{
		if ( ! Authly::signed())
		{
			return Redirect::to_login();
		}
	}
);