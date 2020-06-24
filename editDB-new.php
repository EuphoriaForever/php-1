<?php
  session_start();

  include "connectDB.php";
  include "checkLogin.php";

  if(isset($_POST['submit'])) {

    $db_id = $_POST['db_id'];
    $newName = $_POST['rename'];

    $sql = "UPDATE db SET db_Name = '$newName' WHERE db_ID = $db_id";

    if($conn->query($sql)===TRUE) {
      addAlert("<b>Success!</b> You've edited your database.", "success");
    } else  {
      addAlert("<b>Oh no!</b> Something went wrong. Please try again.", "danger");
    }

  }
  header("Location: ".$_SERVER['HTTP_REFERER']);
?>