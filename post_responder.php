<?php
// Only allow POST requests
if (strtoupper($_SERVER['REQUEST_METHOD']) != 'POST') {
  throw new Exception("\nOnly POST requests are allowed, not ".strtoupper($_SERVER['REQUEST_METHOD']));
}

// Make sure Content-Type is application/json
$content_type = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
if (stripos($content_type, 'application/json') === false) {
  throw new Exception("\nContent-Type must be application/json, not not not not not ".$_SERVER['CONTENT_TYPE']);
}

// Include config file
require_once "config.php";

// Read the input stream
$body = file_get_contents("php://input");
$max_hist = json_decode($body)->max_hist;

// Display the object
// print_r($object);

$sql = "SELECT * FROM rtu_list";
$rtu_list = $mysqli->query($sql);
$returnJSON = array();
if ($rtu_list->num_rows > 0) {
  while($row = $rtu_list->fetch_object()) {
    // sending information about displays, this includes standing alarms too
    $row->standing = array();
    $sql_stnd = "SELECT * from standing_alarms WHERE rtu_id = {$row->rtu_id}";
    $displat_list = $mysqli->query($sql_stnd);
    if ($displat_list->num_rows > 0) {
      while($row_display = $displat_list->fetch_object()) {
        // if ($row_display->is_set) {
          array_push($row->standing, $row_display);
        // }
      }
    }

    //sending the events
    $row->events = array();
    $sql_events = "SELECT * from event_history WHERE rtu_id = {$row->rtu_id} ORDER BY time DESC";
    $events = $mysqli->query($sql_events);
    if ($events->num_rows > 0) {
      $i = $max_hist;
      while(($row_events = $events->fetch_object()) && $i--) {
        array_push($row->events, $row_events);
      }
    }

    array_push($returnJSON, $row);
    unset($row);
  }
} else echo "0 RTUs found";

echo json_encode($returnJSON);
