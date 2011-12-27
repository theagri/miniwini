			
			<?=View::make('board/_header', array(
				'board' => $board
			))->render()?>
			
			<?=View::make('board/_tabs', array(
				'board' => $board,
				'active_tab' => $active_tab
			))->render()?>
			
			<? if ($board->locked()): ?>
			
			<div data-group="notification">
				잠겨 있는 게시판입니다.
			</div>
			
			<? else: ?>
			
			<section data-group="board" data-type="series-list">

				<? foreach ($series_list->results as $s): ?>
				
				<? if (count($s->recent_posts)): ?>
				
				<article>
					
					<?=$s->user->avatar('medium')?>
					
					<header>
						<h1><a href="<?=$s->link($board->alias)?>"><?=$s->title?></a></h1>
						<a data-type="user" href="<?=$s->user->link()?>"><?=$s->user->name?></a>
					</header>
					
					<p data-type="description">
						
						<?=$s->description?>
						
					</p>
					
					<? if (count($s->recent_posts)): ?>
					
					<ol>
						
						<? foreach ($s->recent_posts as $p): ?>
						
						<? if ($p->open()): ?>
					
						<li>
							<span><?=$p->series_sequence?></span> 
							<a href="<?=$p->link($board->alias)?>"><?=$p->title?></a>
							<?=Time::humanized_html($p->created_at)?>
						</li>
						
						<? endif; ?>
						
						<? endforeach; ?>
					
					</ol>
					
					<? endif; ?>
					
				</article>
				
				<? endif; ?>
				
				<? endforeach; ?>
				
				<?=$series_list->links()?>
				
			</section>
			
			<? endif; ?>
			