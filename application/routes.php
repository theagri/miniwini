<?php
return array(
	
	// ---------------------------------------------------------------------

	'GET /' => array('name' => 'home', function(){

		foreach (array('talk', 'share', 'qna') as $alias)
		{
			$board = Board::aliased($alias);
			$posts[$alias] = $board->posts()->with('user')->order_by('id', 'desc')->take(10)->get();
		}
		
		return View::of_front()->partial('content', 'home/index', array(
			'posts' => $posts
		));
	}),
	
	// ---------------------------------------------------------------------
	
	'GET /(:any)' => array('name' => 'user', function($segment){

		if (in_array($segment, array('mobile', 'roadmap', 'commently', 'board', 'admin', 'dashboard', 'auth', 'home')))
		{
			return Redirect::to($segment);
		}
		
		if (is_null($user = User::where_userid($segment)->first())) return Response::error(404);
		
		return View::of_front()->partial('content', 'user/index', array(
			'user' => $user
		));
	}),
	
	// ---------------------------------------------------------------------
	
	'GET /roadmap' => function(){
		return View::of_front()->partial('content', 'home/roadmap');
	},
	
	// ---------------------------------------------------------------------
	
	'GET /mobile' => function(){
		$mobile = Session::get('mobile');
		Session::put('mobile', ! $mobile);
		return Redirect::to_home();
	}
	
);