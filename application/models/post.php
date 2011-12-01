<?php
class Post extends Blaze {
	public static $table = 'board_posts';
	public static $timestamps = TRUE;
	public static $validations = array(
		'board_id' => 'required|integer',
		'user_id' => 'required|integer',
		'body' => 'required',
		'state' => 'in:open,closed,draft',
	);
	
	// ---------------------------------------------------------------------
	
	public function before()
	{
		$this->ip = Request::ip();
	}
	
	// ---------------------------------------------------------------------
	
	public function board()
	{
		return $this->belongs_to('board');
	}
	
	// ---------------------------------------------------------------------
	
	public function user()
	{
		return $this->belongs_to('user');
	}
	
	// ---------------------------------------------------------------------
	
	public function last_commenter()
	{
		return $this->belongs_to('user', 'last_commenter_id');
		//return $this->has_one('user', 'id', 'last_commenter_id');
	}
	
	// ---------------------------------------------------------------------
	
	public function of_user($user_id)
	{
		if ( ! $user_id or ! is_numeric($user_id))
		{
			return FALSE;
		}
		
		return $this->user_id == $user_id;
	}
	
	// ---------------------------------------------------------------------
	
	public function is_draft()
	{
		return $this->state === 'draft';
	}
	
	// ---------------------------------------------------------------------
	
	public function published()
	{
		return $this->state === 'open';
	}
	
	// ---------------------------------------------------------------------
	
	public function closed()
	{
		return $this->state === 'closed';
	}
	
	// ---------------------------------------------------------------------
	
	public function link($board_alias)
	{
		return Config::get('application.url') . '/board/' . $board_alias . '/' . $this->id;
	}
	
	// ---------------------------------------------------------------------
	
	public function open()
	{
		return $this->state === 'open';
	}
	
	// ---------------------------------------------------------------------
	
	public function series()
	{
		return $this->belongs_to('series');
	}
	
	// ---------------------------------------------------------------------
	
	public function summary()
	{
		switch ($this->format)
		{
			case 'markdown':
				$markdown = new Markdown();
				$summary = strip_tags($markdown->parse($this->body));
				return e(mb_substr($summary, 0, 140, 'UTF-8'));
				
			default:
				return e(strip_tags(mb_substr($this->body, 0, 140, 'UTF-8')));
		}
	}
	
	// ---------------------------------------------------------------------
	
	public function short_title()
	{
		return strip_tags($this->title);
	}
	
	// ---------------------------------------------------------------------
	
	public function safe_title()
	{
		return strip_tags($this->title);
	}
	
	// ---------------------------------------------------------------------
	
	public function body_html()
	{
		switch ($this->format)
		{
			case 'markdown':
				$markdown = new Markdown();
				$html = $markdown->parse($this->body);
				break;
				
			default:
				$html = HTML::autolink(nl2br($this->body));

		}

		return strip_tags($html, Config::get('miniwini.available_tags'));
	}
	
	// ---------------------------------------------------------------------
	
	public static function preview($body)
	{
		$html = HTML::autolink(nl2br($body));
		return strip_tags($html, Config::get('miniwini.available_tags'));
	}
}