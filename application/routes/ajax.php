<?php
return array(
	'POST /ajax/preview' => function(){
		$body = Input::get('body');
		
		if (empty($body))
		{
			return '';
		}
		
		return Post::preview($body);
		
	}
);