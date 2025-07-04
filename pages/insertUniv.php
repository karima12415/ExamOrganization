<?php
    include("connection.php");
    if(isset($_POST['submit'])){
        $Name_univ=$_POST['name-univ'];
        $State_univ=$_POST['state-univ']; 
        $Adress_univ=$_POST['adress-univ'];
        //check if the university in the same adress if it's presinte
        $check_query = mysqli_query($con, "SELECT * FROM university WHERE name_univ = '$Name_univ' AND adress_univ = '$Adress_univ'");
        if (mysqli_num_rows($check_query) > 0){
            echo "<script>
                 alert('University with the same name and address already exists!');
                 window.location.href = 'home.php#University';
                </script>";
        }else {
            $Logo_univ=$_FILES['logo-univ'];
            $logo_loc=$_FILES['logo-univ']['tmp_name'];
            $logo_name=$_FILES['logo-univ']['name'];
            $logo_des="logoUniv/".$logo_name;
            move_uploaded_file($logo_loc,'logoUniv/'.$logo_name);
            $query= mysqli_query($con,"INSERT INTO university(name_univ,state_univ,adress_univ,imageU) VALUES ('$Name_univ','$State_univ','$Adress_univ','$logo_des')");
            if ($query) {
                echo "<script>
                        alert('University added successfully!');
                        window.location.href = 'home.php#University';
                      </script>";
            } else {
                echo "Failed to add university.";
            }
        }   
    }
?>