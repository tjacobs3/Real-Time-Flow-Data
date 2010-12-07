<?php
  function get_annotations ($location, $chart, $series) {
    $link = mysql_connect("mysql.imwillow.com", "uiuc492", "492492!!") or die(mysql_error());
    mysql_select_db("uiuc492") or die(mysql_error());

    $query = "SELECT * FROM `Annotation` WHERE location = '" . $location . "' AND chart_title ='" . $chart . "' AND series_name = '" . $series ."' ORDER BY `Annotation`.`create_time` ASC";

    $result = mysql_query($query);
    $a = array();  
    while ($r = mysql_fetch_assoc($result)) {
       $a[$r['time']] = $r['annotation'];
    }

    mysql_close($link);
    return $a;
  }
  
  $location     = $_GET["location"];
  $chart_title  = $_GET["chart_title"];
  $series_name  = $_GET["series_name"];
  
  $annotations = get_annotations($location, $chart_title, $series_name);
  
  header('Cache-Control: no-cache, must-revalidate');
  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
  header('Content-type: application/json');
  
  echo json_encode($annotations);
?>