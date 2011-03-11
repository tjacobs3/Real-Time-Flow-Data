<?php
  function get_annotations ($location, $chart_type, $series_name) {
    $link = mysql_connect("mysql.imwillow.com", "uiuc492", "492492!!") or die(mysql_error());
    mysql_select_db("uiuc492") or die(mysql_error());

    $query = "SELECT * FROM `Annotation` WHERE location = '" . $location . "' AND chart_type ='" . $chart_type . "' AND series_name='" . $series_name . "' ORDER BY `Annotation`.`time` ASC";
    
    $result = mysql_query($query);
    $a = array();  
    while ($r = mysql_fetch_assoc($result)) {
      array_push($a, array('location'   => $location, 
                           'seriesName' => $series_name,
                           'timestamp'  => $r['time'],
                           'content'    => $r['annotation']));
       // $a[$r['time']] = $r['annotation'];
    }

    mysql_close($link);
    return $a;
  }
  
  $location     = $_GET["location"];
  $chart_type  = $_GET["chartType"];
  $series_name  = $_GET["seriesName"];
  
  $annotations = get_annotations($location, $chart_type, $series_name);
  
  header('Cache-Control: no-cache, must-revalidate');
  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
  header('Content-type: application/json');
  
  echo json_encode($annotations);
?>