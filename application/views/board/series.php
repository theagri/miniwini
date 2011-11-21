
			<?=View::make('board/_header', array(
				'board' => $board
			))->get()?>
			
			<?=View::make('board/_tabs', array(
				'board' => $board
			))->get()?>