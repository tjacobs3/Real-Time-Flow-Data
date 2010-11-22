<?php
/** 
This file parses a given url for data.
The array $columns holds the names of each colum (ie. $column[0] => 'agency_name').
The array $streamData is a 2d array that holds the table data ($streamData[row][columnNumber] so to get the third row agency name use $streamData[2][0].
**/

/**
* Given an array holding tab delimited data, finds the column names
**/
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
* Given an array holding tab delimited data, fills a 2d array with the data
**/
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

function getFileAsArray($url)
{
	$lines = file($url);
	return $lines;
}
?>