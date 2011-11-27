<?php
return array(
	
	// ---------------------------------------------------------------------

	'GET /' => array('name' => 'home', function(){

		foreach (array('talk', 'share', 'qna') as $alias)
		{
			$board = Board::aliased($alias);
			$posts[$alias] = $board->posts()->with('user')->where_state('open')->order_by('id', 'desc')->take(10)->get();
		}
		
		
		
		return View::of_front()->nest('content', 'home/index', array(
			'posts' => $posts
		));
	}),
	
	// ---------------------------------------------------------------------
	
	'GET /(:any)' => array('name' => 'user', function($segment){
		if (in_array($segment, array('mobile', 'roadmap', 'commently', 'board', 'admin', 'dashboard', 'auth', 'home')))
		{
			if (strpos('/', $segment) === FALSE)
			{
				$segment = $segment . '/index';
			}
			return Redirect::to($segment);
		}
		
		if (is_null($user = User::where_userid($segment)->first())) return Response::error(404);
		
		return View::of_front()->nest('content', 'user/index', array(
			'user' => $user
		));
	}),
	
	// ---------------------------------------------------------------------
	
	'GET /roadmap' => function(){
		return View::of_front()->partial('content', 'home/roadmap');
	},
	
	// ---------------------------------------------------------------------
	
	'GET /mobile' => function(){
		return Response::error(404);
		$mobile = Session::get('mobile');
		Session::put('mobile', ! $mobile);
		return Redirect::to_home();
	}
);