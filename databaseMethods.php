<?php
  session_start();
  include "connectDB.php";
  include "checkLogin.php";
  
  # add permissions
  if(isset($_POST['addPerms'])) {

    # save inputs
    $user = $_POST['user'];
    $perms = $_POST['permissions'];
    $db = $_POST['db_id'];

    foreach($perms as $val) {
      
      # check each permission if they already exist
      $checkPerm = $conn->query("SELECT * FROM permits WHERE user_ID = $user AND operation = $val AND db = $db");

      if($checkPerm->num_rows === 0) {
        $createPerm = $conn->query("INSERT INTO permits(operation,user_ID,db) VALUES($val,$user,$db)");

        if($createPerm===FALSE) {
          addAlert("<b>Uh oh!</b> Something went wrong. ".$conn->error, "danger");
        break;
        }
      }
    }

    if(empty($_SESSION['alerts'])) {
      addAlert("Successfully added new database permissions!", "success");
    }
  } else 
  
  
  # clear permissions
  if(isset($_GET['clearPerms'])) {

    # get input
    $user = $_GET['user'];
    $db = $_GET['db_id'];

    $clearPermissions = $conn->query("DELETE FROM permits WHERE user_ID = $user AND db = $db");

    if($clearPermissions===TRUE) {
      addAlert("Successfully cleared user permissions!", "success");
    } else {
      addAlert("<b>Uh oh!</b> Something went wrong. ".$conn->error, "danger");
    }
  } else if(isset($_POST['editDB'])) {

    $db_id = $_POST['db_id'];
    $newName = $_POST['rename'];

    $sql = "UPDATE db SET db_Name = '$newName' WHERE db_ID = $db_id";

    if($conn->query($sql)===TRUE) {
      addAlert("<b>Success!</b> You've edited your database.", "success");
    } else  {
      addAlert("<b>Uh oh!</b> Something went wrong. ".$conn->error, "danger");
    }

  } else 
  
  
  
  # edit permissions
  if(isset($_POST['editPerms'])) {

    # save inputs
    $user = $_POST['user'];
    $perms = $_POST['permissions'];
    $db = $_POST['db_id'];

    # get all existing perms for user
    $getAllPerms = $conn->query("SELECT * FROM permits WHERE user_ID = $user AND db = $db");

    if($getAllPerms) {
      # loop through to see if any are disabled and delete
      if($getAllPerms->num_rows > 0) {

        while($permit = $getAllPerms->fetch_assoc()) {
          $operation = $permit['operation'];

          if(!in_array($operation, $perms)) {
            $deleted = $conn->query("DELETE FROM permits WHERE user_ID = $user AND db = $db AND operation = $operation");

            if($deleted === FALSE) {
              addAlert("<b>Uh oh!</b> Something went wrong. ".$conn->error, "danger");
            }
          }
        }
      }

      # we'll add any missing permissions
      foreach($perms as $val) {
      
        # check each permission if they already exist
        $checkPerm = $conn->query("SELECT * FROM permits WHERE user_ID = $user AND operation = $val AND db = $db");
  
        if($checkPerm->num_rows === 0) {
          $createPerm = $conn->query("INSERT INTO permits(operation,user_ID,db) VALUES($val,$user,$db)");
  
          if($createPerm===FALSE) {
            addAlert("<b>Uh oh!</b> Something went wrong. ".$conn->error, "danger");
          break;
          }
        }
      }
    } else {
      addAlert("<b>Uh oh!</b> Something went wrong. ".$conn->error, "danger");
    }

    if(empty($_SESSION['alerts'])) {
      addAlert("Successfully modified permissions!", "success");
    }
  } else 
  
  
  
  
  # create table
  if(isset($_POST['createTB'])) {

    # save input
    $name = $_POST['tbName'];
    $db = $_POST['db_id'];
    
    # check for whitespace 
    if(strcmp($name, str_replace(' ', '', $name)) !== 0) {
       addAlert("<b>Uh oh!</b> There must be no spaces in the table name.", "danger");
    } else {
      # check if table already exists
      $checkTable = $conn->query("SELECT * FROM tb WHERE tb_Name = '$name' AND db_ID = $db");
  
      if($checkTable) {
        if($checkTable->num_rows > 0) {
          addAlert("That table already exists!", "danger");
        } else {
          $createTable = $conn->query("INSERT INTO tb(tb_Name,db_ID) VALUES('$name',$db)");
  
          if($createTable) {
            addAlert("Successfully added new table!", "success");
          } else {
            addAlert("<b>Uh oh!</b> Something went wrong. ".$conn->error, "danger");
          }
        }
      } else {
        addAlert("<b>Uh oh!</b> Something went wrong. ".$conn->error, "danger");
      }
    }
  } else


  # create primary key
  if(isset($_GET['pk'])) {

    # save input
    $tb = $_GET['tb_id'];

    # check if already has PK
    $checkPK = $conn->query("SELECT * FROM attributes WHERE tb_ID = $tb AND isPrimary = 1");

    if(!$checkPK) {
      addAlert("<b>Uh oh!</b> Something went wrong. ".$conn->error, "danger");
    } else {
      if($checkPK->num_rows > 0) {
        addAlert("That table already has a primary key.", "danger");
      } else {

        # create PK
        $createPK = $conn->query("INSERT INTO attributes(attr_Name,colNum,datatype,limitation,isPrimary,isAutoinc,`isNull`,isFK,tb_ID) VALUES('ID',1,'INT',10,1,1,0,0,$tb)");

        if(!$createPK) {
          addAlert("<b>Uh oh!</b> Something went wrong. ".$conn->error, "danger");
        } else {
          addAlert("Successfully created Primary Key (ID)!", "success");
        }
      }
    }
  } else


  # create attribute
  if(isset($_POST['createAttr'])) {

    # save inputs
    $name = $_POST['attr_name'];
    $type = $_POST['datatype'];
    $limit = $_POST['limitation'];
    $pk = isset($_POST['isPrimary']) ? 1 : 0;
    $autoInc = isset($_POST['isAutoInc']) ? 1 : 0;
    $null = isset($_POST['isNull']) ? 1 : 0;
    $fk = isset($_POST['isFK']) ? 1 : 0;
    $fkOf = isset($_POST['isFK']) ? $_POST['FK_of'] : 0;
    $tb = $_POST['table_ID'];
    $pos = $_POST['position'];

    if($fk===1 && $fkOf===0) {
      addAlert("You need to link a foreign key to a table!", "danger");
    } else if(strcmp($name, str_replace(' ', '', $name)) !== 0) {
      addAlert("<b>Uh oh!</b> There must be no spaces in the attribute name.", "danger");
    } else if ($type === -1) {
      addAlert("Please select a datatype!", "danger");
    } else {

      # check if attribute already exists
      $checkAttr = $conn->query("SELECT * FROM attributes WHERE attr_Name = '$name' AND tb_ID = $tb");

      if(!$checkAttr) {
        addAlert("<b>Uh oh!</b> Something went wrong. ".$conn->error, "danger");
      } else {
        if($checkAttr->num_rows > 0) {
          addAlert("An attribute with that name already exists.", "danger");
        } else {

          #check if it's inserted in the middle, and move other columns accordingly
          $checkCol = $conn->query("SELECT * FROM attributes WHERE colNum = $pos AND tb_ID = $tb");

          if(!$checkCol) {
            addAlert("<b>Uh oh!</b> Something went wrong. checkcol num ".$conn->error, "danger");
          } else {
            if($checkCol->num_rows > 0) {

              # get that column and everything after that
              $moveCol = $conn->query("SELECT * FROM attributes WHERE colNum >= $pos AND tb_ID = $tb");

              if(!$moveCol) {
                addAlert("<b>Uh oh!</b> Something went wrong. movecol ".$conn->error, "danger");
              } else {
                while($colRow = $moveCol->fetch_assoc()) {

                  # start moving
                  $colID = $colRow['attr_ID'];
                  $newPos = $colRow['colNum'] + 1;

                  if(!$conn->query("UPDATE attributes SET colNum = $newPos WHERE attr_ID = $colID")) {
                    addAlert("<b>Uh oh!</b> Something went wrong. updating colnum ".$conn->error, "danger");
                  }
                }

              }
            }
            
            if(empty($_SESSION['alerts'])) {
              # create if it doesn't
              $createAttr = $conn->query("INSERT INTO attributes(attr_Name,colNum,datatype,limitation,isPrimary,isAutoInc,`isNull`,isFK,tb_ID) VALUES('$name',$pos,'$type',$limit,$pk,$autoInc,$null,$fk,$tb)");
    
              if(!$createAttr) {
                addAlert("<b>Uh oh!</b> Something went wrong. ".$conn->error, "danger");
              } else if($fk===1) {
                
                # get the id of the insert entry
                $insertID = $conn->insert_id;
    
                # lets create a new relationship (sana all)
                $createRel = $conn->query("INSERT INTO relationships(parent,child) VALUES($fkOf,$insertID)");
    
                if($createRel) {
                  addAlert("Successfully created attribute!", "success");
                } else {
                  $conn->query("DELETE FROM attributes WHERE attr_ID = $insertID");
                  addAlert("<b>Uh oh!</b> Something went wrong. ".$conn->error, "danger");
                }
              } else {
                addAlert("Successfully created attribute!", "success");
              }
            }
          }

        }
      }
    }
  } else



  # rename table
  if (isset($_POST['editTB'])) {

    # save inputs
    $name = $_POST['rename'];
    $tb = $_POST['tb_id'];

    if(strcmp($name, str_replace(' ', '', $name)) !== 0) {
      addAlert("<b>Uh oh!</b> Table name can't have spaces.", "danger");
    } else {

      $checkDuplicate = $conn->query("SELECT * FROM tb where tb_Name = '$name'");

      if(!$checkDuplicate) {
        addAlert("<b>Uh oh!</b> Something went wrong. ".$conn->error, "danger");
      } else {
        
        if($checkDuplicate->num_rows > 0) {
          addAlert("<b>Uh oh!</b> There already exists a table with that name.", "danger");
        } else {
          
          $renameTB = $conn->query("UPDATE tb SET tb_Name = '$name' WHERE tb_ID = $tb");
      
          if(!$renameTB) {
            addAlert("<b>Uh oh!</b> Something went wrong. ".$conn->error, "danger");
          } else {
            addAlert("Successfully renamed table!", "success");
          }
        }

      }
    }

  } else



  # delete table
  if(isset($_GET['deleteTB'])) {

    # save input
    $tb = $_GET['id'];

    $deleteTable = $conn->query("DELETE FROM tb WHERE tb_ID = $tb");

    if(!deleteTable) {
      addAlert("<b>Uh oh!</b> Something went wrong. ".$conn->error, "danger");
    } else {
      addAlert("Successfully delete table!", "success");
    }
  }

  header("Location: ".$_SERVER['HTTP_REFERER']);
?>