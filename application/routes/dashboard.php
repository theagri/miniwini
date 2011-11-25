<?php
return array(
	
	// ---------------------------------------------------------------------
	
	'GET /dashboard' => array('name' => 'dashboard', 'do' => function(){
		return View::of_front()->nest('content', 'dashboard/index');
	}),
	
	// ---------------------------------------------------------------------
);