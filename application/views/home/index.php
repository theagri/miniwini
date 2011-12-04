			
			<section>
				<div data-ui="notification">
					
					<p>미니위니 시즌2 개발 중입니다. 환영합니다 :)</p>
					<p>구글 크롬, 파이어폭스, 오페라 등의 최신 브라우져를 이용해 주시기 바랍니다. 기타 브라우져는 마지막에 확인할 예정입니다.</p>
					<p>아바타는 <a href="http://faceyourmanga.com/" target="_blank">faceyourmanga.com</a>에서 만드시면 보기 좋습니다(?)</p>
				</div>
			
			
				
			</section>
			
			
			<? foreach ($posts as $alias => $listing): ?>
			
			<? if ( ! empty($listing)): ?>
			

			
			<section data-group="board">
				
				<h2 data-board-background="<?=$alias?>"><a href="<?=URL::to('board/'. $alias)?>"><?=$boards[$alias]?></a></h2>

				<? foreach ($listing as $p): ?>

				<article data-type="post" data-mode="home">
					
					<?=$p->user->avatar('small')?>
					
					
		
					<h1>
						<span><?=$p->user->name?></span>
						
						<? if ($p->title): ?>
			
						<a href="<?=$p->link($alias)?>"><?=$p->short_title()?></a> 
						
						<? else: ?>
						
						<a href="<?=$p->link($alias)?>"><?=$p->summary()?></a> 
						
						<? endif; ?>
			
						<?if ($p->comments_count > 0):?>
			
						<span data-type="comments-count"><?=number_format($p->comments_count)?></span>
			
						<? endif; ?>
			
					</h1>
		
		
				</article>

				<? endforeach; ?>

			</section>
			
			<? endif; ?>
			
			<? endforeach; ?>
			