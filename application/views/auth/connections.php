			
			
			<section>
				
				<? foreach (Authly::connections() as $connection): ?>

				<div>
					<?=$connection->provider?> / <?=$connection->auth_id?> / <?=$connection->auth_userid?> / <?=$connection->auth_name?>
				</div>

				<? endforeach; ?>

				<hr>

				<a href="<?=URL::to('auth/connect/twitter')?>">twitter</a><br>
				<a href="<?=URL::to('auth/connect/facebook')?>">facebook</a><br>
				
				<?=Form::open('auth/connect/openid')?>
				<?=Form::text('openid_identifier')?>
				<?=Form::submit('Submit')?>
				<?=Form::close()?>
				
				
			</section>