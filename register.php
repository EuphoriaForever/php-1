<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link rel="stylesheet" href="./styles/bootstrap.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

</head>
<body class="bg-secondary">
  <?php

    session_start(); #start session in each form validation page so that we can all access the super global var $_SESSION

    include "./includes/navIntro.php";


     
      if(!isset($_SESSION['users'])) { # check if this is your first user account, if it is--create the users array
        $_SESSION['users'] = array();

        #since my login validation is gonna rely on $_SESSION, i will have to get the admin account from the database and store it onto a $_SESSION
        $conn = new mysqli("localhost","root","","im2");
        
        if($conn->connect_error){
          die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT * from users WHERE type='user' ";
        $result = $conn->query($sql);
        if($result->num_rows > 0 ){
          $row = $result->fetch_assoc();

            #here I am creating an associative array, which we'll also make for future accounts 
            $_SESSION['users'][$row['username']] =  array('id'=>$row['user_id'],'password' => $row['password'], 'type' => $row['type'], 'name' => $row['username']);
            mysqli_close($conn); 
          }
      }

    


    if(isset($_SESSION['Succeed'])) { # if there is already a detected login session, redirect to welcome page
      header("Location: welcome.php");
    }

    if(isset($_SESSION['err']) && $_SESSION['err'] == "no_account") { # this error was set in login.php || if you tried to login with an email that is not linked to any account, it will flag an alert on your screen and then delete the error so it does not keep repeating
      error("That account does not exist! Please make a new account below.");
      unset($_SESSION['err']);
    }

    //THE FOLLOWING BLOCKS OF CODES ARE SOLELY FOR $_POST['register], it's a lot but it's to make sure it's aight.

    if(isset($_POST['register'])) { #if you hit the register button
      $_SESSION['registration_errors'] = array(); #create a session array that will hold all the errors you've encountered so far 
                  #this will be useful when we have to flag our errors on the form. bec we'll be using in_array() function
     
      $username = $_POST['username'];
      $password = $_POST['password'];
      $type = $_POST['TYPE'];
  
    //   # check if email is valid
    //   if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    //     addErr('email'); #addErr() is a function I made to store which variables have an error in.  naa rani siyas ubos
    //   }

      # check if username is 5+ characters but also not more than 10
      if(strlen($_POST['username']) < 5 || strlen($_POST['username']>10)) {
        addErr('username');
      }

      # check if username match
      if($_POST['username'] != $_POST['repeatUsername']) {
        addErr('repeatUsername');
      }

      # check if password is 8+ characters
      if(strlen($_POST['password']) < 8) {
        addErr('password');
      }

      # check if passwords match
      if($_POST['password'] != $_POST['repeatPass']) {
        addErr('repeatPass');
      }

      if(count($_SESSION['registration_errors']) == 0) { #if you did not encounter any errors, delete error array
        unset($_SESSION['registration_errors']);

        if(!isset($_SESSION['users'])) { # check if this is your first user account, if it is--create the users array
          $_SESSION['users'] = array();
        }
       

        if(isset($_SESSION['users'][$_POST['username']])) { # if all inputs are valid BUT that email is already in use, flag an alert
          error("That username is already in use! Please use another username or <a href='./login.php'>login</a> into your account.");
        
        } else { # if all inputs are valid, save all information as key-value pairs (associative array) using the email as your key
                  $conn = new mysqli("localhost","root","","im2");
        
                   if($conn->connect_error){
                     die("Connection failed: ".$conn->connect_error);
                    }//end of checking for connection error
                 

                        $sql ="INSERT INTO users (user_id,username,password,type) VALUES ('','$username','$password','$type')";
                        if($conn->query($sql)===TRUE){
                             $_SESSION['users'][$_POST['username']] =  array('password' =>$password, 'type' =>$type,'name'=>$username);
                             $_SESSION['newAccount'] = true; # quick session variable just to create a success flag in login.php page to alert you that your account was made succesfully when it redirects
                            header("Location: login.php");
                            mysqli_close($conn); 
                             
                        }//end For Inserting to users
                
        }//end for else

    }//end of IF no errors

     

    } else { # else, if you simply loaded the page and didn't do a form submission, unset any underlying registration errors--so that they're not constantly flagging every refresh/load
      unset($_SESSION['registration_errors']);
    }//END OF CONDITIONS FOR REGISTERING


    //THESE ARE NOW THE FUNCTIONS THAT HAVE BEEN UTILIZED ABOVE AND ON THE FORMS

    function checkErr($err) { # checker function so I don't have to type all of this repeatedly
      return !empty($_SESSION['registration_errors']) && in_array($err, $_SESSION['registration_errors']);
    }

    function feedback($content) { # echo invalid feedback so I don't have to retype
      echo "<div class='invalid-feedback'>".$content."</div>";
    }

    function addErr($err) { # add error, no retype
      array_push($_SESSION['registration_errors'], $err);
    }
    function error($content) { # NO RETYPE FLAG ALERT
      echo "<div class='alert alert-danger w-50 mx-auto my-4'>".$content."</div>";
    }

    // if(isset($_GET['destory'])){
    //   session_destroy();
    // }
  ?>

  <div class="container w-50 position-relative mx-auto p-5 my-5 bg-light shadow">
    <h2 class="font-weight-bold">Register</h2>
    <hr>

    <!-- 
      * create registration form 
      * I have php checkers per input to note when I should flag an error or not
      * I also have a feedback printer so they are prompted as to why their input was considered invalid
      * in an ideal world, I would've taken the time to reinstate the value in each input if you had an error
    -->
    
      <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="POST" class="d-flex flex-column justify-content-center">
        <div class="form-group">
          <label for="username" class="font-weight-bold">Username</label>
          <input type="text" name="username" id="username" class="form-control <?php if(checkErr('username')) echo "is-invalid"; ?>" required="required">
          <?php 
          if(checkErr('username')) {
            feedback("Your first name must be between 5 - 10 characters long!");
          }
          ?>
        </div>

        <div class="form-group">
          <label for="repeatUsername" class="font-weight-bold">Repeat Username</label>
          <input type="text" name="repeatUsername" id="repeatUsername" class="form-control <?php if(checkErr('repeatUsername')) echo "is-invalid"; ?>" required="required">
          <?php 
          if(checkErr('repeatUsername')) {
            feedback("This does not match with previously entered username!");
          }
          ?>
        </div>

        <div class="form-group">
          <label for="password" class="font-weight-bold">Password</label>
          <input type="password" name="password" id="password" class="form-control <?php if(checkErr('password')) echo "is-invalid"; ?>" required>
          <?php 
          if(checkErr('password')) {
            feedback("Password must be at least 8 characters long!");
          }
          ?>
        </div>

        <div class="form-group">
          <label for="repeatPass" class="font-weight-bold">Confirm Password</label>
          <input type="password" name="repeatPass" id="repeatPass" class="form-control <?php if(checkErr('repeatPass')) echo "is-invalid"; ?>" required="required">
          <?php 
          if(checkErr('repeatPass')) {
            feedback("Passwords do not match!");
          }
          ?>
        </div>

        <input type="hidden" name="TYPE" id="TYPE" value='user' class="form-control" required="required">

        <button type="submit" name="register" class="btn btn-success">Register</button>
        <small>Already have an account? <a href="./login.php">Login</a></small>
      </form>
  </div>

  <script src="./scripts/bootstrap.min.js"></script>
     <!-- jQuery first, then Popper.js, then Bootstrap JS -->
     <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

</body>
</html>