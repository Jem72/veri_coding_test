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
	public function __construct(?string $data)
	{
		$this->valid = false;
		if(null != $data)
		{
			$length = strlen($data);
			if($length > 0)
			{
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

	/**
	 * Returns the distance between the locaton and another
	 * @param Location $location
	 * @return float
	 */
	public function distanceTo(Location $location): float
	{
		$distance = 0.0;

		if(($this->isValid()) && ($location->isValid()))
		{
			$distance_x = $this->x - $location->x;
			$distance_y = $this->y - $location->y;
			$distance = sqrt(pow($distance_x, 2) + pow($distance_y, 2));
		}
		return $distance;
	}
}