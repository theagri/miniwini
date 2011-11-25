<?php

class Image_util {
	protected $filepath = NULL;
	protected $info = NULL;

	public function __construct($filepath)
	{
		/*
		Array ( 
		    [0] => int(180)
		    [1] => int(180)
		    [2] => int(3)
		    [3] => string(24) "width="180" height="180""
		    [bits] => int(8)
		    [mime] => string(9) "image/png"
		)
		*/
		$this->filepath = $filepath;
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
	
	public static function make($filepath)
	{
		return new static($filepath);
	}
	
	
	public function resize($size, $savepath)
	{
		$old_x = imageSX($this->image);
		$old_y = imageSY($this->image);
		if ($old_x > $old_y) 
		{
			$thumb_w=$size;
			$thumb_h=$old_y*($size/$old_x);
		}
		if ($old_x < $old_y) 
		{
			$thumb_w=$old_x*($size/$old_y);
			$thumb_h=$size;
		}
		if ($old_x == $old_y) {
			$thumb_w=$size;
			$thumb_h=$size;
		}
		
		$dst = imagecreatetruecolor($thumb_w,$thumb_h);
		
		// define the sharpen matrix
		$sharpen = array(
			array(0.0, -1.0, 0.0),
			array(-1.0, 5.0, -1.0),
			array(0.0, -1.0, 0.0)
		);

		// calculate the sharpen divisor
		$divisor = array_sum(array_map('array_sum', $sharpen));

		
		
		imagecopyresampled($dst,$this->image, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);
		// apply the matrix
		//imageconvolution($dst, $sharpen, $divisor, 0);
		
		imagepng($dst, $savepath);
		return $this;
	}
}