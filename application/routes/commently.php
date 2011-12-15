<?php
return array(
	'GET /commently' => function(){
		$url = Input::get('url');
		$comments = Commently::make($url)->comments();
		return json_encode($comments);
	},
	
	// ---------------------------------------------------------------------
	
	'GET /commently/delete/(:num)' => array('before' => 'signed', function($id){
		$comment = Commently::comment($id);

		if ( ! $comment) return Redirect::back()->with('errors', '코멘트를 찾을 수 없습니다.');
		
		if ( 
			($comment->author_id == Authly::get_id()) and
			Commently::can_delete($comment)
		)
		{
			if (Commently::delete($id))
			{
				Authly::down_exp(5);
				Session::flash('exp', -5);
			}
			
			return Redirect::back()->with('notification', '코멘트를 삭제하였습니다.');
		}

		return Redirect::back()->with('errors', '코멘트를 삭제할 수 없습니다.');
	}),
	
	// ---------------------------------------------------------------------
	
	'POST /commently' => array('before' => 'signed|csrf', function(){
		$rules = array(
			'provider' => 'required',
			'url' => 'required|url',
			'body' => 'required'
		);
		
		$data = Input::all();
		$editable = FALSE;
		
		if ( ! empty($data['id']))
		{
			$comment = Commently::comment($data['id']);
			if ( ! $comment)
			{
				return Redirect::to(Input::get('url'))->with('errors', '코멘트를 찾을 수 없습니다.');
			}
			
			if ( 
				($comment->author_id == Authly::get_id()) and
				Commently::can_edit($comment)
			)
			{
				$editable = TRUE;
			}
			else
			{
				return Redirect::to(Input::get('url'))->with('errors', '코멘트를 수정할 수 없습니다.');
			}
		}
		
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
			
			if ($editable and Commently::edit($data))
			{
				return Redirect::to(Input::get('url'))->with('notification', 'Comment updated!');
			}
			else if (Commently::add($data))
			{
				$rand = mt_rand(1, 1000);
				if ($rand > 990)
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