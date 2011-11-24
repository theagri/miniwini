
			<?=View::make('board/_header', array(
				'board' => $board
			))->get()?>

			<?=View::make('board/_tabs', array(
				'board' => $board
			))->get()?>
			
			<section data-group="board" data-form="general" data-type="post">

				<?=Form::open($series->link($board->alias), 'PUT')?>
				
				<?=Form::token()?>
				
				<label>연재물 제목</label>
				<input type="text" name="title" value="<?=e($series->title)?>">
				
				<label>연재물 소개</label>
				<textarea name="description"><?=$series->description?></textarea>
				
				<div class="actions">
					<input type="submit" value="수정하기">
				</div>
	
				<?=Form::close()?>

				<? foreach (array_reverse($series->posts) as $p): ?>
	
				<article data-type="post" data-mode="listing">
					<?=$p->user->avatar('medium')?>

					<h1>

						<a href="<?=$p->link($board->alias)?>"><?=$p->short_title?></a> 

						<?if ($p->comments_count > 0):?>

						<a data-type="comments-count" href="<?=$p->link($board->alias)?>"><span><?=number_format($p->comments_count)?></span></a>

						<? endif; ?>

					</h1>

					<p data-type="summary">

						<?=$p->summary?>

					</p>

					<footer>
						<a data-type="user" href="<?=$p->user->link?>"><?=$p->user->name?></a>
						/
						<?=Time::humanized_html($p->created_at)?> / 조회 <strong><?=number_format($p->views_count)?></strong>
			
						<? if ($p->last_commenter): ?>

						/ 최근 댓글<?=$p->last_commenter->avatar('small')?>

						<? endif; ?>

					</footer>

				</article>

				<? endforeach; ?>
	
	
			</section>