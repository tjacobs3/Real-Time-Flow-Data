<?php
$server = isset($_GET["server"]) ? $_GET["server"] : "localhost";
$username = isset($_GET["username"]) ? $_GET["username"] : "";
$password = isset($_GET["password"]) ? $_GET["password"] : "";
$db_name = isset($_GET["db_name"]) ? $_GET["db_name"] : "";
$port = isset($_GET["port"]) ? $_GET["port"] : "";

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
$fh = fopen($myFile, 'w') or die("can't open file");
$stringData = "<?php\n\$annotation_db_url  = \"".$server."\";	// MySQL database\n";
$stringData .= "\$annotation_db_port =".$port.";                   // MySQL port\n";
$stringData .= "\$annotation_db_user = \"".$username."\";              // MySQL username\n";
$stringData .= "\$annotation_db_pass = \"".$password."\";             // MySQL password\n";
$stringData .= "\$annotation_db_db   = \"".$db_name."\";              // MySQL database\n";
$stringData .= "?>";
fwrite($fh, $stringData);
fclose($fh);
?>
