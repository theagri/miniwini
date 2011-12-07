<?php
return array(
	'GET /commently' => function(){
		$url = Input::get('url');
		$comments = Commently::make($url)->comments();
		return json_encode($comments);
	},
	
	// ---------------------------------------------------------------------
	
	'POST /commently' => array('before' => 'signed', function(){
		$rules = array(
			'provider' => 'required',
			'url' => 'required|url',
			'body' => 'required'
		);
		$data = Input::all();
		$val = Validator::make($data, $rules);
		if ($val->valid())
		{
			
			
			if (($mentions = Miniwini::mentions(Input::get('body'))))
			{
				$body = $data['body'];
				
				foreach ($mentions as $mention)
				{
					
					$body = str_replace(
						array('@'.$mention->name.'@', '/' . $mention->name .'/'),
						'[userlink:'.$mention->userid.']' . $mention->name . '[/userlink]',
						$body);

					$body = str_replace(
						array('@'.$mention->userid.'@', '/' . $mention->userid .'/'),
						'[userlink:'.$mention->userid.']' . $mention->userid . '[/userlink]',
						$body);
					
					if ($mention->id != Authly::get_id())
					{
						Notification::put(array(
							'action' => 'mention',
							'user_id' => $mention->id,
							'actor_id' => Authly::get_id(),
							'actor_name' => Authly::get_name(),
							'actor_avatar' => Authly::get_avatar_url(),
							'body' => Input::get('body'),
							'url' => Input::get('url'),
							'created_at' => time()
						));
					}
				}
				
				$data['body'] = $body;
			}
			
			
			Commently::add($data);
			
			
			return Redirect::to(Input::get('url'))->with('notification', 'Comment added');
		}
		
		return Redirect::to(Input::get('url'));
	}),
);