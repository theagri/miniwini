<?php
return array(
	'url' => Config::get('application.url') . '/commently',
	
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
			$body = mb_substr($comment->body, 0, 40, 'UTF-8');
			if ($comment->parent_id)
			{
				$parent = Commently::comment($comment->parent_id);
				if ($parent and ($parent->author_id != Authly::get_id()))
				{
					Notification::put(array(
						'action' => 'comment_on_comment',				
						'user_id' => $parent->author_id,
						'actor_name' => $comment->author_name,
						'actor_avatar' => $comment->author_avatar_url,
						'body' => $body,
						'url' => $url,
						'created_at' => time()
					));
				}
			}
			elseif ($post->user_id != Authly::get_id())
			{
				Notification::put(array(
					'action' => 'comment_on_topic',
					'user_id' => $post->user_id,
					'actor_name' => $comment->author_name,
					'actor_avatar' => $comment->author_avatar_url,
					'body' => $body,
					'url' => $url,
					'created_at' => time()
				));
			}
			
		}
	},
	
	'misc' => array(
		'append_html' => "\t\t\t\t\t"
	)
);