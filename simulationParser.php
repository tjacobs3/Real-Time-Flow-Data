<?php
	$locations = Array();
	$data = Array();
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
					$dateTime = $year.'-'.$month.'-'.$day.'-'.$hour;
					break;

				
			}
			$counter = $counter + 1;
		}	

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
		

		//print_r($data['U22']);
	}

	//Get data
	function getData($location)
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

	parseFile();
	getData('U22');
?>
