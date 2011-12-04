<?php
return array(
	
	// ---------------------------------------------------------------------

	'GET /' => array('name' => 'home', function(){
		
		$boards = array(
			'talk' => '자유게시판', 
			'share' => '알짜게시판',
			'qna' => '질문&amp;답변'
		);
		
		foreach ($boards as $alias => $title)
		{
			$board = Board::aliased($alias);
			$posts[$alias] = $board->posts()->with('user')->where_state('open')->order_by('id', 'desc')->take(10)->get();
		}
		
		return View::of_front()->nest('content', 'home/index', array(
			'posts' => $posts,
			'boards' => $boards
		));
	}),
	
	// ---------------------------------------------------------------------
	
	'GET /(:any)' => array('name' => 'user', function($segment){
		if (in_array($segment, array('m', 'roadmap', 'commently', 'ajax', 'board', 'admin', 'dashboard', 'auth', 'home')))
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
		return View::of_front()->nest('content', 'home/roadmap');
	},
	
	// ---------------------------------------------------------------------
	
	'GET /m' => function(){
		return Response::error(404);
		$mobile = Session::get('mobile');
		Session::put('mobile', ! $mobile);
		return Redirect::to_home();
	}
);