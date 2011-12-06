<?php
class HTML extends \Laravel\HTML {
	public static function safe_text($str)
	{
		if ( ! function_exists('htmLawed'))
		{
			require __DIR__ . '/lib/htmLawed.php';
		}
		
		$str = strip_tags($str, '<a><b>');
		return htmLawed(nl2br($str));
	}
	
	public static function autolink( $text, $target='_blank', $nofollow=true )
	{
	  // grab anything that looks like a URL...
	  $urls  =  self::_autolink_find_URLS( $text );
	  if( !empty($urls) ) // i.e. there were some URLS found in the text
	  {
	    array_walk( $urls, function ( &$value, $key, $other=null )
		{
		  $target = $nofollow = null;
		  if( is_array($other) )
		  {
		    $target      =  ( $other['target']   ? " target=\"$other[target]\"" : null );
		    // see: http://www.google.com/googleblog/2005/01/preventing-comment-spam.html
		    $nofollow    =  ( $other['nofollow'] ? ' rel="nofollow"'            : null );     
		  }
		  $value = "<a href=\"$key\"$target$nofollow>$key</a>";
		}, array('target'=>$target, 'nofollow'=>$nofollow) );
	    $text  =  strtr( $text, $urls );
	  }
	return $text;
	}

	private static function _autolink_find_URLS( $text )
	{
	  // build the patterns
	  $scheme         =       '(http:\/\/|https:\/\/)';
	  $www            =       'www\.';
	  $ip             =       '\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}';
	  $subdomain      =       '[-a-z0-9_]+\.';
	  $name           =       '[a-z][-a-z0-9]+\.';
	  $tld            =       '[a-z]+(\.[a-z]{2,2})?';
	  $the_rest       =       '\/?[:a-z0-9._\/~#&=;%+?-]+[a-z0-9\/#=?]{1,1}';            
	  $pattern        =       "$scheme?(?(1)($ip|($subdomain)?$name$tld)|($www$name$tld))$the_rest";

	  $pattern        =       '/'.$pattern.'/is';
	  $c              =       preg_match_all( $pattern, $text, $m );
	  unset( $text, $scheme, $www, $ip, $subdomain, $name, $tld, $the_rest, $pattern );
	  if( $c )
	  {
	    return( array_flip($m[0]) );
	  }
	  return( array() );
	}

	
}




