			
			<? if (Notification::exists()): ?>
			
			<div data-group="notification">
				<?=Notification::get()?>
			</div>
			
			<? endif; ?>
			
			<?=View::make('board/_header', array(
				'board' => $board,
			))->get()?>
			
			
			<?=View::make('board/_tabs', array(
				'board' => $board,
			))->get()?>
			
			
			<?=View::make('board/_listing', array(
				'board' => $board,
				'posts' => $posts
			))->get()?>