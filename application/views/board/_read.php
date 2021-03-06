
			<section data-group="board">

				<article data-type="post" data-mode="single">
					<header>
			
						<?=$post->user->avatar('big')?>
			
						<h1><?=$post->safe_title()?></h1>
			
						<a data-type="author-name" href="<?=$post->user->link()?>"><?=$post->user->name?></a>
			
						<?=Time::humanized_html($post->created_at)?> / 댓글 <strong><?=number_format($post->comments_count)?></strong>개 / 조회 <strong><?=number_format($post->views_count)?></strong>
			
					</header>
		
					<? if ($post->of_user(Authly::get_id())): ?>
		
					<div data-ui="toolbox">
						
						 <a href="<?=$post->link($board->alias)?>/edit">수정하기</a>
						
					</div>
		
					<? endif; ?>
		
					<div data-type="body">
		
					<?=$post->body_html()?>
		
					</div>
					
					<? if ($post->of_user(Authly::get_id())): ?>
					
					<div data-ui="dangerbox">
						
						<? if ($post->is_draft() and $post->of_user(Authly::get_id())): ?>
						
						이 글은 현재 <strong>임시 보관</strong> 상태입니다. <a href="<?=$post->link($board->alias) . '/publish'?>">발행하기</a>
						
						<? elseif ($post->unpublished()): ?>
						
						이 글은 현재 <strong>발행 취소</strong> 상태입니다. <a href="<?=$post->link($board->alias) . '/publish'?>">다시 발행하기</a>
						
						<? else: ?>
						
						<a href="<?=$post->link($board->alias)?>/unpublish">발행 취소하기</a>
						
						<? endif; ?>
						
						<a href="<?=$post->link($board->alias)?>/delete">삭제하기</a>
						
					</div>
					
					<? endif; ?>
		
				</article>
	
			</section>
			

			<? if ($post->series): ?>
			

			<section data-group="board" data-type="series">
	
				<header>
					이 글은 [<a href="<?=$post->series->link($board->alias)?>"><?=$post->series->title?></a>] 연재물에 속해 있습니다. [<a href="#" onclick="$('#series').toggle()">연재물 목록 보기</a>]
				</header>
	
				<ol id="series">
		
					<? foreach (array_reverse($post->series->posts) as $series_post): ?>
					
					<? if ($series_post->open()): ?>
		
					<li><span>#<?=$series_post->series_sequence?></span> <a href='<?=$series_post->link($board->alias)?>'><?=$series_post->title?></a></li>
					
					<? endif; ?>
		
					<? endforeach; ?>
		
				</ol>
	
			</section>

			<? endif; ?>

			<section id="commently-comments" data-group="commently" data-type="comments" data-url="<?=URL::to($board->link() . '/' . $post->id)?>">
				
				<?=Commently::make(URL::to($board->link() . '/' . $post->id))->comments()?>
				
				<?=Commently::make(URL::to($board->link() . '/' . $post->id))->form()?>
				
			</section>


			<script>
			$(function(){
				if (document.location.hash)
				{
					var hash = document.location.hash.substring(1);
					if (hash.indexOf('commently-comment-') === 0)
					{
						//$('#' + hash).addClass('highlighted');
					}
				}
			})
			</script>
