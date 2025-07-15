<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
require 'config.php';
$conn = getDBConnection();

// Get logged-in user's ID
$userId = $_SESSION['user_id'];

// Fetch the user's contact
$stmt = $conn->prepare("SELECT * FROM contacts WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary">👤 My Profile</h2>
        <div>
            <a href="logout.php" class="btn btn-outline-danger">Logout</a>
        </div>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success">
            <?php
            if ($_GET['msg'] == 'added') echo "Profile created successfully!";
            elseif ($_GET['msg'] == 'updated') echo "Profile updated successfully!";
            elseif ($_GET['msg'] == 'deleted') echo "Profile deleted successfully!";
        ?>
        </div>
    <?php endif; ?>

    <?php if ($userData): ?>
        <table class="table table-bordered table-hover bg-white">
            <tr><th>ID</th><td><?= htmlspecialchars($userData['id']) ?></td></tr>
            <tr><th>Name</th><td><?= htmlspecialchars($userData['name']) ?></td></tr>
            <tr><th>Email</th><td><?= htmlspecialchars($userData['email']) ?></td></tr>
            <tr><th>Phone</th><td><?= htmlspecialchars($userData['phone']) ?></td></tr>
            <tr><th>Gender</th><td><?= htmlspecialchars($userData['gender']) ?></td></tr>
            <tr><th>Country</th><td><?= htmlspecialchars($userData['country']) ?></td></tr>
            <tr><th>Hobbies</th><td><?= htmlspecialchars($userData['hobbies']) ?></td></tr>
            <tr>
                <th>Profile Picture</th>
                <td>
                    <?php if (!empty($userData['profile_pic']) && file_exists($userData['profile_pic'])): ?>
                        <img src="<?= $userData['profile_pic'] ?>" width="80" height="80" class="rounded-circle">
                    <?php else: ?>
                        <span class="text-muted">No image</span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <div class="d-flex gap-2">
            <a href="edit.php?id=<?= $userData['id'] ?>" class="btn btn-primary">Edit Profile</a>
            <a href="delete.php?id=<?= $userData['id'] ?>" class="btn btn-danger"
               onclick="return confirm('Are you sure you want to delete your profile?');">Delete Profile</a>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">No profile data found.</div>
    <?php endif; ?>
</div>

</body>
</html>

<?php $conn->close(); ?>
