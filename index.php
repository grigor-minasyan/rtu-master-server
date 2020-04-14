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
      <a class="btn btn-secondary" href="reset-password.php" role="button">Reset password</a>
      <a class="btn btn-danger" href="logout.php" role="button">Sign out</a>
    </nav>
  </div>



  <div class="container">
    <div class="row justify-content-center py-4">
      <div class="col-10 text-center">
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>, welcome to MasterMon</h1>
        <p>To add or remove remote units to monitor, please click the top menu button and fill out the appropriate form</p>
        <p>You can also change units of temperature in the top menu and how many items show in the history page</p>
        <?php
          $sql = "SELECT * FROM rtu_list";
          $rtu_list = $mysqli->query($sql);
          if ($rtu_list->num_rows > 0) {
              // output data of each row
              while($row = $rtu_list->fetch_assoc()) {
                  echo "id: ".$row["rtu_id"]." - IP: ". $row["rtu_ip"]." Port: ".$row["rtu_port"]."<br>";
              }
          } else {
              echo "0 results";
          } 
        ?>
      </div>
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
