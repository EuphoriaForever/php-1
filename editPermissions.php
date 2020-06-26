<?php
  session_start();
  include "connectDB.php";
  include "checkLogin.php";
  
  if(isset($_POST['editPerms'])) {

    # save inputs
    $user = $_POST['user'];
    $perms = $_POST['permissions'];
    $db = $_POST['db_id'];

    # get all existing perms for user
    $getAllPerms = $conn->query("SELECT * FROM permits WHERE user_ID = $user AND db = $db");

    if($getAllPerms) {
      # loop through to see if any are disabled and delete
      if($getAllPerms->num_rows > 0) {

        while($permit = $getAllPerms->fetch_assoc()) {
          $operation = $permit['operation'];

          if(!in_array($operation, $perms)) {
            $deleted = $conn->query("DELETE FROM permits WHERE user_ID = $user AND db = $db AND operation = $operation");

            if($deleted === FALSE) {
              addAlert("<b>Uh oh!</b> Something went wrong. ".$conn->error, "danger");
            }
          }
        }
      }

      # we'll add any missing permissions
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
    } else {
      addAlert("<b>Uh oh!</b> Something went wrong. ".$conn->error, "danger");
    }

    if(empty($_SESSION['alerts'])) {
      addAlert("Successfully modified permissions!", "success");
    }
  }

  header("Location: ".$_SERVER['HTTP_REFERER']);
?>