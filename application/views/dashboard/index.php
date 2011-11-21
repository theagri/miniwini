

			<?=HTML::link('auth/edit', '정보 수정')?>


			<? if (Config::get('authly.connections.enabled')): ?>
				
			<?=HTML::link('auth/connections', 'Connections')?>
			
			<? endif; ?>



