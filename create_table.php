<?php
/**
 * This file contains functionality to create a database based on information submitted by the user
 * Based on the POST data it creates a annotation table and creates an annotations_settings file
 * It uses a simple MySQL query to create the database and writes the information contained in the
 * POST data to the annotations_settings file
 */

$server = isset($_POST["server"]) ? $_POST["server"] : "localhost";
$username = isset($_POST["username"]) ? $_POST["username"] : "";
$password = isset($_POST["password"]) ? $_POST["password"] : "";
$db_name = isset($_POST["db_name"]) ? $_POST["db_name"] : "";
$port = isset($_POST["port"]) ? $_POST["port"] : "";

$link = mysql_connect($server, $username, $password) or die(mysql_error());

mysql_select_db($db_name) or die(mysql_error());

$drop_query = "DROP TABLE IF EXISTS `Annotation`";
$create_query = "CREATE TABLE IF NOT EXISTS `Annotation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `location` varchar(255) NOT NULL,
  `chart_type` varchar(255) NOT NULL,
  `series_name` varchar(255) NOT NULL,
  `time` bigint(255) NOT NULL,
  `annotation` text NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=59 ;";


$result = mysql_query($drop_query);
	if (!$result) {
    	die('Invalid query: ' . mysql_error());
	}
	
$result = mysql_query($create_query);
	if (!$result) {
    	die('Invalid query: ' . mysql_error());
	}



$myFile = "annotation_settings.php";
$fh = fopen($myFile, 'w') or die("Can't open file. Please give create_table.php permission to write to the disk.");
$stringData = "<?php\n\$annotation_db_url  = \"".$server."\";	    // MySQL database\n";
$stringData .= "\$annotation_db_port =".$port.";                   // MySQL port\n";
$stringData .= "\$annotation_db_user = \"".$username."\";              // MySQL username\n";
$stringData .= "\$annotation_db_pass = \"".$password."\";             // MySQL password\n";
$stringData .= "\$annotation_db_db   = \"".$db_name."\";              // MySQL database\n";
$stringData .= "?>";
fwrite($fh, $stringData);
fclose($fh);
echo "Set up succeeded. Please delete setup.php and create_table.php.";
?>
