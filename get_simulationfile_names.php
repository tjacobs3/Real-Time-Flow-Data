<?php
/**
 * Get Simulation File Names
 * 
 * Checks for the files located in the simulationfiles directory
 * and outputs a json object containing those names
 */

header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

if ($handle = opendir('simulationfiles/')) {
	$files = array();
	$i = 0;
	/* This is the correct way to loop over the directory. */
	while (false !== ($file = readdir($handle))) {
	 if ($file != "." && $file != "..") {
		$files[$i] = $file;
		$i++;
	 }
	}
	echo json_encode($files);
	closedir($handle);
}
?>

