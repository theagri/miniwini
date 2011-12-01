
			<?=View::make('board/_header', array(
				'board' => $board,
			))->render()?>
			
			<?=View::make('board/_tabs', array(
				'board' => $board,
				'active_tab' => $active_tab
			))->render()?>
			
			<section data-group="board" data-form="general" data-type="post">
				
				<? if ($edit): ?>
				
				<?=Form::open($post->link($board->alias) . '/edit', 'PUT', array('onsubmit="return miniwini.submitPost(this)"'))?>
				
				<? else: ?>
				
				<?=Form::open($board->link('new'), 'POST', array('onsubmit="return miniwini.submitPost(this)"'))?>
				
				<? endif; ?>

				<?=Form::token()?>
				
				<input type="hidden" name="state" value="open">

				<label for="title"><?=__('miniwini.board_newpost_title')?> <small>(안쓰셔도 됩니다)</small></label>
				<input data-length="full" type="text" id="title" name="title" value="<?=Input::old('title', e($post->title))?>">
				
				
				<div data-ui="tabbed-panel">
					<ul>
						<li onclick="miniwini.setPostType(this)" class="active" data-tab="post-type-text"><a href="#">텍스트</a></li>
						<li onclick="miniwini.setPostType(this)" data-tab="post-type-photo"><a href="#">사진</a></li>
						<li onclick="miniwini.setPostType(this)" data-tab="post-type-link"><a href="#">링크</a></li>
						<li onclick="miniwini.setPostType(this)" data-tab="post-type-option"><a href="#">설정</a></li>
						<li onclick="miniwini.setPostType(this)" data-tab="post-type-preview"><a href="#">미리보기</a></li>
					</ul>
					
					<div id="panel">
						<div id="panel-post-type-text"></div>
						
						<div id="panel-post-type-photo">
							사진 
						</div>

						<div id="panel-post-type-link">
							링크 
						</div>

						<div id="panel-post-type-option">
							
							<? if (Authly::belongs($board->series_level)): ?>

							<div id="add-series">

								<div><input type="radio" name="series" id="series-0" value="0" checked><label for="series-0"><?=__('miniwini.board_newpost_series_type_no_series')?></label></div>

								<? if (Series::count_of($board->id, Authly::get_id()) > 0): ?>

								<div>
									<input type="radio" name="series" id="series-1" value="1"><label for="series-1"><?=__('miniwini.board_newpost_series_type_existing_series')?></label>
									<select name="series_id" id="series_id" disabled>

									<? foreach (Series::of($board->id, Authly::get_id()) as $series): ?>

									<option value="<?=$series->id?>"><?=$series->title?></option>

									<? endforeach; ?>

									</select>
								</div>

								<? endif; ?>

								<div>
									<input type="radio" name="series" id="series-2" value="2"><label for="series-2"><?=__('miniwini.board_newpost_series_type_new_series')?></label>
									<div id="new-series">
										<label for="series_title"><?=__('miniwini.board_newpost_series_title')?></label>
										<input type="text" name="series_title" id="series_title">

										<label for="series_description"><?=__('miniwini.board_newpost_series_description')?></label>
										<textarea name="series_description" id="series_description"></textarea>
									</div>

									<script>
									$(function(){
										$('#new-series').hide();
										$('input[name=series]').change(function(){
											var val = $(this).val(); 
											$('#new-series')[val == 2 ? 'show' : 'hide']();
											$('select[name=series_id]').attr('disabled', val != 1);
										});
										
										<? if ($edit and $post->series_id): ?>
										
										$('#series-1').attr('checked', true);
										$('#series_id').attr('disabled', false).val(<?=$post->series_id?>);
										
										<? endif; ?>
									})
									</script>
								</div>
							</div>

							<? endif; ?> 
						</div>

						<div id="panel-post-type-preview">
						</div>
						
						<div id="common-controls">
							<div>
								
								<?=Form::select('format', array(
									'text' => 'Text',
									'markdown' => 'Markdown'
								),
								$post->format ? $post->format : ((Cookie::has('preferred_format') ? Cookie::get('preferred_format') : 'text')), 
								array('id' => 'format'))?>
								
							</div>
							
							<textarea id="body" name="body" required autofocus><?=Input::old('body', $post->body)?></textarea>
						</div>
					</div>
					
					<div id="preview-section">
						<div data-type="body" id="preview-body"></div>
					</div>
				</div>
				
				
				<div class="multiple-actions">
					<button type="button" class="btn alternative" onclick="return miniwini.saveToDraft(this.form)"><?=__('miniwini.board_newpost_button_draft')?></button>
					
					<? if ($edit): ?>

					<input type="submit" id="submitButton" value="<?=__('miniwini.board_newpost_button_edit')?>">
					
					<? else: ?>
					
					<input type="submit" id="submitButton" value="<?=__('miniwini.board_newpost_button_submit')?>">
					
					<? endif; ?>
				</div>
				
				<?=Form::close()?>
				
			</section>
			