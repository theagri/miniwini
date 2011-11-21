<?php
return array(
	
	/*
	 * =====================================================================
	 *
	 * 			Listing
	 *
	 * =====================================================================
	 */
	
	// ---------------------------------------------------------------------
	
	'GET /board/(:any)' => array('name' => 'board', function($alias){
		
		if (is_null($board = Board::aliased($alias))) return Response::error(404);

		Title::put($board->title);
		$posts = $board->posts()->with('series', 'user', 'last_commenter')->where('state', '=', 'open')->order_by('id', 'desc')->paginate($board->posts_per_page);
		
		return View::of_front()->partial('content', 'board/listing', array(
			'board' => $board,
			'posts' => $posts,
		));
	}),
	
	// ---------------------------------------------------------------------
	
	'GET /board/(:any)/by/(:any)' => function($alias, $userid){
		if (is_null($board = Board::aliased($alias))) return Response::error(404);
		if (is_null($author = User::where_userid($userid)->first())) return Response::error(404);
		
		Title::put($board->title);
		
		return View::of_front()->partial('content', 'board/listing', array(
			'board' => $board,
			'posts' => $board->posts()->with('user')->where_user_id($author->id)->order_by('id', 'desc')->paginate($board->posts_per_page),
		));
	},
	
	// ---------------------------------------------------------------------
	
	'GET /board/(:any)/drafts' => function($alias){
		if (is_null($board = Board::aliased($alias))) return Response::error(404);
		
		Title::put('임시보관함');
		
		return View::of_front()->partial('content', 'board/listing', array(
			'board' => $board,
			'posts' => $board->posts()->with('user')->where_user_id(Authly::get_id())->where_state('draft')->order_by('id', 'desc')->paginate(100)
		));
	},
	
	/*
	 * =====================================================================
	 *
	 * 			Series
	 *
	 * =====================================================================
	 */
	
	// ---------------------------------------------------------------------
	
	'GET /board/(:any)/series' => function($alias){
		if (is_null($board = Board::aliased($alias))) return Response::error(404);
		

		return View::of_front()->partial('content', 'board/series_listing', array(
			'series_list' => $board->with('user', 'posts')->series()->order_by('id', 'desc')->paginate(20),
			'board' => $board
		));
	},
	
	'GET /board/(:any)/series/(:num)' => function($alias, $id){
		if (is_null($board = Board::aliased($alias))) return Response::error(404);
		if (is_null($series = Series::find($id))) return Response::error(404);
		
		return View::of_front()->partial('content', 'board/series', array(
			'board' => $board,
			'series' => $series
		));
	},
	
	/*
	 * =====================================================================
	 *
	 * 			Read
	 *
	 * =====================================================================
	 */
	
	// ---------------------------------------------------------------------
	
	'GET /board/(:any)/(:num)' => array('name' => 'post', function($alias, $id){
		if (is_null($board = Board::aliased($alias))) return Response::error(404);
		if (is_null($post = Post::find($id))) return Response::error(404);
		if ($post->is_draft() and ! $post->of_user(Authly::get_id())) return Response::error(404);
		
		Title::put($post->title);
		
		$post->up('views_count');
		$posts = $board->posts()->with('series', 'user', 'last_commenter')->where('state', '=', 'open')->order_by('id', 'desc')->paginate($board->posts_per_page);
		
		return View::of_front()->partial('content', 'board/read', array(
			'board' => $board,
			'post' => $post,
			'posts' => $posts
		));
	}),
	
	/*
	 * =====================================================================
	 *
	 * 			Publish
	 *
	 * =====================================================================
	 */
	
	// ---------------------------------------------------------------------
	
	'GET /board/(:any)/(:num)/publish' =>array('before' => 'signed', function($alias, $id){
		if (is_null($board = Board::aliased($alias))) return Response::error(404);
		if (is_null($post = Post::find($id))) return Response::error(404);
		if (! $post->of_user(Authly::get_id())) return Response::error(404);
		
		$post->state = 'open';
		if ($post->save())
		{
			return Redirect::to_post(array($alias, $id))->with('notification', 'Published');
		}
		else
		{
			return Response::error(500);
		}
	}),
	
	// ---------------------------------------------------------------------

	'GET /board/(:any)/(:num)/unpublish' =>array('before' => 'signed', function($alias, $id){
		if (is_null($board = Board::aliased($alias))) return Response::error(404);
		if (is_null($post = Post::find($id))) return Response::error(404);
		if (! $post->of_user(Authly::get_id())) return Response::error(404);

		$post->state = 'draft';
		if ($post->save())
		{
			return Redirect::to_post(array($alias, $id))->with('notification', 'Unpublished');
		}
		else
		{
			return Response::error(500);
		}
	}),
	
	/*
	 * =====================================================================
	 *
	 * 			New
	 *
	 * =====================================================================
	 */
	
	// ---------------------------------------------------------------------
	
	'GET /board/(:any)/new' => array('before' => 'signed', function($alias){
		if (is_null($board = Board::aliased($alias))) return Response::error(404);
		
		Title::put('새 글 쓰기');
		
		return View::of_front()->partial('content', 'board/new', array(
			'board' => $board
		));
	}),
	
	// ---------------------------------------------------------------------
	
	'POST /board/(:any)/new' => array('before' => 'signed, csrf', function($alias){
		if (is_null($board = Board::aliased($alias))) return Response::error(404);
		
		$rules = array(
			'board_id' => 'integer',
			'user_id' => 'integer',
			'title' => 'required',
			'body' => 'required',
			'state' => 'in:open,closed,draft'
		);
		
		$data = array(
			'board_id' => $board->id,
			'user_id' => Authly::get_id(),
			'title' => Input::get('title'),
			'body' => Input::get('body'),
			'state' => Input::get('state')
		);
		
		$val = Validator::make($data, $rules);
		if ( ! $val->valid())
		{
			return Redirect::to('board/' . $alias . '/new');
		}
		
		$post = new Post;
		$post->fill($data);
		if ($post->save())
		{
			// series
			$series_type = Input::get('series');
			$new_series = NULL;

			if ($series_type == 1)
			{
				$series_id = Input::get('series_id');
				$series = Series::find($series_id);
				if ($series AND ($series->user_id == Authly::get_id()))
				{
					$post->series_id = $series->id;
					$post->series_sequence = Series::where_id($series->id)->count() + 1;
					$post->save();
				}	
			}
			elseif ($series_type == 2)
			{
				$new_series = new Series;
				$new_series->fill(array(
					'user_id' => Authly::get_id(),
					'board_id' => $board->id,
					'title' => Input::get('series_title'),
					'description' => Input::get('series_description')
				));

				if ($new_series->save())
				{
					$post->series_id = $new_series->id;
					$post->series_sequence = Series::where_id($new_series->id)->count();
					$post->save();
				}
			}

			return Redirect::to_board(array($alias));
		}
		
		
		return Response::error(500);
	}),

	/*
	 * =====================================================================
	 *
	 * 			Edit
	 *
	 * =====================================================================
	 */
	
	// ---------------------------------------------------------------------
	
	'GET /board/(:any)/(:num)/edit' => array('before' => 'signed', function($alias, $id){
		if (is_null($board = Board::aliased($alias))) return Response::error(404);
		if (is_null($post = Post::find($id))) return Response::error(404);
		if ( ! $post->of_user(Authly::get_id())) return Response::error(500);

		return View::of_front()->partial('content', 'board/edit', array(
			'board' => $board,
			'post' => $post
		));
	}),	
	
	'PUT /board/(:any)/(:num)/edit' => array('before' => 'csrf, signed', function($alias, $id){
		if (is_null($board = Board::aliased($alias))) return Response::error(404);
		if (is_null($post = Post::find($id))) return Response::error(404);
		if ( ! $post->of_user(Authly::get_id())) return Response::error(500);
		
		trace(Input::all());
	}),
	
	/*
	 * =====================================================================
	 *
	 * 			Delete
	 *
	 * =====================================================================
	 */
	
	// ---------------------------------------------------------------------
	
	'GET /board/(:any)/(:num)/delete' => function($alias, $id){
		if (is_null($board = Board::aliased($alias))) return Response::error(404);
		if (is_null($post = Post::find($id))) return Response::error(404);
		if ( ! $post->of_user(Authly::get_id())) return Response::error(500);
		
		if ($post->delete())
		{
			return Redirect::to_board(array($alias))->with('notification', 'Deleted.');
		}
		
		return Redirect::to_board(array($alias));
		
	}
);