<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/all.min.css">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="website icon" type="png" href="/images/logo.png">
    <title>Exam Organization</title>
</head>
<body>
    
    <div class="dashboard" id="dashboard">
        <div class="sidebar">
            <div class="logo">
                <img src="/images/logo.png" alt="Logo">
                <h1>Exam<span class="danger">Org</span></h1> 
            </div>
            <ul>
                <li><a class="active" href="#Home"><i class="fa-solid fa-house"></i><span>Home</span></a></li>
                <li><a href="#University"><i class="fa-solid fa-graduation-cap"></i><span>University</span></a></li>
                <li><a href="#Faculty"><i class="fa-solid fa-school"></i><span>Faculty</span></a></li>
                <li><a href="#Department"><i class="fa-solid fa-building-user"></i><span>Department</span></a></li>
                <li><a href="#Filiere"><i class="fa-solid fa-building"></i><span>Filiere</span></a></li>   
                <li><a href="#Profile"><i class="fa-solid fa-user"></i><span>Profile</span></a></li> 
                <li>
                <form action="logout.php" method="POST" style="display: inline;">
                <button type="submit" class="btn-logout" style="background:none; border:none; cursor:pointer; color: #0252d1; font: inherit; padding: 20px; display: flex; align-items: center;">
                    <i class="fa-solid fa-sign-out-alt" style="margin-right: 8px;"></i><span>Logout</span>
                </button>
                </form>
                </li> 
            </ul>
        </div>
          
        <div class="body-dash">
            <div class="navigation">
                <button class="btn" data-target="#dashboard" data-btn><i class="fa-solid fa-bars"></i></button>
                <div class="nav-settings">
                    <button class="btn1" id="dark-mode-toggle"><i class="fa-solid fa-lightbulb "></i></button>
                    <button class="btn1" style="font-size: 22px;" onclick="window.location.href='users.php';" ><i class="fa-solid fa-users-gear"></i></button>
                </div>    
            </div>

            <!-- Content Sections -->
            <section>
                <div id="Home" class="page active">
                    <div class="container-home">
                        <h2>Home</h2>
                        <div class="card">
                            <h2>Add University</h2>
                            <p>You can add a new university by providing its name, state, address, and logo.  
                               Once all required details are filled in, click the "Add" button to save the university information.  
                               Additionally, you can view a list of existing universities, delete unwanted entries, or edit details as needed.
                            </p>
                        </div>
                        <div class="card">
                            <h2>Add faculty</h2>
                            <p>Add a new faculty by providing the university id,and the faculty name, and uploading the faculty logo.  
                               After filling out the necessary details, click the "Add" button to save the faculty information.  
                               You can also manage existing faculties by editing or deleting their details.
                            </p>
                        </div>
                        <div class="card">
                            <h2>Add Department</h2>
                            <p>Create a new department by providing the faculty id and specifying details such as the department name, number of rooms, and floor name.  
                                Once all details are provided, click the "Add" button to save the department.  
                                You can also modify or remove existing department entries when needed.
                            </p>
                        </div>
                        <div class="card">
                            <h2>Filiere</h2>
                            <p>Add a new filiere by providing department id and the filiere name, then click "Add" to save. View, edit, or delete existing filieres from the list.</p>
                        </div>
                    </div>
                    
                </div>
            </section>
            <section>
                <div id="University" class="page">
                    <div class="container-univ">
                       <h2>University</h2>
                        <form action="insertUniv.php" method="post" enctype="multipart/form-data" class="form">
                            <div class="input-group">
                                <input type="text" name="name-univ" required><label>Name University</label>
                            </div>
                            <div class="input-group">
                                <input type="text" name="state-univ" required><label>State University</label>
                            </div>
                            <div class="input-group">
                                <input type="text" name="adress-univ" required><label>Adress University</label>
                            </div>
                            <div class="input-group">
                               <input class="input-file" type="file" name="logo-univ" required><!--<label>Logo University</label>-->
                            </div>  
                            <input class="btn-submit" type="submit" value="Add" name="submit"> 
                        </form> 
                    </div> 
                    <table class="table">
                        <thead>
                             <tr>
                                 <th>Id</th>
                                 <th>Name university</th>
                                 <th>State university</th>
                                 <th>Adress university</th>
                                 <th>Logo university</th>
                                 <th>Delete </th>
                                 <th>Edit </th>  
                             </tr>
                        </thead>
                        <tbody>
                            <?php 
                                include("connection.php");
                                $sql = mysqli_query($con,"SELECT * FROM `university`");
                                while($row = mysqli_fetch_array($sql)){
                                    echo "
                                       <tr>
                                          <td>$row[id_univ]</td>
                                          <td>$row[name_univ]</td>
                                          <td>$row[state_univ]</td>
                                          <td>$row[adress_univ]</td>
                                          <td><img src='$row[imageU]' width='50' height='50'></td>
                                          <td><a href='deleteUniv.php?Id=$row[id_univ]' 
                                               onclick=\"if (!confirm('Are you sure you want to delete this university?')) return false;\">
                                               <i class='fa-solid fa-trash'></i></a>
                                          </td>
                                          <td><a href='editUniv.php? Id=$row[id_univ]'><i class='fa-solid fa-pen-to-square'></i></a></td>
                                        </tr>
                                    ";
                                }
                            ?>   
                        </tbody>
                    </table>   
                </div>
            </section>
            <section>
                <div id="Faculty" class="page">   
                    <div class="container-facu">
                        <h2>Faculty</h2>
                        <form action="insertFact.php" method="post" enctype="multipart/form-data" class="form">
                             <div class="input-group">
                                 <input type="number" name="id-univ" required><label>Id University</label>
                             </div>
                             <div class="input-group">
                                 <input type="text" name="name-faculty" required><label>Name Faculty</label>
                             </div>
                             <div class="input-group">
                                <input class="input-file" type="file" name="logo-faculty"><!-- <label>Logo Faculty</label>-->
                             </div> 
                             <input class="btn-submitF" type="submit" value="Add" name="submit"> 
                        </form>  
                    </div> 
                    <table class="table">
                        <thead>
                             <tr>
                                 <th>Id</th>
                                 <th>Name Faculty</th>
                                 <th>Logo university</th>
                                 <th>Delete </th>
                                 <th>Edit </th>
                             </tr>
                        </thead>
                        <tbody>
                            <?php 
                                include("connection.php");
                                $sqlF = mysqli_query($con,"SELECT * FROM `faculty`");
                                while($row = mysqli_fetch_array($sqlF)){
                                    echo "
                                       <tr>
                                          <td>$row[id_faculty]</td>
                                          <td>$row[name_faculty]</td>
                                          <td><img src='$row[image_faculty]' width='50' height='50'></td>
                                          <td><a href='deleteFact.php?Id=$row[id_faculty]' 
                                               onclick=\"if (!confirm('Are you sure you want to delete this faculty?')) return false;\">
                                               <i class='fa-solid fa-trash'></i></a>
                                          </td>
                                          <td><a href='editFact.php? Id=$row[id_faculty]'><i class='fa-solid fa-pen-to-square'></i></a></td>
                                        </tr>
                                    ";
                                }
                            ?>   
                        </tbody>
                    </table>     
                </div>
            </section>
            <section>
                <div id="Department" class="page">
                    <div class="container-depar">
                        <h2>Department</h2>
                        <form action="insertDepa.php" method="post" enctype="multipart/form-data" class="form" >
                             <div class="input-group">
                                 <input type="text" name="id_faculty" required><label>Id Faculty</label>
                             </div>
                             <div class="input-group">
                                 <input type="text" name="name_deprtment" required><label>Name Department</label>
                             </div>
                             <div class="input-group">
                                <input type="number" name="nbOfRoom" required><label>Number of rooms</label>
                            </div>
                            <div class="input-group">
                                <input type="text" name="floor" required><label>Number of floor</label>
                            </div>
                            <input class="btn-submit" type="submit" value="Add" name="submit">
                        </form>
                    </div> 
                    <table class="table">
                        <thead>
                            <tr>
                                 <th>Id</th>
                                 <th>Name Department</th>
                                 <th>Number of rooms</th>
                                 <th>Number of floor</th>
                                 <th>Delete</th>
                                 <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                include("connection.php");
                                $sqlD = mysqli_query($con,"SELECT * FROM `department`");
                                while($row = mysqli_fetch_array($sqlD)){
                                    echo "
                                       <tr>
                                          <td>$row[id_department]</td>
                                          <td>$row[name_deprtment]</td>
                                          <td>$row[nbOfRoom]</td>
                                          <td>$row[floor]</td>
                                          <td><a href='deleteDepa.php?Id=$row[id_department]' 
                                               onclick=\"if (!confirm('Are you sure you want to delete this department?')) return false;\">
                                               <i class='fa-solid fa-trash'></i></a>
                                          </td>
                                          <td><a href='editDepa.php? Id=$row[id_department]'><i class='fa-solid fa-pen-to-square'></i></a></td>
                                        </tr>
                                    ";
                                }
                            ?>   
                        </tbody>
                    </table>    
                </div>
            </section>
            <section>
                <div id="Filiere" class="page">
                    <div class="container-fili">
                        <h2>Filiere</h2>
                        <form action="insertFili.php" method="post" enctype="multipart/form-data" class="form">
                            <div class="input-group">
                                <input type="number" name="id_department" required><label>Id Department</label>
                            </div>
                            <div class="input-group">
                                <input type="text" name="name_filiere" required><label>Name Filiere</label>
                            </div>
                            <input class="btn-submit" type="submit" value="Add" name="submit">
                        </form>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                             <th>Id</th>
                             <th>Name Filiere</th>
                             <th>Delete</th>
                             <th>Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                include("connection.php");
                                $sqlFil = mysqli_query($con,"SELECT * FROM `filiere`");
                                while($row = mysqli_fetch_array($sqlFil)){
                                    echo "
                                        <tr>
                                            <td>$row[id_filiere]</td>
                                            <td>$row[name_filiere]</td>
                                            <td><a href='deleteFili.php?Id=$row[id_filiere]' 
                                                onclick=\"if (!confirm('Are you sure you want to delete this filiere?')) return false;\">
                                                <i class='fa-solid fa-trash'></i></a>
                                            </td>
                                            <td><a href='editFili.php?Id=$row[id_filiere]'><i class='fa-solid fa-pen-to-square'></i></a></td>
                                        </tr>
                                    ";
                                }
                            ?>   
                        </tbody>
                    </table>    
                </div>
            </section>
            <section>
                <div id="Profile" class="page">
                    <div class="container-profile">
                        <?php 
                        include("connection.php");
                        $email =$_SESSION['email'];     
                        $role = $_SESSION['role'];
                        if ($role == 'admin1'){
                            $query = mysqli_query($con, "SELECT * FROM admin1 WHERE email = '$email'");
                            if($admin= mysqli_fetch_assoc($query)){
                            echo '
                            <h2>Update your profile Information</h2>
                            <form action="updateProfileAD1.php" method="post" class="form">
                                <div class="input-group_eye">
                                    <input type="email" name="email" value="' . htmlspecialchars($admin['email']) . '" required>
                                    <label>Email</label>
                                </div>
                                <div class="input-group_eye">
                                    <input type="password" id="oldPassword"  name="old_password" maxlength="10" style="letter-spacing: 0.125em; font-family:Verdana;">
                                    <label>old Password</label>
                                     <i class="fas fa-eye" id="toggleOldPassword" style="position: absolute; right: 10px; top: 12px; cursor: pointer;"></i>
                                </div>
                                <div class="input-group_eye">
                                    <input type="password" id="newPassword" name="new_password" maxlength="10" style="letter-spacing: 0.125em; font-family:Verdana;">
                                    <label>New Password</label>
                                    <i class="fas fa-eye" id="toggleNewPassword" style="position: absolute; right: 10px; top: 12px; cursor: pointer;"></i>
                                </div>
                                <input type="submit" value="Update" class="btn-submitF" name="update_profile">
                            </form>
                            ';
                            }
                        }else{    
                            echo "<p>You are not authorized to view this section.</p>";
                        }
                        ?>
                    </div>
                </div>  
            </section>
        </div>
    </div>
    <script src="/scripts/script.js"></script>
    <script>
       window.addEventListener('DOMContentLoaded', () => {
       const hash = window.location.hash;
       if(hash){
            document.querySelectorAll('.page').forEach(page => page.classList.remove('active'));
            const target = document.querySelector(hash);
            if (target){
                target.classList.add('active');
            }
            document.querySelectorAll('.sidebar ul li a').forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === hash) {
                link.classList.add('active');
            }
            });
            }
        });
      </script>

</body>
</html>