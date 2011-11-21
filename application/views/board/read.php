			
			<?=View::make('board/_header', array(
				'board' => $board
			))->get()?>
			
			<?=View::make('board/_tabs', array(
				'board' => $board
			))->get()?>
			
			<?=View::make('board/_read', array(
				'board' => $board,
				'post' => $post
			))->get()?>
			
			<?=View::make('board/_tabs', array(
				'board' => $board
			))->get()?>
			
			<?=View::make('board/_listing', array(
				'board' => $board,
				'post' => $post,
				'posts' => $posts
			))->get()?>