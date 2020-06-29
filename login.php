<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
  <link rel="stylesheet" href="./styles/bootstrap.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
  <link rel="stylesheet" href="./styles/chess.css">
  <title>Login</title>
</head>
<body class="bg-secondary">
  <?php
    # start session, connect to database, print navbar
    session_start();
    include "connectDB.php";
    include "./includes/navIntro.php";

    # if you are logged in, you can't visit this page
    if(isset($_SESSION['Succeed'])) {
      header("Location: welcome.php");
    }

    # usable functions
    function checkErr($err) {
      return in_array($err, $_SESSION['loginErr']);
    }

    # check if you hit the login button
    if(isset($_POST['login'])) {

      # save inputs
      $username = $_POST['username'];
      $pass = $_POST['password'];

      # reset login errors
      $_SESSION['loginErr'] = array();

      # to not have a cluttered session, just do a DB call to check for account existence
      $findUser = $conn->query("SELECT * FROM users WHERE username = '$username'");

      if($findUser === FALSE) {
        addAlert("<b>Uh oh!</b> Something went wrong! ".$conn->error, "danger");
      } else {
        
        # no rows with that username, meaning no account
        if($findUser->num_rows === 0) {
          array_push($_SESSION['loginErr'], 'username');
        } else {

          $user = $findUser->fetch_assoc();
  
          # user found, now compare passwords
          if(strcmp($pass, $user['password']) !== 0) {

            array_push($_SESSION['loginErr'], 'password');
          } else {

            # save user info as Succeed authentication (less Session clutter) and redirect
            $_SESSION['Succeed'] = array(
              'id' => $user['user_id'],
              'username' => $user['username'],
              'type' => $user['type']
            );
            header("Location: welcome.php");
          }
        }
      }
    } else {
      $_SESSION['loginErr'] = array();
    }
    
    displayAlert();
  ?>

  <div class="container bg-white w-50 mx-auto p-5 my-5 shadow rounded">
    <h2 class="font-weight-bold">Login</h2>
    <hr>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" id="login">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" class="form-control" required>

        <!-- show only if there are errors in this section from the php code above -->
        <?php if(checkErr('username')) { ?>        
          <div class="invalid-feedback">
            That account does not exist. Click <a href="register.php">here</a> to register.
          </div>
        <?php } ?>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" class="form-control" required>

        <!-- show only if there are errors in this section from the php code above -->
        <?php if(checkErr('password')) { ?>
          <div class="invalid-feedback">
            Incorrect password! Please try again.
          </div>
        <?php } ?>
      </div>

      <hr>

      <div class="form-group">
        <button type="submit" name="login" class="btn btn-success w-100 mb-2">Login</button>
        <small>Don't have an account? <a href="register.php">Register</a></small>
      </div>
    </form>
  </div>

   <!-- jQuery first, then Popper.js, then Bootstrap JS -->
   <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
</body>
</html>