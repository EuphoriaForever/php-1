<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
  <link rel="stylesheet" href="./styles/bootstrap.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
  <link rel="stylesheet" href="./styles/chess.css">
  <title>Welcome</title>
</head>
<body class="bg-secondary">
  <?php
    # start session, connect db, check login
    session_start();
    include "connectDB.php";

    # add inserting in this section so the navbar can adopt the new database name
    if(isset($_POST['createDB'])) {

      # save info
      $db = $_POST['dbName'];
      $author = $_SESSION['Succeed']['id'];

      # check if there is already a database with that name
      $checkExistence = $conn->query("SELECT * FROM db WHERE db_Name = '$db'");

      if($checkExistence->num_rows > 0) {
        
        # display alert
        addAlert("A database with that name already exists.", "danger");
      } else {

        # create database and flag a success alert
        $create = $conn->query("INSERT INTO db(db_Name,Author) VALUES('$db','$author')");

        # check if query was successfully done
        if($create === TRUE) {
          addAlert("Database successfully made!", "success");
        } else {
          addAlert("<b>Uh oh!</b> Something went wrong! ".$conn->error, "danger");
        }
      }
    }

    include "checkLogin.php";

    # print user type
    function userType() {
      return $_SESSION['Succeed']['type'] === 'administrator' ? 'admin' : 'regular user';
    }

    displayAlert();
  ?>

  <!-- main container BOC -->
  <div class="container w-50 bg-white p-5 mx-auto my-5 rounded shadow">
    <div class="jumbotron jumbotron-fluid">
      <div class="container">
        <h1 class="text-success">Welcome, <?php echo $_SESSION['Succeed']['username']; ?>!</h1>
        <hr>
        <p>
          If you're seeing this page, then your login session is currently active. Logout below to reset the whole process.
        </p>
        <hr>
        <p>
          User type: <?php echo userType();?>
        </p>
        <a href="?logout=true" class="btn btn-danger">Logout</a>
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#newDatabase">New Database</button>
      </div>
    </div>
  </div>
  <!-- main container EOC -->



  <!-- new database modal BOC -->
    <div class="modal fade" id="newDatabase" tabindex="-1" role="dialog" aria-labelledby="newDatabaseHeader" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="newDatabaseHeader">Create New Database</h5>

            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <div class="modal-body">
              <div class="form-group">
                <label for="dbName" class="required">Database Name</label>
                <input type="text" name="dbName" id="dbName" class="form-control" required>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-success" name="createDB">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  <!-- new database modal EOC -->

  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
</body>
</html>