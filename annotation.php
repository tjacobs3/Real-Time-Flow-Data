<?php
  function add_annotation($l, $ct, $sn, $t, $a) {
    $l  = addslashes($l);
    $ct = addslashes($ct);
    $sn = addslashes($sn);
    $t  = addslashes($t);
    $a  = addslashes($a);

    mysql_connect("mysql.imwillow.com", "uiuc492", "492492!!") or die ("{status: 201, error:'". mysql_error() . "'}");
    mysql_select_db("uiuc492");
    
    $query = "INSERT INTO Annotation (location, chart_title, series_name, time, annotation) VALUES ('" . $l . "','" . $ct . "','" . $sn . "','" . $t . "','" . $a . "');";
    //echo $query;
    
    mysql_query($query) or die (mysql_error());
    
    return 1;
  }
  
  $location     = $_GET["location"];
  $chart_title  = $_GET["chart_title"];
  $series_name  = $_GET["series_name"];
  $time         = $_GET["time"];
  $annotation   = $_GET["annotation"];
  
  add_annotation($location, $chart_title, $series_name, $time, $annotation);
?>