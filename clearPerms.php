<?php
  session_start();
  include "connectDB.php";
  include "checkLogin.php";

  if(isset($_GET['clearPerms'])) {

    # get input
    $user = $_GET['user'];
    $db = $_GET['db_id'];

    $clearPermissions = $conn->query("DELETE FROM permits WHERE user_ID = $user AND db = $db");

    if($clearPermissions===TRUE) {
      addAlert("Successfully cleared user permissions!", "success");
    } else {
      addAlert("<b>Uh oh!</b> Something went wrong. ".$conn->error, "danger");
    }
  }

  header("Location: ".$_SERVER['HTTP_REFERER']);
?>