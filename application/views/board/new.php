
			<?=View::make('board/_header', array(
				'board' => $board,
			))->render()?>
			
			<?=View::make('board/_tabs', array(
				'board' => $board,
				'active_tab' => $active_tab
			))->render()?>
			

			<section>
				
				<? if ($post and $post->is_draft()): ?>
				
				<div data-ui="helpbox">
					이 글은 현재 <strong>임시 보관</strong> 상태입니다.
				</div>
				
				<? elseif ($post and $post->unpublished()): ?>
				
				<div data-ui="helpbox">
					이 글은 현재 <strong>발행 취소</strong> 상태입니다.
				</div>
				
				<? endif; ?>
				
				
				<? if ($edit): ?>
				
				<?=Form::open($post->link($board->alias) . '/edit', 'PUT', array('onsubmit' => "return miniwini.submitPost(this)"))?>
				
				<? else: ?>
				
				<?=Form::open($board->link('new'), 'POST', array('onsubmit' => 'return miniwini.submitPost(this)'))?>
				
				<? endif; ?>

				<?=Form::token()?>
				
				<?=Form::hidden('state', 'open')?>
				
				<div data-ui="control-box-full-vertical">
					<label for="title"><?=__('miniwini.board_newpost_title')?> <small>(안쓰셔도 됩니다)</small></label>
					<input type="text" id="title" name="title" value="<?=Input::old('title', e($post->title))?>">
				</div>
				
				<div data-ui="tabbed-panel">
					<ul>
						<li onclick="miniwini.setPostType(this)" class="active" data-tab="post-type-text"><a href="#">텍스트</a></li>
						<li onclick="miniwini.setPostType(this)" data-tab="post-type-photo"><a href="#">사진</a></li>
						<li onclick="miniwini.setPostType(this)" data-tab="post-type-link"><a href="#">링크</a></li>
						<li onclick="miniwini.setPostType(this)" data-tab="post-type-option"><a href="#">설정</a></li>
						<li onclick="miniwini.setPostType(this)" data-tab="post-type-preview"><a href="#">미리보기</a></li>
					</ul>
					
					<div data-ui="panel">
						<div data-panel="text" id="panel-post-type-text"></div>
						
						<div data-panel="photo" id="panel-post-type-photo">
							
							<? if (($conn = Authly::connection('flickr'))): ?>
							
							<p id="upload-to-flickr">Flickr의 <strong><?=$conn->auth_name?></strong> 계정으로 업로드할 수 있습니다.</p>
							
							<script>
							$(function(){
								if (localStorage && localStorage.uploadedPhoto)
								{
									var photos = JSON.parse(localStorage.uploadedPhoto);
									if (photos && photos.length)
									{
										$('#upload-to-flickr').append(' <small>[<a href="#" onclick="return miniwini.loadRecentPhoto()">최근 업로드 사진('+photos.length+'개) 불러오기</a>]</small>')
									}
									
								}
							})
							</script>
							
							<iframe id="upload-frame" src="<?=URL::to('board/upload')?>"></iframe>
							
							<? else: ?>
							
							<p id="upload-to-flickr">Flickr 계정을 연결하면 사진을 쉽고 편하게 업로드할 수 있습니다. <small>[<a href="<?=URL::to('auth/edit#connections')?>">계정 연결하러 가기</a>]</small></p>
							
							<? endif; ?>
							
							<div id="uploaded-photos"></div>
						</div>

						<div data-panel="link" id="panel-post-type-link">
							링크 
						</div>

						<div data-panel="option" id="panel-post-type-option">
							
							<? if (Authly::belongs($board->series_level)): ?>

							<fieldset data-ui="sub-panel" id="add-series">
								
								<legend>연재물 설정</legend>

								<div data-ui="control-box-horizontal"><input type="radio" name="series" id="series-0" value="0" checked> <label for="series-0"><?=__('miniwini.board_newpost_series_type_no_series')?></label></div>

								<? if (Series::count_of($board->id, Authly::get_id()) > 0): ?>

								<div data-ui="control-box-horizontal">
									<input type="radio" name="series" id="series-1" value="1"> <label for="series-1"><?=__('miniwini.board_newpost_series_type_existing_series')?></label>
									<select name="series_id" id="series_id" disabled>

									<? foreach (Series::of($board->id, Authly::get_id()) as $series): ?>

									<option value="<?=$series->id?>"><?=$series->title?></option>

									<? endforeach; ?>

									</select>
								</div>

								<? endif; ?>

								<div data-ui="control-box-horizontal">
									<input type="radio" name="series" id="series-2" value="2"> <label for="series-2"><?=__('miniwini.board_newpost_series_type_new_series')?></label>

									<fieldset id="new-series" data-ui="sub-panel">
										<legend>새 연재물을 만듭니다</legend>
										
										<div data-ui="control-box-full-vertical">
											<label for="series_title"><?=__('miniwini.board_newpost_series_title')?></label>
											<input type="text" name="series_title" id="series_title">
										</div>
										
										<div data-ui="control-box-full-vertical">
											<label for="series_description"><?=__('miniwini.board_newpost_series_description')?></label>
											<textarea name="series_description" id="series_description"></textarea>
										</div>
									</fieldset>

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
						</fieldset>

						<div id="panel-post-type-preview"></div>
						
						<div id="common-controls">
							<div data-align="right" data-ui="control-box-horizontal">
								
								<?=Form::select('format', array(
									'text' => 'Text',
									'markdown' => 'Markdown'
								),
								$post->format ? $post->format : ((Cookie::has('preferred_format') ? Cookie::get('preferred_format') : 'text')), 
								array('id' => 'format'))?>
								
							</div>
							<div data-ui="control-box-full-vertical"><textarea id="body" name="body" required autofocus><?=Input::old('body', $post->body)?></textarea></div>
						</div>
					</div>
					
					<div id="preview-section">
						<div data-type="body" id="preview-body"></div>
					</div>
				</div>
				
				
				
					
				<? if ($post and $post->is_draft()): ?>
				
				<div class="actions">
					<input type="submit" id="submitButton" value="<?=__('miniwini.board_newpost_button_edit')?>">
				</div>
				
				<? else: ?>
				
				
				<? if ($edit): ?>
				
				<div class="actions">
					<input type="submit" id="submitButton" value="<?=__('miniwini.board_newpost_button_edit')?>">
				</div>
				
				<? else: ?>
				
				<div class="multiple-actions">
				
					<button type="button" class="btn alternative" onclick="return miniwini.saveToDraft(this.form)"><?=__('miniwini.board_newpost_button_draft')?></button>
					<input type="submit" id="submitButton" value="<?=__('miniwini.board_newpost_button_submit')?>">
				</div>
				
				<? endif; ?>
				
				<? endif; ?>
					
				
				<?=Form::close()?>
				
			</section>
			
			
			
			<script id="tpl-uploaded-photo" type="text/x-jquery-tmpl">
			<div>
				<div class="uploaded-photo">
					<a href="${url}" target="_blank"><img src="${url}" width="100"></a>
					<div>
						<label>URL <input onclick="this.select()" type="text" value="${url}"></label>
						<label>Markdown <input onclick="this.select()" type="text" value="![](${url})"></label>
					</div>
				</div>
			</div>
			</script>