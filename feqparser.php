<?php

	//Variable definitions
	//These will hold the date,time,flow,elevation data
	$U22 = Array();
	$U25XS2501 = Array();
	$U35 = Array();
	$D37 = Array();
	$U39 = Array();
	$D41 = Array();
	$D45 = Array();
	$D52 = Array();
	$D57 = Array();
	$D60 = Array();
	$U69 = Array();
	$D75 = Array();
	$D80 = Array();
	$D83 = Array();
	$U84 = Array();
	$U96 = Array();
	$D97 = Array();
	$U98 = Array();
	$location101 = Array();
	$location104 = Array();
	$location108 = Array();


	//Parses a line of input based on the given format
	//and populate the different global arrays that are to be returned
	//Split based on spaces and add content to array based on index
	function parseLine($input)
	{
		global $U22;	
		global $U25XS2501;
		global $U35;
		global $D37;
		global $U39;
		global $D41;
		global $D45;
		global $D52;
		global $D57;
		global $D60;
		global $U69;
		global $D75;
		global $D80;
		global $D83;
		global $U84;
		global $U96;
		global $D97;
		global $U98;
		global $location101;
		global $location104;
		global $location108;

		$dateTime;

		$delimiter = "/[\s]+/ ";
		$contents = preg_split($delimiter, $input);

		$counter = 0;
		$year = 0;
		$month = 0;
		$day = 0;
		$hour = 0;
		$flow = 0;

		foreach($contents as $val)
		{

			switch($counter){
	
			//Counter == 0 means it is the year value
			case 0: $year = $val;
				break;

			//Counter == 1 means it is the month value
			case 1: $month = $val;
				break;

			//Counter == 2 means it is the month value
			case 2: $day = $val;
				break;

			//Counter == 3 means its is the hour value
			case 3:	$hour = $val;
			$dateTime = $year.'-'.$month.'-'.$day.'-'.$hour;
				break;

			//Counter == 4 means U22 flow
			case 4: $U22[$dateTime]["flow"]  = $val;
				break;
			//Counter == 5 means U22 elevation 
			case 5: $U22[$dateTime]["elevation"] = $val;
				break;

			//Counter == 6 means U25XS2501 flow
			case 6: $U25XS2501[$dateTime]["flow"] = $val;
				break;

			//Counter == 7 means U25XS2501 elevation
			case 7: $U25XS2501[$dateTime]["elevation"] = $val;
				break;

			//Counter == 8 means U35 flow
			case 8: $U35[$dateTime]["flow"] = $val;
				break;
			
			//Counter == 9 means U35 elevation
			case 9: $U35[$dateTime]["elevation"] = $val;
				break;

			//Counter == 10 means D37 flow
			case 10: $D37[$dateTime]["flow"] = $val;
				break;
	
			//Counter == 11 means D37 elevation
			case 11: $D37[$dateTime]["elevation"] = $val;
				break;
			
			//Counter == 12 means U39 flow
			case 12: $U39[$dateTime]["flow"] = $val;
				break;

			//Counter == 13 means U39 elevation
			case 13: $U39[$dateTime]["elevation"] = $val;
				break;		


			//Counter == 14 means D41 flow
			case 14: $D41[$dateTime]["flow"] = $val;
				break;

			//Counter == 15 means D41 elevation
			case 15: $D41[$dateTime]["elevation"] = $val;
				break;


			//Counter == 16 means D45 flow
			case 16: $D45[$dateTime]["flow"] = $val;
				break;

			//Counter == 17 means D45 elevation
			case 17: $D45[$dateTime]["elevation"] = $val;
				break;
				
			//Counter == 18 means D52 flow
			case 18: $D52[$dateTime]["flow"] = $val;
				break;
			
			//Counter == 19 means D52 elevation
			case 19: $D52[$dateTime]["elevation"] = $val;
				break;
		
			//Counter == 20 means D57 flow
			case 20: $D57[$dateTime]["flow"] = $val;
				break;
	
			//Counter == 21 means D57 elevation
			case 21: $D57[$dateTime]["elevation"] = $val;
				break;


			//Counter == 22 means D60 flow
			case 22: $D60[$dateTime]["flow"] = $val;
				break;
	
			//Counter == 23 means D60 elevation
			case 23: $D60[$dateTime]["elevation"] = $val;
				break;
				
			//Counter == 24 means U69 flow
			case 24: $U69[$dateTime]["flow"] = $val;
				break;
			
			//Counter == 25 means U69 elevation 
			case 25: $U69[$dateTime]["elevation"] = $val;
				break;
	
			//Counter == 26 means D75 flow
			case 26: $D75[$dateTime]["flow"] = $val;
				break;
	
			//Counter == 27 means D75 elevation
			case 27: $D75[$dateTime]["elevation"] = $val;
				break;

			//Counter == 28 means D80 flow
			case 28: $D80[$dateTime]["flow"] = $val;
				break;

			//Counter == 29 means D80 elevation
			case 29: $D80[$dateTime]["elevation"] = $val;
				break;				

			//Counter == 30 means D83 flow
			case 30: $D83[$dateTime]["flow"] = $val;
				break;
	
			//Counter == 31 means D83 elevation
			case 31: $D83[$dateTime]["elevation"] = $val;
				break;

			//Counter == 32 means U84 flow
			case 32: $U84[$dateTime]["flow"] = $val;
				break;

                        //Counter == 33 means U84 elevation
                        case 33: $U84[$dateTime]["elevation"] = $val;
                                break;

                        //Counter == 34 means U96 flow
                        case 34: $U96[$dateTime]["flow"] = $val;
                                break;

                        //Counter == 35 means U96 elevation
                        case 35: $U96[$dateTime]["elevation"] = $val;
                                break;

                        //Counter == 36 means D97 flow
                        case 36: $D97[$dateTime]["flow"] = $val;
                                break;

                        //Counter == 37 means D97 elevation
                        case 37: $D97[$dateTime]["elevation"] = $val;
                                break;

                        //Counter == 38 means U98 flow
                        case 38: $U98[$dateTime]["flow"] = $val;
                                break;

                        //Counter == 39 means U98 elevation
                        case 39: $U98[$dateTime]["elevation"] = $val;
                                break;

                        //Counter == 40 means location101 flow
                        case 40: $location101[$dateTime]["flow"] = $val;
                                break;

                        //Counter == 41 means location101 elevation
                        case 41: $location101[$dateTime]["elevation"] = $val;
                                break;

                        //Counter == 42 means location104 flow
                        case 42: $location104[$dateTime]["flow"] = $val;
                                break;

                        //Counter == 43 means location104 elevation
                        case 43: $location104[$dateTime]["elevation"] = $val;
                                break;

                        //Counter == 44 means location108 flow
                        case 44: $location108[$dateTime]["flow"] = $val;
                                break;

                        //Counter == 45 means location108 elevation
                        case 45: $location108[$dateTime]["elevation"] = $val;
                                break;
			}


			$counter += 1;
		}


	}

        //Return all location108 data
        function getlocation108()
        {
            global $location108;
            return $location108;
        }

        //Return all location104 data
        function getlocation104()
        {
            global $location104;
            return $location104;
        }

        //Return all location101 data
        function getlocation101()
        {
            global $location101;
            return $location101;
        }

        //Return all U98 data
	function getU98()
	{
		global $U98;
		return $U98;
	}


        //Return all D97 data
	function getD97()
	{
		global $D97;
		return $D97;
	}

         //Return all U96 data
	function getU96()
	{
		global $U96;
		return $U96;
	}

        //Return all U84 data
	function getU84()
	{
		global $U84;
		return $U84;
	}

	//Return all D83 data
	function getD83()
	{
		global $D83;
		return $D83;
	}


	//Return all D80 data
	function getD80()
	{
		global $D80;
		return $D80;
	}

	//Return all D75 data
	function getD75()
	{
		global $D75;
		return $D75;
	}

	//Return all U69 data
	function getU69()
	{
		global $U69;
		return $U69;
	}

	//Return all D60 data
	function getD60()
	{
		global $D60;
		return $D60;
	}
	

	//Return all D57 data
	function getD57()
	{
		global $D57;
		return $D57;
	}

	//Return all D52 data
	function getD52()
	{
		global $D52;
		return $D52;
	}

	//Return all D45 data
	function getD45()
	{
		global $D45;
		return $D45;
	}


	//Return all D41 data
	function getD41()
	{
		global $D41;
		return $D41;
	}

	//Return all U39 data
	function getU39()
	{
		global $U39;
		return $U39;
	}

	//Return all U22 data
	function getU22()
	{
		global $U22;
		return $U22;
	}	

	//Return all U25XS2501 data
	function getU25XS2501()
	{
		global $U25XS2501;
		return $U25XS2501;
	}

	//Return all U35 data
	function getU35()
	{
		global $U35;
		return $U35;
	}	

	//Return all D37 data
	function getD37()
	{
		global $D37;
		return $D37;
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
			//Start parsing the line if startCounter >= 3
			if($startCounter >= 3)
			{
				parseLine($val);
			}
			$startCounter += 1;
		}
	}

?>
