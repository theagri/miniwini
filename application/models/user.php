<?php
class User extends Blaze {
	public static $table = 'users';
	
	// ---------------------------------------------------------------------
	
	public function posts()
	{
		return $this->has_many('post');
	}
	
	// ---------------------------------------------------------------------
	
	public function series()
	{
		return $this->has_many('series');
	}
	
	// ---------------------------------------------------------------------
	
	public function link($append = NULL)
	{
		return Config::get('application.url'). '/' . $this->userid . (is_null($append) ? '' : '/' . $append);
	}
	
	// ---------------------------------------------------------------------
	
	public function avatar($class = 'small')
	{
		if ($this->avatar_url)
		{
			$tooltip = $this->name;
			return '<figure data-type="avatar-' . $class. '"><a href="'. $this->link .'" title="' . $tooltip . '"><img alt="' . $this->name . '" src="' . $this->avatar_link($class) . '"></a></figure>';
		}
		
		return '';
	}
	
	public function avatar_link($class)
	{
		return $this->avatar_url;
	}
}