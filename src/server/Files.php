<?php

class Files
{
    public static function createPathIfNoExistis($dirName)
	{
		if(!file_exists($dirName))
			{
				$path = '';
				foreach(explode('/', $dirName) as $piece)
				{
					$path.= "{$piece}/";
					if(!file_exists($path))
						mkdir($path, 0777, true);
				}
			}
    }
    
    public static function saveDataStructure($filename, array $dataStructure)
    {
		$fileContent = json_encode($dataStructure, JSON_PRETTY_PRINT);

		if(!file_exists($filename))
            self::createPathIfNoExistis(dirname($filename));
        else
            unlink($filename);

		$fileHandler = fopen($filename, 'a');
		if($fileHandler)
		{
			fwrite($fileHandler, $fileContent, strlen($fileContent));
			fclose($fileHandler);

			return true;
		}
		
		return false;
		
	}
	
	public static function readDataStructure($filename)
	{
        $string = '';

        if(!file_exists($filename))
            return array();

        $fileHandler = fopen($filename, 'r');

        if($fileHandler)
        {
            $string = fread($fileHandler, filesize($filename));
            fclose($fileHandler);

            return ($string ? json_decode($string, true) : array());
        }
		
	}
}