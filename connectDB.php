<?php
  $conn = new mysqli("localhost","root","");
  $noDB = '
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-5">
    <a href="#" class="navbar-brand">IM 2</a>
  </nav>
  <div class="alert alert-danger w-50 mt-5 mx-auto" role="alert"><b>Unable to select database.</b> Please check if the database "im2" exists before proceeding.</div>
  ';
  if($conn->connect_error){
   die("Connection failed: " . $conn->connect_error);
  }

  mysqli_select_db($conn, "im2") or die($noDB);

  # let's make an alert array to make displaying alerts faster
  function addAlert($alert, $type = "info") {

    # check if alert array exists in the session
    if(!isset($_SESSION['alerts'])) {
      $_SESSION['alerts'] = array();
    }

    # push the new error
    array_push($_SESSION['alerts'], array("type"=>$type, "message"=>$alert));
  }

  # lets make a callable function that displays all alerts we need
  function displayAlert() {
    if(isset($_SESSION['alerts'])) {

      # loop through all alerts
      foreach ($_SESSION['alerts'] as $alert) {
        $type = $alert['type'];
        $message = $alert['message'];
        # check type to see what kind of alert this is 
        echo "<div class='alert alert-$type alert-dismissible fade show w-50 mx-auto mt-4' role='alert'>$message
        <button type='button' class='close' data-dismiss='alert' aria-label='Close'>
          <span aria-hidden='true'>&times;</span>
        </button>
        </div>";
      }

      # since we already displayed them and we don't want to KEEP displaying them, let's clear the alert array
      unset($_SESSION['alerts']);
    }
  }

  function permissions($db) {
    $permissions = array();
    $author = $GLOBALS['conn']->query("SELECT Author FROM db WHERE db_ID = $db")->fetch_assoc()['Author'];

    if($_SESSION['Succeed']['type'] === 'administrator' || $author === $_SESSION['Succeed']['id'] ) {

      # if admin, add all perms
      array_push($permissions, 1, 2, 3, 4);
    } else {

      # if user, add only allowed perms
      $checkPermit = $GLOBALS['conn']->query("SELECT * FROM permits WHERE user_ID = ".$_SESSION['Succeed']['id']." AND db = $db");

      if($checkPermit) {
        if($checkPermit->num_rows > 0) {
          while($permit = $checkPermit->fetch_assoc()) {
            array_push($permissions, $permit['operation']);
          }
        }
      }
    }

    return $permissions;
  }

  function isAllowed($op) {
    return in_array($op, $GLOBALS['permissions']);
  }
?>