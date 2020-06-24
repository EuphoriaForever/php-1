<?php
  include "connectDB.php";

  if(isset($_GET['delete_id'])) {
    $deleteDB = $_GET['delete_id'];
    # first lets delete all values, rows, attributes, and tables from the db || this is called cascade deletion
    $findTables = $conn->query("SELECT * FROM tb where db_ID = $deleteDB");
    # find all the tables in the db
    if($findTables->num_rows > 0) {
      while($deleteTable = $findTables->fetch_assoc()) {
        $findAttr = $conn->query("SELECT * FROM attributes WHERE tb_ID = ".$deleteTable['tb_ID']);
        # find all attributes in the table
        if($findAttr->num_rows > 0) {
          while($deleteAttr = $findAttr->fetch_assoc()) {
            $findRows = $conn->query("SELECT * FROM `rows` WHERE attr_ID = ".$deleteAttr['attr_ID']);
            # find all the rows in the attribute
            if($findRows->num_rows >0) {
              while($deleteRow = $findRows->fetch_assoc()) {
                $deleteVal = $conn->query("DELETE FROM val WHERE val_ID = ".$deleteRow['val_ID']);
              }

              $conn->query("DELETE FROM `rows` WHERE attr_ID = ".$deleteAttr['attr_ID']);
            }
          }
          $conn->query("DELETE FROM attributes WHERE tb_ID = ".$deleteTable['tb_ID']);

        }
      }
      $conn->query("DELETE FROM tb WHERE db_ID = ".$deleteDB);
    }

    $conn->query("DELETE FROM db WHERE db_ID = ".$deleteDB);
    addAlert("Successfully deleted database!", "success");
    header("Location: welcome.php");
  }
?>