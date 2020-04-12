<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<head>
    <title>MasterMon | Home</title>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/jquery-3.5.0.min.js"></script>
</head>
<body>
  <div class="container">
    <div class="row justify-content-center py-4">
      <div class="col-10 text-center">
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>, welcome to MasterMon</h1>
      </div>
    </div>
    <div class="row justify-content-center">
      <div class="col-5">
        <a class="btn btn-outline-secondary btn-block" href="reset-password.php" role="button">Reset Your Password</a>
      </div>
      <div class="col-5">
        <a class="btn btn-outline-danger btn-block" href="logout.php" role="button">Sign Out of Your Account</a>
      </div>
    </div>
  </div>
</body>
</html>
