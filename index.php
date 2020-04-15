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
            <form class="form-group my-2 my-lg-0" onsubmit="submit_rtu_data()">
              <div class="form-group">
                <input type="text" class="form-control mr-sm-2" id="ip_address" placeholder="IP address">
              </div>
              <div class="form-group">
                <input type="text" class="form-control mr-sm-2" id="port" placeholder="Port">
              </div>
              <div class="form-group">
                <input type="text" class="form-control mr-sm-2" id="device_id" placeholder="Device id">
              </div>
              <button type="submit" class="btn btn-primary my-2 my-sm-0">Submit</button>
            </form>
          </div>

          <div class="col-sm">
            <h4 class="text-primary">Fill out this form and submit to remove a device from monitoring</h4>
            <form class="form-group my-2 my-lg-0" onsubmit="id_to_remove()">
              <div class="form-group">
                <input type="text" class="form-control mr-sm-2" id="device_id_to_remove" placeholder="Device id">
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



  <div class="container">
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
        $str = '<div class="col-2 nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">';
        $str2 = '<div class="col-10 tab-content" id="v-pills-tabContent">';

        if ($rtu_list->num_rows > 0) {
            // output data of each row
            $i = 0;
            while($row = $rtu_list->fetch_assoc()) {
              $cur_id = strval($row['rtu_id']);
              $str .= "<a class=\"nav-link" . ($i ? "" : " active") . "\" id=\"devices_tab_{$cur_id}";
              $str .= "\" data-toggle=\"pill\" href=\"#devices_tab_inside_{$cur_id}";
              $str .= "\" role=\"tab\" aria-controls=\"devices_tab_inside_{$cur_id}";
              $str .= "\" aria-selected=\"" . ($i ? "false" : "true") . "\">Device #{$cur_id}</a>";


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

              // $str2 .= "<td>" . strval($row["rtu_id"]) . "</td>";
              // $str2 .= "<td>" . strval($row["rtu_ip"]) . "</td>";
              // $str2 .= "<td>" . strval($row["rtu_port"]) . "</td>";
              // $str2 .= "<td>" . strval($row["type"]) . "</td>";
              // $str2 .= "<td>" . ($row["link"] ? "<span class = \"text-success\">Online</span>" : "<span class = \"text-danger\">Offline</span>") . "</td>";
              // $str2 .= "<td>" . strval($row["display_count"]) . "</td>";

              $str2 .= "</tr></tbody></table>";


              $displays_text_tab = $displays_text = "";
              for ($x = 1; $x <= $row['display_count']; $x++) {
                $displays_text_tab .= "<a class=\"nav-link\" id=\"v-pills-display-{$cur_id}_{$x}-tab\" data-toggle=\"pill\" href=\"#v-pills-display-{$cur_id}_{$x}\" role=\"tab\" aria-controls=\"v-pills-display-{$cur_id}_{$x}\" aria-selected=\"false\">Display #{$x}</a>";
                $displays_text .= "<div class=\"tab-pane fade\" id=\"v-pills-display-{$cur_id}_{$x}\" role=\"tabpanel\" aria-labelledby=\"v-pills-display-{$cur_id}_{$x}-tab\"></div>";
              }

              $stnd_alm_txt = "<table class=\"table\"><thead><tr>
                                            <th scope=\"col\">Display</th>
                                            <th scope=\"col\">Major under</th>
                                            <th scope=\"col\">Minor under</th>
                                            <th scope=\"col\">Minor over</th>
                                            <th scope=\"col\">Major over</th>
                                          </tr></thead><tbody id = \"standing_table_{$cur_id}\"></tbody></table>";

              $str2 .= "<div class=\"row\">
                          <div class=\"col-3\">
                            <div class=\"nav flex-column nav-pills\" id=\"v-pills-tab\" role=\"tablist\" aria-orientation=\"vertical\">
                              <a class=\"nav-link active\" id=\"v-pills-event-history-{$cur_id}-tab\" data-toggle=\"pill\" href=\"#v-pills-event-history-{$cur_id}\" role=\"tab\" aria-controls=\"v-pills-event-history-{$cur_id}\" aria-selected=\"true\">Event history</a>
                              <a class=\"nav-link\" id=\"v-pills-standing-{$cur_id}-tab\" data-toggle=\"pill\" href=\"#v-pills-standing-{$cur_id}\" role=\"tab\" aria-controls=\"v-pills-standing-{$cur_id}\" aria-selected=\"false\">Standing alarms</a>
                              {$displays_text_tab}
                            </div>
                          </div>
                          <div class=\"col-9\">
                            <div class=\"tab-content\" id=\"v-pills-tabContent\">
                              <div class=\"tab-pane fade show active\" id=\"v-pills-event-history-{$cur_id}\" role=\"tabpanel\" aria-labelledby=\"v-pills-event-history-{$cur_id}-tab\">Inside Event history {$cur_id}</div>
                              <div class=\"tab-pane fade\" id=\"v-pills-standing-{$cur_id}\" role=\"tabpanel\" aria-labelledby=\"v-pills-standing-{$cur_id}-tab\">{$stnd_alm_txt}</div>
                              {$displays_text}
                            </div>
                          </div>
                        </div>";


              // $str2 .= '<h4>Current temperature is <span class = "temp_to_update" id="temp' . strval($row['rtu_id']);
              // $str2 .= '"></span>' . '</h4><h4>Current humidity is <span id="hum' . strval($row['rtu_id']);
              // $str2 .= '"></span>%</h4>';
              // $str2 .= '<canvas id="threshold_canvas_' . strval($row['rtu_id']);
              // $str2 .= '" width="500" height="100">Your browser does not support the canvas element.</canvas>';
              // $str2 .= '<h4><span id="alarm' . strval($row['rtu_id']);
              // $str2 .= '"></span></h4><p>History</p><table class="table table-striped">';
              // $str2 .= '<thead><tr><th scope="col">#</th><th scope="col">Date</th><th scope="col">Time</th><th scope="col">Temperature</th><th scope="col">Humidity</th></tr></thead>';
              // $str2 .= '<tbody id = "history' . strval($row['rtu_id']);
              // $str2 .= '"></tbody></table>';
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

    <!-- <div class="row justify-content-center">
      <div class="col-5">
        <a class="btn btn-outline-secondary btn-block" href="reset-password.php" role="button">Reset password</a>
      </div>
      <div class="col-5">
        <a class="btn btn-outline-danger btn-block" href="logout.php" role="button">Sign out</a>
      </div>
    </div> -->
  </div>
</body>
</html>
