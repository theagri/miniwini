<?php

return array(

	'layouts.front' => array('name' => 'front', function($view)
	{
		$view->with('visitors', Visitor::all());
	}),
	
	'layouts.blank' => array('name' => 'blank', function($view)
	{
	}),
);