			
			<section data-group="board" data-form="general" data-type="post">
				
				<?=Form::open($post->link($board->alias) . '/delete', 'DELETE')?>
				
				<?=Form::token()?>
				
				<div class="actions">
					<input type="submit" value="삭제하기">
				</div>
				
				<?=Form::close()?>
			</section>