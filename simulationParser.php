<?php
/**
 * Simulation Parser
 * 
 * This file contains the functionality for reading an FEQ file
 */
 
	//Global arrays to hold location names and all data related to them
	/** 
	 * @global $locations used to store locations in an array
	 * @global $data used to store all parsed data
	 * @global $range used to store the range for which data is being requested
	$locations = Array();
	$data = Array();
	$range;

	/**
	 * Clear Invalid Entries
	 * 
	 * Remove invalid and blank entries from the location names global variable.
	 * @param $count The total count of the number of locations detected
	 */
	function clearInvalidEntries($count)
	{
		global $locations;
		for($i = 0; $i < $count; $i++)
		{
			if($locations[$i] == '')
			{
				unset($locations[$i]);		
			}
		}
	}

	/**
	 * Parse Line
	 * Parses each line of the file
	 * Splits it based on the number of spaces (greater than 2)
	 * Stores related information about each location in the global data array
	 * @param array $line Array holding the tab delimited data where each array value is a string representing one line of data
	 * @return boolean true if successfully parsed the given line.  
	 */
	function parseLine($line)
	{
		global $locations;
		global $data;
		global $range;
		$delimiter = "/[\s]+/ ";
		$contents = preg_split($delimiter, $line);
		
		$counter = 0;
		$year = 0;
		$month = 0;
		$day = 0;
		$hour = 0;
		$flow = 0;

		while($counter <= 3)
		{

			switch($counter){

				//Counter == 0 means it is the year value
					case 0: $year = $contents[$counter];
					break;

				//Counter == 1 means it is the month value
				case 1: $month = $contents[$counter];
					break;

				//Counter == 2 means it is the month value
				case 2: $day = $contents[$counter];
					break;

				//Counter == 3 means its is the hour value
				case 3:	$hour = $contents[$counter];
					$hour = explode(".", $hour);
					$dateTime = $year.'-'.$month.'-'.$day.' '.$hour[0].':'.($hours[1]*.6) ;
					$tmpDate = strtotime($dateTime); 
					$today = strtotime (date("Y-m-d"));
					if($today - $tmpDate > $range) 
					{
						return false;
					}
					break;

				
			}
			$counter = $counter + 1;
		}	

		//Store location data for each location name
		foreach($locations as $place)
		{
			if($contents[$counter] != NULL)
			{
				$data[$place][$dateTime]['flow'] = $contents[$counter];
				$counter++;
				$data[$place][$dateTime]['elevation'] = $contents[$counter];
				$counter++;
			}
		}
		return true;
	}

	/**
	 * Get Simulation Data
	 * Returns the data based on the location passed in.
	 * Global variable data must be set beforehand by calling parseFile
	 * @param string $location Location to get simulated data for
	 * @return Array simulated data for location.  
	 */
	function getSimulationData($location)
	{
		global $data;
		return $data[$location];
	}

	/**
	 * Parse Names
	 * Gets the locations that the FEQ file contains.  This is done by
	 * passing in the string that represents the line of the FEQ file that 
	 * contains the names.  Does not return a value, instead it sets the 
	 * $locations global
	 * @param string $val line from the FEQ file that contains the locations
	 */
	function parseNames($val)
	{
		$delimiter = "/[\s]{2,}+/ ";
		$contents = preg_split($delimiter, $val);

		global $locations;
		$count = 0;
		foreach($contents as $val)
		{
			$locations[$count] = $val;
			$count = $count + 1;
		}
		clearInvalidEntries($count);
	}

	//Parses a given Simulated data flat file according to a fixed file format
	/**
	 * Parse File 
	 * Parses an FEQ file for data within the past $timePeriod
	 * Sets the global variable $data
	 * @param string $fileLocation file which contains simulated data
	 * @param int $timePeriod number of days to look for.
	 */
	function parseFile($fileLocation, $timePeriod)
	{
		//Set the range
		global $range;	
		$range = $timePeriod* 86400;
		//Open the simulated output file
		$filename = $fileLocation;
		$fd = fopen ($filename, "r");
		$contents = fread ($fd,filesize ($filename));
		fclose ($fd);


		//Split contents according to each line
		$delimiter = "\n";
		$splitcontents = explode($delimiter, $contents);

		//Keep track when to start parsing the data
		$startCounter = count($splitcontents);
		$time;
		$flowVal;
		$elevVal;

		parseNames($splitcontents[1]);
		//Go through file and parse each line
		for($i = $startCounter; $i >= 0; $i--)
		{
			//If line = 1 then get all names
			//if($startCounter == 1) {parseNames($val);}
				
			//Start parsing the line if startCounter >= 3
			if($startCounter >= 3)
			{
				if(!empty($splitcontents[$i]))
				{
					if(parseLine($splitcontents[$i]) == false)
						break;
				}
			}
			$startCounter -= 1;
		}

	}
?>
