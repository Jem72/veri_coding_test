<?php
/**
 * Created by PhpStorm.
 * User: jameshoward
 * Date: 2020-06-16
 * Time: 10:38
 */

/** @noinspection PhpIncludeInspection */
include_once(dirname(__FILE__) . '/CSVReader.php');

/**
 * Reads a CSV File with field names in the first line. Uses the base CSV reader to read the raw data and then parses
 * each line into a key-value array describing the item.
 *
 * @property array $objectFields - the field names
 * @property array $objectData - array of key-value arrays containing data
 * @property bool $valid - is the data valid
 */
class ObjectCSVReader extends CSVReader
{
	private $objectFields = null;
	private $objectData = array();
	private $valid = false;

	public function __construct(String $fileName)
	{
		parent::__construct($fileName);
	}

	public function getFieldNames()
	{
		return $this->objectFields;
	}

	/**
	 * Gets an item by its index
	 * @param int $index
	 * @return array
	 */
	public function getItem(int $index): ?array
	{
		$response = null;
		if($index < count($this->objectData))
		{
			$response = $this->objectData[$index];
		}
		return $response;
	}

	/**
	 * Is the data file valid. At present it is not valid if there are no data in the file
	 * @return bool
	 */
	public function isValid(): bool
	{
		return $this->valid;
	}

	/**
	 * Parses the file data as described
	 * @param String $fileName
	 */
	protected function parseFile(String $fileName)
	{
		$this->objectData = array();
		parent::parseFile($fileName);

		if(count($this->fileData) > 1)
		{
			$this->objectFields = $this->fileData[0];
		}

		if(null !== $this->objectFields)
		{
			for($index = 1; $index < $this->getLineCount(); $index++)
			{
				$object = $this->parseItem($this->fileData[$index]);
				if(null !== $object)
				{
					$this->objectData[] = $object;
				}
			}

			if(count($this->objectData) > 0)
			{
				$this->valid = true;
			}
		}
	}


	/**
	 * Parses a single item in the file. We assume that each record has the correct number of columns
	 * @param $fileItem
	 * @return array|null
	 */
	private function parseItem($fileItem): ?array
	{
		$parsedItem = null;
		if((null != $fileItem) && (true === is_array($fileItem)))
		{
			$parsedItem = array();
			$columnCount = count($fileItem);
			$fieldCount = count($this->objectFields);

			if($columnCount === $fieldCount)
			{
				foreach($this->objectFields as $index => $objectField)
				{
					$parsedItem[$objectField] = $fileItem[$index];
				}
			}
			else
			{
				Log::get_instance()->error("Item not fully described");
			}
		}
		return $parsedItem;
	}

	public function getItemCount(): int
	{
		return count($this->objectData);
	}
}