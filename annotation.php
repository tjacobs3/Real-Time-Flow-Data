<?php
/**
 * Annotation.php
 * 
 * This file contains functionality for getting and setting annotations
 * and interacting with the annotation database.
 * @author Chengyin Liu
 */
 
 
 /**
 * Call DataBase
 * Attempts to connect to the database and perform a query
 * @param string $query Database query to return
 * @return resource Query result 
 */
function callDB($query) {
  include_once("annotation_settings.php");
  
  $link = mysql_connect($annotation_db_url, $annotation_db_user, $annotation_db_pass) 
    or die(mysql_error());
      
  mysql_select_db($annotation_db_db) or die(mysql_error());
  
  return mysql_query($query);
}

/**
 * Serve annotations as JSON
 * Fetch annotations from the server and output it as a JSON file. Parameters are read from the HTTP Request.
 * @return integer
 */
function get_annotations() {
  $location    = addslashes($_GET["location"]);
  $chart_type  = addslashes($_GET["chartType"]);
  $series_name = addslashes($_GET["seriesName"]);
  
  $query = "SELECT * FROM `Annotation` WHERE location = '" . $location . "' AND chart_type ='" . $chart_type . "' AND series_name='" . $series_name . "' ORDER BY `Annotation`.`time` ASC";
  
  $result = callDB($query);
  $a = array();  

  while ($r = mysql_fetch_assoc($result)) {
    array_push($a, array('location'   => $location, 
                         'seriesName' => $series_name,
                         'timestamp'  => $r['time'],
                         'content'    => $r['annotation']));
  }
  
  header('Cache-Control: no-cache, must-revalidate');
  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
  header('Content-type: application/json');
  
  echo json_encode($a);
}

/**
 * Add annotations to the database
 * Add annotations to the database. Parameters are read from the HTTP Request.
 * @return integer 
 */
function add_annotations() {
  $location     = addslashes($_GET["location"]);
  $chart_type   = addslashes($_GET["chartType"]);
  $series_name  = addslashes($_GET["seriesName"]);
  $time         = addslashes($_GET["timestamp"]);
  $annotation   = addslashes($_GET["content"]);
  
  $query = "INSERT INTO Annotation (location, chart_type, series_name, time, annotation) VALUES ('" . $location . "','" . $chart_type . "','" . $series_name . "','" . $time . "','" . $annotation . "');";
  
  callDB($query);
}

/**
 * Delete annotations from the database
 * Delete annotations from the database. Parameters are read from the HTTP Request.
 * @return integer 
 */
function delete_annotation() {
  $location     = addslashes($_GET["location"]);
  $chart_type   = addslashes($_GET["chartType"]);
  $series_name  = addslashes($_GET["seriesName"]);
  $time         = addslashes($_GET["timestamp"]);
  $annotation   = addslashes($_GET["content"]); 
  
  $query = "DELETE FROM `Annotation` WHERE location = '" . $location . "' AND chart_type ='" . $chart_type . "' AND series_name='" . $series_name . "' AND time = '" . $time . "' AND annotation='" . $annotation . "';";
  
  echo $query;
  
  callDB($query);
}

$method = $_GET["method"];

if ($method == 'annotation.get') {
  get_annotations();
} else if ($method == "annotation.add") {
  add_annotations();
} else if ($method == "annotation.delete") {
  delete_annotation();
}

?>