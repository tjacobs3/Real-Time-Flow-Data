<?php
/** 
* Observed Data Parser
*
* This file parses a given url for data.
* The array $columns holds the names of each colum (ie. $column[0] => 'agency_name').
* The array $streamData is a 2d array that holds the table data ($streamData[row][columnNumber] so to get the third row agency name use $streamData[2][0].
*/

/**
 * Get Column Names
 * Given an array holding tab delimited data from the USGS servers, finds the column names
 * @param array $lines Array holding the tab delimited data where each array value is a string representing one line of data
 * @return array Array containing the names of each column.  
 */
function getColumnNames($lines)
{
	$counter = 0;
	$columns = array();
	foreach($lines as $line)
	{
		$comment = false;
		$colCount = 0;
		$lineData = explode("\t", $line);
		foreach($lineData as $data)
		{
			if(strpos($data, "#") !== false)
			{
				$comment = true;
				break;
			}
			else if($counter == 0)
			{
				$columns[$colCount] = $data;
			}
			$colCount++;
		}
		if($counter > 0) return $columns;
		if(!$comment)
		{
			$counter++;
		}
	}
	return $columns;
}

/**
 * Get Data
 * Given an array holding tab delimited data, fills a 2d array with the data
 * @param array $ines Array holding the tab delimited data where each array value is a string representing one line of data
 * @return array Array containing the data.  
 */
function getData($lines)
{
	$counter = 0;
	$streamData = array();
	foreach($lines as $line)
	{
		$comment = false;
		$colCount = 0;
		$lineData = explode("\t", $line);
		foreach($lineData as $data)
		{
			if(strpos($data, "#") !== false)
			{
				$comment = true;
				break;
			}
			else if($counter > 1)
			{
				$streamData[$counter-1][$colCount] = $data;
			}
			$colCount++;
		}
		if(!$comment)
		{
			$counter++;
		}
	}
	return $streamData;
}

/**
 * Get File as array
 * Downloads the given url into an array where each value in the array corresponds to one line in the text downloaded.
 * @param string $ines URL of the data to download
 * @return array Array containing the data.  
 */
function getFileAsArray($url)
{
	$lines = file($url);
	return $lines;
}
?>