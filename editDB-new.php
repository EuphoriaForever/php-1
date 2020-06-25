<?php
  session_start();

  include "connectDB.php";
  include "checkLogin.php";

  if(isset($_POST['editDB'])) {

    $db_id = $_POST['db_id'];
    $newName = $_POST['rename'];

    $sql = "UPDATE db SET db_Name = '$newName' WHERE db_ID = $db_id";

    if($conn->query($sql)===TRUE) {
      addAlert("<b>Success!</b> You've edited your database.", "success");
    } else  {
      addAlert("<b>Uh oh!</b> Something went wrong. ".$conn->error, "danger");
    }

  }
  header("Location: ".$_SERVER['HTTP_REFERER']);
?>