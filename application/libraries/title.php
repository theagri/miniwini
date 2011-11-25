<?php
/**
 * Title
 *
 * Simple HTML title generator
 *
 * @author		Yunseok Kim <mywizz@gmail.com>
 * @copyright	Copyright (c) 2011, Yunseok Kim
 */

use Laravel\Config;

class Title {
	
	/**
	 * Default delimiter
	 *
	 * @var string
	 */
	const DELIMITER = ' | ';
	
	/**
	 * The titles
	 *
	 * @var array
	 */
	protected static $titles = array();
	
	// ---------------------------------------------------------------------
	
	/**
	 * Add title
	 *
	 * @param  string	$title  
	 * @return void
	 */
	public static function put($title = NULL)
	{
		if ( ! $title OR strlen(trim($title)) === 0) return;
		
		static::$titles[] = trim(strip_tags($title));
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Returns last added title
	 *
	 * @return string
	 */
	public static function last()
	{
		if (count(static::$titles) === 0) return Config::get('title.default_title_when_empty');

		return static::$titles[count(static::$titles) - 1];
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Returns generated title for display
	 *  
	 * @return string
	 */
	public static function get()
	{
		static $default_added = FALSE;
		
		if (count(static::$titles) === 0)
		{
			return Config::get('title.default_title_when_empty', '');
		}
		
		if ($default_added === FALSE)
		{
			if ( ! is_null(Config::get('title.default_title')))
			{
				array_unshift(static::$titles, Config::get('title.default_title'));
			}
			
			$default_added = TRUE;
		}

		return implode(Config::get('title.delimiter', static::DELIMITER), Config::get('title.reverse') === TRUE ? array_reverse(static::$titles) : static::$titles);
	}
}