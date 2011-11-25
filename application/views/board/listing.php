			
			
			
			<?=View::make('board/_header', array(
				'board' => $board,
			))->render()?>
			
			
			<?=View::make('board/_tabs', array(
				'board' => $board,
			))->render()?>
			
			
			<? if ($board->locked()): ?>
			
			<div data-group="notification">
				잠겨 있는 게시판입니다.
			</div>
			
			<? else: ?>
			
			<?=View::make('board/_listing', array(
				'board' => $board,
				'posts' => $posts
			))->render()?>
			
			<? endif; ?>