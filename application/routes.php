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
		if (in_array($segment, array('m', 'roadmap', 'commently', 'ajax', 'board', 'admin', 'dashboard', 'auth', 'home', 'tweets')))
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
	
	'GET /notifications' => array('before' => 'signed', function(){
		$data = json_decode(Notification::histories(Authly::get_id()));

		return View::of_front()->nest('content', 'dashboard/notifications', array(
			'histories' => $data
		));
	}),
	
	// ---------------------------------------------------------------------
	
	'GET /m' => function(){
		return Response::error(404);
		$mobile = Session::get('mobile');
		Session::put('mobile', ! $mobile);
		return Redirect::to_home();
	},
	
	// ---------------------------------------------------------------------
	
	'GET /tweets' => function()
	{
		return Response::error(404);
		require_once LIBRARY_PATH . '/authly/factory.php';
		$data = null;
		$conn = Authly::connection('twitter');
		$data = array(
			'oauth_token_secret' => $conn->auth_token_secret,
			'oauth_token' => $conn->auth_token,
		);
		
		$module = \Authly\Factory::create('twitter', $data);
		$tweets = $module->send_signed_request('https://api.twitter.com/1/lists/statuses.json', 'GET', array(
			'slug' => 'miniwini',
			'owner_screen_name' => 'miniwini_twt',
			'per_page' => 50
			
		));
		
		
		return View::of_front()->nest('content', 'home/tweets', array(
			'tweets' => json_decode($tweets)
		));
	}
);