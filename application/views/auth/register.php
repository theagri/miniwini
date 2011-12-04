			
			<h1>회원 가입</h1>
			<fieldset data-ui="compact-form">
	
				<?=Form::open('auth/register', 'POST')?>

				<div data-ui="control-box-full-vertical">
					<label for="userid">아이디</label>
					<input type="text" name="userid" id="userid" value="<?=Input::old('userid')?>" required autofocus>
				</div>
				
				<div data-ui="control-box-full-vertical">
					<label for="email">이메일</label>
					<input type="email" name="email" id="email" value="<?=Input::old('email')?>" required>
				</div>
				
				<div data-ui="control-box-full-vertical">
					<label for="name">이름</label>
					<input type="text" name="name" id="name" value="<?=Input::old('name')?>" required>
				</div>
				
				<div data-ui="control-box-full-vertical">
					<label for="password">비밀번호</label>
					<input type="password" name="password" id="password" required>
				</div>
				
				<div class="actions">
					<input type="submit" class="button" value="가입하기">
				</div>

				<?=Form::close()?>

			</fieldset>
			