
			<?=View::make('board/_header', array(
				'board' => $board,
			))->render()?>
			
			<?=View::make('board/_tabs', array(
				'board' => $board,
			))->render()?>
			
			<section data-group="board" data-form="general" data-type="post">

				
				<?=Form::open($board->link('new'), 'POST')?>

				<?=Form::token()?>
				
				<input type="hidden" name="state" value="open">

				<label for="title"><?=__('miniwini.board_newpost_title')?></label>
				<input type="text" id="title" name="title" value="<?=Input::old('title')?>" required autofocus>
				
				<label for="body"><?=__('miniwini.board_newpost_body')?></label>
				<textarea id="body" name="body" required><?=Input::old('body')?></textarea>
				
				<label for="format">형식</label>
				<select id="format" name="format">
					<option value="text">Text</option>
					<option value="markdown">Markdown</option>
				</select>
				
				<hr>
		
				<? if (Authly::belongs($board->series_level)): ?>
				
				<div id="add-series">
					
					<div><input type="radio" name="series" id="series-0" value="0" checked><label for="series-0"><?=__('miniwini.board_newpost_series_type_no_series')?></label></div>
					
					<? if (Series::count_of($board->id, Authly::get_id()) > 0): ?>
					
					<div>
						<input type="radio" name="series" id="series-1" value="1"><label for="series-1"><?=__('miniwini.board_newpost_series_type_existing_series')?></label>
						<select name="series_id" disabled>
						
						<? foreach (Series::of($board->id, Authly::get_id()) as $series): ?>
						
						<option value="<?=$series->id?>"><?=$series->title?></option>
						
						<? endforeach; ?>
							
						</select>
					</div>
					
					<? endif; ?>
					
					<div>
						<input type="radio" name="series" id="series-2" value="2"><label for="series-2"><?=__('miniwini.board_newpost_series_type_new_series')?></label>
						<div id="new-series">
							<label><?=__('miniwini.board_newpost_series_title')?></label>
							<input type="text" name="series_title">
							
							<label><?=__('miniwini.board_newpost_series_description')?></label>
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
					<button type="button" class="btn alternative" onclick="return miniwini.saveToDraft(this.form)"><?=__('miniwini.board_newpost_button_draft')?></button>
					<input type="submit" value="<?=__('miniwini.board_newpost_button_submit')?>">
				</div>
				
				<?=Form::close()?>
				
			</section>
			