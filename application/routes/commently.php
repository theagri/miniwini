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
				
				$meta = array('mentions' => array());
				
				foreach ($mentions as $mention)
				{
					$meta['mentions'][] = array(
						'id' => $mention->id,
						'userid' => $mention->userid,
						'name' => $mention->name
					);
				}
				
				$data['meta'] = json_encode($meta);
				$data['body'] = $body;
			}
			
			

			
			if (Commently::add($data))
			{
				$rand = mt_rand(0, 1000);
				if ($rand >= 990)
				{
					Authly::up_exp(10);
					Session::flash('exp', 10);
					Session::flash('exp_critical', TRUE);
				}
				else
				{
					Authly::up_exp(1);
					Session::flash('exp', 1);
				}
				
			}
			
			return Redirect::to(Input::get('url'))->with('notification', 'Comment added');
		}
		
		return Redirect::to(Input::get('url'));
	}),
);