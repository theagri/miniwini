<?php
class Visitor extends Blaze {
	public static $table = 'sessions';
	
	// ---------------------------------------------------------------------
	
	public static function all($within_min = 30)
	{
		$sessions = self::get();
		$users = array();
		$guest_count = 0;
		foreach ($sessions as $sess)
		{
			$data = unserialize($sess->data);
			if ((time() - $sess->last_activity) > $within_min * 60)
			{
				continue;
			}
			
			if ( ! empty($data['authly_key']))
			{
				$users[] = $data['authly_key'];
			}
			else
			{
				$guest_count += 1;
			}
		}
		
		if (count($users))
		{
			return array(
				'users' => User::where_in('id', array_unique($users))->get(),
				'guest_count' => $guest_count
			);
		}
		
		return array(
			'users' => array(),
			'guest_count' => $guest_count
		);
	}
}