<?php
session_start();
$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Static admin credentials (you can change)
    $admin_username = "admin";
    $admin_password = "Admin@123";  // Plain text

    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin'] = true;
        header("Location: admin.php");
        exit();
    } else {
        $error = "❌ Invalid admin credentials.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .eye-icon {
            position: absolute;
            right: 15px;
            top: 37px;
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header text-center bg-dark text-white">
                    <h4> Admin Login</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Admin Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>

                        <div class="mb-3 position-relative">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                            <span class="eye-icon" onclick="togglePassword()">👁️</span>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark">Login as Admin</button>
                        </div>
                        <p class="mt-3 text-center text-muted small">Default: admin / Admin@123</p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById("password");
    input.type = input.type === "password" ? "text" : "password";
}
</script>
</body>
</html>
