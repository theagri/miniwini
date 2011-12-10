			
			<h1>Notifications</h1>
			
			<section data-ui="notifications">
				
				<? if ( ! empty($histories)): ?>
			
				<? foreach ($histories as $h): ?>
				
				<article>
					
					<img src="<?=$h->actor_avatar?>" height="24">
					<a href="<?=$h->url?>">
						<strong><?=__('miniwini.notification_action_'.$h->action, array('name' => $h->actor_name))?></strong>
						<?=Time::humanized_html($h->created_at)?>
						<br>
						<q><?=$h->body?>…</q>
					</a>
				</article>
				
				<? endforeach; ?>
				
				<? endif; ?>
				
			</section>