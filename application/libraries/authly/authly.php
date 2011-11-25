<?php namespace Authly;
/**
 * Authly
 *
 * @package    Authly
 * @version    1.0
 * @author     mywizz
 */

use \Config;
use \DB;
use \Request;
use \Input;
use \Session;
use \Validator;

class Authly {
	
	/**
	 * Table for users
	 *
	 * @var  string
	 */
	protected static $table_user;
	
	/**
	 * Table for connections
	 *
	 * @var  string
	 */
	protected static $table_connections;
	
	/**
	 * Unique field
	 *
	 * @var  string
	 */
	protected static $uniq_field;
	
	/**
	 * User data retrieved from database
	 *
	 * @var  string
	 */
	protected static $user = NULL;
	
	// ---------------------------------------------------------------------
	
	/**
	 * Run Authly
	 *
	 * @return  void
	 */
	public static function initialize()
	{
		static::$table_user = Config::get('authly.table_user');
		static::$table_connections = Config::get('authly.table_connections');
		static::$uniq_field = Config::get('authly.uniq_field');
		
		if ( ! is_null($id = Session::get(Config::get('authly.authly_key'))))
		{
			static::$user = DB::table(static::$table_user)
				->where(static::$table_user.'.id', '=', $id)
				->first();
		}
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Checks if user with given id/email exists
	 *
	 * @param   string  
	 * @return  bool
	 */
	public static function exists($id_or_email)
	{
		return ! is_null(DB::table(static::$table_user)->where(static::$uniq_field, '=', $id_or_email)->first());
	}
	
	// ---------------------------------------------------------------------
	
	public static function update($data)
	{
		return DB::table(static::$table_user)->where('id', '=', self::get_id())->update($data);
	}
	
	// ---------------------------------------------------------------------
	
	public static function is($role)
	{
		return static::belongs($role);
	}
	
	
	// ---------------------------------------------------------------------
	
	public static function belongs($param)
	{
		if (! $param OR empty($param))
		{
			return FALSE;
		}

		$my_role = static::get_role();
		$roles = Config::get('authly.roles');


		if ( ! $my_role OR ! in_array($my_role, $roles))
		{
			return FALSE;
		}

		

		$my_idx = array_search($my_role, $roles);

		if ( ! $my_idx)
		{
			$my_idx = 0;
		}
	

		if (substr($param,-1) == '+')
		{
			$check_role = substr($param,0,strlen($param)-1);
			$check_idx = array_search($check_role, $roles);
			return $my_idx >= $check_idx;
		}
		else
		{
			$list = preg_split('/[~,]/', $param, -1, PREG_SPLIT_NO_EMPTY);
			if (count($list) == 1)
			{
				$check_role = $list[0];
				return $my_role == $check_role;
			}
			else
			{
				$list2 = explode(',', $param);
				if (count($list2) > 1)
				{
					return in_array($my_role, $list2);
				}
				else
				{
					$idx1 = array_search($list[0], $roles);
					$idx2 = array_search($list[1], $roles);
					$min = min($idx1, $idx2);
					$max = max($idx1, $idx2);

					return $my_idx >= $min AND $my_idx <= $max;
				}
			}
		}

		return FALSE;
	}
	
	// ---------------------------------------------------------------------
	
	public static function signed()
	{
		return ! is_null(static::$user);
	}
	
	// ---------------------------------------------------------------------
	
	public static function is_reserved($id)
	{
		$list = Config::get('authly.reserved_userids');
		if ( ! $list)
		{
			return FALSE;
		}
		
		foreach (explode(",", $list) as $reserved)
		{
			$reserved = trim($reserved);
			if (strlen($reserved) === 0)
			{
				continue;
			}
			
			if ($reserved == $id)
			{
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Validate registration data
	 *
	 * @param   mixed
	 * @return  TRUE|mixed
	 */
	public static function validate($data)
	{
		$val = Validator::make($data, Config::get('authly.validation_rules'));
		return $val->valid();
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Register user
	 *
	 * @param   mixed
	 * @return  bool
	 */
	public static function register($data)
	{
		$userdata = array();
		
		if (static::validate($data) !== TRUE)
		{
			return FALSE;
		}
		
		$uniq_val = $data[Config::get('authly.uniq_field')];
		if (static::exists($uniq_val))
		{
			return FALSE;
		}
		
		if (static::is_reserved($uniq_val))
		{
			return FALSE;
		}
		
		foreach ($data as $key => $val)
		{
			if ($key == 'password')
			{
				$val = static::encrypt_password($val);
			}
			
			$userdata[$key] = $val;
		}
		
		// activate user account automatically?
		$userdata['is_activated'] = Config::get('authly.activate_on_register') == TRUE ? 1 : 0;
		
		// default role
		$userdata['role'] = Config::get('authly.default_role_on_register');
		
		// time
		$userdata['created_at'] = date('Y-m-d H:i:s');
		$userdata['updated_at'] = date('Y-m-d H:i:s');
		
		// need activation code?
		if ( ! Config::get('authly.activate_on_register'))
		{
			$code = static::generate_activation_code();
			$userdata['activation_code'] = $code;
			
			// put email into session for later use
			Session::flash('activation_code', $code);
		}
		
		$id = DB::table(static::$table_user)->insert_get_id($userdata);
		if ( ! $id)
		{
			return FALSE;
		}
		
		return TRUE;
	}
	
	public static function activate_by_code($code)
	{
		$rec = DB::table(static::$table_user)->where_activation_code($code)->first();
		if ( ! $rec)
		{
			return FALSE;
		}
		
		return DB::table(static::$table_user)->where_id($rec->id)->update(array(
			'is_activated' => 1,
			'activation_code' => NULL
		));
	}
	
	// ---------------------------------------------------------------------
	
	public static function unregister($id)
	{
		
	}
	
	// ---------------------------------------------------------------------
	
	public static function check_password($password)
	{
		require_once __DIR__ . '/lib/PasswordHash.php';
		$hasher = new \PasswordHash(8, FALSE);
		return $hasher->CheckPassword($password, static::get_password());
	}
	
	// ---------------------------------------------------------------------
	
	public static function change_password($password)
	{
		$pwd = static::encrypt_password($password);
		return static::update(array(
			'password' => $pwd
		));
	}
	
	// ---------------------------------------------------------------------
	
	protected static function generate_activation_code()
	{
		require_once __DIR__ . '/lib/PasswordHash.php';
		$hasher = new \PasswordHash(8, FALSE);
		$hash = $hasher->HashPassword(uniqid());
		return $hash;
	}
	
	// ---------------------------------------------------------------------
	
	public static function sign_in($id_or_email, $password)
	{
		$rec = DB::table(static::$table_user)->where(static::$uniq_field, '=', $id_or_email)->first();
		if ( ! $rec)
		{
			return FALSE;
		}
		
		if ( ! $rec->is_activated)
		{
			return FALSE;
		}
		
		require_once __DIR__ . '/lib/PasswordHash.php';
		$hasher = new \PasswordHash(8, FALSE);
		$res = $hasher->CheckPassword($password, $rec->password);
		if ($res === FALSE)
		{
			return FALSE;
		}
		
		Session::put(Config::get('authly.authly_key'), $rec->id);
		return TRUE;
	}
	
	// ---------------------------------------------------------------------
	
	private static function sign_in_with_id($id)
	{
		$res = DB::table(static::$table_user)->where_id($id)->first();
		if ( ! $res)
		{
			return FALSE;
		}
		
		static::$user = $res;
		
		Session::put(Config::get('authly.authly_key'), $res->id);
		return TRUE;
	}
	
	// ---------------------------------------------------------------------
	
	public static function sign_out()
	{
		static::$user = null;
		Session::forget(Config::get('authly.authly_key'));
		return TRUE;
	}
	
	// ---------------------------------------------------------------------
	
	private static function encrypt_password($password)
	{
		require_once __DIR__ . '/lib/PasswordHash.php';
		$hasher = new \PasswordHash(8, FALSE);
		$hash = $hasher->HashPassword($password);
		if (strlen($hash) < 20)
		{
			return NULL;
		}
		
		return $hash;
	}
	
	// ---------------------------------------------------------------------
	
	public static function connections()
	{
		if ( ! static::signed())
		{
			return NULL;
		}
		
		return DB::table(static::$table_connections)->where_user_id(static::get_id())->order_by('id', 'asc')->get();
	}
	
	// ---------------------------------------------------------------------
	
	public static function connect($service, $data = array())
	{
		$module = Factory::create($service, $data);
		return $module->redirect_url();
	}
	
	// ---------------------------------------------------------------------
	
	public static function connected($service, $data = NULL)
	{
		$module = Factory::create($service);
		
		if ( ! is_null($token = Session::get('authly_auth_' . $service)))
		{
			$data = array_merge($token, $data);
			$module->set_token($data);
		}
		$userdata = $module->connect($data);
		
		$result = static::finalize_connection($userdata);

		return TRUE;
	}

	
	// ---------------------------------------------------------------------
	
	private static function finalize_connection($userdata)
	{
		$timestamp = date('Y-m-d H:i:s');
		
		if (static::signed())
		{
			$userdata['user_id'] = static::get_id();
			$userdata['updated_at'] = $timestamp;
			
			$rec = DB::table(static::$table_connections)->where_provider($userdata['provider'])->where_auth_id($userdata['auth_id'])->first();
			if (is_null($rec))
			{
				$userdata['created_at'] = $timestamp;
				DB::table(static::$table_connections)->insert($userdata);
			}
			else
			{
				return DB::table(static::$table_connections)->where_id($rec->id)->update($userdata);
			}
		}
		else
		{
			$rec = DB::table(static::$table_connections)->where_provider($userdata['provider'])->where_auth_id($userdata['auth_id'])->first();
			if (is_null($rec))
			{
				// new user
				$userdata['created_at'] = $timestamp;
				$userdata['updated_at'] = $timestamp;
				
				if ($provider_id = DB::table(static::$table_connections)->insert_get_id($userdata))
				{
					if ($user_id = DB::table(static::$table_user)->insert_get_id(array(
						'role' => Config::get('authly.default_role_on_register'),
						'last_ip' => Request::ip(),
						'last_active_at' => $timestamp,
						'created_at' => $timestamp,
						'updated_at' => $timestamp,
						'provider' => $userdata['provider']
					)))
					{
						DB::table(static::$table_connections)->where_id($provider_id)->update(array('user_id' => $user_id));
						return static::sign_in_with_id($user_id);
					}
				}
			}
			else
			{
				return static::sign_in_with_id($rec->user_id);
			}
		}
	}
	
	// ---------------------------------------------------------------------
	
	public static function __callStatic($method, $arg)
	{
		if (preg_match('/^get_(.+)/', $method, $m))
		{
			if ( ! empty($m[1]))
			{
				$key = $m[1];
				return static::$user ? static::$user->$key : NULL;
			}
		}
		elseif (in_array($method, array('id', 'name', 'email', 'userid')))
		{
			return static::$user ? static::$user->$method : NULL;
		}
	}
}
