			
			
			
			<section data-group="form" data-form="general"> 
				
				<h1>정보 수정</h1>
				
				<? if (Notification::exists()): ?>

				<div data-group="notification">
					<?=Notification::get()?>
				</div>

				<? endif; ?>
				
				<? if (Form::has_errors()): ?>

				<div data-group="error">

					<? foreach (Form::all_errors() as $err): ?>

					<p><?=$err?></p>

					<? endforeach; ?>

				</div>

				<? endif; ?>
				
				<?=Form::open_for_files('auth/edit', 'PUT')?>
				
				<?=Form::token()?>
				
				<label>이름</label>
				<input type="text" name="name" value="<?=Input::old('name', Authly::get_name())?>" required autofocus>
				<?=Form::error('name')?>
				
				<label>아바타</label>
				<input type="text" name="avatar_url" value="<?=Input::old('avatar_url', Authly::get_avatar_url())?>" required>
				<?=Form::error('avatar_url')?>
				
				<label>파일</label>
				<input type="file" name="avatar">
				
				
				<br><img src="<?=Authly::get_avatar_url()?>">
				
				
				<div class="actions">
					<input type="submit">
				</div>
				
				<?=Form::close()?>
				
			</section>