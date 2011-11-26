<?php
return array(
	'GET /notification/all' => function(){

		if ( ! Authly::signed())
		{
			return '';
		}
		
		return Notification::of(Authly::get_id());
	},
	
	'GET /notification/count' => function()
	{
		if ( ! Authly::signed())
		{
			return '';
		}
		
		return Notification::count_of(Authly::get_id());
	},
	
	'GET /notification/read' => function()
	{
		if ( ! Authly::signed())
		{
			return;
		}
		return Notification::read(Authly::get_id(), Input::get('time'));
	}
);