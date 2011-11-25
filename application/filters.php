<?php
return array(

	'before' => function()
	{
		Authly::initialize();
	},


	'after' => function($response)
	{
		if ($response and $response->status === 200)
		{
			$result = array();
			$lines = explode("\n", $response->content);

			foreach ($lines as $line)
			{
				if ( ! trim($line) OR preg_match('/^\s*$/', $line))
				{
					continue;
				}

				$result[] = $line;
			}
			
			if (Config::$items['session']['driver'] !== '')
			{
				IoC::core('session')->save();
			}
			
			die (implode("\n", $result));
		}
	},

	'csrf' => function()
	{
		if (Request::forged()) return Response::error('500');
	},
	
	
	'signed' => function()
	{
		if ( ! Authly::signed())
		{
			return Redirect::to_login()
				->with('message', 'login required')
				->with('back_to', Request::uri());
		}
	},
);