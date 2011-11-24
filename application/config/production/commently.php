<?php
return array(
	'url' => 'http://s2.miniwini.com/commently',
	
	'accounts' => function()
	{
		if ( ! Authly::signed()) return array();
		
		return array(
			'default' => array(
				'id' => Authly::get_id(),
				'avatar_url' => Authly::get_avatar_url()
			)
		);
	},
	
	'account_by_provider' => function($provider){
		switch ($provider)
		{
			case 'default':
				
				return array(
					'author_id' => Authly::get_id(),
					'author_name' => Authly::get_name(),
					'author_url' => Config::get('application.url') . '/' . Authly::get_userid(),
					'author_avatar_url' => Authly::get_avatar_url()
				);
		}
		
		return NULL;
	},
	
	'after_hook' => function($page, $comment){
		$url = $page->url;
		preg_match('/(?:talk|share|qna)\/(\d+)$/', $url, $m);
		if ($m)
		{
			// count
			$id = $m[1];
			$post = Post::find($id);
			$post->comments_count += 1;

			// last_commenter
			if ($comment and $comment->author_id)
			{
				$post->last_commenter_id = $comment->author_id;
				$post->last_commented_at = $comment->created_at;
			}
			
			$post->save();			
		}
	},
	
	'misc' => array(
		'append_html' => "\t\t\t\t\t"
	)
);