<?php

return array(

	'GET /' => function()
	{
		return View::of_front()->partial('content', 'home/index');
	},

);