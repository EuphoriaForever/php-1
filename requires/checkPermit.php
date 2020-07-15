<?php 
function checkPermit($operation,$db_ID,$conn){
  $isOkay = FALSE;
  if($_SESSION['Succeed']['type']==="administrator"){
    $isOkay = TRUE;
  }else{;                                 
    $userID = $_SESSION['Succeed']['id'];
    $sql = "SELECT * FROM users WHERE user_id ='$userID'";                 
    $result = $conn->query($sql);
    if($result->num_rows>0){
      $row = $result->fetch_assoc();
      $userID = $row['user_id'];
      $sql2 = "SELECT * FROM db where db_ID = $db_ID";
      $result2 = $conn->query($sql2);
      if($result2->num_rows>0){
        $row2 = $result2->fetch_assoc();
        $AuthorID = $row2['Author'];
        if($userID == $AuthorID){
          $isOkay = TRUE;
        }else{
          $sql3 = "SELECT * FROM permits WHERE operation=$operation AND user_ID = $userID AND db = $db_ID";
          $result3 = $conn->query($sql3);
          if($result3->num_rows>0){
            $isOkay = TRUE;
          }
        }
      }
    }
  }
  return $isOkay;
}
?>