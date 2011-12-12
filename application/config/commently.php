<?php
return array(
	'url' => Config::get('application.url') . '/commently',
	
	// ---------------------------------------------------------------------
	
	'max_depth' => 10,
	
	// ---------------------------------------------------------------------
	
	'max_seconds_to_edit' => 3600, // 1 hour
	
	// ---------------------------------------------------------------------
	
	'max_seconds_to_delete' => 3600, // 1 hour
	
	// ---------------------------------------------------------------------
	
	'available_tags' => '<br><strong><em><u><del><img><a><p><ol><ul><li><code><pre><blockquote><q><cite><span><h1><h2><h3><h4><h5><h6><hr>',
	
	// ---------------------------------------------------------------------
	
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
	
	// ---------------------------------------------------------------------
	
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
	
	// ---------------------------------------------------------------------
	
	'after_delete_hook' => function($page, $comment){
		$url = $page->url;
		preg_match('/(?:talk|share|qna)\/(\d+)$/', $url, $m);
		if ($m)
		{
			// count
			$id = $m[1];
			$post = Post::find($id);
			$post->comments_count -= 1;
			$post->save();
		}
	},
	
	// ---------------------------------------------------------------------
	
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
			$body = Notification::summarize($comment->body);
			$to = NULL;
			$link = $url . '#commently-comment-' . $comment->id;
			if ($comment->parent_id)
			{
				$parent = Commently::comment($comment->parent_id);
				if ($parent and ($parent->author_id != Authly::get_id()))
				{
					$to = $parent->author_id;
					$action = 'comment_on_comment';
				}
			}
			elseif ($post->user_id != Authly::get_id())
			{
				$to = $post->user_id;
				$action = 'comment_on_topic';
			}
			
			$notifications = array();
			
			if ($comment->meta)
			{
				$meta = json_decode($comment->meta);
				if ($meta)
				{
					$mentions = $meta->mentions;
					for ($i = 0; $i < count($mentions); $i++)
					{
						$mention = $mentions[$i];
						if ($mention->id == $to)
						{
							if ($action == 'comment_on_topic')
							{
								$action = 'comment_and_mention_on_topic';
							}
							elseif ($action == 'comment_on_comment')
							{
								$action = 'comment_and_mention_on_comment';
							}
							
							$notifications[] = array(
								'action' => $action,				
								'user_id' => $to,
								'actor_id' => $comment->author_id,
								'actor_name' => $comment->author_name,
								'actor_avatar' => $comment->author_avatar_url,
								'body' => $body,
								'url' => $link,
								'created_at' => time()
							);
							continue;
						}
						
						if ($mention->id == Authly::get_id())
						{
							continue;
						}
						
						$notifications[] = array(
							'action' => 'mention',				
							'user_id' => $mention->id,
							'actor_id' => $comment->author_id,
							'actor_name' => $comment->author_name,
							'actor_avatar' => $comment->author_avatar_url,
							'body' => $body,
							'url' => $link,
							'created_at' => time()
						);
					}
				}
			}
			elseif ( ! is_null($to))
			{
				$notifications[] = array(
					'action' => $action,				
					'user_id' => $to,
					'actor_id' => $comment->author_id,
					'actor_name' => $comment->author_name,
					'actor_avatar' => $comment->author_avatar_url,
					'body' => $body,
					'url' => $link,
					'created_at' => time()
				);
			}
			

			
			if ( ! empty($notifications))
			{
				foreach ($notifications as $noti)
				{
					Notification::put($noti);
				}
				
			}
			
			
		}
	},
	
	'misc' => array(
		'append_html' => "\t\t\t\t\t"
	)
);