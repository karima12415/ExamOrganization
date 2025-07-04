<?php require "functions.php" ?>
<?php
    include("connection.php");
    $search = $_GET['search'] ?? '';
    $where = '';
    if (!empty($search)) {
        $search = $con->real_escape_string($search);
        $where = "WHERE name LIKE '%$search%' OR email LIKE '%$search%' OR university_name LIKE '%$search%' OR faculty_name LIKE '%$search%' OR university_address LIKE '%$search%'";
    }
?>
<!DOCTYPE html>
<html lang="en" >
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/all.min.css">
    <link rel="stylesheet" href="/css/users.css">
    <link rel="website icon" type="png" href="/images/logo.png">
    <title>Exam Organization</title>
</head>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.querySelector('input[name="search"]');
    const userCards = document.querySelectorAll('.user-card');
    const noResultsMessage = document.getElementById('noResultsMessage');

    searchInput.addEventListener('input', function () {
        const filter = this.value.toLowerCase().trim();
        let anyVisible = false;

        userCards.forEach(card => {
            const name = card.querySelector('h3').textContent.toLowerCase();
            const email = card.querySelector('.fa-envelope').parentElement.textContent.toLowerCase();
            const university = card.querySelector('.fa-university').parentElement.textContent.toLowerCase();
            const faculty = card.querySelector('.fa-graduation-cap').parentElement.textContent.toLowerCase();
            const match = name.includes(filter) || email.includes(filter) || university.includes(filter) || faculty.includes(filter);
           /* const match = name.includes(filter) || email.includes(filter) || role.includes(filter);*/
            card.style.display = match ? 'block' : 'none';
            if (match) anyVisible = true;
        });

        noResultsMessage.style.display = anyVisible ? 'none' : 'block';
    });
});
</script>
<body>
    <div class="container">
        <div class="navigationS">
            <div class="logoS">
                <img src="/images/logo.png" alt="Logo">
                <h1>Exam<span class="danger">Org</span></h1> 
            </div>
            <li><a href="/pages/home.php"><i class="fa-solid fa-circle-left"></i></a></li>
        </div>
        <h1>Admin</h1>
        <div class="search-container">
            <form method="GET" action="">
                <div class="search-box">
                    <input type="text" name="search" placeholder="Search by name or email or university or faculty name,address" value="<?= htmlspecialchars($search) ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </div>
                <?php if(!empty($search)){?>
                    <a href="users.php" class="clear-search">Clear search</a>
                <?php }?>
            </form>
        </div>
        <div class="users-grid" id="usersGrid">
            <p id="noResultsMessage" class="no-users" style="display: none;">Not exist</p>
            <?php
                // get user in database 
                $sql = mysqli_query($con, "SELECT admin2.*, 
                    university.name_univ AS university_name,
                    university.adress_univ AS university_address,
                    faculty.name_faculty AS faculty_name 
                    FROM admin2
                    LEFT JOIN university ON admin2.id_univ = university.id_univ
                    LEFT JOIN faculty ON admin2.id_faculty = faculty.id_faculty
                    $where");
                if(mysqli_num_rows($sql)>0){
                    while($row = mysqli_fetch_array($sql)){
                        $roleClass = strtolower($row["role"]);
                        echo '
                        <div class="user-card" 
                            data-id="' . $row["id"] . '" 
                            data-name="' . $row["name"] . '" 
                            data-email="' . $row["email"] . '" 
                            data-role="' . $row["role"] . '"
                            data-university-id="'. $row['id_univ'].'"
                            data-faculty-id="' .$row['id_faculty']. '">
                            <div class="user-header">
                            <h3>' . $row["name"] . '</h3>
                             <span class="role-badge ' . $roleClass . '">' . $row["role"] . '</span>
                            </div>
                            <div class="user-details">
                            <p><i class="fas fa-envelope"></i> ' . $row["email"] . '</p>
                            <p><i class="fas fa-university"></i> ' . $row["university_name"] . ' - ' . $row["university_address"] . '</p>
                            <p><i class="fas fa-graduation-cap"></i> ' . $row["faculty_name"] . '</p>
                            ' . ($row["password"] ? '<p><i class="fa-solid fa-key"></i>  ••••••••••</p>' : '') . '
                            </div>
                        </div>';
                    }
                }else{
                    echo '<p class="no-users">There are no users yet</p>';
                }
            
            ?>
        </div>
        <button class="add-user-btn" id="addUserBtn"><i class="fas fa-plus"></i>Add a new user</button>  
        <div class="add-user-modal" id="addUserModal">
            <div class="modal-content">
            <h2 id="modalTitle">Add a new user</h2>
                <form id="userForm" action="add_user.php" method="POST" name="my-form">
                <input type="hidden" id="userId" name="id">
                    <div class="form-group">
                       <input type="text" id="name" name="name" required><label>Name</label>
                    </div>
                    <div class="form-group">
                        <input type="email" id="email" name="email" required><label>Email</label>
                    </div>
                    <div class="form-group">
                        <input list="universities" name="univer_input" id="univer_input" required oninput="handleUniversityInput(this.value)" placeholder="university">
                        <datalist id="universities">
                         <?php 
                             $universities = getuniversity();
                             foreach ($universities as $university) {
                             $label = $university['name_univ'] . ' - ' . $university['adress_univ'];
                             echo "<option data-id='{$university['id_univ']}' value='{$label}'>";
                            }
                          ?>
                        </datalist>
                        <input type="hidden" name="univer" id="univer_hidden">
                    </div>
                    <div class="form-group">
                        <input list="faculties" name="facul_input" id="facul_input" oninput="handleFacultyInput(this.value)" required disabled placeholder="faculty">
                        <datalist id="faculties"></datalist>
                        <input type="hidden" name="facul" id="facul_hidden">
                    </div>
                    <div class="form-group">
                    <input type="hidden" name="role" value="Admin"  id="role"> <!-- Automatically set role to Admin -->
                    </div>
                    <div class="form-group" id="passwordGroup" style="display: none;">
                        <input type="password" id="password" name="password" maxlength="10" style="letter-spacing: 0.125em; font-family:Verdana;" required><label>password</label>
                        <i class="fas fa-eye" id="togglePassword" style="position: absolute; right: 10px; top: 12px; cursor: pointer;"></i>
                    </div>
                    <div class="form-group" id="oldPasswordGroup" style="display: none;">
                        <input type="password" id="oldPassword" name="old_password" maxlength="10" style="letter-spacing: 0.125em; font-family:Verdana;"><label>Old Password</label>
                        <i class="fas fa-eye" id="toggleOldPassword" style="position: absolute; right: 10px; top: 12px; cursor: pointer;"></i>
                    </div>
                    <div class="form-group" id="newPasswordGroup" style="display: none;">
                        <input type="password" id="newPassword" name="new_password" maxlength="10" style="letter-spacing: 0.125em; font-family:Verdana;"><label>New Password</label>
                        <i class="fas fa-eye" id="toggleNewPassword" style="position: absolute; right: 10px; top: 12px; cursor: pointer;"></i>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="cancel-btn" id="cancelBtn">cancel</button>
                        <button type="submit" class="submit-btn">Save</button>
                        <button type="button" class="delete-btn" id="deleteBtn" style="display: none;">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="/scripts/addusers.js"></script>
    <script src="/scripts/scriptslect.js"></script>
</body>
</html>