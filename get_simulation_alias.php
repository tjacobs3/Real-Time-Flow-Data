<?php
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

	$file_alias = array(
			"gate798.wsq" => "A",
			"ld2g1.2011.wsq" => "B",
			"LD3G1.wsq" => "C",
			"SB3G3.wsq" => "D",
			"US3G2.wsq" => "E"
		);

echo json_encode($file_alias);
?>	

