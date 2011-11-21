<?php
return array(

	'before' => function($method, $uri)
	{
		// Do stuff before every request to your application.
	},


	'after' => function($response, $method, $uri)
	{
		if ($response and $response->status === 200)
		{
			$result = array();
			$lines = explode("\n", $response);

			foreach ($lines as $line)
			{
				if ( ! trim($line) OR preg_match('/^\s*$/', $line))
				{
					continue;
				}

				$result[] = $line;
			}

			die (implode("\n", $result));
		}
	},


	'auth' => function()
	{
		return ( ! Auth::check()) ? Redirect::to_login() : null;
	},


	'csrf' => function()
	{
		return (Input::get('csrf_token') !== Form::raw_token()) ? Response::error('500') : null;
	},
	
	
	'signed' => function()
	{
		if ( ! Authly::signed())
		{
			return Redirect::to_login()->with(array(
				'message' => 'login required',
				'back_to' => Request::absolute_uri()
			));
		}
	},

);