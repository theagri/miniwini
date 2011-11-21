
			<section data-group="board">
	
				<? if ( ! empty($posts->results)): ?>

				<? foreach ($posts->results as $p): ?>

				<article data-type="post" data-mode="listing"<?=(Time::is_today($p->created_at) ? ' class="today"':'')?>>
					<?=$p->user->avatar('medium')?>
		
					<div>
						
						<h1>
				
							<? if ($p->series): ?>
				
							<span data-type="series-title"><a href="<?=$p->series->link($board->alias)?>"><?=$p->series->title?></a></span>
					
							<? endif; ?>
				
							<a href="<?=$p->link($board->alias)?>?page=<?=$posts->page?>"><?=$p->short_title?></a> 
				
							<?if ($p->comments_count > 0):?>
				
							<a data-type="comments-count" href="#"><span><?=number_format($p->comments_count)?></span></a>
				
							<? endif; ?>
				
						</h1>
			
						<p data-type="summary">
			
							<?=$p->summary?>
			
						</p>
		
						<footer>
							<a data-type="user" href="<?=$p->user->link?>"><?=$p->user->name?></a>
				
							<?=Time::humanized_html($p->created_at)?> / 조회 <strong><?=number_format($p->views_count)?></strong>
							<? if ($p->last_commenter): ?>
			
							/ 최근 댓글<?=$p->last_commenter->avatar('small')?>
			
							<? endif; ?>

						</footer>
					</div>
		
				</article>

				<? endforeach; ?>

			<? endif; ?>


			<?=$posts->links()?>
	
			</section>