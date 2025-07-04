<?php require "functions.php" ?>
<?php
session_start();
$status = $_SESSION['email_status'] ?? '';
$errorMessage = $_SESSION['email_error'] ?? '';
$loginError = $_SESSION['login_error'] ?? '';
unset($_SESSION['email_status'], $_SESSION['email_error'], $_SESSION['login_error']);
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/css/all.min.css">
  <link rel="stylesheet" href="/css/dash.css">
  <link rel="website icon" type="png" href="/images/logo.png">
  <title>Exam Organization</title>
</head>

<body>
  <?php if ($status == 'success') { ?>
    <div class="alert-message success">Success!</div>
  <?php } elseif ($status == 'fail') { ?>
    <div class="alert-message fail">Fail: <?php echo htmlspecialchars($errorMessage); ?></div>
  <?php } ?>

  <div class="container" id="container">
    <div class="from-container sign-in">
      <form action="signin.php" method="post">
        <h1>Sign In</h1>
        <div class="input_grope">
          <input type="email" id="email" name="email" required>
          <label for="email">Email</label>
        </div>
        <!--<div class="input_grope">
          <select id="role" name="role" required>
            <option value="admin">Admin</option>
            <option value="manager">Manager</option>
            <option value="organizer">Organizer</option>
          </select>
        </div>-->
        <div class="input_grope">
          <input type="password" id="password" name="password" maxlength="10" style="letter-spacing: 0.125em; font-family:Verdana;" required>
          <label for="password">Password</label>
          <i class="fa-solid fa-eye-slash toggle-password" id="togglePassword"></i>
        </div>
        <button type="submit" id="signInBtn" class="btn">Enter</button>
      </form>
    </div>
    <div class="from-container sign-up">
      <form name="my-form" action="/phpmailer-master/mailer/gmaphp.php" method="post">
        <p>send email to get password register</p>
        <div class="input_grope">
          <input type="email" id="email" name="sender_Email" required>
          <label for="">Email to</label>
        </div>
        <div class="input_grope">
          <input list="universities" name="univer_input" id="univer_input" required oninput="handleUniversityInput(this.value)" placeholder="Your university">
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
        <div class="input_grope">
          <input list="faculties" name="facul_input" id="facul_input" oninput="handleFacultyInput(this.value)" required disabled placeholder="Your faculty">
          <datalist id="faculties"></datalist>
          <input type="hidden" name="facul" id="facul_hidden">
        </div>
        <div class="input_grope">
          <input type="text" id="name" name="name" required>
          <label for="">Your name</label>
        </div>
        <div class="input_grope">
          <input type="email" id="email" name="your_email" required>
          <label for="">Your email</label>
        </div>
        <div class="input_grope">
          <input type="text" id="text" name="your_role" required>
          <label for="">Your role</label>
        </div>
        <button type="submit" id="signUpBtn" class="btn">Send</button>
      </form>
    </div>
    <?php if (!empty($loginError)) { ?>
      <div class="try_again"><?php echo htmlspecialchars($loginError); ?></div>
    <?php } ?>
    <div class=" toggle_container">
      <div class="toggle">
        <div class="toggle-panal toggle-left">
          <img src="/images/logo.png" alt="none">
          <h1>Exam Organization</h1>
          <p>Welcome to the exam organization app for universities and colleges.</p>
          <button class="hidden" id="login">Sign in</button>
        </div>
        <div class="toggle-panal toggle-right">
          <img src="/images/logo.png" alt="photo_2025-02-14_19-12-32.jpg">
          <h1>Exam Organization</h1>
          <p>Welcome to the exam organization app for universities and colleges.</p>
          <button class="hidden" id="register">Get an account</button>
        </div>
      </div>
    </div>
  </div>
  <script src="/scripts/dashboard.js"></script>
  <script src="/scripts/scriptslect.js"></script>
  <script>
    const alertBox = document.querySelector(".alert-message");
    if (alertBox) {
      alertBox.addEventListener("animationend", () => {
        alertBox.style.display = "none";
      });
    }
  </script>
</body>

</html>