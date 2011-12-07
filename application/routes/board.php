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
			'active_tab' => 'all',
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
		
		$active_tab = 'my';
		if ($author->id != Authly::get_id())
		{
			$board->author_tab = array(
				'userid' => $author->userid,
				'name' => $author->name,
			);
			$active_tab = 'author';
		}
		
		return View::of_front()->nest('content', 'board/listing', array(
			'board' => $board,
			'active_tab' => $active_tab,
			'posts' => $board->posts()->with('user')->where_user_id($author->id)->order_by('id', 'desc')->paginate($board->posts_per_page),
		));
	},
	
	// ---------------------------------------------------------------------
	
	'GET /board/(:any)/drafts' => function($alias){
		if (is_null($board = Board::aliased($alias))) return Response::error(404);
		
		Title::put('보관함');
		
		return View::of_front()->nest('content', 'board/listing', array(
			'board' => $board,
			'active_tab' => 'draft',
			'posts' => $board->posts()->with('user')->where_user_id(Authly::get_id())->where_in( 'state' , array('draft', 'unpublished'))->order_by('id', 'desc')->paginate(100)
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
			'active_tab' => 'series',
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
			'active_tab' => 'series',
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
			'active_tab' => 'series',
			'series' => $series
		));
	}),
	
	// ---------------------------------------------------------------------
	
	'PUT /board/(:any)/series/(:num)' => array('before' => 'signed|csrf', function($alias, $id){
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
		
		$active_tab = 'my';
		if ($post->user_id != Authly::get_id())
		{
			$board->author_tab = array(
				'userid' => $post->user->userid,
				'name' => $post->user->name,
			);
			
			$active_tab = 'author';
		}
		
		$post->up('views_count');
		
		return View::of_front()->nest('content', 'board/read', array(
			'board' => $board,
			'active_tab' => $active_tab,
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
			
		return View::of_front()->nest('content', 'board/publish', array(
			'board' => $board,
			'post' => $post
		));
	}),
	
	// ---------------------------------------------------------------------
	
	'PUT /board/(:any)/(:num)/publish' =>array('before' => 'signed', function($alias, $id){
		if (is_null($board = Board::aliased($alias)) or
			is_null($post = Post::find($id)) or 
			! $post->of_user(Authly::get_id())
		) 
			return Response::error(404);
		
		
		
		
		if ($post->state == 'draft')
		{
			$attrs = array('board_id', 'series_id', 'series_sequence', 'user_id', 'title', 'body', 'format');
			$new_post = new Post();
			foreach ($attrs as $attr)
			{
				$new_post->{$attr} = $post->{$attr};
			}
			$new_post->state = 'open';
			if ($new_post->save())
			{
				$post->delete();
				return Redirect::to_post(array($alias, $new_post->id))->with('notification', '발행되었습니다!');
			}
		}
		elseif ($post->state == 'unpublished')
		{
			$post->state = 'open';
			
			if ($post->save())
			{
				return Redirect::to_post(array($alias, $id))->with('notification', '다시 발행되었습니다!');
			}
		}

		return Response::error(500);
	}),
	
	// ---------------------------------------------------------------------

	'GET /board/(:any)/(:num)/unpublish' =>array('before' => 'signed', function($alias, $id){
		if (is_null($board = Board::aliased($alias)) or
			is_null($post = Post::find($id)) or 
			! $post->of_user(Authly::get_id())
		)
			return Response::error(404);

		$post->state = 'unpublished';
		
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
		
		return View::of_front()->nest('content', 'board/new', array(
			'edit' => FALSE,
			'board' => $board,
			'active_tab' => 'new'
		));
	}),
	
	// ---------------------------------------------------------------------
	
	'POST /board/(:any)/new' => array('before' => 'signed|csrf', function($alias){
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
		
		Cookie::forever('preferred_format', Input::get('format'));

		return Redirect::to_board(array($alias));
	}),
	
	/*
	 * =====================================================================
	 *
	 * 			Upload
	 *
	 * =====================================================================
	 */
	'GET /board/upload' => array('before' => 'signed', function(){
		return View::of_blank()->nest('content', 'board/upload');
	}),
	
	'POST /board/upload' => array('before' => 'signed|csrf', function(){
		
		require_once LIBRARY_PATH . '/authly/factory.php';
		
		$conn = Authly::connection('flickr');
		
		if ( ! $conn)
		{
			return Response::error(500);
		}

		$data = array(
			'oauth_token_secret' => $conn->auth_token_secret,
			'oauth_token' => $conn->auth_token,
		);
		
		$file = Input::file('photo');
		$module = \Authly\Factory::create('flickr', $data);
		$photo_id = $module->upload('@' . $file['tmp_name']);
		if ($photo_id)
		{
			@unlink($file['tmp_name']);
			
			$sizes = $module->send_signed_request('http://api.flickr.com/services/rest', 'GET', array(
				'method' => 'flickr.photos.getSizes',
				'nojsoncallback' => 1,
				'photo_id' => $photo_id,
				'format' => 'json',
				'api_key' => Config::get('authly.connections.flickr.consumer_key')
			));
			
			$result = json_decode($sizes);
		
			$width_limit = 700;
			$max_width = 0;
			$max_photo = NULL;
			$default_photo = NULL;
			foreach ($result->sizes->size as $s)
			{
				if (is_null($default_photo))
				{
					$default_photo = $s;
				}
				
				$width = $s->width;
				if ($width > $max_width and $width < $width_limit)
				{
					$max_width = $width;
					$max_photo = $s;
				}
			}
			
			if (is_null($max_photo))
			{
				$max_photo = $default_photo;
			}
			
			$url = $max_photo->source;
			$timestamp = time();
			$result = <<<SCRIPT
			<script>top.miniwini.photoUploaded({
				url: '{$url}',
				created_at: {$timestamp}
			});
			location.href = "/board/upload";
			</script>
SCRIPT;
			die($result);
		}
		
		die('<script>top.miniwini.photoUploadFailed();location.href = "/board/upload";</script>');
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

		return View::of_front()->nest('content', 'board/new', array(
			'edit' => TRUE,
			'board' => $board,
			'post' => $post,
		));
	}),	
	
	// ---------------------------------------------------------------------
	
	'PUT /board/(:any)/(:num)/edit' => array('before' => 'signed|csrf', function($alias, $id){
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
			'format' => Input::get('format')
		);

		$post->fill($data);

		if ( ! $post->save())
		{
			return Redirect::to('board/' . $alias . '/' . $id . '/edit');
		}

		return Redirect::to('board/' . $alias . '/' . $id)->with('notification', '글이 수정되었습니다!');
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
	
	'DELETE /board/(:any)/(:num)/delete' => array('before' => 'signed|csrf', function($alias, $id){
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