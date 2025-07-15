<?php
session_start();
require 'config.php';
$conn = getDBConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id       = $_POST['id'] ?? null;
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $gender   = $_POST['gender'] ?? '';
    $country  = $_POST['country'] === 'Other' ? trim($_POST['other_country']) : $_POST['country'];
    $username = trim($_POST['username']);
    $hobbies  = $_POST['hobbies'] ?? [];
    $errors   = [];

    if (!$id || !$name || !$email || !$phone || !$gender || !$country || !$username) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        $errors[] = "Phone number must be 10 digits.";
    }

    if (!in_array($gender, ['Male', 'Female', 'Other'])) {
        $errors[] = "Invalid gender selected.";
    }

    // Check for duplicate username excluding current ID
    $stmt = $conn->prepare("SELECT id FROM contacts WHERE username = ? AND id != ?");
    $stmt->bind_param("si", $username, $id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $errors[] = "Username already taken.";
    }
    $stmt->close();

    // Get old image
    $stmt = $conn->prepare("SELECT profile_pic FROM contacts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $oldData = $stmt->get_result()->fetch_assoc();
    $oldImage = $oldData['profile_pic'] ?? '';
    $stmt->close();

    // Handle file upload
    $profilePic = $oldImage;
    $targetDir = "uploads/";
    if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
    if (!empty($_FILES["profile_pic"]["name"])) {
        $fileName = basename($_FILES["profile_pic"]["name"]);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];
        if (in_array($fileExt, $allowed)) {
            $newFile = $targetDir . uniqid() . "." . $fileExt;
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $newFile)) {
                if (!empty($oldImage) && file_exists($oldImage)) unlink($oldImage);
                $profilePic = $newFile;
            }
        }
    }

    if (empty($errors)) {
        $hobbyStr = implode(", ", $hobbies);
        $stmt = $conn->prepare("UPDATE contacts SET name=?, email=?, phone=?, gender=?, country=?, hobbies=?, profile_pic=?, username=? WHERE id=?");
        $stmt->bind_param("ssssssssi", $name, $email, $phone, $gender, $country, $hobbyStr, $profilePic, $username, $id);
        if ($stmt->execute()) {
            header("Location: index.php?msg=updated");
            exit();
        } else {
            echo "❌ Update failed: " . $conn->error;
        }
        $stmt->close();
    } else {
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_old'] = $_POST;
        header("Location: edit.php?id=$id");
        exit();
    }

    $conn->close();
}
?>
