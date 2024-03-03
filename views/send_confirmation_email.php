<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; 

function generateConfirmationCode($email, $db) {
    $email = mysqli_real_escape_string($db, $email);
    
    $confirmation_code = substr(md5(uniqid(mt_rand(), true)), 0, 6);
    
    $stmt = $db->prepare("INSERT INTO GeneratedCodes (code, email) VALUES (?, ?)");
    $stmt->bind_param("ss", $confirmation_code, $email);
    $stmt->execute();
    
    if ($stmt->affected_rows === 1) {
        return $confirmation_code;
    } else {
        return false;
    }
}

function sendConfirmationEmail($recipient_email, $confirmation_code) {
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.hostinger.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'admin@gamer-chronicle.world'; 
        $mail->Password = 'mM#4T:ir';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        
        $mail->setFrom('admin@gamer-chronicle.world', 'GamerChronicle email confirmation'); 
        $mail->addAddress($recipient_email); 
        
        $mail->isHTML(true);
        $mail->Subject = 'Email confirmation code';
        $mail->Body = 'Your confirmation code: ' . $confirmation_code;
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mail sending error: " . $mail->ErrorInfo);
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["email"])) {
    global $db;
    include("../config/db_config.php");
    $email = $_POST["email"];
    
    $generated_code = generateConfirmationCode($email, $db);
    if ($generated_code && sendConfirmationEmail($email, $generated_code)) {
        echo 'success';
    } else {
        echo "error";
    }
    
    $db->close();
} else {
    echo "invalid_request";
}

?>
