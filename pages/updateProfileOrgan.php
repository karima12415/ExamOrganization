<?php
session_start();
include("connection.php");
$id_organizer = $_SESSION['id_organizer'];
$email = $_SESSION['email'];
// Fetch current organizer data
$query = mysqli_query($con, "SELECT * FROM organizer WHERE id = '$id_organizer'");
$data = mysqli_fetch_assoc($query);
$success = "";
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = mysqli_real_escape_string($con, $_POST['name']);
    $new_email = mysqli_real_escape_string($con, $_POST['email']);
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    if (!empty($new_password)) {
        // Require old password verification
        if (!password_verify($old_password, $data['password'])) {
            $error = "Old password is incorrect.";
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update = mysqli_query($con, "UPDATE organizer SET name='$new_name', email='$new_email', password='$hashed_password' WHERE id='$id_organizer'");
            if ($update) {
                $_SESSION['email'] = $new_email;
                $success = "Profile updated successfully.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    } else {
        $update = mysqli_query($con, "UPDATE organizer SET name='$new_name', email='$new_email' WHERE id='$id_organizer'");
        if ($update) {
            $_SESSION['email'] = $new_email;
            $success = "Profile updated successfully.";
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/all.min.css">
    <link rel="stylesheet" href="/css/styleupdat.css">
    <link rel="website icon" type="png" href="/images/logo.png">
    <title>Exam Organization</title>
</head>

<body>
    <div class="navigationS">
        <div class="logoS">
            <img src="/images/logo.png" alt="Logo">
            <h1>Exam<span class="danger">Org</span></h1>
        </div>
        <div class="navS">
            <li><a href="/pages/homeorgan.php"><i class="fa-solid fa-circle-left"></i></a></li>
            <li>
                <form action="logout.php" method="POST" style="display: inline;">
                    <button type="submit" class="btn-logout" style="background:none; border:none; cursor:pointer; color: #0252d1; font: inherit; margin-left:12px">
                        <i class="fa-solid fa-sign-out-alt" style="margin-right: 8px;"></i><span>Logout</span>
                    </button>
                </form>
            </li>
        </div>
    </div>
    <div class="form_container" class="container" style="padding: 90px;">
        <h2>Update Profile</h2>
        <?php
        if ($success) echo "<div id='successMessage' class='alert alert-success'>$success</div>";
        if ($error) echo "<div id='errorMessage' class='alert alert-danger'>$error</div>";
        ?>
        <form method="POST" action="" class="form">
            <div class="input-group">
                <input type="text" name="name" value="<?php echo htmlspecialchars($data['name']); ?>" required>
                <label>Name</label>
            </div>
            <div class="input-group">
                <input type="email" name="email" value="<?php echo htmlspecialchars($data['email']); ?>" required>
                <label>Email</label>
            </div>
            <div class="input-group">
                <input type="password" name="old_password" maxlength="10" style="letter-spacing: 0.125em; font-family:Verdana;">
                <label>Old Password</label>
                <i class="fas fa-eye" id="toggleOldPassword" style="position: absolute; right: 10px; top: 12px; cursor: pointer;"></i>
            </div>
            <div class="input-group">
                <input type="password" name="new_password" maxlength="10" style="letter-spacing: 0.125em; font-family:Verdana;">
                <label>New Password</label>
                <i class="fas fa-eye" id="toggleNewPassword" style="position: absolute; right: 10px; top: 12px; cursor: pointer;"></i>
            </div>
            <button class="btn-submit" type="submit">Update</button>
        </form>
    </div>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide success message
        const successMessage = document.getElementById('successMessage');
        if (successMessage) {
            setTimeout(() => {
                successMessage.style.opacity = '0';
                setTimeout(() => successMessage.remove(), 500);
            }, 1000);
        }
        // Auto-hide error message
        const errorMessage = document.getElementById('errorMessage');
        if (errorMessage) {
            setTimeout(() => {
                errorMessage.style.opacity = '0';
                setTimeout(() => errorMessage.remove(), 500);
            }, 1000);
        }
    });
</script>

</html>