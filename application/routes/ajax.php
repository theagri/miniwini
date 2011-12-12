<?php
return array(
	'POST /ajax/preview' => function(){
		$body = Input::get('body');
		
		if (empty($body))
		{
			return '';
		}
		
		return Post::preview($body);
	},
	
	'GET /ajax/find_user' => function(){
		$k = trim(Input::get('keyword'));
		if ( ! $k)
		{
			return '';
		}
		
		$k = '%'. $k . '%';
		$users = DB::table('users')->select(array('id', 'userid', 'name', 'avatar_url'))->where('userid', 'LIKE', $k)->or_where('name', 'LIKE', $k)->get();
		die(json_encode($users));
	},
	
	'GET /ajax/commently/(:num)' => function($id)
	{
		$comment = Commently::comment($id);
		die(json_encode($comment));
	}
);