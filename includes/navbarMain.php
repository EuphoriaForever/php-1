<nav class="navbar navbar-expand-lg navbar-dark bg-dark text-white">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
    <a class="navbar-brand" href="welcome.php">Mini phpMyAdmin</a>
    <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
    <?php
                $server = "localhost";
                $username = "root";
                $password = "";
                $dbname = "im2";
            
                $conn = new mysqli($server,$username,$password,$dbname);
                if($conn->connect_error){
                    die("Connection failed: " . $conn->connect_error);
                }

                $sql = "SELECT * FROM db";
                $result = $conn->query($sql);
            
                if($result->num_rows > 0){
                    while($row = $result->fetch_assoc()){
                      echo '
                      <li class="nav-item">
                          <a class="nav-link" href="database.php?db_id='.$row['db_ID'].'">'.$row['db_Name'].'<span class="sr-only"></span></a>
                      </li>
                      ';
                    }
                }else{
                    echo '<li class="nav-item"><a class="nav-link" >No databases yet</a></li>';
                }   
    ?>

    </ul>
    <a href="?logout=true" class="nav-link text-danger ml-auto">Logout</a>
  </div>
</nav>