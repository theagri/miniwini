			
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
				
				<?=Form::open('auth/login')?>
				
				<?=Form::token()?>
				
				<? if ( ! empty($back_to)): ?>
				
				<?=Form::hidden('back_to', $back_to)?>
				
				<? endif; ?>


				<label for="userid">아이디</label>
				<input type="text" id="userid" name="userid" value="<?=Input::old('userid')?>" required autofocus>

				<label for="password">비밀번호</label>
				<input id="password" type="password" name="password" required>

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



			</section>