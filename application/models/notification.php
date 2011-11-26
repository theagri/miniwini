<?php
class Notification extends Blaze {
	public static $table = 'notifications';
	
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
	
	// ---------------------------------------------------------------------
	
	public static function put($data)
	{
		$ts = time();
		$rec = static::where_user_id($data['user_id'])->first();
		if ( ! $rec)
		{
			$rec = new static();
			$rec->user_id = $data['user_id'];
		}
		
		$curr = $rec->data ? json_decode($rec->data) : array();

		array_unshift($curr, $data);
		
		$curr = array_slice($curr, 0, 10);
		
		$rec->data = json_encode($curr);
		$rec->last_updated_at = time();
		$rec->new_count = count($curr);
		
		return $rec->save();
	}
	
	// ---------------------------------------------------------------------
	
	public static function read($user_id, $timestamp)
	{
		$rec = static::where_user_id($user_id)->first();
		if ($rec)
		{
			$rec->last_read_at = $timestamp;
			$curr = $rec->data ? json_decode($rec->data) : array();
			$remain = array();
			if ( ! empty($curr))
			{

				for ($i = 0; $i < count($curr); $i++)
				{
					$r = $curr[$i];
					if ($r->created_at <= intval($timestamp))
					{
						continue;
					}
					
					$remain[] = $r;
				}
				
				$remain = array_slice($remain, 0, 10);
				$rec->data = json_encode($remain);
				$rec->new_count = count($remain);
			}
			
			
			return $rec->save();
		}
	}
	
	// ---------------------------------------------------------------------
	
	public static function of($user_id)
	{
		$rec = static::where_user_id($user_id)->first();
		return $rec->data;
		if (is_null($rec))
		{
			return NULL;
		}
		
		return ($rec->data ? $rec->data : null);
	}
	
	// ---------------------------------------------------------------------
	
	public static function count_of($user_id)
	{
		$rec = static::where_user_id($user_id)->first();
		return json_encode(array(
			'count' => $rec->new_count,
			'last_updated_at' => $rec->last_updated_at
		));
	}
}