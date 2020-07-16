<?php 
    # start session, connect to database, print navbar
    session_start();
    include "./connectDB.php";
    include "navIntro.php";

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