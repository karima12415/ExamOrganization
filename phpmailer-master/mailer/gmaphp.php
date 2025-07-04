<?php
require_once 'mail.php';
session_start(); 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sender_Email = $_POST["sender_Email"];
    $your_university =$_POST["univer_input"];
    $your_faculty =$_POST["facul_input"];
    $name = $_POST["name"];
    $your_email = $_POST["your_email"];
    $your_role = $_POST["your_role"];

    $fixed_Receiver =  $sender_Email;

    $mail->setFrom($sender_Email);
    $mail->addAddress($fixed_Receiver);
    $mail->Subject = $your_email;
    $mail->Body = "
    <h3>New Message</h3>
    <p>University: $your_university</p>
    <p>Faculty: $your_faculty</p>
    <p>From: $name</p>
    <p>Email: $your_email</p>
    <p>Role: $your_role</p>
    ";
    if ($mail->send()) {
        $_SESSION['email_status'] = "success";
        header("Location:  ../../pages/dashboard.php");    
        exit;
    } else {
        $_SESSION['email_status'] = "fail";
        $_SESSION['email_error'] = $mail->ErrorInfo;
        header("Location: ../../pages/dashboard.php");
        exit;
    }
}
?>