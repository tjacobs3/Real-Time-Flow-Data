<?php

	//Global arrays to hold location names and all data related to them
	$locations = Array();
	$data = Array();

	//Remove invalid entries from the location names
	//removes blank entries from the array
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

	//Parses each line of the file
	//Splits it based on the number of spaces (greater than 2)
	//Stores related information about each location in the data array
	function parseLine($line)
	{
		global $locations;
		global $data;
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
	}

	//Returns the data based on the location passed in
	function getSimulationData($location)
	{
		global $data;
		return $data[$location];
	}


	//Get all location names
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
	function parseFile()
	{
		//Open the simulated output file
		$filename = "gate798.wsq";
		$fd = fopen ($filename, "r");
		$contents = fread ($fd,filesize ($filename));
		fclose ($fd);


		//Split contents according to each line
		$delimiter = "\n";
		$splitcontents = explode($delimiter, $contents);

		//Keep track when to start parsing the data
		$startCounter = 0;
		$time;
		$flowVal;
		$elevVal;

		//Go through file and parse each line
		foreach($splitcontents as $val)
		{
			//If line = 1 then get all names
			if($startCounter == 1) {parseNames($val);}
				
			//Start parsing the line if startCounter >= 3
			if($startCounter >= 3)
			{
				parseLine($val);
			}
			$startCounter += 1;
		}

	}
?>
