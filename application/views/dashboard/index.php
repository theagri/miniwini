
			<?=View::make('dashboard/_header')->render()?>
			
			<section>
				

				<? if (Config::get('authly.connections.enabled')): ?>
				
				<?=HTML::link('auth/connections', 'Connections')?>
			
				<? endif; ?>

			</section>