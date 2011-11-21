			
			<?=View::make('board/_header', array(
				'board' => $board
			))->get()?>
			
			<?=View::make('board/_tabs', array(
				'board' => $board
			))->get()?>
			
			<section data-group="board" data-type="series-list">

				<? foreach ($series_list->results as $s): ?>
				
				<article>
					
					<?=$s->user->avatar('medium')?>
					
					<header>
						<h1><a href="<?=$s->link($board->alias)?>"><?=$s->title?></a></h1>
						<a data-type="user" href="<?=$s->user->link?>"><?=$s->user->name?></a>
					</header>
					
					<? if (count($s->posts)): ?>
					
					<ol>
						
						<? foreach ($s->posts as $p): ?>
					
						<li>
							<span><?=$p->series_sequence?></span> 
							<a href="<?=$p->link($board->alias)?>"><?=$p->title?></a>
							<?=Time::humanized_html($p->created_at)?>
						</li>
					
						<? endforeach; ?>
					
					</ol>
					
					<? endif; ?>
					
				</article>
				
				<? endforeach; ?>
				
				<?=$series_list->links()?>
				
			</section>