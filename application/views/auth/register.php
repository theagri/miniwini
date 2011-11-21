			
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
			
			<section data-group="form" data-form="general">
	
				<?=Form::open('auth/register', 'POST')?>

	
				<label for="userid">아이디</label>
				<input type="text" name="userid" id="userid" value="<?=Input::old('userid')?>" required autofocus>
		
				<label for="email">이메일</label>
				<input type="email" name="email" id="email" value="<?=Input::old('email')?>" required>

				<label for="name">이름</label>
				<input type="text" name="name" id="name" value="<?=Input::old('name')?>" required>
	
				<label for="password">비밀번호</label>
				<input type="password" name="password" id="password" required>
		
				<div class="actions">
					<input type="submit" class="button" value="가입하기">
				</div>

				<?=Form::close()?>

			</section>









