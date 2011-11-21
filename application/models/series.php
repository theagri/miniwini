<?php
class Series extends Blaze {
	public static $table = 'board_series';
	public static $timestamps = TRUE;
	
	// ---------------------------------------------------------------------
	
	public function user()
	{
		return $this->belongs_to('user');
	}
	
	// ---------------------------------------------------------------------
	
	public function posts()
	{
		return $this->has_many('post');
	}
	
	// ---------------------------------------------------------------------
	
	public function link($alias)
	{
		return Config::get('application.url') . '/board/' . $alias . '/series/' . $this->id;
	}
	
	// ---------------------------------------------------------------------
	
	public static function of($board_id, $user_id)
	{
		return self::where_board_id($board_id)->where_user_id($user_id)->order_by('id', 'asc')->get();
	}
	
	// ---------------------------------------------------------------------
	
	public static function count_of($board_id, $user_id)
	{
		return self::where_board_id($board_id)->where_user_id($user_id)->count();
	}
}