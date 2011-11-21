<?php
return array(
	'GET /commently' => function(){
		$url = Input::get('url');
		$comments = Commently::make($url)->comments();
		return json_encode($comments);
	},
	
	// ---------------------------------------------------------------------
	
	'POST /commently' => function(){
		return Commently::add(Input::all());
	}
);