			
			<h1>로그인</h1>
			
			<fieldset data-ui="compact-form">
				
				<?=Form::open('auth/login')?>
				
				<?=Form::token()?>
				
				<? if ( ! empty($back_to)): ?>
				
				<?=Form::hidden('back_to', $back_to)?>
				
				<? endif; ?>

				<div data-ui="control-box-full-vertical">
					<label for="userid">아이디</label>
					<input type="text" id="userid" name="userid" value="<?=Input::old('userid')?>" required autofocus>
				</div>
				
				<div data-ui="control-box-full-vertical">
					<label for="password">비밀번호</label>
					<input id="password" type="password" name="password" required>
				</div>

				<div class="actions">
					<input type="submit" class="button" value="로그인">
				</div>

				<?=Form::close()?>
	
				<? if (Config::get('authly.connections.enabled')): ?>
	
				<? foreach (Config::get('authly.connections.services') as $service): ?>
	
				<div>
					<a href="<?=URL::to('auth/connect/' . $service)?>"><?=$service?></a>
				</div>
	
				<? endforeach; ?>
	
				<? endif; ?>



			</fieldset>