			
			<?=View::make('dashboard/_header')->get()?>
			
			<section data-group="form" data-form="general"> 

				<?=Form::open_for_files('auth/edit', 'PUT')?>
				
				<?=Form::token()?>
				
				<label>이름 <small>(최소 <?=Config::get('miniwini.user.min_userid_size')?>글자, 최대 <?=Config::get('miniwini.user.max_userid_size')?>글자)</small></label>
				<input type="text" name="name" value="<?=e(Input::old('name', Authly::get_name()))?>" required autofocus>
				
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
				
				<hr>
				
				<?=Form::open('auth/change_password')?>
				
				<?=Form::token()?>
				
				<label>현재 비밀번호</label>
				<input type="password" name="password_current" required>
				
				<label>새 비밀번호</label>
				<input type="password" name="password" required>
				
				<label>새 비밀번호를 한번 더 입력해주세요</label>
				<input type="password" name="password_confirmation" required>
				
				<div class="actions">
					<input type="submit" value="변경하기">
				</div>
				
				<?=Form::close()?>
				
			</section>