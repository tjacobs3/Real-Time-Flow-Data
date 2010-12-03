<?php
 
	include("Mobile_Detect.php");
	$detect = new Mobile_Detect();
	
	if($detect->isMobile())
	{
		echo "Mobile";
	}
	else
	{
		echo "Computer";
	}
   
 
?>

