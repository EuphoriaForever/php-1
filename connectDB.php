<?php
  $conn = new mysqli("localhost","root","","im2");
  if($conn->connect_error){
   die("Connection failed: " . $conn->connect_error);
  }

  # let's make an alert array to make displaying alerts faster
  function addAlert($alert, $type) {

    # check if alert array exists in the session
    if(!isset($_SESSION['alerts'])) {
      $_SESSION['alerts'] = array();
    }

    # push the new error
    $_SESSION['alerts'][$type] = $alert;
  }

  # lets make a callable function that displays all alerts we need
  function displayAlert() {
    if(isset($_SESSION['alerts'])) {
      echo "You have alerts";
      # loop through all alerts
      foreach ($_SESSION['alerts'] as $type => $message) {
        echo "yeet";
        # check type to see what kind of alert this is 
        echo "<div class='alert alert-$type w-50 mx-auto' role='alert'>$message</div>";
      }

      # since we already displayed them and we don't want to KEEP displaying them, let's clear the alert array
      unset($_SESSION['alerts']);
    }
  }
?>