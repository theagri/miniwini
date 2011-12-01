
			<section data-group="board">
	
				<? if ( ! empty($posts->results)): ?>
				
				<? foreach ($posts->results as $p): ?>

				<article data-type="post" data-mode="listing"<?=(Time::is_today($p->created_at) ? ' class="today"':'')?>>

					<?=$p->user->avatar('medium')?>
					
					<? if ($p->title): ?>
		
					<h1>
			
						<? if ($p->series): ?>
			
						<span data-type="series-title"><a href="<?=$p->series->link($board->alias)?>"><?=$p->series->title?></a></span>
				
						<? endif; ?>
			
						<a href="<?=$p->link($board->alias)?>?page=<?=$posts->page?>"><?=$p->short_title()?></a> 
			
						<?if ($p->comments_count > 0):?>
			
						<span data-type="comments-count"><?=number_format($p->comments_count)?></span>
			
						<? endif; ?>
			
					</h1>
					
					<p data-type="summary">
		
						<?=$p->summary()?>
		
					</p>
					
					<? else: ?>
					
					<h1>
			
						<? if ($p->series): ?>
			
						<span data-type="series-title"><a href="<?=$p->series->link($board->alias)?>"><?=$p->series->title?></a></span>
				
						<? endif; ?>
			
						<a href="<?=$p->link($board->alias)?>?page=<?=$posts->page?>"><?=$p->summary()?></a> 
			
						<?if ($p->comments_count > 0):?>
			
						<span data-type="comments-count"><?=number_format($p->comments_count)?></span>
			
						<? endif; ?>
			
					</h1>
					
					<? endif; ?>
					

	
					<footer>
						<a data-type="user" href="<?=$p->user->link()?>"><?=$p->user->name?></a>
						/
						<?=Time::humanized_html($p->created_at)?> / 조회 <?=number_format($p->views_count)?>
						
						<? if ($p->last_commenter): ?>
		
						/ 최근 댓글<?=$p->last_commenter->avatar('small')?>
		
						<? endif; ?>

					</footer>
		
				</article>

				<? endforeach; ?>

			<? endif; ?>


			<?=$posts->links()?>
	
			</section>