			
			
			<h1>환경 설정</h1>
			
			<div data-ui="tabbed-panel">
				<ul>
					<li onclick="return miniwini.selectTab(this)" class="active" data-tab="general"><a href="#">기본 정보</a></li>
					<li onclick="return miniwini.selectTab(this)" data-tab="avatar"><a href="#">아바타</a></li>
					<li onclick="return miniwini.selectTab(this)" data-tab="password"><a href="#">비밀번호</a></li>
					<li onclick="return miniwini.selectTab(this)" data-tab="connections"><a href="#">계정 연결</a></li>
					<li onclick="return miniwini.selectTab(this)" data-tab="pref"><a href="#">기타 설정</a></li>				
				</ul>
				
				<div data-ui="panel">
					
					<div data-panel="general" id="panel-general">
					
						<?=Form::open_for_files('auth/edit', 'PUT')?>

						<?=Form::token()?>
						
						<div data-ui="control-box-full-vertical">
							<label>이름 <small>(최소 <?=Config::get('miniwini.user.min_name_size')?>글자, 최대 <?=Config::get('miniwini.user.max_name_size')?>글자)</small></label>
							<input data-length="short" type="text" name="name" value="<?=e(Input::old('name', Authly::get_name()))?>" required>
						</div>
							
						<div class="actions">
							<input type="submit" value="수정하기">
						</div>

						<?=Form::close()?>
						
					</div>
					
					<div data-panel="connections" id="panel-connections">
						
						<? foreach (Config::get('authly.connections.services') as $service): ?>
						
						<a href="<?=URL::to('auth/connect/' . $service)?>"><?=$service?> 연결하기</a>
						
						<? endforeach; ?>
						
						<hr>
						
						
						<? foreach (Authly::connections() as $conn): ?>

						<div>
							<figure data-type="avatar-medium"><img src="<?=$conn->auth_avatar_url?>"/></figure>
							<?=$conn->provider?> 연결됨 - <?=$conn->auth_name?>
						</div>

						<? endforeach; ?>
						
					</div>
					
					<div data-panel="avatar" id="panel-avatar">
						
						<?=Form::open_for_files('auth/change_avatar', 'PUT')?>

						<?=Form::token()?>
						
						<div data-ui="control-box-full-vertical">
							<label>아바타 파일 업로드 <small>(파일 크기는 최대 <?=Config::get('miniwini.avatar.max_size')?>KB)</small></label>
							<input type="file" name="avatar">
						</div>

						<div data-ui="control-box-full-vertical">
							<strong>현재 아바타</strong>
							<br><img src="<?=Authly::get_avatar_url()?>">
							<br><?=Authly::get_avatar_url()?>
						</div>

						<div class="actions">
							<input type="submit" value="수정하기">
						</div>

						<?=Form::close()?>
						 
					</div>
					
					<div data-panel="password" id="panel-password">
						
						<?=Form::open('auth/change_password', 'PUT')?>

						<?=Form::token()?>
						
						<div data-ui="control-box-full-vertical">
							<label>현재 비밀번호</label>
							<input type="password" name="password_current" required>
						</div>
						
						<div data-ui="control-box-full-vertical">
							<label>새 비밀번호</label>
							<input type="password" name="password" required>
						</div>
						
						<div data-ui="control-box-full-vertical">
							<label>새 비밀번호를 한번 더 입력해주세요</label>
							<input type="password" name="password_confirmation" required>
						</div>
						
						<div class="actions">
							<input type="submit" value="변경하기">
						</div>

						<?=Form::close()?> 
					</div>
					
					<div data-panel="pref" id="panel-pref">
						
						<?=Form::open('auth/change_pref', 'PUT')?>
						
						<?=Form::token()?>
						
						<div data-ui="control-box-full-vertical">
							<label>폰트 <small>(미니위니 기본 폰트를 설정합니다)</small></label>
							
							<?=Form::select('font', array(
								'' => '지정 안함',
								'NanumGothic' => '나눔 고딕',
								'맑은 고딕' => '맑은 고딕'
							), Authly::pref('font'))?>
							
						</div>
						
						<div class="actions">
							<input type="submit" value="변경하기">
						</div>
						
						<?=Form::close()?>
						
					</div>
				</div>
			</div>
			
			
			
			<script>
			$(function(){
				if (document.location.hash)
				{
					miniwini.selectTab($('[data-tab='+document.location.hash.substring(1)+']'))
				}
			})
			</script>
			
			