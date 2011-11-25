<?php
class Blaze extends Eloquent {
	
	public static $validations = NULL;
	
	public $errors = NULL;
	
	// ---------------------------------------------------------------------
	
	public function valid()
	{
		return is_null($this->errors);
	}
	
	// ---------------------------------------------------------------------
	
	public function inject($attrs)
	{
		$tmp = DB::table(static::$table)->first();
		$attributes = array();
		foreach ($tmp as $k => $v)
		{
			$attributes[] = $k;
		}
		
		foreach ($attrs as $key => $val)
		{
			if ( ! in_array($key, $attributes))
			{
				unset($attrs[$key]);
				continue;
			}
			
			if (is_array($val))
			{
				$attrs[$key] = implode(',', $val);
			}
		}
		
		return $this->fill($attrs);
	}
	
	// ---------------------------------------------------------------------
	
	/**
	 * Save the model to the database.
	 *
	 * @return bool
	 */
	public function save()
	{
		if (method_exists($this, 'before'))
		{
			call_user_func(array($this, 'before'));
		}
		
		// Do we have validation rules for this model?
		if (isset(static::$validations) and ! empty(static::$validations))
		{
			$val = Validator::make($this->attributes, static::$validations);
			
			if ( ! $val->valid())
			{
				$this->errors = $val->errors;
				return FALSE;
			}
		}
		

		$result = parent::save();
		
		if (method_exists(get_called_class(), 'after'))
		{
			call_user_func(array($this, 'after'));
		}
		
		return $result;
	}
	
	// ---------------------------------------------------------------------
	
	public function up($field, $count = 1)
	{
		$this->{$field} = $this->{$field} + $count;
		$this->save();
	}
	
	// ---------------------------------------------------------------------
	
	public function down($field, $count = 1)
	{
		$this->{$field} = $this->{$field} - abs($count);
		$this->save();
	}
}