<?php
// Initialize the session
session_start();

// Check if the user is logged in, otherwise redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate new password
    if(empty(trim($_POST["new_password"]))) $new_password_err = "Please enter the new password.";
    elseif(strlen(trim($_POST["new_password"])) < 6) $new_password_err = "Password must have atleast 6 characters.";
    else $new_password = trim($_POST["new_password"]);

    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))) $confirm_password_err = "Please confirm the password.";
    else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)) $confirm_password_err = "Password did not match.";
    }

    // Check input errors before updating the database
    if(empty($new_password_err) && empty($confirm_password_err)){
        // Prepare an update statement
        $sql = "UPDATE users SET password = ? WHERE id = ?";

        if($stmt = $mysqli->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("si", $param_password, $param_id);

            // Set parameters
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Password updated successfully. Destroy the session, and redirect to login page
                session_destroy();
                header("location: login.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $mysqli->close();
}
?>

<!DOCTYPE html>
<head>
    <title>Reset Password</title>
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="/css/bootstrap.min.css">
</head>
<body>


  <div class="container text-center">
    <div class="row justify-content-center py-4">
      <div class="col-lg-6 col-md-6 col-sm-12">
        <h2>Reset Password</h2>
        <p>Please fill out this form to reset your password.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($new_password_err)) ? 'has-error' : ''; ?>">
                <input type="password" name="new_password" class="form-control" placeholder="New Password" value="<?php echo $new_password; ?>">
                <span class="form-text text-danger"><?php echo $new_password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <input type="password" name="confirm_password" placeholder="Confirm Password" class="form-control">
                <span class="form-text text-danger"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group row">
                <div class="col"><input type="submit" class="btn btn-primary btn-block" value="Submit"></div>
                <div class="col"><a class="btn btn-secondary btn-block" href="index.php">Cancel</a></div>
            </div>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
