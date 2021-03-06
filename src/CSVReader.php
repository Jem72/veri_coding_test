<?php
/**
 * Created by PhpStorm.
 * User: jameshoward
 * Date: 2020-06-15
 * Time: 19:07
 */
include_once(dirname(__FILE__) . '/Log.php');

/**
 * Reads a simple CSV file
 * @property array $fileData Parsed CSV rows in an array of arrays
 * @property bool  $fileLoaded Did the CSV file load correctly
 */
class CSVReader
{
	protected $fileData = null;
	private $fileLoaded = false;

	public function __construct(String $fileName)
	{
		Log::get_instance()->info("Constructing");
		$this->parseFile($fileName);
	}

	/**
	 * Parses the CSV file text and places it in an array of arrays
	 * @param String $fileName
	 */
	protected function parseFile(String $fileName)
	{
		$this->fileLoaded = false;
		$this->fileData = array();
		$file = @fopen($fileName, "r");

		if(FALSE === $file)
		{
			Log::get_instance()->error("Could not open file " . $fileName);
		}

		if(FALSE !== $file)
		{
			Log::get_instance()->info("Opened CSV File " . $fileName);
			$this->fileLoaded = true;

			// Use fgetcsv to read each line of data into an array
			while(false !== ($line = @fgetcsv($file)))
			{
				if(true == is_array($line))
				{
					// Avoid adding empty lines
					if((count($line) > 1) || (null !== $line[0]))
					{
						$trimmed = array();
						foreach($line as $item)
						{
							$trimmed[] = trim($item);
						}
						$this->fileData[] = $trimmed;
					}
				}
			}
			fclose($file);
		}
	}

	/**
	 * Did we manage to load a file
	 * @return bool
	 */
	public function isFileLoaded()
	{
		return $this->fileLoaded;
	}

	/**
	 * Get the number of items in the file
	 * @return int
	 */
	public function getItemCount(): int
	{
		return $this->getLineCount();
	}

	/**
	 * Get the number of lines in the file - equal here
	 * @return int
	 */
	public function getLineCount(): int
	{
		$itemCount = 0;

		if(false == $this->isFileLoaded())
		{
			Log::get_instance()->error("No file loaded");
		}
		else
		{
			$itemCount = count($this->fileData);
		}
		return $itemCount;
	}
}