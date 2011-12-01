			
			
			<h1>환경 설정</h1>
			
			<div data-ui="tabbed-panel" data-form="general">
				<ul>
					<li onclick="return miniwini.selectTab(this)" class="active" data-tab="general"><a href="#">기본 정보</a></li>
					<li onclick="return miniwini.selectTab(this)" data-tab="avatar"><a href="#">아바타</a></li>
					<li onclick="return miniwini.selectTab(this)" data-tab="password"><a href="#">비밀번호</a></li>
					<li onclick="return miniwini.selectTab(this)" data-tab="pref"><a href="#">설정</a></li>
				</ul>
				
				<div id="panel">
					
					<div id="panel-general">
					
						<?=Form::open_for_files('auth/edit', 'PUT')?>

						<?=Form::token()?>

						<label>이름 <small>(최소 <?=Config::get('miniwini.user.min_name_size')?>글자, 최대 <?=Config::get('miniwini.user.max_name_size')?>글자)</small></label>
						<input data-length="short" type="text" name="name" value="<?=e(Input::old('name', Authly::get_name()))?>" required autofocus>


						<div class="actions">
							<input type="submit" value="수정하기">
						</div>

						<?=Form::close()?>
						
					</div>
					
					<div id="panel-avatar">
						
						<?=Form::open_for_files('auth/change_avatar', 'PUT')?>

						<?=Form::token()?>
						
						<label>아바타 파일 업로드 <small>(파일 크기는 최대 <?=Config::get('miniwini.avatar.max_size')?>KB)</small></label>
						<input type="file" name="avatar">

						<hr>

						<div>
							<strong>현재 아바타</strong>
							<br><img src="<?=Authly::get_avatar_url()?>">
							<br><?=Authly::get_avatar_url()?>
						</div>

						<div class="actions">
							<input type="submit" value="수정하기">
						</div>

						<?=Form::close()?>
						 
					</div>
					
					<div id="panel-password">
						
						<?=Form::open('auth/change_password', 'PUT')?>

						<?=Form::token()?>

						<label>현재 비밀번호</label>
						<input data-length="medium" type="password" name="password_current" required>

						<label>새 비밀번호</label>
						<input data-length="medium" type="password" name="password" required>

						<label>새 비밀번호를 한번 더 입력해주세요</label>
						<input data-length="medium" type="password" name="password_confirmation" required>

						<div class="actions">
							<input type="submit" value="변경하기">
						</div>

						<?=Form::close()?> 
					</div>
					
					<div id="panel-pref">
						설정 
					</div>
				</div>
			</div>
			
			
			
			<script>$(function(){
				if (document.location.hash)
				{
					miniwini.selectTab($('[data-tab='+document.location.hash.substring(1)+']'))
				}
				
			})
			</script>
			
			