<?php
class Post extends Blaze {
	public static $table = 'board_posts';
	public static $timestamps = TRUE;
	public static $validations = array(
		'board_id' => 'required|integer',
		'user_id' => 'required|integer',
		'title' => 'required',
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
	
	public function series()
	{
		return $this->belongs_to('series');
	}
	
	// ---------------------------------------------------------------------
	
	public function summary()
	{
		$summary = mb_substr($this->body, 0, 120, 'UTF-8');
		return e(strip_tags($summary));
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
		$html = nl2br(strip_tags($this->body, '<b><i>'));
		return HTML::autolink($html);
	}
}