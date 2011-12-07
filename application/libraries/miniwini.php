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
}