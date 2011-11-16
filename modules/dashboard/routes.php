<?php
return array(
	
	// ---------------------------------------------------------------------
	
	'GET /dashboard' => array('name' => 'dashboard', 'do' => function(){
		return View::of_front()->partial('content', 'dashboard/index');
	}),
	
	// ---------------------------------------------------------------------
);