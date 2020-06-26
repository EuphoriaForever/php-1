<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="./styles/bootstrap.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body class="bg-secondary">
  <?php
    include "./includes/navIntro.php";
    session_start(); #start session in each form validation page so that we can all access the super global var $_SESSION

    if(isset($_SESSION['Succeed'])) { # if there is already a detected login session, redirect to welcome page
      header("Location: welcome.php");
    }

    if(isset($_SESSION['newAccount'])) { # if you're redirected here from register.php because you just made a new account, show this alert banner then unset the newAccount session alert so that it does not keep appearing
      echo "<div class='alert alert-success w-50 mx-auto my-4'>Account successfully made! Please login below. </div>";
      unset($_SESSION['newAccount']);
    }

    if(isset($_POST['login'])) { #if you're trynna login, check credentials here
      $username = $_POST['username'];
      $password = $_POST['password'];

      $conn = new mysqli("localhost","root","","im2");
      if($conn->connect_error){
        die("Connection failed: " . $conn->connect_error);
       }//end of checking for connection error

       $sql = "SELECT * FROM users WHERE username='$username'";
       $result = $conn->query($sql);
       if($result->num_rows > 0 ){
        $row = $result->fetch_assoc();
        if($_POST['password'] === $row['password']){
          #I have to create 
          $_SESSION['users'][$row['username']] =  array('id'=>$row['user_id'],'password' => $row['password'], 'type' => $row['type'] , 'name' => $row['username']);
          $_SESSION['Succeed'] = $_POST['username'];
          header("Location: welcome.php");
        }else{
          echo "<div class='alert alert-danger w-50 mx-auto my-4'>You have given the wrong password!</div>";
        }
       }else{
            $_SESSION['err'] = "no_account";
            header("Location: register.php");
       }
    }
  ?>
  <div class="container w-50 position-relative mx-auto p-5 my-5 bg-light shadow">
    <h2 class="font-weight-bold">Login</h2>
    <hr>
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" class="d-flex flex-column justify-content-center">
      <div class="form-group">
        <label for="username" class="font-weight-bold">Username</label>
        <input type="text" name="username" id="username" class="form-control" required>
      </div>

      <div class="form-group">
        <label for="password" class="font-weight-bold">Password</label>
        <input type="password" name="password" id="password" class="form-control" required>
      </div>

      <button type="submit" name="login" class="btn btn-success">Login</button>
      <small>No account? <a href="./register.php">Register</a></small>
    </form>
  </div>
  <script src="./scripts/bootstrap.min.js"></script>
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</body>
</html>