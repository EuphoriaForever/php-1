<?php
  session_start();

  if(!isset($_SESSION['Succeed'])) {
    header("Location: login.php");
  } else {
    header("Location: welcome.php");
  }

?>