<?php
/**
 * Created by PhpStorm.
 * User: jameshoward
 * Date: 2020-06-16
 * Time: 11:43
 */

/** @noinspection PhpIncludeInspection */
include_once(dirname(__FILE__) . '/Generic.php');
include_once(dirname(__FILE__) . '/Location.php');


/**
 * Class Workplace - stores a single workplace record
 *
 * @property int      $id: The ID of the record
 * @property string   $name: The name of the record
 * @property Location $location: The location of the record
 */
class Workplace extends Generic
{
	private static $source_keys = array('id', 'name', 'location');

	public function __construct($arrayData)
	{
		parent::__construct();
		$complete = $this->verifyComplete($arrayData, self::$source_keys);

		if(true == $complete)
		{
			$this->parseBasicRecordData($arrayData);
			$this->validate();
		}
	}
}