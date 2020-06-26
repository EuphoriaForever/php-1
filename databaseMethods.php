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
        $createPK = $conn->query("INSERT INTO attributes(attr_Name,datatype,limitation,isPrimary,isAutoinc,`isNull`,isParent,ParentOf,isFK,FK_of,tb_ID) VALUES('ID','INT',10,1,1,0,0,0,0,0,$tb)");

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
    $fkOf = $_POST['FK_of'];

    if($fk===1 && $fkOf===0) {
      addAlert("You need to link a foreign key to a table!", "danger");
    } else if(strcmp($name, str_replace(' ', '', $name)) !== 0) {
      addAlert("<b>Uh oh!</b> There must be no spaces in the attribute name.", "danger");
    } else {

      # check if attribute already exists
      $checkAttr = $conn->query("SELECT * FROM attributes WHERE attr_Name = $name");

      if(!$checkAttr) {
        addAlert("<b>Uh oh!</b> Something went wrong. ".$conn->error, "danger");
      } else {
        if($checkAttr->num_rows > 0) {
          addAlert("An attribute with that name already exists.", "danger");
        } else {

          # create if it doesn't
          $createAttr = $conn->query("INSERT INTO attributes(attr_Name,datatype,limitation,isPrimary,isAutoInc,`isNull`,isParent,ParentOf,isFK,FK_of,tb_ID) VALUES('$name','$type',$limit,$pk,$autoInc,$null,$fk,$fkOd)");

          if(!$createAttr) {
            addAlert("<b>Uh oh!</b> Something went wrong. ".$conn->error, "danger");
          } else if(fk===1) {
            
            # get the id of the insert entry
            $insertID = $conn->insert_id;

            # lets update the parent
            

          }
        }
      }
    }
  }

  header("Location: ".$_SERVER['HTTP_REFERER']);
?>