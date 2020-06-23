<html>
<head>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="./styles/bootstrap.min.css">
    <title>Database</title>
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
                    <!--Adding Table BOC-->
                    <button type="button" class="btn btn-success " data-toggle="modal" data-target="#exampleModal">
                         New Table
                     </button>
                     <a class="btn btn-danger" href="database.php?delete_id=<?php echo $_GET['db_id'] ?>">Delete Database</a>
                     <a class="btn btn-info" href="editDB.php?db_ID=<?php echo $_GET['db_id'] ?>">Edit Database</a>
                    <!-- Modal -->
                    <div class="modal fade text-dark" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">What's in a name...</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div><!--modal header EOC-->
                                     <!--Content within the modal modal-content BOC-->
                                        <form action="database.php" method="POST" enctype="multipart/form-data">
                                          <div class="modal-body">
                                            <div class="form-row">
                                                    <div class="form-group col-12">
                                                        <label for="tbName">Table Name</label>
                                                        <input type="text" class="form-control" name="tbName" placeholder="Enter table name" required>
                                                   </div>
                                            </div><!--form row EOC-->

                                            <input type="hidden" class="form-control" name="db_id" value="<?php echo $_GET['db_id']?>" required>
                                          </div>
                                          <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-success" name="submit">Create Table</button>
                                          </div>
                                        </form><!--EOC form-->
                                     </div><!-- modal-content EOC-->
                             </div><!--Modal-dialog EOC-->
                     </div><!--MODAL EOC-->
                  
<?php
 
    $db_id = $_GET['db_id'];

    $conn = new mysqli("localhost","root","","im2");
    if($conn->connect_error){
     die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM db WHERE db_ID = $db_id";
    $result = $conn->query($sql);

    if($result->num_rows > 0){
      $row = $result->fetch_assoc();
      $db_Name = $row['db_Name'];

        echo '
                <div class="jumbotron jumbotron-fluid ">
                     <div class="container"> 
                         <h1 class="text-success">Welcome,  '.$_SESSION['users'][$_SESSION['Succeed']]['name'].' !</h1>
                         <h4 class="text-info">Database is: '.$db_Name.' </h4>
                         <hr>
                         
                      <div class="accordion" id="accordionExample">';
       

                    //This is where we are creating an accordian of all the tables within this database
                    //Inside each accordian are the table's attributes, and the option to add new attributes as well so naay button and modal up ahead.
                  ;
                  $sql2 = "SELECT * FROM tb WHERE db_ID =$db_id";
                  $result2 = $conn->query($sql2);

                  if($result2->num_rows>0){
                    echo '<h5 class="text-info text-center">Tables </h5>';
                    $num = 1;
                    while($row2 = $result2->fetch_assoc()){
                      #for the accordian to work, the data-target and ID of the collapsable DIV have to be different attributes everytime.
                      #for the first accordian, it has to be data-target="#collapse1" and it's the div class of that should be div=id="collapse1" as well, 
                      #the next accordian must be #collapse2 so that when u click on the 2nd accordian, ang second ray maopen, dili ang previous. 
                      #'cause if they were to share the same data-target and ID name, all accordians would be opened which is not what we want
                        echo'
                           <div class="card">
                              <div class="card-header" id="heading'.$num.'">
                                <h2 class="mb-0">
                               <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse'.$num.'" aria-expanded="false" aria-controls="collapse'.$num.'">
                                 '.$row2['tb_Name'].'
                               </button>
                                </h2>
                           </div>
                    
                          <div id="collapse'.$num.'" class="collapse" aria-labelledby="heading'.$num.'" data-parent="#accordionExample">
                          <div class="card-body">
                            <a class="btn btn-success" href="database.php?tb_ID='.$row2['tb_ID'].'&db_ID='.$db_id.'">Create a Primary Key (ID)</a>
                            
                            <button type="button" class="btn btn-success " data-toggle="modal" data-target="#exampleModal'.$num.'">
                                 Create an Attribute
                             </button>
                           
                            
                            <div class="modal fade text-dark" id="exampleModal'.$num.'" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">New Attribute</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                             
                                                <form action="database.php" method="POST" enctype="multipart/form-data">
                                                  <div class="modal-body">
                                                    <div class="form-row">
                                                            <div class="form-group col-md-5">
                                                                <label for="attr_Name">Attribute Name</label>
                                                                <input type="text" class="form-control" name="attr_Name" placeholder="Enter attribute name" required>
                                                           </div>
                                                           <div class="form-group col-md-4">
                                                                <label for="datatype">Datatype</label>
                                                                <input type="text" class="form-control" name="datatype" placeholder="Enter datatype" required>
                                                          </div>
                                                          <div class="form-group col-md-3">
                                                          <label for="limitation">Limitation</label>
                                                          <input type="number" class="form-control" name="limitation" placeholder="Num pls" required>
                                                          </div>
                                                    </div>                                                    
                                                    <div class="form-row">
                                                        <div class="form-group col-md-4">
                                                           <label for="isPrimary" class="font-weight-bold">Primary Key</label>
                                                           <select id="isPrimary" name="isPrimary" class="form-control" required="required">
                                                              <option selected>Choose...</option>
                                                              <option value="1">True</option>
                                                              <option value="0">False</option>
                                                            </select>
                                                         </div>
                                                        <div class="form-group col-md-4">
                                                        <label for="isAutoInc" class="font-weight-bold">Auto Increment</label>
                                                        <select id="isAutoInc" name="isAutoInc" class="form-control" required="required">
                                                           <option selected>Choose...</option>
                                                           <option value="1">True</option>
                                                           <option value="0">False</option>
                                                         </select>
                                                      </div>
                                                        <div class="form-group col-md-4">
                                                        <label for="isNull" class="font-weight-bold">Null</label>
                                                        <select id="isNull" name="isNull" class="form-control" required="required">
                                                           <option selected>Choose...</option>
                                                           <option value="1">True</option>
                                                           <option value="0">False</option>
                                                         </select>
                                                      </div>
                                                    </div>
                                                    <div class="form-row">
                                                        <div class="form-group col-md-4">
                                                            <label for="isFK" class="font-weight-bold">Foreign Key</label>
                                                            <select id="isFK" name="isFK" class="form-control" required="required">
                                                                <option selected>Choose...</option>
                                                                <option value="1">True</option>
                                                                <option value="0">False</option>
                                                           </select>
                                                          </div>


                                                          <div class="form-group col-md-4">
                                                            <label for="FK_of" class="font-weight-bold">FK of what table</label>
                                                            <select id="FK_of" name="FK_of" class="form-control" required="required">
                                                              <option selected>Choose...</option>
                                                              <option value="0">None</option>
                                                    
                                                    
                                                  
        
                                                    ';
                                                    
                                                    $table_ID = $row2['tb_ID'];
                                                      #in the event that the attribute we're creating is an FK, we have to specify what table it FKs to... Hence we were fetching all tables 
                                                      #within the same DB. except the current table we're opening in the accordian.
                                                    $sql3 = "SELECT * FROM tb WHERE db_ID = $db_id AND tb_ID <> $table_ID ";
                                                    $result3 = $conn->query($sql3);

                                                    if($result3->num_rows>0){
                                                      
                                                      while($row3 = $result3->fetch_assoc()){
                                                        $tb_ID = $row3['tb_ID'];
                                                        $tb_Name = $row3['tb_Name'];
                                                        #ato dayon gicheck if ang kana na table sd naa nabay PK daan gimake, otherwise walay purpose nga iapil sila diri kung wa pay PK
                                                          $sql4 = "SELECT * FROM attributes WHERE isPrimary='1' AND tb_ID = $tb_ID";
                                                          $result4 = $conn->query($sql4);

                                                          if($result4->num_rows>0){
                                                                echo '
                                                                  <option value="'.$tb_ID.'"><p>'.$tb_Name.'</p></option>
                                                              ';
                                                          }
                                                         
                                                        }
                                                    }

                                                  echo'    
                                                            </select>
                                                          </div>
                                                          </div>
                                                          
                                                        <input type="hidden" class="form-control" name="db_id" value="'.$db_id.'" required>
                                                        <input type="hidden" class="form-control" name="tb_ID" value="'. $row2['tb_ID'].'" required>
                                                      </div>
                                                      <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-success" name="submit">Create Attribute</button>
                                                      </div>
                                                </form>
                                             </div>
                                     </div>
                             </div>
                            
                          </div>
                        </div>
                      </div>
                        ';
                        $num++;
                    }
                    
                  }else{
                    echo '<h5 class="text-info text-center">No tables yet </h5>';
                  }
            
         echo'    
                  </div>
                </div> ';      
    }
    mysqli_close($conn); 
   ?>
                </div><!--jumbotron EOC-->
            </div><!--Container EOC-->
           

 <?php
  $conn = new mysqli("localhost","root","","im2");
  if($conn->connect_error){
   die("Connection failed: " . $conn->connect_error);
  }

        if(isset($_POST['submit'])){
            $dbID = $_POST['db_id'];
            $tb_Name = $_POST['tbName'];

            $sql = "INSERT INTO tb (tb_ID,tb_Name,db_ID) VALUES ('','$tb_Name','$dbID')";
            if($conn->query($sql)===TRUE){   
                    //Supposedly after inserting into tb, mureload ang kani na page parin with the $_GET['db_id'] parin pero it won't be passed as well for some reason
                    echo "<script language='javascript'>alert('Table Successfully Added!');window.location.href='database.php?db_id=$dbID';</script>";
                    //the navbar of these stuff btw are in includes>navbarMain.php
            }
            
        }

        if(isset($_POST['submitAttr'])){
          $attr_Name = $_POST['attr_Name'];
          $datatype =  $_POST['datatype'];
          $limitation = $_POST['limitation'];
          $isPrimary = $_POST['isPrimary'];
          $isAutoInc = $_POST['isAutoInc'];
          $isNull = $_POST['isNull'] ; 
          $isFK =  $_POST['isFK'];
          $FK_of = $_POST['FK_of'];
 
          $tb_ID = $_POST['tb_ID'];
          $db_ID = $_POST['db_ID'];
          $isOkay = FALSE;
         
          # Here we check for any errors or mishaps
 
 
         if($isPrimary==="1"){
           #we are checking if there already exists a PK
                 $checkPK = "SELECT * FROM attributes WHERE isPrimary = 1 AND tb_ID = $tb_ID";
                 $checkQuery = $conn->query($checkPK);
                 $hasPK = $checkQuery->num_rows;
 
                 if($hasPK >0){
                     echo "<script language='javascript'>alert('That table already has a primary key!');window.location.href='database.php?db_id=$db_ID';</script>";
                 }else{
                   $isOkay = TRUE;
                 }
         }else{
           
                 if($isFK === "1"){
                       if($FK_of === "0"){
                         echo "<script language='javascript'>alert('You did not specify which table your attribute is an FK of');window.location.href='database.php?db_id=$db_ID';</script>";
                       }else{
                             $sql = "SELECT * FROM attributes WHERE isPrimary = 1 and tb_ID = $FK_of ";
                             $result = $conn->query($sql);
                             if($result->num_rows>0){
                               $row = $result->fetch_assoc();
                               $attr_ID = $row['attr_ID'];
 
                                 $sql2 = "UPDATE attributes SET isParent = '1' , ParentOf = '$tb_ID' WHERE attr_ID = $attr_ID";
                                 if($conn->query($sql2)===TRUE){
                                     $isOkay = TRUE;
                                 }
                             }
 
                       }
 
                 }else{
                   $isOkay = TRUE;
                 }
         }
 
                     #after all of the checking, we see if we're still good to input the new attribute
 
                   if($isOkay === TRUE){
                     $sql3 = "INSERT INTO attributes (attr_ID,attr_Name,datatype,limitation,isPrimary,isAutoInc,isNull,isParent,ParentOf,isFK,FK_of,tb_ID) VALUES ('','$attr_Name','$datatype','$limitation','$isPrimary','$isAutoInc','$isNull','0','0','$isFK','$FK_of','$tb_ID')";
                     if($conn->query($sql3)===TRUE){
                       echo "<script language='javascript'>alert('A new Attribute has been created!');window.location.href='database.php?db_id=$db_ID';</script>";
                     }
                   }
           
         }

        if(isset($_GET['delete_ID'])){
            $db_ID = $_GET['delete_ID'];
            $sql = "DELETE FROM db WHERE db_ID= $db_ID";
            if($conn->query($sql)===TRUE){
                echo "<script language='javascript'>alert('Database Successfully Deleted!');window.location.href='welcome.php';</script>";
              
              
            }
        }

        if(isset($_GET['tb_ID'])){
          $tb_ID = $_GET['tb_ID'];
          $dbID = $_GET['db_id'];
           $checkPK = "SELECT * from attributes WHERE isPrimary = 1 AND tb_ID = $tb_ID";
           $checkerQuery = $conn->query($checkPK);
           $hasPK = $checkerQuery->num_rows;
           if($hasPK > 0) {
            echo "<script language='javascript'>alert('That table already has a primary key!');window.location.href='database.php?db_id=$dbID';</script>";
           } else {
            $sql = "INSERT INTO attributes (attr_ID,attr_Name,datatype,limitation,isPrimary,isAutoInc,isNull,isParent,ParentOf,isFK,FK_of,tb_ID) VALUES ('','ID','INT','10','1','1','0','0','','0','','$tb_ID')";

            if($conn->query($sql)===TRUE){
              $sql2 = "SELECT * FROM tb WHERE tb_ID = $tb_ID";
              $result = $conn->query($sql2);
  
              if($result->num_rows > 0){
                $row = $result->fetch_assoc();
                $dbID = $row['db_ID'];
  
                echo "<script language='javascript'>alert('Attribute Successfully Added!');window.location.href='database.php?db_id=$dbID';</script>";
              }
             }
           }
          }

        mysqli_close($conn); 


?>

<!--HELLO I AM BIG FEAR!!!!! this is where I got stuck!-->





   <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  
</body>
</html>