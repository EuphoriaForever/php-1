<?php
  session_start();
  include "connectDB.php";
  include "checkLogin.php";
  
  if(isset($_POST['addPerms'])) {

    # save inputs
    $user = $_POST['user'];
    $perms = $_POST['permissions'];
    $db = $_POST['db_id'];

    foreach($perms as $val) {
      
      # check each permission if they already exist
      $checkPerm = $conn->query("SELECT * FROM permits WHERE user_ID = $user AND operation = $val AND db = $db");

      if($checkPerm->num_rows === 0) {
        $createPerm = $conn->query("INSERT INTO permits(operation,user_ID,db) VALUES($val,$user,$db)");

        if($createPerm===FALSE) {
          addAlert("<b>Uh oh!</b> Something went wrong. ".$conn->error, "danger");
        break;
        }
      }
    }

    if(empty($_SESSION['alerts'])) {
      addAlert("Successfully added new database permissions!", "success");
    }
  }

  header("Location: ".$_SERVER['HTTP_REFERER']);
?>