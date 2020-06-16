<?php
/**
 * Created by PhpStorm.
 * User: jameshoward
 * Date: 2020-06-16
 * Time: 10:38
 */

include_once __DIR__ . '/CSVReader.php';

class ObjectCSVReader extends CSVReader
{
	private $objectFields = null;
	private $objectData = array();

	public function __construct(String $fileName)
	{
		parent::__construct($fileName);
	}

	public function getFieldNames()
	{
		return $this->objectFields;
	}

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
		}
	}


	private function parseItem($fileItem)
	{
		$parsedItem = null;
		if((null != $fileItem) && (true === is_array($fileItem)))
		{
			$parsedItem = new stdClass();
			$columnCount = count($fileItem);
			$fieldCount = count($this->objectFields);

			if($columnCount === $fieldCount)
			{
				foreach($this->objectFields as $index => $objectField)
				{
					$parsedItem->$objectField = $fileItem[$index];
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