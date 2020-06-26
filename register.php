<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
  <link rel="stylesheet" href="./styles/bootstrap.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <link rel="stylesheet" href="./styles/chess.css">
  <title>Register</title>
</head>
<body class="bg-secondary">
  <?php
    # start session, connect to database, and print navbar
    session_start();
    include "connectDB.php";
    include "./includes/navIntro.php";
    
    # if you are logged in, you can't visit this page
    if(isset($_SESSION['Succeed'])) {
      header("Location: welcome.php");
    }

    # usable functions
    function checkErr($err) {
      return isset($_SESSION['regErr'][$err]);
    }

    # check if you hit the register button
    if(isset($_POST['register'])) {

      # save inputs
      $username = $_POST['username'];
      $pass = $_POST['password'];
      $confirm = $_POST['confirm'];

      # reset past errors
      $_SESSION['regErr'] = array();

      # validation checking
      if(strlen($username) < 4) {
        $_SESSION['regErr']['username'] = 'Username must be at least 4 characters long.';
      }

      # use a DB call to avoid Session clutter
      $findUsername = $conn->query("SELECT * FROM users WHERE username = '$username'");

      if($findUsername->num_rows > 0) {
        $_SESSION['regErr']['username'] = 'That username is already taken.';
      }

      if(strlen($pass) < 8 || strlen($pass) > 12) {
        $_SESSION['regErr']['password'] = 'Password must be 8 - 12 characters long.';
      }

      if(strcmp($pass, $confirm) !== 0) {
        $_SESSION['regErr']['confirm'] = 'Passwords do not match.';
      }

      # if there are no errors, save user info
      if(empty($_SESSION['regErr'])) {
        $register = "INSERT INTO users(username,password,type) VALUES('$username','$pass','user')";

        # if query successful, add alert to be printed in login page and redirect, otherwise, flag an alert error on this page
        if($conn->query($register) === TRUE) {
          addAlert("Account successfully made! Please login below.", "success");
          header("Location: login.php");
        } else {
          addAlert("<b>Uh oh!</b> Something went wrong! ".$conn->error, "danger");
        }
      }
    } else {
      # if this was just a refresh, clear error list and display any alerts
      $_SESSION['regErr'] = array();
      displayAlert();
    }

  ?>

  <div class="container bg-white w-50 mx-auto p-5 my-5 shadow rounded">
    <h2 class="font-weight-bold">Register</h2>
    <hr>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
      <div class="form-group">
        <label for="username" class="required">Username</label>
        <input type="text" name="username" id="username" 
        class="form-control <?php if(checkErr('username')) { echo 'is-invalid';} ?>" 
        required>

        <!-- show only if there are errors in this section from the php code above -->
        <?php if(checkErr('username')) { ?>
          <div class="invalid-feedback">
            <?php echo $_SESSION['regErr']['username']; ?>
          </div>
        <?php } ?>
      </div>

      <div class="form-group">
        <label for="password" class="required">Password</label>
        <input type="password" name="password" id="password" 
        class="form-control <?php if(checkErr('password')) { echo 'is-invalid'; } ?>" 
        required>

        <!-- show only if there are errors in this section from the php code above -->
        <?php if(checkErr('password')) { ?>
          <div class="invalid-feedback">
            <?php echo $_SESSION['regErr']['password']; ?>
          </div>
        <?php } ?>
      </div>

      <div class="form-group">
        <label for="confirm" class="required">Confirm Password</label>
        <input type="password" name="confirm" id="confirm" 
        class="form-control <?php if(checkErr('confirm')) { echo 'is-invalid'; } ?>"  
        required>

        <!-- show only if there are errors in this section from the php code above -->
        <?php if(checkErr('confirm')) { ?>
          <div class="invalid-feedback">
            <?php echo $_SESSION['regErr']['confirm']; ?>
          </div>
        <?php } ?>
      </div>
      <hr>
      <div class="form-group">
          <button name="register" type="submit" class="btn btn-success w-100 mb-2">Register</button>
          <small>Already have an account? <a href="login.php">Login</a></small>
      </div>
    </form>
  </div>

  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="./scripts/bootstrap.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>