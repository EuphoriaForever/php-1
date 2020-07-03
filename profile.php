<?php
  // include "./includes/navbar.php";

    session_start(); #start session in each form validation page so that we can all access the super global var $_SESSION

    include "connectDB.php";
    include "checkLogin.php";

    displayAlert();    
  ?>
<html>

<!--$_GET['db_id'] -->

<head>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
    integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <link rel="stylesheet" href="./styles/bootstrap.min.css">
  <title> <?php echo ''.$_SESSION['Succeed']['username'].''?> </title>
</head>

<body class="bg-secondary">
    <div class="container w-50 position-relative mx-auto p-5 my-5 bg-light shadow">       
      <a class="btn btn-danger" href="profile.php?delete_ID=<?php echo$_SESSION['Succeed']['id'] ?>">Delete Profile</a>
      <a class="btn btn-info" href="profile.php?edit_ID=<?php echo $_SESSION['Succeed']['id'] ?>">Edit Profile Name</a>
    </div>              
          <br>THIS IS A MARKER FOR ME CAUSE I GET LOST IN THE INSPECTOR MODE A LOT HUHUHUHU
    <?php 
    #this too me longer than I would like to admit 
       $conn = new mysqli("localhost","root","","im2");
        //check connection
        if($conn->connect_error){
          die("Connection failed: " . $conn->connect_error);
        }
          if(isset($_GET['delete_ID'])){
            $userID=$_GET['delete_ID'];

            $sql = "DELETE FROM users WHERE user_id= $userID";
              if($conn->query($sql)===TRUE){
                  echo "<script language='javascript'>alert('Profile Successfully Deleted!');window.location.href='?logout=true';</script>";
              }
              else{
                  echo "<script language='javascript'>alert('Uh oh! That wasn't supposed to happen!');</script>";
              }
          }else if(isset($_GET['edit_ID'])){
            $user=$_GET['edit_ID'];
            $sql_1="SELECT * FROM users WHERE user_id=$user";

            if($conn->query($sql_1)){              
              $result=$conn->query($sql_1);
              $data=$result->fetch_assoc();
                  echo'
                      <form action="profile.php" method="POST" enctype="multipart/form-data">
                            <div class="form-row">
                                 <div class="form-group col-md-6">
                                     <label for="fname">Asshole</label>
                                     <input type="text" class="form-control" name="new_name" placeholder="Enter new username" required>
                                     <input type="hidden"  class="form-control" name="user_id" value="'.$data['user_id'].'" id="user_id"  required>
                                  </div>
                             </div>
          
                                  <input type="submit" class="btn btn-dark text-white" value="Submit" name="submit" required>
                                  <a href="profile.php?user_id='.$data['user_id'].'">
                                  <button type="button" class="btn btn-warning text-white" data-dismiss="modal">Close</button>
                                  </a>
                      </form>
                  ';
            
            }
          }
          if(isset($_POST['submit'])){
                $user_name = $_POST['new_name'];
                $user_id = $_POST['user_id'];
                  $sql = "UPDATE users SET username='$user_name' WHERE user_id=$user_id;";
                    if($conn->query($sql)===TRUE){
                      $_SESSION['Succeed']['username']=$user_name;
                      echo "<script language='javascript'>alert('Username Updated!');window.location.href='profile.php?user_id=$user_id';</script>";
                    }else{
                      echo "ERROR!:".$conn->error;
                    }              
            }

        
     ?>
     
      <!-- Optional JavaScript -->
      <!-- jQuery first, then Popper.js, then Bootstrap JS -->
      <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
        integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous">
      </script>
      <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
      </script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
        integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
      </script>
</body>

</html>