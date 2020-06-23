<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/bootstrap.min.css">
    <link rel="stylesheet" href="./styles/chess.css">
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
    <button type="button" class="btn btn-success " data-toggle="modal" data-target="#createTable">New Table</button>
    
    <a class="btn btn-danger" href="functions.php?delete_id=<?php echo $_GET['db_id'] ?>">Delete Database</a>
    
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
            $primaries = array(); #this will hold all tables with primary keys

            # lets save the tables in the db object as an array. this way, the embedding of php into html is minimal and it's not labad sa ulo
            while($table = $tablesResult->fetch_assoc()) {
              array_push($db['tables'], $table);

              # lets check if this table has a PK
              $checkTablePrimeQuery = "SELECT * from attributes WHERE tb_ID = ".$table['tb_ID']." AND isPrimary = 1";
              $checkTablePrimeResult = $conn->query($checkTablePrimeQuery);

              # if it has PK, we add it to our primaries collection
              if($checkTablePrimeResult->num_rows > 0) {
                array_push($primaries, $table);
              }
            }
    
          }
        }
      ?>
      <h4 class="text-info">Database: <?php echo $db['name']; ?></h4>
      <hr>
      <h5 class="text-info text-center">Tables</h5>
      
      <!-- tables accordion BOC -->
      <div class="accordion" id="tables">

        <!-- okay now let's loop through the tables we've saved in line 63 -->
        <?php if(isset($db['tables'])) { foreach($db['tables'] as $ind=>$tb){ ?>
          <div class="card">
            <div class="card-header" id="heading-<?php echo $ind; ?>">
              <h2 class="mb-0">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse-<?php echo $ind; ?>" aria-expanded="false" aria-controls="collapse-<?php echo $ind; ?>"><?php echo $tb['tb_Name']; ?></button>
              </h2>
            </div>

            <div class="collapse" id="collapse-<?php echo $ind; ?>" aria-labelledby="heading-<?php echo $ind; ?>" data-parent="#tables">
              <div class="card-body">
                <a href="database.php?pk=1&tb_id=<?php echo $tb['tb_ID']; ?>&db_id=<?php echo $db_id; ?>" class="btn btn-success">Create a Primary Key (ID)</a>

                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#createAttr-<?php echo $ind; ?>">Create Attribute</button>

              </div>
            </div>
          </div>

          <!-- corresponding attribute modal BOC -->
          <div class="modal fade" id="createAttr-<?php echo $ind; ?>" tabindex="-1" role="dialog" aria-labelledby="createAttrLabel-<?php echo $ind; ?>" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">

                <div class="modal-header">
                  <h5 class="modal-title" id="createAttrLabel-<?php echo $ind; ?>">Create New Attribute</h5>

                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
                </div>

                <form action="database.php" method="post" enctype="multipart/form-data">
                  <div class="modal-body">
                    <div class="form-row">
                      <div class="form-group col-md-6 col-sm-12">
                        <label class="required" for="attr_name-<?php echo $ind; ?>">Attribute Name</label>
                        <input type="text" name="attr_name" id="attr_name-<?php echo $ind; ?>" class="form-control" placeholder="Enter Attribute Name" required>
                      </div>

                      <div class="form-group col-md-6 col-sm-12">
                        <label class="required" for="datatype-<?php echo $ind; ?>">Datatype</label>
                        <input type="text" name="datatype" id="datattype-<?php echo $ind; ?>" class="form-control" placeholder="Enter Datatype" required>
                      </div>
                    </div>

                    <div class="form-row">
                      <div class="form-group col-md-6 col-sm-12">
                        <label for="limitation-<?php echo $ind; ?>" class="required">Limitation</label>
                        <input type="number" name="limitation" id="limitation-<?php echo $ind; ?>" class="form-control" placeholder="0000" required>
                      </div>

                      <div class="form-group col-md-6 col-sm-12">
                        <div class="custom-control custom-switch">
                          <input type="checkbox" name="isPrimary" value="1" id="primary-<?php echo $ind; ?>" class="custom-control-input">
                          <label for="primary-<?php echo $ind; ?>" class="custom-control-label">Primary Key</label>
                        </div>

                        <div class="custom-control custom-switch">
                          <input type="checkbox" name="isAutoInc" value="1" id="autoInc-<?php echo $ind; ?>" class="custom-control-input">
                          <label for="autoInc-<?php echo $ind; ?>" class="custom-control-label">Auto Increment</label>
                        </div>

                        <div class="custom-control custom-switch">
                          <input type="checkbox" name="isNull" value="1" id="null-<?php echo $ind; ?>" class="custom-control-input">
                          <label for="null-<?php echo $ind; ?>" class="custom-control-label">Nullable</label>
                        </div>
                      </div>
                    </div>

                    <div class="form-row">
                      <div class="form-group col-md-6 col-sm-12">
                        <label class="hidden">Empty</label>
                        <div class="custom-control custom-switch">
                          <input type="checkbox" name="isFK" value="1" id="fk-<?php echo $ind; ?>" class="custom-control-input" index="<?php echo $ind; ?>">
                          <label for="fk-<?php echo $ind; ?>" class="custom-control-label">Foreign Key</label>
                        </div>
                      </div>

                      <div class="form-group col-md-6 col-sm-12">
                        <label for="FK_of-<?php echo $ind; ?>">Choose a Table</label>
                        <select name="FK_of" id="FK_of-<?php echo $ind; ?>" class="form-control" disabled>
                          <option selected value="0" style="display: none">Choose</option>
                          <!-- loop through our primaries collection and display the tables -->
                          <?php

                            # count the number of available tables to choose from
                            $count = 0;
                            
                            foreach($primaries as $pkTB) {
                              if($pkTB['tb_ID'] !== $tb['tb_ID']) {
                                $count++;
                                echo '<option value="'.$pkTB['tb_ID'].'">'.$pkTB['tb_Name'].'</option>';
                              }
                            }
                            
                            if($count === 0) {
                              echo '<option disabled value="0">No tables available</option>';
                            }
                          ?>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" name="submit">Create Attribute</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <!-- corresponding attribute modal EOC -->
        <?php } } else { ?>
          <h6 class="text-center">There are no tables in this database yet.</h6>
        <?php } ?>

      </div>
      <!-- tables accordion EOC -->
    </div>
  </div>
  <!-- main container EOC -->
  
  <!-- modals BOC -->
  
    <!-- create new table modal BOC -->
    <div class="modal fade" id="createTable" tabeindex="-1" role="dialog" aria-labelledby="newTableHeader" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="newTableHeader">Create New Table</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">

              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <form action="database.php" method="post" enctype="multipart/form-data">
            <div class="modal-body">
              <div class="form-row">
                <div class="form-group col-12">
                  <label for="tbName">Table Name</label>
                  <input type="text" name="tbName" id="tbName" class="form-control" required="required" placeholder="Enter table name">
                </div>
              </div>

              <input type="hidden" name="db_id" value="<?php echo $db_id; ?>">
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-success" name="submit">Create Table</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- create new table modal EOC -->

  <!-- modals EOC -->


  <!-- alright here are the php actions -->
  <?php

  ?>


   <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script>
      $(document).ready(()=>{
        $('input[name*="isFK"]').click((e)=>{
          let code = $(e.target).attr('index');
          if($(e.target).is(":checked")) {
            $(`select#FK_of-${code}`).removeAttr("disabled");
          } else {
            $(`select#FK_of-${code}`).attr("disabled", "");
          }
        })
      });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  
</body>
</html>