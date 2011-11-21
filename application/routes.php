<?php

return array(

	'GET /' => array('name' => 'home', function(){
		return View::of_front()->partial('content', 'home/index');
	}),
	
	
	'GET /(:any)' => array('name' => 'user', function($segment){

		if (in_array($segment, array('roadmap', 'commently', 'board', 'admin', 'dashboard', 'auth', 'home')))
		{
			return Redirect::to($segment);
		}
		
		if (is_null($user = User::where_userid($segment)->first())) return Response::error(404);
		
		return View::of_front()->partial('content', 'user/index', array(
			'user' => $user
		));
	}),
	
	'GET /roadmap' => function()
	{
		return View::of_front()->partial('content', 'home/roadmap');
	},
	
);