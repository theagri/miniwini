
			<?=View::make('board/_header', array(
				'board' => $board
			))->render()?>
			
			<?=View::make('board/_tabs', array(
				'board' => $board
			))->render()?>
			
			<section data-group="board" data-type="post">
				
				<? if ($series->of_user(Authly::id())): ?>

				<div data-group="toolbox">
					[<a href="<?=$series->link($board->alias)?>/manage">수정하기</a>]
				</div>
				
				<? endif; ?>
				
				<h1 data-type="series-title"><?=$series->title?></h1>
				
				<p data-type="series-description">
					
					<?=HTML::safe_text($series->description)?>
					
				</p>

				<? foreach (array_reverse($series->posts) as $p): ?>
				
				<article data-type="post" data-mode="listing">
					<?=$p->user->avatar('medium')?>
		
					<h1>
			
						<a href="<?=$p->link($board->alias)?>"><?=$p->short_title()?></a> 
			
						<?if ($p->comments_count > 0):?>
			
						<a data-type="comments-count" href="<?=$p->link($board->alias)?>"><span><?=number_format($p->comments_count)?></span></a>
			
						<? endif; ?>
			
					</h1>
		
					<p data-type="summary">
		
						<?=$p->summary()?>
		
					</p>
	
					<footer>
						<a data-type="user" href="<?=$p->user->link()?>"><?=$p->user->name?></a>
						/
						<?=Time::humanized_html($p->created_at)?> / 조회 <strong><?=number_format($p->views_count)?></strong>
						
						<? if ($p->last_commenter): ?>
		
						/ 최근 댓글<?=$p->last_commenter->avatar('small')?>
		
						<? endif; ?>

					</footer>
		
				</article>
			
				<? endforeach; ?>
				
				
			</section>