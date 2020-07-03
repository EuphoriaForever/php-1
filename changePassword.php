<?php
    session_start(); #start session in each form validation page so that we can all access the super global var $_SESSION    
    include "connectDB.php";
    include "checkLogin.php";
    displayAlert();        
    $user="";  
    $conn = new mysqli("localhost","root","","im2");
?>

<html>
  <head>
      <!-- Bootstrap CSS -->
      <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/ Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous"> 
      <link rel="stylesheet" href="./styles/bootstrap.min.css"> 
      <title><?php echo ''.$_SESSION['Succeed']['username'].''?> Profile Edit </title>
  </head>

<body class="bg-secondary">     
          <div class="container w-50 position-relative mx-auto p-5 my-5 bg-light shadow">
<?php     
     #start of password changing          
            if(isset($_GET['pass_ID'])){            
              $user=$_GET['pass_ID'];
            }
                if($conn->connect_error){
                  die("Connection failed: " .$conn->connect_error);
                }else{                  
                  $sql_2="SELECT * FROM users WHERE user_id =$user";
                  if($conn->query($sql_2)){
                    $result2=$conn->query($sql_2);
                    $info=$result2->fetch_assoc();
                    echo'
                        <form action="changePassword.php" method="POST" enctype="multipart/form-data">
                          <div class="form-row">
                               <div class="form-group col-md-6">
                                   <label for="fname">Password</label>
                                   
                                   <input type="password" class="form-control" name="new_pass" placeholder="Enter new password" required>
                                   <input type="password" class="form-control" name="repeat" placeholder="Re-enter new password" required>

                                   <input type="hidden"  class="form-control" name="user_id" value="'.$info['user_id'].'" id="user_id"  required>
                                </div>
                           </div>                                                      
                                <input type="submit" class="btn btn-dark text-white" value="Submit" name="sub" required>
                                <a href="profile.php?user_id='.$info['user_id'].'">
                                  <button type="button" class="btn btn-warning text-white" data-dismiss="modal">Close</button>
                                </a>
                        </form>
                          ';                        
                  }
                }
                if(isset($_POST['sub'])){
                  $password = $_POST['new_pass'];
                  $repeat=$_POST['repeat'];
                  $user_id = $_POST['user_id'];
                    if(strlen($password) < 8 || strlen($password) > 12) {
                      echo "<script language='javascript'>alert('Password must be 8 - 12 characters long.');window.location.href='changePassword.php?pass_ID=$user_id';</script>";                               
                    }else if(strcmp($password, $repeat) !== 0){
                      echo "<script language='javascript'>alert('Password and Repeat Password must match!');window.location.href='changePassword.php?pass_ID=$user_id';</script>";                               
                    }else{
                      $sql = "UPDATE users SET password='$password' WHERE user_id=$user_id;";
                        if($conn->query($sql)===TRUE){
                          echo "<script language='javascript'>alert('Password Updated!');window.location.href='profile.php?user_id=$user_id';</script>";
                        }else{
                          echo "ERROR!:".$conn->error;
                        }              
                      }                                       
                }#end of password changing        
?>            
          </div>

   <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  
</body>
</html>