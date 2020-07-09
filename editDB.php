<html>
<head>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/bootstrap.min.css">
    <title>Database</title>
</head>
<body class="bg-secondary">
     
    <?php
  // include "./includes/navbar.php";

    session_start(); #start session in each form validation page so that we can all access the super global var $_SESSION

    
    if(!isset($_SESSION['Succeed'])) { #if there is no current login session detected, go to login page
      header("Location: login.php");
    }else{

    //   if($_SESSION['users'][$_SESSION['Succeed']]['type'] ==="administrator"){
    //     include "./includes/navbarAdmin.php";
    //   }else {
    //     include "./includes/navbarUser.php";
    //   }
        include "./includes/navbarMain.php";

    }

    if(isset($_GET['logout'])) { #if u wanna logout, remove current login session and redirect to login page
      unset($_SESSION['Succeed']);
      header("Location: login.php");
    }

    
  ?>
          <div class="container w-50 position-relative mx-auto p-5 my-5 bg-light shadow">
<?php
                    $server = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "im2";
                    $db_ID = 0;

                    if(isset($_GET['db_ID'])){
                      $db_ID = $_GET['db_ID'];
                    }
                
                    $conn = new mysqli($server,$username,$password,$dbname);
                    if($conn->connect_error){
                        die("Connection failed: " . $conn->connect_error);
                    }
    
                    $sql = "SELECT * FROM db WHERE db_ID = $db_ID";
                    $result = $conn->query($sql);

                    if($result->num_rows > 0){
                      $row = $result->fetch_assoc();
                    echo'
                      <form action="editDB.php" method="POST" enctype="multipart/form-data">
                      <div class="form-row">
                           <div class="form-group col-md-6">
                               <label for="fname">First Name</label>
                               <input type="text" class="form-control" name="db_Name" value="'.$row['db_Name'].'" placeholder="Enter database name" required>
                               <input type="hidden"  class="form-control" name="db_ID" value="'.$db_ID.'" id="db_ID"  required>
                               </div>
                       </div>
    
                      <input type="submit" class="btn btn-dark text-white" value="Submit" name="submit" required>
                      <a href="database.php?db_id='.$db_ID.'">
                        <button type="button" class="btn btn-warning text-white" data-dismiss="modal">Close</button>
                      </a>  
                    </form>
                       ';                          
                    }

                    
              if(isset($_POST['submit'])){
                $db_Name = $_POST['db_Name'];
                $db_ID = $_POST['db_ID'];

                $isOkay = checkPermit('3',$db_ID,$conn);

                if($isOkay==TRUE){  
                  $sql = "UPDATE db SET db_Name = '$db_Name' WHERE db_ID = $db_ID ";
                  if($conn->query($sql)===TRUE){
                    echo "<script language='javascript'>alert('Information Successfully Edited!');window.location.href='database.php?db_id=$db_ID';</script>";
                    }
                  }else{
                    echo "<script language='javascript'>alert('Uh oh! You do not have a permit to tinker on this TB');window.location.href='database.php?db_id=$db_ID';</script>";
                  }
                
            }


            function checkPermit($operation,$db_ID,$conn){
              $isOkay = FALSE;

              if($_SESSION['users'][$_SESSION['Succeed']]['type']==="administrator"){
                  $isOkay = TRUE;
              }else{
<<<<<<< HEAD

                  $username = $_SESSION['Succeed'];
                  $sql = "SELECT * FROM users WHERE username ='$username'";
                 
=======
                #users can edit databases but also the databases that don't belong to them(a user who owns a db can crud their own db but not others)
                  $userID = $_SESSION['Succeed']['id'];#I'll change this to get the ID instead because ID is absolutely unique, yes username is as well but that's due to conditions, what if someone manages to change the sql file
                  $sql = "SELECT * FROM users WHERE user_id ='$userID'";
                 #BUMP
>>>>>>> 571faa1... my line 102 I changed it from username to id, using the pk
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
    </div>
<!--HELLO I AM BIG FEAR!!!!! this is where I got stuck!-->





   <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  
</body>
</html>