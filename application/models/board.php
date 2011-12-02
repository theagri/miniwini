<?php
class Board extends Blaze {
	public static $table = 'boards';
	
	// ---------------------------------------------------------------------
	
	public function posts()
	{
		return $this->has_many('post');
	}
	
	// ---------------------------------------------------------------------
	
	public function link($append = NULL)
	{
		return Config::get('application.url') . '/board/' . $this->alias . (is_null($append) ? '' : '/' . $append);
	}
	
	// ---------------------------------------------------------------------
	
	public function series()
	{
		return $this->has_many('series');
	}
	
	// ---------------------------------------------------------------------
	
	public function draft_count($user_id)
	{
		return Post::where_board_id($this->id)->where_user_id($user_id)->where_in('state', array('draft', 'unpublished'))->count();
	}
	
	// ---------------------------------------------------------------------
	
	public static function aliased($alias)
	{
		return self::where_alias($alias)->first();
	}
	
	// ---------------------------------------------------------------------
	
	public function open()
	{
		return $this->state === 'open';
		
	}
	
	public function closed()
	{
		return $this->state === 'closed';
	}
	
	// ---------------------------------------------------------------------
	
	public function locked()
	{
		return $this->state === 'locked';
	}
	
	// ---------------------------------------------------------------------
	
	public function series_enabled()
	{
		return $this->enable_series == 1;
	}
}