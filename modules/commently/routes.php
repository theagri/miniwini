<?php
return array(
	'GET /commently' => function(){
		$url = Input::get('url');
		$comments = Commently::make($url)->comments();
		return json_encode($comments);
	},
	
	// ---------------------------------------------------------------------
	
	'POST /commently' => function(){
		$rules = array(
			'provider' => 'required',
			'url' => 'required|url',
			'body' => 'required'
		);
		
		$val = Validator::make(Input::all(), $rules);
		if ($val->valid())
		{
			Commently::add(Input::all());
			return Redirect::back()->with('notification', 'Comment added');
		}
		
		return Redirect::back();
	}
);