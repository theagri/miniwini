			
			<section data-group="board" data-form="general" data-type="post">
				<div data-group="confirmbox">
					
					
					<strong>이 글을 삭제하시겠습니까?</strong>
					<p>
						되돌릴 수 없습니다.
					</p>
				
				</div>
				
				<?=Form::open($post->link($board->alias) . '/delete', 'DELETE')?>
				
				<?=Form::token()?>
				
				<div class="actions">
					<input type="submit" value="삭제하기">
				</div>
				
				<?=Form::close()?>
			</section>