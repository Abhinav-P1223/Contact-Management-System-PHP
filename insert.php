<?php
session_start();
require 'config.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn = getDBConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $gender   = $_POST['gender'] ?? '';
    $country  = ($_POST['country'] === 'Other') ? trim($_POST['other_country']) : ($_POST['country'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $hobbies  = $_POST['hobbies'] ?? [];

    $errors = [];

    // Validation
    if (!$name || !$email || !$phone || !$gender || !$country || !$username || !$password) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (!preg_match('/^\d{10}$/', $phone)) {
        $errors[] = "Phone number must be exactly 10 digits.";
    }

    if (
        strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[0-9]/', $password)
    ) {
        $errors[] = "Password must be at least 8 characters long, with at least 1 uppercase and 1 number.";
    }

    $stmt = $conn->prepare("SELECT id FROM contacts WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Username already taken.";
    }
    $stmt->close();

    // File upload
    $profilePic = "";
    $targetDir = "uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if (!empty($_FILES["profile_pic"]["name"])) {
        $fileName = preg_replace("/[^a-zA-Z0-9\.\-_]/", "", basename($_FILES["profile_pic"]["name"]));
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed  = ['jpg', 'jpeg', 'png'];

        if (!in_array($fileExt, $allowed)) {
            $errors[] = "Invalid file type. Only JPG, JPEG, and PNG allowed.";
        } elseif ($_FILES["profile_pic"]["size"] > 2 * 1024 * 1024) {
            $errors[] = "Image must be less than 2MB.";
        } else {
            $newFile = $targetDir . uniqid("img_", true) . "." . $fileExt;
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $newFile)) {
                $profilePic = $newFile;
            } else {
                $errors[] = "Image upload failed.";
            }
        }
    }

    if (empty($errors)) {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        $hobbyStr = implode(", ", $hobbies);

        $stmt = $conn->prepare("INSERT INTO contacts (name, email, phone, gender, country, username, password, hobbies, profile_pic) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $name, $email, $phone, $gender, $country, $username, $passwordHash, $hobbyStr, $profilePic);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();

            // ✅ Send email with login info
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'pinnamaneniabhinav@gmail.com'; //  your Gmail
                $mail->Password   = 'ybva bacj beki gvnj';   //  16-digit App Password
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom('yourgmail@gmail.com', 'Contact Registration');
                $mail->addAddress($email, $name);

                $mail->isHTML(true);
                $mail->Subject = 'Welcome to Our App - Login Details';
                $mail->Body    = "
                    <h3>Welcome, $name!</h3>
                    <p>Thanks for registering. Below are your login details:</p>
                    <ul>
                        <li><strong>Username:</strong> $username</li>
                        <li><strong>Password:</strong> $password</li>
                    </ul>
                    <p><em>Note: Please keep this information secure.</em></p>
                ";

                $mail->send();
            } catch (Exception $e) {
                error_log("Mailer Error: {$mail->ErrorInfo}");
            }

            header("Location: index.php?msg=added");
            exit();
        } else {
            $errors[] = "Database insert failed: " . $conn->error;
            $stmt->close();
        }
    }

    $_POST['hobbies'] = $hobbies;
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_old'] = $_POST;
    header("Location: add.php");
    exit();
}
?>
