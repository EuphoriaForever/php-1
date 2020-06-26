<html>
<head>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/bootstrap.min.css">
    <title>Table</title>
</head>
<body class="bg-secondary">
     
    <?php
  // include "./includes/navbar.php";

    session_start(); #start session in each form validation page so that we can all access the super global var $_SESSION

    
    if(!isset($_SESSION['Succeed'])) { #if there is no current login session detected, go to login page
      header("Location: login.php");
    }else{

    //   if($_SESSION['users'][$_SESSION['Succeed']]['type'] ==="administrator"){
    //     include "./includes/navbarAdmin.php";
    //   }else {
    //     include "./includes/navbarUser.php";
    //   }
        include "./includes/navbarMain.php";

    }

    if(isset($_GET['logout'])) { #if u wanna logout, remove current login session and redirect to login page
      unset($_SESSION['Succeed']);
      header("Location: login.php");
    }

    
  ?>
          <div class="container w-50 position-relative mx-auto p-5 my-5 bg-light shadow">
<?php
    $server = "localhost";
    $username = "root";
    $password = "";
    $dbname = "im2";

    if(isset($_GET['tb_ID'])){
        $tb_ID = $_GET['tb_ID'];
    } else {
        $tb_ID = 0;
    }

    if(isset($_GET['db_id'])){
        $db_ID = $_GET['db_id'];
    } else {
        $tb_ID = 0;
    }

    $conn = new mysqli($server,$username,$password,$dbname);
    if($conn->connect_error){
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM attributes WHERE tb_ID = $tb_ID";
    $result = $conn->query($sql);
    $attributeArray = array();  
    if($result->num_rows > 0){
        while($attribute = $result->fetch_assoc()){
            if($attribute['isPrimary'] == 1){ // if primary key move to the front of the array
                array_unshift($attributeArray, $attribute);
            } else {
                $attributeArray[] = $attribute;
            }
        }
        
        //forms for inserting
        echo '<form action="addRow.php" method="POST" enctype="multipart/form-data">';
        for($ctr=0; $ctr<count($attributeArray); $ctr++){
            // if characters only if varchar and numbers only if int
            if($attributeArray[$ctr]['isAutoInc']!=1){
                switch($attributeArray[$ctr]['datatype']){
                    case 'INT': $input = 'number'; break;
                    case 'varchar': $input = 'text'; break;
                }
                // potential problem for attributes that are named the same?
                echo '
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>'.$attributeArray[$ctr]["attr_Name"].'</label>
                        <input type="'.$input.'" class="form-control" name="'.$attributeArray[$ctr]["attr_ID"].'" required>
                    </div>
                </div>
                ';
            } else {
                $sql = "SELECT * FROM `rows` WHERE `attr_ID`=".$attributeArray[$ctr]['attr_ID']." ORDER BY `rowNum` DESC LIMIT 1";
                $result = $conn->query($sql);
                if($result!=null && $result->num_rows > 0){
                    $preVal = ($result->fetch_assoc())['value'];
                    $preVal++;
                    echo '<input type="hidden"  class="form-control" name="'.$attributeArray[$ctr]["attr_ID"].'" value="'.$preVal.'" id="tb_ID"  required>';
                } else {
                    echo '<input type="hidden"  class="form-control" name="'.$attributeArray[$ctr]["attr_ID"].'" value=1 id="tb_ID"  required>';
                }
            }
        }
        echo '
        <input type="hidden"  class="form-control" name="db_ID" value="'.$db_ID.'" id="db_ID"  required>
        <input type="hidden"  class="form-control" name="tb_ID" value="'.$tb_ID.'" id="tb_ID"  required>
        <input type="submit" class="btn btn-dark text-white" value="Submit" name="submit" required>
        </form>'; 
    } else {
            // table does not exist
    }

    
    if(isset($_POST['submit'])){
        $tb_ID = $_POST['tb_ID'];
        $db_ID = $_POST['db_ID'];
        $sql = "SELECT * FROM attributes WHERE tb_ID = $tb_ID";
        $result = $conn->query($sql);
        $attributeArray = array();  
        if($result->num_rows > 0){
            while($attribute = $result->fetch_assoc()){
                if($attribute['isPrimary'] == 1){ // if primary key move to the front of the array
                    array_unshift($attributeArray, $attribute);
                } else {
                    $attributeArray[] = $attribute;
                }
            }
            $message= "worked";
        } else {
            $message = "didn't work";
        }
        $isOkay = checkPermit('3',$db_ID,$conn);
        if($isOkay==TRUE){   
            // auto increment check previous entry
            // for rows table insert by attribute and value
            // you need attributeID, value and last row number
            // check primary key's latest rowNum
            $sql = "SELECT * FROM `rows` WHERE `attr_ID`=".$attributeArray[0]['attr_ID']." ORDER BY `rowNum` DESC LIMIT 1";
            $checklastRowNum = $conn->query($sql);
            if($checklastRowNum!=null && $checklastRowNum->num_rows>0){
                $rowNum = (($checklastRowNum->fetch_assoc())['rowNum'])+1;
                
            } else {
                $rowNum = 1;
            }

            for($ctr=0; $ctr<count($attributeArray); $ctr++){
                $sql = "INSERT INTO `rows`(rowNum, attr_ID, value) VALUES($rowNum, ".$attributeArray[$ctr]['attr_ID'].", '".$_POST[$attributeArray[$ctr]['attr_ID']]."')";
                $conn->query($sql);
            }
            if($ctr==count($attributeArray)){
                echo "<script language='javascript'>alert('Information Successfully Edited!');window.location.href='database.php?db_id=$db_ID';</script>";
            } else {
                echo "sad life";
            }
        }else{
            echo "<script language='javascript'>alert('Uh oh! You do not have a permit to tinker on this TB');window.location.href='database.php?db_id=$db_ID';</script>";
        }
    }

    function checkPermit($operation,$db_ID,$conn){
        $isOkay = FALSE;
        if($_SESSION['users'][$_SESSION['Succeed']]['type']==="administrator"){
            $isOkay = TRUE;
        }else{
            $username = $_SESSION['Succeed'];
            $sql = "SELECT * FROM users WHERE username ='$username'";
            $result = $conn->query($sql);
            if($result->num_rows>0){
                $row = $result->fetch_assoc();
                $userID = $row['user_id'];
                $sql2 = "SELECT * FROM db where db_ID = $db_ID";
                $result2 = $conn->query($sql2);
                if($result2->num_rows>0){
                    $row2 = $result2->fetch_assoc();
                    $AuthorID = $row2['Author'];
                    if($userID == $AuthorID){
                        $isOkay = TRUE;
                    }else{
                        $sql3 = "SELECT * FROM permits WHERE operation=$operation AND user_ID = $userID AND db = $db_ID";
                        $result3 = $conn->query($sql3);
                        if($result3->num_rows>0){
                            $isOkay = TRUE;
                        }
                    }
                }
            }
        }
        return $isOkay;
    }

?>
    </div>
<!--HELLO I AM BIG FEAR!!!!! this is where I got stuck!-->





   <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  
</body>
</html>