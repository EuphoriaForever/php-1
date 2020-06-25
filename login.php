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

    if(isset($_POST['login'])) {
      $username = $_POST['username'];
      $pass = $_POST['password'];

      $_SESSION['loginErr'] = array();

      $findUser = $conn->query("SELECT * FROM users WHERE username = '$username'");
      if($findUser === FALSE) {
        addAlert("Something went wrong!<br>".$conn->error, "danger");
      } else {
        if($findUser->num_rows === 0) {
          array_push($_SESSION['loginErr'], 'username');
        } else {
          $user = $findUser->fetch_assoc();
  
          if(strcmp($pass, $user['password']) !== 0) {
            array_push($_SESSION['loginErr'], 'password');
          } else {
            $_SESSION['Succeed'] = array(
              'id' => $user['user_ID'],
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
        <?php if(checkErr('username')) { ?>
        
          <div class="invalid-feedback">
            That account does not exist. Click <a href="register.php">here</a> to register.
          </div>
        <?php } ?>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" class="form-control" required>
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
   <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="./scripts/bootstrap.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>