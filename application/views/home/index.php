			
			<section>
				<div data-group="notification">
					미니위니 시즌2 개발 중입니다. 환영합니다 :)
				</div>
			
			
				<div data-group="notification">
					구글 크롬, 파이어폭스, 오페라 등의 최신 브라우져를 이용해 주시기 바랍니다. 기타 브라우져는 마지막에 확인할 예정입니다.
				</div>
				
			</section>
			
			
			<? foreach ($posts as $alias => $listing): ?>
			
			<? if ( ! empty($listing)): ?>
			
			<?=$alias?>
			
			<section data-group="board">
		

				<? foreach ($listing as $p): ?>

				<article data-type="post" data-mode="home">
					<?=$p->user->avatar('small')?>
		
					<h1>
			
						<a href="<?=$p->link($alias)?>"><?=$p->short_title?></a> 
			
						<?if ($p->comments_count > 0):?>
			
						<a data-type="comments-count" href="<?=$p->link($alias)?>"><span><?=number_format($p->comments_count)?></span></a>
			
						<? endif; ?>
			
					</h1>
		
		
				</article>

				<? endforeach; ?>

			</section>
			
			<? endif; ?>
			
			<? endforeach; ?>
			