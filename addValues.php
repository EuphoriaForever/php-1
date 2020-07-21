<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/bootstrap.min.css">
    <link rel="stylesheet" href="./styles/chess.css">
  <title>Database</title>
</head>
<body class="bg-secondary">
  <?php
    # start session, connect to DB, checkLogin
    session_start();
    include "connectDB.php";
    include "checkLogin.php";

    # check if inputs are present
    if(isset($_GET['tb_id']) && isset($_GET['db_id'])) {
      
      $db_id = $_GET['db_id'];
      $tb_id = $_GET['tb_id'];
      $permissions = permissions($db_id);

      if(!isAllowed(1)) {
        addAlert("<b>Oops!</b> You do not have the correct permissions to do that.", "danger");
        header("Location: ".$_SERVER['HTTP_REFERER']);
      } else {

        # get table name
        $tbName = $conn->query("SELECT tb_Name FROM tb WHERE tb_ID = $tb_id")->fetch_assoc()['tb_Name'];

        $getHeaders = $conn->query("SELECT * FROM attributes WHERE tb_ID = $tb_id ORDER BY colNum ASC");

        if(!$getHeaders) {
          addAlert("<b>Uh oh!</b> Something went wrong. ".$conn->error, "danger");
        } else {
          if($getHeaders->num_rows === 0) {
            addAlert("<b>Hmm!</b> It looks like there are no columns to insert values into!");
          } else {
            $inputs = array();

            while($header = $getHeaders->fetch_assoc()) {
              $name = $header['attr_Name'];
              $id = $header['attr_ID'];
              $type = $header['datatype'];
              $primary = $header['isPrimary'] == 1;
              $nullable = $header['isNull'] == 1;

              $inputs[$name] = array("type" => $type, "id" => $id, "name" => $name, "primary" => $primary, "nullable" => $nullable);

              if($header['datatype'] == 4) {
                $getEnum = $conn->query("SELECT * FROM enum WHERE attr_ID = $id ORDER BY enum_ID ASC");

                if(!$getEnum) {
                  addAlert("<b>Uh oh!</b> Something went wrong. ".$conn->error, "danger");
                } else {
                  if($getEnum->num_rows > 0) {
                    $inputs[$name]['values'] = array();
  
                    while($enum = $getEnum->fetch_assoc()) {
                      $enum_id = $enum['enum_ID'];
                      $value = $enum['value'];
                      $inputs[$name]['values'][$value] = $enum_id;
                    }
                  }
                }
              }
            }

            if(count($inputs) === 1 && isset($inputs['ID'])) {
              header("Location: ".$_SERVER['HTTP_REFERER']);
            } else {
              # display alerts at the top
              displayAlert();
            } 

          }
        }

      }
    } else if(isset($_POST['insertValue'])) {
      $db_id = $_POST['db_id'];
      $tb_id = $_POST['tb_id'];
      $permissions = permissions($db_id);

      if(!isAllowed(1)) {
        addAlert("<b>Oops!</b> You do not have the correct permissions to do that.", "danger");
        header("Location: ".$_SERVER['HTTP_REFERER']);
      } else {
        $headers = 'row';
        
      }
    } else {
      header("location:javascript://history.go(-1)");
    }
  ?>

  <div class="containr w-50 bg-white mx-auto p-5 my-5 rounded shadow">
    <div class="container-fluid p-2 bg-light">
      <h3 class="text-center">Table: <?php echo $tbName; ?></h3>
      <hr>
      <form action="addRow-new.php" class="row" method="post">
        <?php foreach($inputs as $name => $info) { ?>
          <?php if(!$info['primary']) { ?>
            <div class="form-group col-6">
              <?php switch ($info['type']) {
                case 1:
                ?> 
                  <label for="<?php echo $name; ?>" class="text-capitalize"><?php echo $name; ?></label>
                  <input type="number" name="<?php echo $name; ?>" id="<?php echo $name; ?>" placeholder="0000" class="form-control" <?php $info['nullable'] ? '' : 'required'; ?>>
                <?php
                  break;
                
                case 2:
                ?>
                  <label for="<?php echo $name; ?>" class="text-capitalize"><?php echo $name; ?></label>
                  <input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" placeholder="Enter value" class="form-control" <?php $info['nullable'] ? '' : 'required'; ?>>
                <?php
                  break;

                case 3:
                ?>
                  <div class="custom-control custom-switch">
                    <input type="checkbox" name="<?php echo $name; ?>" value="1" id="<?php echo $name; ?>" class="custom-control-input" <?php $info['nullable'] ? '' : 'required'; ?>>
                    <label for="<?php echo $name; ?>" class="custom-control-label"><?php echo $name; ?></label>
                  </div>
                <?php
                  break;

                  case 4:
                ?>
                  <label for="<?php echo $name; ?>" class="text-capitalize"><?php echo $name; ?></label>
                  <select name="<?php echo $name; ?>" id="<?php echo $name; ?>" class="form-control" <?php $info['nullable'] ? '' : 'required'; ?>>
                    <option value="" style="display: none">Choose values</option>
                    <?php if(isset($info['values']) && count($info['values']) > 0) { 
                      foreach($info['values'] as $val => $id) { ?>
                        <option value="<?php echo $id; ?>"><?php echo $val; ?></option>
                    <?php } } else  { ?>
                      <option value="-1" selected>No values available</option>
                    <?php } ?>
                  </select>
                <?php
                    break;

                    default:
                ?>
                  <span class="text-danger">Unknown datatype found!</span>
                <?php 
                    break;
              }?>
            </div>
        <?php } } ?>
        <input type="hidden" name="tb_id" value="<?php echo $tb_id; ?>">
        <input type="hidden" name="db_id" value="<?php echo $db_id; ?>">
        <div class="row w-100 justify-content-end mx-3">
          <button type="button" class="btn btn-secondary mr-2">Cancel</button>
          <button type="submit" class="btn btn-success" name="insertValue">Insert</button>
        </div>
      </form>
    </div>
  </div>

<!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
</body>
</html>