<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/bootstrap.min.css">
  <title>Database</title>
</head>
<body class="bg-secondary">
  <?php
    # start session
    session_start();

    # check current login session. if null, ask to login
    if(!isset($_SESSION['Succeed'])) {
      header("Location: login.php");
    }

    # add navbar
    include "./includes/navbarMain.php";

    # logout from session
    if(isset($_GET['logout'])){
      unset($_SESSION['Succeed']);
    }
  ?>

  <!-- main container BOC -->
  <div class="container col-6 mx-auto p-5 my-5 bg-white shadow rounded">
    <button type="button" class="btn btn-success " data-toggle="modal" data-target="#exampleModal">New Table</button>
    
    <a class="btn btn-danger" href="database.php?delete_id=<?php echo $_GET['db_id'] ?>">Delete Database</a>
    
    <a class="btn btn-info" href="editDB.php?db_ID=<?php echo $_GET['db_id'] ?>">Edit Database</a>

    <hr>

    <div class="container-fluid p-4 bg-light">
      <?php 
        # connect to DB
        include "connectDB.php";

        # get current db info
        $db_id = $_GET['db_id'];
        $getDBQuery = "SELECT * FROM db WHERE db_ID = $db_id";
        $dbResult = $conn->query($getDBQuery);
        
        # check if db with that ID exists
        if($dbResult->num_rows > 0) {
          $dbRow = $dbResult->fetch_assoc();
          $db = array('name' => $dbRow['db_Name']);

          # get all tables of that database
          $getTablesQuery = "SELECT * FROM tb WHERE db_ID = $db_id";
          $tablesResult = $conn->query($getTablesQuery);
          
          # check if db has tables
          if($tablesResult->num_rows > 0) {
            $db['tables'] = array();

            # lets save the tables in the db object as an array. this way, the embedding of php into html is minimal and it's not labad sa ulo
            while($table = $tablesResult->fetch_assoc()) {
              array_push($db['tables'], $table);
            }
          }
        }
      ?>
      <h4 class="text-info">Database: <?php echo $db['name']; ?></h4>
      <hr>
      <h5 class="text-info text-center">Tables</h5>
    </div>
  </div>
  <!-- main container EOC -->
  
  <!-- modals BOC -->

  <!-- modals EOC -->
</body>
</html>