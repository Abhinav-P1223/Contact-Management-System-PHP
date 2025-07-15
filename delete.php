<?php
session_start();
require 'config.php'; 

$conn = getDBConnection();

if (!isset($_SESSION['admin']) && !isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    // Check if the contact exists
    $stmt = $conn->prepare("SELECT profile_pic FROM contacts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // Delete profile picture if it exists
        if (!empty($row['profile_pic']) && file_exists($row['profile_pic'])) {
            unlink($row['profile_pic']);
        }

        // Admins can delete any contact
        if (isset($_SESSION['admin'])) {
            $stmt = $conn->prepare("DELETE FROM contacts WHERE id = ?");
            $stmt->bind_param("i", $id);
        } 
        // Normal users can delete only their own contact
        else {
            $userId = $_SESSION['user_id'];
            $stmt = $conn->prepare("DELETE FROM contacts WHERE id = ? AND id = ?");
            $stmt->bind_param("ii", $id, $userId);
        }

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();

            if (isset($_SESSION['admin'])) {
                header("Location: admin.php?msg=deleted");
            } else {
                header("Location: index.php?msg=deleted");
            }
            exit();
        } else {
            echo "❌ Failed to delete contact.";
        }
    } else {
        echo "⚠️ Contact not found.";
    }
} else {
    echo "⚠️ Invalid request.";
}

$conn->close();
?>
