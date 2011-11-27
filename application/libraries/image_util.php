<?php

class Image_util {

	protected $info = NULL;

	public function __construct($filepath)
	{
		$info = getimagesize($filepath);
		$type = NULL;
		
		switch ($info['mime'])
		{
			case 'image/gif':
				$this->image = imagecreatefromgif($filepath);
				break;
				
			case 'image/png':
				$this->image = imagecreatefrompng($filepath);
				break;
				
			case 'image/jpeg':
				$this->image = imagecreatefromjpeg($filepath);
				break;
		}
		
		if ( ! $this->image)
		{
			throw new Exception('Not supported');
		}
	}
	
	// ---------------------------------------------------------------------
	
	public static function make($filepath)
	{
		return new static($filepath);
	}
	
	// ---------------------------------------------------------------------
	
	public function resize($size, $savepath)
	{
		$old_x = imageSX($this->image);
		$old_y = imageSY($this->image);
		if ($old_x > $old_y) 
		{
			$thumb_w = $size;
			$thumb_h = $old_y*($size/$old_x);
		}
		if ($old_x < $old_y) 
		{
			$thumb_w = $old_x*($size/$old_y);
			$thumb_h = $size;
		}
		if ($old_x == $old_y) {
			$thumb_w = $size;
			$thumb_h = $size;
		}
		
		$dst = imagecreatetruecolor($thumb_w,$thumb_h);
		imagecopyresampled($dst,$this->image, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);
		imagepng($dst, $savepath);
		return $this;
	}
}