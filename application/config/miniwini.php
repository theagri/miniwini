<?php
return array(
	'title' => '미니위니',
	'description' => '미니위니',
	'open' => TRUE,
	
	'contact' => array(
		'name' => '미니위니',
		'email' => 'mywizz@gmail.com'
	),
	
	'avatar' => array(
		'sizes' => array(
			'small' => 24,
			'medium' => 30,
			'big' => 60,
			'huge' => 100
		),
		'max_size' => 100,
		'no_avatar_url' => Config::get('application.url') . '/img/no_avatar.png'
	),
	
	'user' => array(
		'min_userid_size' => 2,
		'max_userid_size' => 10,
		'min_name_size' => 2,
		'max_name_size'=> 16,
	),
	
	'available_tags' => '<br><strong><em><del><img><a><p><ol><ul><li><code><pre><blockquote><q><cite><span><h1><h2><h3><h4><h5><h6><hr>'
);