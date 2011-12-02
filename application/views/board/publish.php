			
			
			<section data-group="board" data-form="general" data-type="post">
				
				
				<div data-group="confirmbox">
					
					<? if ($post->is_draft()): ?>
					
					<strong>이 글을 처음으로 발행하시겠습니까?</strong>
					<p>
						이 글은 현재 <strong>임시 보관</strong> 상태입니다.
						<br>발행하면 게시판 목록에 이 글이 새 글로 표시됩니다.
					</p>
					
					<? elseif ($post->unpublished()): ?>
					
					<strong>이 글을 다시 발행하시겠습니까?</strong>
					<p>
						발행하면, 게시판 목록에 이 글이 표시됩니다.
					</p>
					
					<? endif; ?>
				</div>
				
				<?=Form::open($post->link($board->alias) . '/publish', 'PUT')?>
				
				<?=Form::token()?>
				
				<div class="actions">
					<input type="submit" value="발행하기">
				</div>
				
				<?=Form::close()?>
			</section>