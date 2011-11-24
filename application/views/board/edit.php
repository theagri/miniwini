			
			<?=View::make('board/_header', array(
				'board' => $board,
			))->get()?>
			
			<?=View::make('board/_tabs', array(
				'board' => $board,
			))->get()?>
			
			<section data-group="board" data-form="general" data-type="post">
				
				<?=Form::open($post->link($board->alias) . '/edit', 'PUT')?>

				<?=Form::token()?>
				
				<input type="hidden" name="state" value="open">

				<label for="title"><?=__('miniwini.board.newpost.title')?></label>
				<input type="text" id="title" name="title" value="<?=Input::old('title', $post->title)?>" required autofocus>
				
				<label for="body"><?=__('miniwini.board.newpost.body')?></label>
				<textarea id="body" name="body" required><?=Input::old('body', $post->body)?></textarea>
				
				<? if (FALSE and Authly::belongs($board->series_level)): ?>
				
				<div id="add-series">
					
					<div><input type="radio" name="series" id="series-0" value="0" checked><label for="series-0"><?=__('miniwini.board.newpost.series_type.no_series')?></label></div>
					
					<? if (Series::count_of($board->id, Authly::id()) > 0): ?>
					
					<div>
						<input type="radio" name="series" id="series-1" value="1"><label for="series-1"><?=__('miniwini.board.newpost.series_type.existing_series')?></label>
						<select name="series_id" disabled>
						
						<? foreach (Series::of($board->id, Authly::id()) as $series): ?>
						
						<option value="<?=$series->id?>"><?=$series->title?></option>
						
						<? endforeach; ?>
							
						</select>
					</div>
					
					<? endif; ?>

					<div>
						<input type="radio" name="series" id="series-2" value="2"><label for="series-2"><?=__('miniwini.board.newpost.series_type.new_series')?></label>
						<div id="new-series">
							<label><?=__('miniwini.board.newpost.series_title')?></label>
							<input type="text" name="series_title">
							
							<label><?=__('miniwini.board.newpost.series_description')?></label>
							<textarea name="series_description"></textarea>
						</div>
						
						<script>
						$(function(){
							$('#new-series').hide();
							$('input[name=series]').change(function(){
								var val = $(this).val(); 
								$('#new-series')[val == 2 ? 'show' : 'hide']();
								$('select[name=series_id]').attr('disabled', val != 1);
							});
						})
						</script>
					</div>
				</div>
				
				<? endif; ?>
				
				<div class="multiple-actions">
					<button type="button" class="btn alternative" onclick="return miniwini.saveToDraft(this.form)"><?=__('miniwini.board.newpost.button.draft')?></button>
					<input type="submit" value="<?=__('miniwini.board.newpost.button.edit')?>">
				</div>
				
				<?=Form::close()?>
				
			</section>



