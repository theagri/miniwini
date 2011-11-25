<?php
class Time {
	public static function to_html($date, $display_format = NULL, $is_pubdate = FALSE)
	{
		$timestamp = strtotime($date);
		$display = is_null($display_format) ? $date : date($display_format);
		return '<time datetime="' . date('c', $timestamp) .'"'.($is_pubdate ? ' pubdate' : '').'>' . $display . '</time>';
	}
	
	public static function is_today($date)
	{
		$timestamp = strtotime($date);
		return date('Y-m-d', $timestamp) === date('Y-m-d');
	}
	
	public static function is_yesterday($date)
	{
		$ts = strtotime($date);
		$s = mktime(0,0,0,date('n'),date('j') - 1,date('Y'));
		$e = mktime(23,59,59,date('n'),date('j') - 1,date('Y'));
		return $ts >= $s && $ts <= $e;
	}
	
	
	public static function humanized_html($date, $is_pubdate = FALSE)
	{
		$timestamp = strtotime($date);
		$str = self::humanized($date);
		return '<time datetime="' . date('c', $timestamp) .'"'.($is_pubdate ? ' pubdate' : '').'>' . $str . '</time>';
	}
	
	public static function humanized($str)
	{
		$ts = strtotime($str);
		if (self::is_today($str))
		{
			return sprintf('오늘 %s %s시 %s분',
				date('H',$ts) >= 12 ? '오후':'오전',
				date('g',$ts),
				(int)date('i',$ts)
			);
		}
		else if (self::is_yesterday($str))
		{
			return sprintf('어제 %s %s시 %s분',
				date('H',$ts) >= 12 ? '오후':'오전',
				date('g',$ts),
				(int)date('i',$ts)
			);
		}

		return sprintf('%s월 %s일 %s %s시 %s분',
				date('n',$ts),
				date('j',$ts),
				date('H',$ts) >= 12 ? '오후':'오전',
				date('g',$ts),
				(int)date('i',$ts)
			);
	}
}