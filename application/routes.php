<?php

return array(

	'GET /' => array('name' => 'home', 'do' => function(){
		return View::of_front()->partial('content', 'home/index');
	}),

);