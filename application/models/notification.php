<?php
class Notification extends Blaze {
	public static $table = 'notifications';
	public static $timestamps = TRUE;
	
	// ---------------------------------------------------------------------
	
	public static function exists()
	{
		return Session::get('notification');
	}
	
	// ---------------------------------------------------------------------
	
	public static function get()
	{
		return Session::get('notification');
	}
}