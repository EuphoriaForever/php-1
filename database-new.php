<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/bootstrap.min.css">
    <link rel="stylesheet" href="./styles/chess.css">
  <title>Database</title>
</head>
<body class="bg-secondary">
  <?php
    # start session
    session_start();

    # include login checker and connect db
    include "connectDB.php";
    include "checkLogin.php";

    # get current db info
    $db_id = $_GET['db_id'];
    $getDBQuery = "SELECT * FROM db WHERE db_ID = $db_id";
    $dbResult = $conn->query($getDBQuery);
    
    # check permission functions

    function isAllowed($op) {
      return in_array($op, $GLOBALS['permissions']);
    }

    # check if db with that ID exists
    if($dbResult->num_rows > 0) {
      # actions here

      $dbRow = $dbResult->fetch_assoc();
      $db = array('name' => $dbRow['db_Name']);
      
      # get author of table
      $authorID = $dbRow['Author'];
      $getAuthorResult = $conn->query("SELECT * FROM users WHERE user_id = $authorID");
      
      if($getAuthorResult->num_rows > 0) {
        $author = $getAuthorResult->fetch_assoc();
        $db['author'] = $author['username'];
        $db['authorID'] = $author['user_id'];
      }
      
      # get your permissions
      $permissions = array();

      if($_SESSION['Succeed']['type'] === 'administrator' || $db['authorID'] === $_SESSION['Succeed']['id']) {

        # if admin, add all perms
        array_push($permissions, 1, 2, 3, 4);
      } else {

        # if user, add only allowed perms
        $checkPermit = $conn->query("SELECT * FROM permits WHERE user_ID = ".$_SESSION['Succeed']['id']." AND db = $db_id");

        if($checkPermit) {
          if($checkPermit->num_rows > 0) {
            while($permit = $checkPermit->fetch_assoc()) {
              array_push($permissions, $permit['operation']);
            }
          }
        }
      }

      # get all tables of that database
      $getTablesQuery = "SELECT * FROM tb WHERE db_ID = $db_id";
      $tablesResult = $conn->query($getTablesQuery);
      
      # check if db has tables
      if($tablesResult->num_rows > 0) {
        $db['tables'] = array();
        $primaries = array(); #this will hold all tables with primary keys

        # lets save the tables in the db object as an array. this way, the embedding of php into html is minimal and it's not labad sa ulo
        while($table = $tablesResult->fetch_assoc()) {
          $hasPrime = FALSE;
          $pushTable = array(
            'id' => $table['tb_ID'],
            'name' => $table['tb_Name'],
            'db' => $table['db_ID'],
            'headers' => array(),
            'rows' => array()
          );

          # lets get the headers
          $getHeaders = $conn->query("SELECT * FROM attributes WHERE tb_ID = ".$pushTable['id']." ORDER BY colNum ASC");

          if($getHeaders) {
            if($getHeaders->num_rows > 0) {

              $pushTable['headerCount'] = $getHeaders->num_rows;

              # save to headers array and add values in the column
              while($header = $getHeaders->fetch_assoc()) {
                array_push($pushTable['headers'], array('name' => $header['attr_Name'], 'colNum' => $header['colNum'], 'id' => $header['attr_ID']));

                if($header['isPrimary']) {
                  $pushTable['pk'] = $header['attr_ID'];
                  array_push($primaries, $pushTable);
                }
                # look for rows and save to corresponding index
                $getRows = $conn->query("SELECT * FROM `rows` WHERE attr_ID = '".$header['attr_ID']."' ORDER BY rowNum ASC");

                if($getRows) {
                  if($getRows->num_rows > 0) {

                    # save rows in rows array with index
                    while($tbRow = $getRows->fetch_assoc()) {

                      # temp save info
                      $rowNum = $tbRow['rowNum'];
                      $val = $tbRow['value'];
                      $rowID = $tbRow['row_ID'];
                      $rowColId = $tbRow['attr_ID'];

                      if(!isset($pushTable['rows'][$rowNum])) {
                        $pushTable['rows'][$rowNum] = array();
                      }
                      
                      $pushTable['rows'][$rowNum][$rowColId] = array(
                        'value' => $val,
                        'id' => $rowID,
                      );
                    }
                  }
                } else {
                  addAlert("<b>Uh oh!</b> Something went wrong. ".$conn->error, "danger");
                }
              }
            }
          } else {
            addAlert("<b>Uh oh!</b> Something went wrong. ".$conn->error, "danger");
          }
          array_push($db['tables'], $pushTable);
        }

      }

      # get permissions for table
      $permitted = array();
      $admins = array();

      # add all admins
      $getAdmins = $conn->query("SELECT * FROM users WHERE type = 'administrator'");

      if($getAdmins->num_rows > 0) {

        # add all admins to permitted array with "All access"
        while($admin = $getAdmins->fetch_assoc()) {
          array_push($admins, $admin['username']);
        }
      }

      # get all database specific permissions
      $getPermissionsResult = $conn->query("SELECT * FROM permits WHERE db = $db_id");
      
      if($getPermissionsResult->num_rows > 0) {
        while($permittedUser = $getPermissionsResult->fetch_assoc()) {
          
          # get the user connected to the permission
          $userID = $permittedUser['user_ID'];


          $findUserResult = $conn->query("SELECT * FROM users WHERE user_ID = $userID");

          if($findUserResult->num_rows > 0) {

            $userRow = $findUserResult->fetch_assoc();
            $user = $userRow['username'];
            $permitted[$user]['id'] = $userID;

            # run through the permit table again to see if a certain user has more than one permission
            $checkPerms = $conn->query("SELECT * FROM permits WHERE db = $db_id AND user_ID = $userID");


            if($checkPerms->num_rows === 3) { # if they have 4 permissions on the database (meaning complete crud), save them under "all access"
              $permitted[$user]['perms'] = array("All Access");
            } else if ($checkPerms->num_rows > 0) {

              # if they don't have complete perms, find these operations one by one
              $opID = $permittedUser['operation'];
              
              # search for the operation name in operations table
              $findOp = $conn->query("SELECT * FROM operations WHERE op_ID = $opID");

              if($findOp->num_rows > 0) {
                $operation = $findOp->fetch_assoc();

                # add the operation to the user's array of permissions
                if(isset($permitted[$user]['perms'])) {

                  # if they already have a pre-exsiting array, push into it
                  array_push($permitted[$user]['perms'], $operation['operation']);
                } else {

                  # if not, create an array for the user
                  $permitted[$user]['perms'] = array($operation['operation']);
                }
              }
            }
            
          }
        }
      }

      # save all users for future use in adding permissions
      $allUsers = array();

      $getUsers = $conn->query("SELECT * FROM users WHERE user_id <> ".$db['authorID']." AND `type` <> 'administrator'");

      if($getUsers->num_rows > 0) {
        while($thisUser = $getUsers->fetch_assoc()) {

          # save the user in the allUsers array as a key-value pair
          $allUsers[$thisUser['user_id']] = $thisUser['username'];
        }
      }

      # get all datatypes 
      $datatypes = array();

      $getDatatypes = $conn->query("SELECT * FROM datatypes");

      if($getDatatypes->num_rows > 0) {
        while($foundType = $getDatatypes->fetch_assoc()) {
          $datatypes[$foundType['data_ID']] = $foundType['data_Name'];
        }
      }
    } else {
      header("Location: welcome.php");
    }

    # will display all alerts at the top
    displayAlert();

  ?>

  <!-- main container BOC -->
  <div class="container col-6 mx-auto p-5 my-5 bg-white shadow rounded">
    <?php if(isAllowed(1)) { ?>
      <button type="button" class="btn btn-success " data-toggle="modal" data-target="#createTable">New Table</button>
    <?php } ?>

    <?php if(isAllowed(3)) { ?>
      <button type="button" class="btn btn-info" data-toggle="modal" data-target="#editDB">Edit Database</button>
    <?php } ?>
    
    <?php if(isAllowed(4)) { ?>
      <a class="btn btn-danger" href="deleteDB.php?delete_id=<?php echo $_GET['db_id'] ?>">Delete Database</a>
    <?php } ?>
    
    <button class="btn btn-dark" data-toggle="modal" data-target="#permissions">Permissions</button>

    <hr>

    <div class="container-fluid p-4 bg-light">
      <h4 class="text-info">Database: <?php echo $db['name']; ?></h4>
      <hr>
      <h5 class="text-info text-center">Tables</h5>
      
      <!-- tables accordion BOC -->
      <div class="accordion border-bottom rounded" id="tables">

        <!-- okay now let's loop through the tables we've saved in line 63 -->
        <?php if(isset($db['tables'])) { foreach($db['tables'] as $ind=>$tb){ ?>
          <div class="card">
            <div class="card-header" id="heading-<?php echo $ind; ?>">
              <h2 class="mb-0 row justify-content-between">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse-<?php echo $ind; ?>" aria-expanded="false" aria-controls="collapse-<?php echo $ind; ?>"><?php echo $tb['name']; ?></button>

                <div class="row justify-content-end pr-3">
                  <?php if(isAllowed(1)) { ?>
                    <a href="addRow.php?tb_id=<?php echo $tb['id']; ?>" class="btn btn-success">
                      <i class="fa fa-plus mr-1"></i>
                      Insert Values
                    </a>
                  <?php } ?>

                  <?php if(!empty($permissions)) { ?>
                    <div class="dropdown ml-3">
                      <button class="btn dropdown-toggle btn-outline-dark border-0" type="button" id="tableSettings-<?php echo $ind; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-cog"></i>
                      </button>
    
                      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="tableSettings-<?php echo $ind; ?>">

                        <?php if(isAllowed(1)) { ?>
                          <a href="databaseMethods.php?pk=true&tb_id=<?php echo $tb['id']; ?>&db_id=<?php echo $db_id; ?>" class="dropdown-item">Create a Primary Key (ID)</a>

                          <button type="button" class="dropdown-item" data-toggle="modal" data-target="#createAttr-<?php echo $ind; ?>">Create Attribute</button>
                        <?php } ?>

                        <?php if(isAllowed(3)) { ?>
                          <button type="button" data-toggle="modal" data-target="#editTB-<?php echo $ind; ?>" class="dropdown-item">Edit Table</button>
                        <?php } ?>

                        <?php if(isAllowed(4)) { ?>
                          <a href="databaseMethods.php?deleteTB=true&id=<?php echo $tb['id']; ?>" class="dropdown-item alert-danger border-top">Delete Table</a>
                        <?php } ?>
                      </div>
                    </div>
                  <?php } ?>
                </div>

              </h2>
            </div>

            <div class="collapse" id="collapse-<?php echo $ind; ?>" aria-labelledby="heading-<?php echo $ind; ?>" data-parent="#tables">
              <div class="card-body">
                <?php if(!empty($tb['headers'])) { ?>
                  <table class="table table-striped">
                    <thead class="thead-dark">
                      <?php foreach($tb['headers'] as $head) { ?>
                        <th scope="col"><?php echo $head['name']; ?></th>
                      <?php } ?>
                    </thead>
                    <?php if(!empty($tb['rows'])) { ?>
                      <tbody>
                        <?php foreach ($tb['rows'] as $rowArr) {?>
                          <tr>
                            <?php foreach ($tb['headers'] as $head) { ?>
                              <td>
                                <?php 
                                  $headerID = $head['id'];

                                  echo isset($rowArr[$headerID]) ? $rowArr[$headerID]['value'] : '<i>Null</i>';
                                ?>
                              </td>
                            <?php } ?>
                          </tr>
                        <?php } ?>
                      </tbody>
                    <?php } ?>
                  </table>
                <?php } ?>

                <?php if(empty($tb['headers'])) { ?>
                  <p>This table is empty.</p>
                <?php } ?>
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

                <form action="databaseMethods.php" method="post" enctype="multipart/form-data">
                  <div class="modal-body">
                    <div class="form-row">
                      <div class="form-group col-md-6 col-sm-12">
                        <label class="required" for="attr_name-<?php echo $ind; ?>">Attribute Name</label>
                        <input type="text" name="attr_name" id="attr_name-<?php echo $ind; ?>" class="form-control" placeholder="Enter Attribute Name" required>
                      </div>

                      <div class="form-group col-md-6 col-sm-12">
                        <label class="required" for="datatype-<?php echo $ind; ?>">Datatype</label>
                        <select class="form-control" name="datatype" id="datatype-<?php echo $ind; ?>" required>
                          <option value="-1" style="display: none" selected>Choose datatype...</option>
                          <?php foreach($datatypes as $id => $val) { ?>
                            <option value="<?php echo $id; ?>"><?php echo $val; ?></option>
                          <?php } ?>
                        </select>
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
                          <option selected value="-1" style="display: none">Choose</option>
                          <!-- loop through our primaries collection and display the tables -->
                          <?php

                            # count the number of available tables to choose from
                            $count = 0;
                            
                            foreach($primaries as $pkTB) {
                              if($pkTB['id'] !== $tb['id']) {
                                $count++;
                                echo '<option value="'.$pkTB['pk'].'">'.$pkTB['name'].'</option>';
                              }
                            }
                            
                            if($count === 0) {
                              echo '<option disabled value="0">No tables available</option>';
                            }
                          ?>
                        </select>

                        <input type="hidden" name="table_ID" value="<?php echo $tb['id']; ?>">
                      </div>
                      
                      <?php if(!empty($tb['headers'])) { ?>
                        <div class="form-group col-12">
                          <label for="position-<?php echo $ind; ?>">Insert...</label>
                          <select name="position" id="position-<?php echo $ind; ?>" class="form-control">
                            <option value="1">at the beginning of table</option>
                            <?php foreach($tb['headers'] as $headNum => $headerInfo) { ?>
                              <option 
                              value="<?php echo $headerInfo['colNum']+1; ?>"
                              <?php echo $headNum === $tb['headerCount'] - 1 ? 'selected' : ''; ?>>after <?php echo $headerInfo['name']; ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      <?php } ?>

                      <?php if(empty($tb['headers'])) { ?>
                        <input type="hidden" name="position" value="1">
                      <?php } ?>
                    </div>
                  </div>

                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" name="createAttr">Create Attribute</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <!-- corresponding attribute modal EOC -->


          <!-- corresponding edit table modal BOC -->
          <div class="modal fade" id="editTB-<?php echo $ind; ?>" role="dialog" tabindex="-1" aria-labelledby="editTBHeader-<?php echo $ind; ?>" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="editTBHeader-<?php echo $ind; ?>">Edit Table</h5>
                  
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>

                <form action="databaseMethods.php" method="post">
                  <div class="modal-body">
                    <div class="form-row">
                      <div class="form-group col-12">
                        <label for="rename">Rename Table</label>
                        <input type="text" name="rename" id="rename" class="form-control" required="required" placeholder="Enter new table name">
                      </div>
                    </div>

                    <input type="hidden" name="tb_id" value="<?php echo $tb['id']; ?>">
                  </div>

                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" name="editTB">Edit Table</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <!-- corresponding edit table modal EOC -->
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

          <form action="databaseMethods.php" method="post" enctype="multipart/form-data">
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
              <button type="submit" class="btn btn-success" name="createTB">Create Table</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- create new table modal EOC -->

    <!-- edit database modal BOC -->
    <div class="modal fade" id="editDB" role="dialog" tabindex="-1" aria-labelledby="editDBHeader" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editDBHeader">Edit Database</h5>
            
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <form action="databaseMethods.php" method="post">
            <div class="modal-body">
              <div class="form-row">
                <div class="form-group col-12">
                  <label for="rename">Rename Database</label>
                  <input type="text" name="rename" id="rename" class="form-control" required="required" placeholder="Enter new database name">
                </div>
              </div>

              <input type="hidden" name="db_id" value="<?php echo $db_id; ?>">
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-success" name="editDB">Edit Database</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- edit database modal EOC -->

    <!-- permissions list modal BOC -->
    <div class="modal fade" id="permissions" role="dialog" tabindex="-1" aria-labelledby="permissionsHeader" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="permissionsHeader">User Permissions</h5>

            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="modal-body">
          <!-- if you can found the author before, print it -->
            <div class="row align-items-center justify-content-between mx-3 mb-3">
              <?php if(isset($db['author'])) { ?>
                <p class="col-3">
                  <b>Author:</b> <?php echo $db['author']; ?>
                </p>
              <?php } ?>
            
              <?php if(isAllowed(1)) { ?>
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addPermission"><i class="fa fa-plus mr-1"></i> Add Permission</button>
              <?php } ?>
            </div>


            <!-- if no extra permissions, say so. If there are, print it. -->
            <?php if(empty($permitted) && empty($admins)) { ?>
              <h5 class="text-center font-weight-bold">There are no extra permissions for this table.</h5>
            <?php } else { ?>
              <table class="table table-striped text-center">
                <thead class="thead-dark">
                  <tr>
                    <th scope="col">User</th>
                    <th scope="col">Operation</th>
                    <th></th>
                  </tr>
                </thead>

                <tbody>
                <!-- go through the list of permitted users -->
                  <?php foreach($admins as $admin) {?>
                    <tr>
                      <td><?php echo $admin; ?></td>
                      <td>All Access</td>
                      <td></td>
                    </tr>
                  <?php } 
                  
                    foreach($permitted as $user => $info) { ?>
                    <tr>
                      <td><?php echo $user; ?></td>
                      <td>
                        <?php 
                          $str = "";

                          foreach($info['perms'] as $ind => $perm) {

                            if($ind > 0) {
                              $str .= ", ";
                            }

                            $str .= $perm;
                          }

                          echo $str;
                        ?>
                      </td>
                      <td>
                        <?php if(isAllowed(3)) { ?>
                          <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editPerms-<?php echo $info['id']; ?>"><i class="fa fa-edit mr-1"></i> Edit</button>
                        <?php } ?>

                        <?php if(isAllowed(4)) { ?>
                          <a href="databaseMethods.php?clearPerms=true&user=<?php echo $info['id'] ?>&db_id=<?php echo $db_id; ?>" class="btn btn-danger">
                            <i class="fa fa-trash mr-1"></i> Clear Permissions
                          </a>
                        <?php } ?>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
    <!-- permissions list modal EOC -->

    <!-- new permission modal BOC -->
    <div class="modal fade" id="addPermission" role="dialog" tabindex="-1" aria-labelledby="addPermissionHeader" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addPermissionHeader">Add New Database Permission</h5>

            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <form action="databaseMethods.php" method="post">
            <div class="modal-body">
              <div class="form-row">
                <div class="form-group col-6">
                  <label for="user">Choose User</label>
                  <select name="user" id="user" class="form-control">
                    <option value="0" style="display: none">Choose User...</option>
                    
                    <!-- lets print all our users saved in allUsers array -->
                    <?php foreach($allUsers as $id => $user) { ?>
                      <option value="<?php echo $id; ?>"><?php echo $user; ?></option>
                    <?php } ?>

                  </select>
                </div>

                <div class="form-group col-6">
                  <label class="hidden">Empty</label>
                  <div class="row">
                    <div class="col-6">
                      <div class="custom-control custom-switch">
                        <input type="checkbox" name="permissions[]" class="custom-control-input" id="create" value="1">
                        <label class="custom-control-label" for="create">Create</label>
                      </div>

                      
                      <div class="custom-control custom-switch">
                        <input type="checkbox" name="permissions[]" id="update" class="custom-control-input" value="3">
                        <label for="update" class="custom-control-label">Update</label>
                      </div>
                    </div>

                    <div class="col-6">
                      <div class="custom-control custom-switch">
                        <input type="checkbox" name="permissions[]" id="delete" class="custom-control-input" value="4">
                        <label for="delete" class="custom-control-label">Delete</label>
                      </div>

                      <input type="hidden" name="db_id" value="<?php echo $db_id; ?>">
                    </div>

                  </div>
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-success" name="addPerms">Add Permission</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- new permission modal EOC -->


    <!-- loop through edit perms modals BOC -->
    <?php foreach ($permitted as $user => $info) { ?>
      <div class="modal fade" id="editPerms-<?php echo $info['id']; ?>" role="dialog" tabindex="-1" arialabelledby="editPermsHeader-<?php echo $info['id']; ?>" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="editPermsHeader-<?php echo $info['id']; ?>">Edit Permissions for <?php echo $user; ?></h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>

              <form action="databaseMethods.php" method="post">
                <div class="modal-body">
                  <div class="form-group">
                    <div class="custom-control custom-switch custom-control-inline">
                      <input type="checkbox" name="permissions[]" class="custom-control-input" id="create-<?php echo $info['id']; ?>" value="1" 
                      <?php if(in_array('Create', $info['perms']) || in_array("All Access", $info['perms'])) { echo 'checked'; }?>
                      >
                      <label class="custom-control-label" for="create-<?php echo $info['id']; ?>">Create</label>
                    </div>

                    <div class="custom-control custom-switch custom-control-inline">
                      <input type="checkbox" name="permissions[]" id="update-<?php echo $info['id']; ?>" class="custom-control-input" value="3"
                      <?php if(in_array('Update', $info['perms']) || in_array("All Access", $info['perms'])) { echo 'checked'; }?>
                      >
                      <label for="update-<?php echo $info['id']; ?>" class="custom-control-label">Update</label>
                    </div>

                    <div class="custom-control custom-switch custom-control-inline">
                      <input type="checkbox" name="permissions[]" id="delete-<?php echo $info['id']; ?>" class="custom-control-input" value="4"
                      <?php if(in_array('Delete', $info['perms']) || in_array("All Access", $info['perms'])) { echo 'checked'; }?>
                      >
                      <label for="delete-<?php echo $info['id']; ?>" class="custom-control-label">Delete</label>
                    </div>

                    <input type="hidden" name="db_id" value="<?php echo $db_id; ?>">
                    <input type="hidden" name="user" value="<?php echo $info['id']; ?>">
                  </div>
                </div>

                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-success" name="editPerms">Edit Permissions</button>
                </div>
              </form>

            </div>
        </div>
      </div>
    <?php } ?>
    <!-- loop through edit perms modals EOC -->

  <!-- modals EOC -->

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