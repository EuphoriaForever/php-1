<?php
  if(!isset($_SESSION['Succeed'])) {
    header("Location: login.php");
  } else {
    include "./includes/navbarMain.php";
  }

  if(isset($_GET['logout'])) {
    unset($_SESSION['Succeed']);
    header("Location: login.php");
  }
?>