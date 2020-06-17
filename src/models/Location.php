<?php
/**
 * Created by PhpStorm.
 * User: jameshoward
 * Date: 2020-06-16
 * Time: 11:53
 */

class Location
{
	public $x;
	public $y;

	private $valid;

	/**
	 * Location constructor.
	 * @param $data: Uses data in form (x, y) to create the location object
	 */
	public function __construct($data)
	{
		$this->valid = false;
		$length = strlen($data);
		if('(' == ($data[0]) && (')' == $data[$length - 1]))
		{
			$trimmed = trim($data, '()');
			$parts = explode(',', $trimmed);

			if(2 == count($parts))
			{
				if((true == is_numeric($parts[0])) && (true == is_numeric($parts[1])))
				{
					$this->valid = true;
					$this->x = (int)$parts[0];
					$this->y = (int)$parts[1];
				}
			}
		}
	}

	/**
	 * Could we decode a valid location from the supplied data
	 * @return bool
	 */
	public function isValid()
	{
		return $this->valid;
	}
}