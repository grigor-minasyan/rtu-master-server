<?php
// Initialize the session
session_start();

// Include config file
require_once "config.php";
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<head>
    <title>MasterMon | Home</title>
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
</head>
<body>
  <div class="pos-f-t sticky-top">
    <div class="collapse" id="navbarToggleExternalContent">
      <div class="bg-light p-4">
        <div class="row">
          <div class="col-sm">
            <h4 class="text-primary">Fill out this form and submit to add a new device to monitor</h4>
            <form class="form-group my-2 my-lg-0" onsubmit="submit_rtu_data(); return false">
              <div class="form-group">
                <input type="text" class="form-control mr-sm-2" id="ip_address" placeholder="IP address" required>
              </div>
              <div class="form-group">
                <input type="text" class="form-control mr-sm-2" id="port" placeholder="Port" required>
              </div>
              <div class="form-group">
                <input type="text" class="form-control mr-sm-2" id="device_id" placeholder="Device id" required>
              </div>
              <div class="form-group">
                <label for="select_rtu_type">Select RTU type</label>
                <select class="form-control" id="select_rtu_type" required>
                  <option>arduino</option>
                  <option>temp_def_g2</option>
                </select>
              </div>
              <button type="submit" class="btn btn-primary my-2 my-sm-0">Submit</button>
            </form>
          </div>
          <div class="col-sm">
            <h4 class="text-primary">Fill out this form and submit to remove a device from monitoring</h4>
            <form class="form-group my-2 my-lg-0" onsubmit="id_to_remove(); return false">
              <div class="form-group">
                <input type="text" class="form-control mr-sm-2" id="device_id_to_remove" placeholder="Device id" required>
              </div>
              <button type="submit" class="btn btn-primary my-2 my-sm-0">Submit</button>
            </form>
          </div>
          <div class="col-sm">
            <h4 class="text-primary">Max number of items in history page</h4>
            <form class="form-group my-2 my-lg-0" onsubmit="change_hisory_count(); return false">
              <div class="form-group">
                <input type="text" class="form-control mr-sm-2" id="new_history_count" placeholder="History count">
              </div>
              <button type="submit" class="btn btn-primary my-2 my-sm-0">Change</button>
            </form>
          </div>
          <div class = "col-sm">
            <h4 class="text-primary">Click this to toggle between Celcius and Fahrenheit</h4>
            <button type="button" class="btn btn-primary btn-lg btn-block" onclick="temp_toggle()" id="temp_toggle">F</button>
          </div>
        </div>
      </div>
    </div>
    <nav class="navbar navbar-dark bg-primary">
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <button type="button" class="btn btn-light" href="#" onclick="topFunction()">Home</button>
      <a class="btn btn-light" href="reset-password.php" role="button">Reset password</a>
      <a class="btn btn-danger" href="logout.php" role="button">Sign out</a>
    </nav>
  </div>
  <div class="container-fluid">
    <div class="row justify-content-center py-4">
      <div class="col-10 text-center">
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>, welcome to MasterMon</h1>
        <p>To add or remove remote units to monitor, please click the top menu button and fill out the appropriate form</p>
        <p>You can also change units of temperature in the top menu and how many items show in the history page</p>
      </div>
    </div>
    <div class="row mt-4">
      <?php
        $sql = "SELECT * FROM rtu_list";
        $rtu_list = $mysqli->query($sql);
        $str = '<div class="col-2 nav flex-column nav-pills bg-light p-2" id="v-pills-tab" role="tablist" aria-orientation="vertical"><h4>Select a device to monitor</h4>';
        $str2 = '<div class="col-10 tab-content" id="v-pills-tabContent">';
        if ($rtu_list->num_rows > 0) {
            // output data of each row
            $i = 0;
            while($row = $rtu_list->fetch_assoc()) {
              $cur_id = strval($row['rtu_id']);
              $str .= "<a class=\"nav-link" . ($i ? "" : " active") . "\" id=\"devices_tab_{$cur_id}";
              $str .= "\" data-toggle=\"pill\" href=\"#devices_tab_inside_{$cur_id}";
              $str .= "\" role=\"tab\" aria-controls=\"devices_tab_inside_{$cur_id}";
              $str .= "\" aria-selected=\"" . ($i ? "false" : "true") . "\">Device #{$cur_id} <span class = \"text-danger alarm-icon-{$cur_id}\"></span></a>";


              $str2 .= "<div class=\"tab-pane fade" . ($i ? "" : " show active") . "\" id=\"devices_tab_inside_{$cur_id}";
              $str2 .= "\" role=\"tabpanel\" aria-labelledby=\"devices_tab_{$cur_id}";
              $str2 .= "\"><div class=\"col\">";

              $str2 .= "<h2>Device information</h2>";
              $str2 .= "<table class=\"table\"><thead><tr>
                              <th scope=\"col\">RTU id</th>
                              <th scope=\"col\">RTU IP</th>
                              <th scope=\"col\">Listening port</th>
                              <th scope=\"col\">Type</th>
                              <th scope=\"col\">Link status</th>
                              <th scope=\"col\">Displays</th>
                            </tr></thead><tbody><tr id = \"device_info_table_{$cur_id}\">";

              $str2 .= "</tr></tbody></table>";


              $displays_text_tab = $displays_text = "";
              for ($x = 1; $x <= $row['display_count']; $x++) {
                if ($row['type'] == "arduino") {
                  $displays_text_tab .= "<a class=\"nav-link\" id=\"v-pills-display-{$cur_id}_{$x}-tab\" data-toggle=\"pill\" href=\"#v-pills-display-{$cur_id}_{$x}\" role=\"tab\" aria-controls=\"v-pills-display-{$cur_id}_{$x}\" aria-selected=\"false\">Display #{$x}</a>";
                  $displays_text .= "<div class=\"tab-pane fade\" id=\"v-pills-display-{$cur_id}_{$x}\" role=\"tabpanel\" aria-labelledby=\"v-pills-display-{$cur_id}_{$x}-tab\"></div>";
                }
                else if ($row['type'] == "temp_def_g2") {
                  if ($x < $row['analog_start'] || $x > $row['analog_end']) {
                    continue;
                  } else{
                    for ($y = 1; $y <= 2; $y++) {
                      $id = $row['rtu_id'];
                      $actual_point = (1+($y-1)*32);
                      $sql = "SELECT is_enabled FROM standing_alarms WHERE rtu_id = {$id} AND display = {$x} AND point = {$actual_point}";
                      $is_enabled_row = $mysqli->query($sql);
                      $is_enabled_obj = $is_enabled_row->fetch_assoc();
                      if ($is_enabled_obj['is_enabled'] == 1 || $is_enabled_obj['is_enabled'] == "1") {
                        $displays_text_tab .= "<a class=\"nav-link\" id=\"v-pills-display-{$cur_id}_{$x}_{$y}-tab\" data-toggle=\"pill\" href=\"#v-pills-display-{$cur_id}_{$x}_{$y}\" role=\"tab\" aria-controls=\"v-pills-display-{$cur_id}_{$x}_{$y}\" aria-selected=\"false\">Display #{$x} {$y}</a>";
                        $displays_text .= "<div class=\"tab-pane fade\" id=\"v-pills-display-{$cur_id}_{$x}_{$y}\" role=\"tabpanel\" aria-labelledby=\"v-pills-display-{$cur_id}_{$x}_{$y}-tab\"></div>";      
                      }

                    }
                  }
                }
              }



              // change those two to be the exact same withot a copy paste, only 1 word is different
              $stnd_alm_txt = "<table class=\"table table-striped\"><thead><tr>
                                            <th scope=\"col\">Display #</th>
                                            <th scope=\"col\">Point</th>
                                            <th scope=\"col\">Description</th>
                                            <th scope=\"col\">Status</th>
                                            </tr></thead><tbody id = \"standing_table_{$cur_id}\"></tbody></table>";
              $all_alm_txt = "<table class=\"table table-striped\"><thead><tr>
                                            <th scope=\"col\">Display #</th>
                                            <th scope=\"col\">Point</th>
                                            <th scope=\"col\">Description</th>
                                            <th scope=\"col\">Status</th>
                                            </tr></thead><tbody id = \"all_alarm_table_{$cur_id}\"></tbody></table>";


              $events_in_text = "<table class=\"table table-striped\"><thead><tr>
                                          <th scope=\"col\">Delete</th>
                                          <th scope=\"col\">Time</th>
                                          <th scope=\"col\">Description</th>
                                          <th scope=\"col\">Type</th>
                                          <th scope=\"col\">Display</th>
                                          <th scope=\"col\">Point</th>
                                          <th scope=\"col\">Value</th>
                                          <th scope=\"col\">Unit</th>
                                          </tr></thead><tbody id = \"events_table_{$cur_id}\"></tbody></table>";


              $str2 .= "<div class=\"row mt-5\">
                          <div class=\"col-3 bg-light p-2\">
                            <div class=\"nav flex-column nav-pills\" id=\"v-pills-tab\" role=\"tablist\" aria-orientation=\"vertical\">
                              <a class=\"nav-link active\" id=\"v-pills-event-history-{$cur_id}-tab\" data-toggle=\"pill\" href=\"#v-pills-event-history-{$cur_id}\" role=\"tab\" aria-controls=\"v-pills-event-history-{$cur_id}\" aria-selected=\"true\">Event history</a>
                              <a class=\"nav-link\" id=\"v-pills-standing-{$cur_id}-tab\" data-toggle=\"pill\" href=\"#v-pills-standing-{$cur_id}\" role=\"tab\" aria-controls=\"v-pills-standing-{$cur_id}\" aria-selected=\"false\">Standing alarms <span class = \"text-danger alarm-icon-{$cur_id}\"></span></a>
                              <a class=\"nav-link\" id=\"v-pills-all-{$cur_id}-tab\" data-toggle=\"pill\" href=\"#v-pills-all-{$cur_id}\" role=\"tab\" aria-controls=\"v-pills-all-{$cur_id}\" aria-selected=\"false\">All alarms <span class = \"text-danger alarm-icon-{$cur_id}\"></span></a>
                              {$displays_text_tab}
                            </div>
                          </div>
                          <div class=\"col-9\">
                            <div class=\"tab-content\" id=\"v-pills-tabContent\">
                              <div class=\"tab-pane fade show active\" id=\"v-pills-event-history-{$cur_id}\" role=\"tabpanel\" aria-labelledby=\"v-pills-event-history-{$cur_id}-tab\">$events_in_text</div>
                              <div class=\"tab-pane fade\" id=\"v-pills-standing-{$cur_id}\" role=\"tabpanel\" aria-labelledby=\"v-pills-standing-{$cur_id}-tab\">{$stnd_alm_txt}</div>
                              <div class=\"tab-pane fade\" id=\"v-pills-all-{$cur_id}\" role=\"tabpanel\" aria-labelledby=\"v-pills-all-{$cur_id}-tab\">{$all_alm_txt}</div>
                              {$displays_text}
                            </div>
                          </div>
                        </div>";
              $str2 .= '</div></div>';

              $i++;
            }
            $str .= '</div>';
            $str2 .= '</div>';
            echo $str;
            echo $str2;
        } else echo "0 RTUs found";
      ?>

    </div>
  </div>
</body>
</html>
