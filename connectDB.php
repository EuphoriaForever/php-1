<?php
  $conn = new mysqli("localhost","root","","im2");
  if($conn->connect_error){
   die("Connection failed: " . $conn->connect_error);
  }
?>