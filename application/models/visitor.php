<?php
class Visitor extends Blaze {
	public static $table = 'sessions';
	
	// ---------------------------------------------------------------------
	
	public static function all()
	{
		$sessions = self::get();
		$ids = array();
		foreach ($sessions as $sess)
		{
			$data = unserialize($sess->data);
			if ( ! empty($data['authly_key']))
			{
				$ids[] = $data['authly_key'];
			}
		}
		
		if (count($ids))
		{
			return User::where_in('id', array_unique($ids))->get();
		}
		
		return array();
	}
}