<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome!</title>
  <link rel="stylesheet" href="./styles/bootstrap.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

</head>
<body class="bg-secondary">

  <?php

    # always start session
    session_start();
    
    # check login creds and connect to DB
    include "checkLogin.php";
    include "connectDB.php";

    displayAlert();
  ?>

    <div class="container w-50 mx-auto p-5 my-5 bg-white shadow rounded">
        <div class="jumbotron jumbotron-fluid ">
        <div class="container">
             <!-- welcome the user by their saved first name -->
                 <h1 class="text-success">Welcome, <?php echo $_SESSION['users'][$_SESSION['Succeed']]['name'] ?>!</h1>
            <hr>
            <p>
                 If you're seeing this page, then your login session is currently active. Logout below to reset the whole process.
                </p>
            <hr>
            <?php
            
                 if($_SESSION['users'][$_SESSION['Succeed']]['type'] === "administrator"){
                    echo '<p>You issa admin girl</p>';
                }else {
                     echo '<p>You issa regular user girl</p>';
                }
            ?>
        </div><!-- container EOCr-->
      <!-- logout get method -->
      <a href="?logout=true" class="btn btn-danger">Logout?</a>
         
         <!--Adding Button and Modal BOC-->
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-success " data-toggle="modal" data-target="#exampleModal">
            New Database 
        </button>

        <!-- Modal -->
        <div class="modal fade text-dark" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">What's in a name...</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div><!--modal header EOC-->
                 <!--Content within the modal modal-content BOC-->
                    <form action="welcome.php" method="POST" enctype="multipart/form-data">
                      <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-12">
                                 <label for="dbName">Database Name</label>
                                <input type="text" class="form-control" name="dbName" placeholder="Enter database name" required>
                            </div>
                     
                         </div><!--form row EOC-->
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success" name="submit">Submit</button>
                      </div>
                    </form><!--EOC form-->
              </div><!-- modal-content EOC-->
            </div><!--Modal-dialog EOC-->
         </div><!--MODAL EOC-->
        <!--Adding Button and Modal EOC-->
      </div> <!-- jumbotron EOC-->
    </div><!--Container with the width thing EOC-->

<?php
     if(isset($_POST['submit'])){
     
        $db = $_POST['dbName'];
        $author = $_SESSION['users'][$_SESSION['Succeed']]['id'] ;

        $sql = "INSERT INTO db (db_ID,db_Name,Author) VALUES ('','$db','$author')";
                           
        if($conn->query($sql)===TRUE){
          addAlert("Database created successfully!", "success");
        }
     }
?>

  <script src="./scripts/bootstrap.min.js"></script>
     <!-- jQuery first, then Popper.js, then Bootstrap JS -->
     <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

</body>
</html>