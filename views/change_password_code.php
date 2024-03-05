<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; 

function generateConfirmationCode($email, $db) {
    $email = mysqli_real_escape_string($db, $email);
    
    // Проверка наличия пользователя с указанным email
    $check_user_query = $db->prepare("SELECT email FROM users WHERE email = ?");
    $check_user_query->bind_param("s", $email);
    $check_user_query->execute();
    $check_user_result = $check_user_query->get_result();
    
    if ($check_user_result->num_rows == 0) {
        return "user_not_found"; // Возвращаем специальный код ошибки
    }
    
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
        
        $mail->setFrom('admin@gamer-chronicle.world', 'GamerChronicle change password'); 
        $mail->addAddress($recipient_email); 
        
        $mail->isHTML(true);
        $mail->Subject = 'Change password confirmation code';
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
    if ($generated_code == "user_not_found") { // Проверка специального кода ошибки
        echo "user_not_found"; // Возвращаем код ошибки
    } elseif ($generated_code && sendConfirmationEmail($email, $generated_code)) {
        echo 'success';
    } else {
        echo "error";
    }
    
    $db->close();
} else {
    echo "invalid_request";
}

?>
