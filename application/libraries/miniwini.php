<?php
class Miniwini {
	
	public static function mentions($body)
	{

		if (preg_match_all('/(@|\/)(.+?)\1/', $body, $match, PREG_SET_ORDER))
		{
			$mentions = array();
			for ($i = 0; $i < count($match); $i++)
			{
				$m = $match[$i][2];
				$mentions[] = $m;
			}
			
			$users = array_unique($mentions);


			$users = (
				User::where_in('name', $users)->or_where_in('userid', $users)->select(array('id', 'userid', 'name'))->get()
//				User::where_in('userid', $users)->select(array('id', 'userid', 'name'))->get('id')
			);
			return count($users) > 0 ? $users : FALSE;
		}
		
		return FALSE;
	}
	
	// ---------------------------------------------------------------------
	
	public static function sanitized_markdown($body)
	{
		$markdown = new Markdown();
		
		$html = $markdown->parse(($body));
		$html = str_replace(
			array('<pre><code>', '</code></pre>', '<code>', '</code>'),
			array('===PRE_START===', '===PRE_END===', '===CODE_START===', '===CODE_END'),
			$html
		);
		
		$html = static::strip_attributes(strip_tags($html, Config::get('miniwini.available_tags')));
		$html = str_replace(
			
			array('===PRE_START===', '===PRE_END===', '===CODE_START===', '===CODE_END'),
			array('<pre><code>', '</code></pre>', '<code>', '</code>'),
			$html
		);

		return $html;
	}
	
	// ---------------------------------------------------------------------
	
	public static function sanitized_text($body)
	{
		return HTML::autolink(nl2br(strip_tags($body)));
	}
	
	// ---------------------------------------------------------------------
	
	private static function strip_attributes($str) 
	{
	    $reg = '/([^<]*<\s*[a-z](?:[0-9]|[a-z]{0,9}))(?:(?:\s*[a-z\-]{2,14}\s*=\s*(?:"[^"]*"|\'[^\']*\'))*)(\s*\/?>[^<]*)/i';
	    $chunks = preg_split($reg, $str, -1,  PREG_SPLIT_DELIM_CAPTURE);
	    $cnt = count($chunks);
	    $buffer = array();
	    for ($i = 1; $i < $cnt; $i++) {
	        $buffer[] = $chunks[$i];
	    }

	    return implode('', $buffer);
	}
	
	public static function parse_mentions($body, $meta)
	{
		if ( ! $meta)
		{
			return $body;
		}
		
		$meta = json_decode($meta);

		if ( ! empty($meta->mentions))
		{
			for ($i = 0; $i < count($meta->mentions); $i++)
			{
				$mention = $meta->mentions[$i];
				
				$body = str_replace(
					array('@'.$mention->name.'@', '/' . $mention->name .'/'),
					'<a href="'. URL::to($mention->userid) . '">' . $mention->name . '</a>',
					$body);

				$body = str_replace(
					array('@'.$mention->userid.'@', '/' . $mention->userid .'/'),
					'<a href="'. URL::to($mention->userid) . '">' . $mention->userid . '</a>',
					$body);
			}
		}
		return $body;
	}
}