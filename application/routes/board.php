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
		
		return View::of_front()->nest('content', 'board/listing', array(
			'board' => $board,
			'posts' => $board->posts()->with('series', 'user', 'last_commenter')->where('state', '=', 'open')->order_by('id', 'desc')->paginate($board->posts_per_page),
		));
	}),
	
	// ---------------------------------------------------------------------
	
	'GET /board/(:any)/by/(:any)' => function($alias, $userid){
		if (
			is_null($board = Board::aliased($alias)) or 
			is_null($author = User::where_userid($userid)->first())
		) 
			return Response::error(404);
			
		Title::put($board->title);
		
		if ($author->id != Authly::get_id())
		{
			$board->author_tab = array(
				'userid' => $author->userid,
				'name' => $author->name,
			);
		}
		
		return View::of_front()->nest('content', 'board/listing', array(
			'board' => $board,
			'posts' => $board->posts()->with('user')->where_user_id($author->id)->order_by('id', 'desc')->paginate($board->posts_per_page),
		));
	},
	
	// ---------------------------------------------------------------------
	
	'GET /board/(:any)/drafts' => function($alias){
		if (is_null($board = Board::aliased($alias))) return Response::error(404);
		
		Title::put('임시보관함');
		
		return View::of_front()->nest('content', 'board/listing', array(
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
		
		return View::of_front()->nest('content', 'board/series_listing', array(
			'series_list' => $board->with('user', 'posts')->series()->order_by('id', 'desc')->paginate(20),
			'board' => $board
		));
	},
	
	// ---------------------------------------------------------------------
	
	'GET /board/(:any)/series/(:num)' => function($alias, $id){
		if (is_null($board = Board::aliased($alias)) or
			is_null($series = Series::find($id))
		) 
			return Response::error(404);
		
		return View::of_front()->nest('content', 'board/series', array(
			'board' => $board,
			'series' => $series
		));
	},
	
	// ---------------------------------------------------------------------
	
	'GET /board/(:any)/series/(:num)/manage' => array('before' => 'signed', function($alias, $id){
		if (is_null($board = Board::aliased($alias)) or
			is_null($series = Series::find($id)) or 
			! $series->of_user(Authly::id())
		) 
			return Response::error(404);
		
		return View::of_front()->nest('content', 'board/series_manage', array(
			'board' => $board,
			'series' => $series
		));
	}),
	
	// ---------------------------------------------------------------------
	
	'PUT /board/(:any)/series/(:num)' => array('before' => 'signed, csrf', function($alias, $id){
		if (is_null($board = Board::aliased($alias)) or
			is_null($series = Series::find($id)) or 
			! $series->of_user(Authly::id())
		) 
			return Response::error(404);
		
		$val = Validator::make(array('title' => Input::get('title')), array('title' => 'required'));
		if ($val->valid())
		{
			$series->title = Input::get('title');
			$series->description = Input::get('description');
			if ($series->save())
			{
				return Redirect::to('board/' . $alias . '/series/' . $id)->with('notification', 'Updated!');
			}
		}
		
		return Response::error(500);
	}),
	
	/*
	 * =====================================================================
	 *
	 * 			Read
	 *
	 * =====================================================================
	 */
	
	// ---------------------------------------------------------------------
	
	'GET /board/(:any)/(:num)' => array('name' => 'post', function($alias, $id){
		if (is_null($board = Board::aliased($alias)) or
			is_null($post = Post::find($id)) or 
			($post->is_draft() and ! $post->of_user(Authly::get_id()))
		) 
			return Response::error(404);
			
		Title::put($post->title);
		
		if ($post->user_id != Authly::get_id())
		{
			$board->author_tab = array(
				'userid' => $post->user->userid,
				'name' => $post->user->name,
			);
		}
		
		$post->up('views_count');
		
		return View::of_front()->nest('content', 'board/read', array(
			'board' => $board,
			'post' => $post,
			'posts' => $board->posts()->with('series', 'user', 'last_commenter')->where('state', '=', 'open')->order_by('id', 'desc')->paginate($board->posts_per_page)
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
		if (is_null($board = Board::aliased($alias)) or
			is_null($post = Post::find($id)) or 
			! $post->of_user(Authly::get_id())
		) 
			return Response::error(404);
		
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
		if (is_null($board = Board::aliased($alias)) or
			is_null($post = Post::find($id)) or 
			! $post->of_user(Authly::get_id())
		)
			return Response::error(404);

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
	
	'POST /board/(:any)/preview' => array('before' => 'signed', function($alias){
		if (is_null($board = Board::aliased($alias))) return Response::error(404);

		switch (Input::get('format'))
		{
			case 'markdown':
				$markdown = new Markdown();
				$body = $markdown->parse(Input::get('body'));
				break;
				
			default:
				$body = HTML::autolink(nl2br(Input::get('body')));
		}
		
		Title::put('미리보기');
		
		$body = strip_tags($body, Config::get('miniwini.available_tags'));
		
		return View::of_front()->nest('content', 'board/preview', array(
			'body' => $body
		));
	}),
	
	// ---------------------------------------------------------------------
	
	'GET /board/(:any)/new' => array('before' => 'signed', function($alias){
		if (is_null($board = Board::aliased($alias))) return Response::error(404);
		
		Title::put('새 글 쓰기');
		
		return View::of_front()->nest('content', 'board/new', array(
			'board' => $board
		));
	}),
	
	// ---------------------------------------------------------------------
	
	'POST /board/(:any)/new' => array('before' => 'signed, csrf', function($alias){
		if (is_null($board = Board::aliased($alias)) or
			$board->closed() or
			$board->locked()
		) 
			return Response::error(404);

		$data = array(
			'board_id' => $board->id,
			'user_id' => Authly::get_id(),
			'title' => Input::get('title'),
			'body' => Input::get('body'),
			'state' => Input::get('state'),
			'format' => Input::get('format')
		);
		
		$post = new Post;
		$post->fill($data);
		if ( ! $post->save())
		{
			return Redirect::to('board/' . $alias . '/new');
		}

		// series
		$series_type = Input::get('series');
		$new_series = NULL;

		if ($series_type == 1)
		{
			$series_id = Input::get('series_id');
			$series = Series::find($series_id);
			if ($series and ($series->user_id == Authly::get_id()))
			{
				$post->series_id = $series->id;
				$post->series_sequence = Post::where_series_id($series->id)->count() + 1;
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
				$post->series_sequence =  1;
				$post->save();
			}
		}

		return Redirect::to_board(array($alias));
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
		if (is_null($board = Board::aliased($alias)) or
			is_null($post = Post::find($id)) or 
			! $post->of_user(Authly::get_id())
		)
		 	return Response::error(404);

		return View::of_front()->nest('content', 'board/edit', array(
			'board' => $board,
			'post' => $post
		));
	}),	
	
	// ---------------------------------------------------------------------
	
	'PUT /board/(:any)/(:num)/edit' => array('before' => 'signed, csrf', function($alias, $id){
		if (is_null($board = Board::aliased($alias)) or
			is_null($post = Post::find($id)) or 
			! $post->of_user(Authly::get_id())
		)
		 	return Response::error(404);
		
		$data = array(
			'board_id' => $board->id,
			'user_id' => Authly::get_id(),
			'title' => Input::get('title'),
			'body' => Input::get('body'),
			'state' => Input::get('state'),
			'format' => Input::get('format')
		);

		$post->fill($data);

		if ( ! $post->save())
		{
			return Redirect::to('board/' . $alias . '/' . $id . '/edit');
		}

		return Redirect::to('board/' . $alias . '/' . $id)->with('notification', 'OK');
	}),
	
	/*
	 * =====================================================================
	 *
	 * 			Delete
	 *
	 * =====================================================================
	 */
	
	// ---------------------------------------------------------------------
	
	'GET /board/(:any)/(:num)/delete' => array('before' => 'signed', function($alias, $id){
		if (is_null($board = Board::aliased($alias)) or
			is_null($post = Post::find($id)) or 
			! $post->of_user(Authly::get_id())
		)
		 	return Response::error(404);

		return View::of_front()->nest('content', 'board/delete', array(
			'board' => $board,
			'post' => $post
		));
	}),
	
	// ---------------------------------------------------------------------
	
	'DELETE /board/(:any)/(:num)/delete' => array('before' => 'signed, csrf', function($alias, $id){
		if (is_null($board = Board::aliased($alias)) or
			is_null($post = Post::find($id)) or 
			! $post->of_user(Authly::get_id())
		)
		 	return Response::error(404);

		if ($post->delete())
		{
			return Redirect::to_board(array($alias))->with('notification', 'Deleted.');
		}

		return Redirect::to_board(array($alias));
	}),
);