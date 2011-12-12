<?php
class Redirect extends \Laravel\Redirect {
	public static function back($step = 1)
	{
		if (isset($_SERVER['HTTP_REFERER']))
		{
			return self::to($_SERVER['HTTP_REFERER']);
		}
	}
}