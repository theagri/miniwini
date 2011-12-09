<?php

use \Config;
use \DB;
use \Request;

class Commently {

	protected static $MAX_PAD = 11;
	
	protected static $table_pages = 'commently_pages';
	
	protected static $table_data = 'commently_data';
	
	protected $url = NULL;
	
	protected static $page = NULL;
	
	protected static $user = NULL;

	/**
	 * @constructor
	 * @param   string  $url
	 */
	public function __construct($url)
	{
		$this->url = static::normalize_url($url);

	}
	
	// ---------------------------------------------------------------------
	
	public static function make($url = NULL)
	{
		if (is_null($url))
		{
			$url = Request::absolute_uri();
		}
		
		static::loadUser();
		return new static($url);
	}
	
	// ---------------------------------------------------------------------
	
	public static function loadUser()
	{
		if (is_null(static::$user))
		{
			static::$user = is_callable(Config::get('commently.accounts')) ? call_user_func(Config::get('commently.accounts')) : array();
		}
		
		return static::$user;
	}
	
	// ---------------------------------------------------------------------
	
	public static function comment($id)
	{
		return DB::table(static::$table_data)->where_id($id)->first();
	}
	
	// ---------------------------------------------------------------------
	
	private static function normalize_url($url)
	{
		$url = rtrim($url, '/');
		return $url;
	}
	
	// ---------------------------------------------------------------------
	
	private static function find_or_create_by_url($url)
	{
		if ( ! is_null(static::$page))
		{
			return static::$page;
		}
		
		$url = static::normalize_url($url);

		$rec = DB::table(static::$table_pages)->where_url($url)->first();
		
		if ( ! $rec)
		{
			$id = DB::table(static::$table_pages)->insert_get_id(array(
				'url' => $url
			));
			
			$rec = DB::table(static::$table_pages)->where_id($id)->first();
		}
		return (static::$page = $rec);
	}
	
	// ---------------------------------------------------------------------
	
	private static function find_page_by_url($url)
	{
		if ( ! is_null(static::$page))
		{
			return static::$page;
		}
		
		$url = static::normalize_url($url);

		$rec = DB::table(static::$table_pages)->where_url($url)->first();
		return (static::$page = $rec);
	}
	
	// ---------------------------------------------------------------------
	
	public static function add($data)
	{
		$url = $data['url'];
		
		// check account
		$provider = $data['provider'];
		
		$func = Config::get('commently.account_by_provider');
		if (is_callable($func))
		{
			$account = call_user_func($func, $provider);
			if ( ! $account)
			{
				return FALSE;
			}
		}

		$page = static::find_or_create_by_url($url);
		if ($page)
		{
			$comment_data = array_merge(array(
				'provider' => $provider,
				'parent_id' => empty($data['parent_id']) ? NULL : $data['parent_id'],
				'page_id' => $page->id,
				'body' => $data['body'],
				'meta' => ! empty($data['meta']) ? $data['meta'] : NULL,
				'format' => ($data['format'] ? $data['format'] : 'text'),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
				'ip' => Request::ip()
			), $account);
			
			$comment_id = DB::table(static::$table_data)->insert_get_id($comment_data);
			
			$rec = DB::table(static::$table_data)->where_id($comment_id)->first();
			
			// call 'after_hook'
			$hook = Config::get('commently.after_hook');
			if (is_callable($hook))
			{
				call_user_func_array($hook, array($page, $rec));
			}
			
			Cookie::forever('preferred_format', $data['format']);
			
			return TRUE;
			
		}
		return FALSE;
	}
	
	// ---------------------------------------------------------------------
	
	
	public function comments()
	{
		$page = static::find_page_by_url($this->url);
		if ( ! $page)
		{
			return '';
		}
		
		$comments = DB::table(static::$table_data)->where_page_id($page->id)->order_by('id', 'asc')->get();
		$h = array();
		
		$append = Config::get('commently.misc.append_html');
		$result = array();
		$map = array();
		$parsed = array();
		$sorted = array();
		for ($i = 0; $i < count($comments); $i++)
		{
			$c = $comments[$i];
			$parent_id = is_null($c->parent_id) ? $c->id : $c->parent_id;
			$map[$c->id] = $parent_id;
		}
		
		for ($i = 0; $i < count($comments); $i++)
		{
			$c = $comments[$i];
			$parent_id = $c->parent_id;
			$path = array();
			while (TRUE)
			{
				if ( ! isset($map[$parent_id]) or $parent_id == $map[$parent_id])
				{
					if ( ! is_null($parent_id))
					{
						array_unshift($path, $parent_id);
					}
					
					break;
				}
				array_unshift($path, $parent_id);
				$parent_id = $map[$parent_id];
			}
			
			$parsed[$c->id] = $path;
			
			if (count($path))
			{
				for ($j = 0; $j < count($path); $j++)
				{
					$path[$j] = str_pad($path[$j], static::$MAX_PAD,'0',STR_PAD_LEFT);
				}
			}

			$merged = count($path) ? implode('-', $path) : '';
			$seq = ltrim($merged . '-' . str_pad($c->id, static::$MAX_PAD, '0', STR_PAD_LEFT) , '-');
			$c->depth = substr_count($seq, '-');			
			
			$sorted[$seq] = $c;

		}

		ksort($sorted);
		
		$h = array();
		foreach ($sorted as $path => $c)
		{
			$h[] = static::comment_to_html($c, $page);
		}
		
		return implode("\n", $h);
	}
	
	// ---------------------------------------------------------------------

	protected static function comment_to_html($c, $page)
	{
		$account = '<figure data-type="avatar-small"><img alt="' . $c->author_name . '" src="'.$c->author_avatar_url.'"></figure>';
		$time = Time::humanized_html($c->created_at);

		switch ($c->format)
		{
			case 'markdown':
				$body = Miniwini::sanitized_markdown($c->body);
				break;
				
			default:
				$body = Miniwini::sanitized_text($c->body);
		}

		$body = Miniwini::parse_mentions($body, $c->meta);
		
		$today = Time::is_today($c->created_at) ? ' data-today="y"' : '';

		if (empty(static::$user) or $c->depth > Config::get('commently.max_depth') - 1)
		{
			$reply = '';
//			$reply = '<a data-type="reply-button" href="javascript:void(0)" onclick="javascript:commently.reply('. $c->id . ')">댓글 ↵</a>';
		}
		else
		{
			$reply = '<a data-type="reply-button" href="javascript:void(0)" onclick="javascript:commently.reply('. $c->id . ')">댓글 ↵</a>';
		}

		return <<<HTML

				<article id="commently-comment-{$c->id}" data-type="comment" data-url="{$page->url}"{$today} class="depth-{$c->depth}">
					<header>
						{$account}
						<span data-type="user"><a href="{$c->author_url}">{$c->author_name}</a></span>

						{$time}

						{$reply}

					</header>
					<div data-type="body">

					{$body}

					</div>
					<div id="commently-reply-{$c->id}"></div>
				</article>

HTML;

	}
	
	// ---------------------------------------------------------------------
	
	public function form()
	{
		if (empty(static::$user))
		{
			return '';
		}
		
		$post_url = Config::get('commently.url');

		
		$account_html = array();
		foreach (static::$user as $provider => $acc)
		{
			$account_html[] = '<figure data-type="avatar-medium"><img alt="" src="'.$acc['avatar_url'].'"></figure>';
		}
		
		$account_html = implode('', $account_html);
		$format = '';
		if (Cookie::get('preferred_format') == 'markdown')
		{
			$format = ' checked';
		}
		
		$html = <<<HTML
		
				<!-- Commently form -->
				<div data-group="commently" data-type="form-wrapper" data-url="{$this->url}">
					
					<div data-group="commently" data-type="form-container" data-url="{$this->url}">
						
						<form action="{$post_url}" class="commently-form" method="POST" accept-charset="UTF-8">
						
						<div class="commently-help">
							<span>HTML은 사용할 수 없습니다.</span>
							<span>
								<label id="label-preview"><input type="checkbox" name="preview" value="on"> Markdown 미리보기</label>
								<label><input type="checkbox" name="format" value="markdown"{$format}> Markdown 사용</label>
							</span>
						</div>
					
						
						<div data-type="controls">
						
								<input type="hidden" name="provider" value="default">
								<input type="hidden" name="url" value="{$this->url}">
								<input type="hidden" name="parent_id" value="">
								<textarea id="commently-body" name="body"></textarea>
								
								<div data-type="body" id="commently-preview"></div>
							
						</div>
						<div id="commently-autocomplete"></div>
						<div data-type="footer">
							<div data-type="accounts">
						
								{$account_html}
						
							</div>
					
							<div data-type="button">
								<input type="submit" value="등록" accesskey="s">
							</div>
						</div>
					
						</form>
						<script>
						$(function(){
							commently.initialize();
						})
						</script>
					</div>
				</div>
HTML;
		return $html;
	}
}