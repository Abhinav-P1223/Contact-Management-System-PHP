<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require 'config.php';
$conn = getDBConnection();

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = $conn->prepare("SELECT id, username, password FROM contacts WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user['username'];
                $_SESSION['user_id'] = $user['id'];

                // Check if admin
                if (strtolower($user['username']) === 'admin') {
                    $_SESSION['is_admin'] = true;
                    header("Location: admin.php");
                } else {
                    $_SESSION['is_admin'] = false;
                    header("Location: index.php");
                }
                exit();
            } else {
                $error = "❌ Invalid password.";
            }
        } else {
            $error = "❌ Username not found.";
        }
        $stmt->close();
    } else {
        $error = "⚠️ Please fill in both username and password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
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
                <div class="card-header text-center bg-primary text-white">
                    <h4>🔐 User Login</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="login.php">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required autofocus>
                        </div>

                        <div class="mb-3 position-relative">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                            <span class="eye-icon" onclick="togglePassword()">👁️</span>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                        <p class="mt-3 text-center text-muted small">Don't have an account? <a href="add.php">Register here</a>.</p>
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
