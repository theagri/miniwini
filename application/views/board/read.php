			
			<?=View::make('board/_header', array(
				'board' => $board
			))->render()?>
			
			<?=View::make('board/_tabs', array(
				'board' => $board
			))->render()?>
			
			<?=View::make('board/_read', array(
				'board' => $board,
				'post' => $post
			))->render()?>
			
			<?=View::make('board/_tabs', array(
				'board' => $board
			))->render()?>
			
			<?=View::make('board/_listing', array(
				'board' => $board,
				'post' => $post,
				'posts' => $posts
			))->render()?>