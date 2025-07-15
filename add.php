<?php
session_start();
require 'config.php';
$conn = getDBConnection();

$errors = $_SESSION['form_errors'] ?? [];
$old = $_SESSION['form_old'] ?? [];

unset($_SESSION['form_errors'], $_SESSION['form_old']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Contact</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h2 class="mb-4 text-primary">Add New Contact</h2>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="insert.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
    <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" id="name" class="form-control" required value="<?= $old['name'] ?? '' ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" id="email" class="form-control" required value="<?= $old['email'] ?? '' ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" id="phone" class="form-control" required value="<?= $old['phone'] ?? '' ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Gender</label><br>
        <?php foreach (['Male', 'Female', 'Other'] as $g): ?>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" value="<?= $g ?>" <?= (isset($old['gender']) && $old['gender'] === $g) ? 'checked' : '' ?>>
                <label class="form-check-label"><?= $g ?></label>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mb-3">
        <label class="form-label">Country</label>
        <select name="country" id="country" class="form-select" onchange="toggleOtherCountry()" required>
            <option value="">Select</option>
            <option value="India" <?= ($old['country'] ?? '') == 'India' ? 'selected' : '' ?>>India</option>
            <option value="USA" <?= ($old['country'] ?? '') == 'USA' ? 'selected' : '' ?>>USA</option>
            <option value="Other" <?= ($old['country'] ?? '') == 'Other' ? 'selected' : '' ?>>Other</option>
        </select>
        <input type="text" name="other_country" id="otherCountry" placeholder="Enter Country" class="form-control mt-2"
               style="display: <?= ($old['country'] ?? '') == 'Other' ? 'block' : 'none' ?>;"
               value="<?= $old['other_country'] ?? '' ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" id="username" class="form-control" required value="<?= $old['username'] ?? '' ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" required>
        <div class="form-text">At least 8 chars, 1 uppercase, 1 number</div>
    </div>

    <div class="mb-3">
        <label class="form-label">Hobbies</label><br>
        <?php
        $allHobbies = ['Reading', 'Gaming', 'Sports', 'Music', 'Travel', 'Coding'];
        $checkedHobbies = $old['hobbies'] ?? [];
        foreach ($allHobbies as $hobby): ?>
            <div class="form-check form-check-inline">
                <input type="checkbox" class="form-check-input" name="hobbies[]" value="<?= $hobby ?>" <?= in_array($hobby, $checkedHobbies) ? 'checked' : '' ?>>
                <label class="form-check-label"><?= $hobby ?></label>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mb-3">
        <label class="form-label">Profile Picture</label>
        <input type="file" name="profile_pic" class="form-control">
    </div>

    <button type="submit" class="btn btn-primary">Add Contact</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>

<script>
function toggleOtherCountry() {
    const countrySelect = document.getElementById('country');
    const otherInput = document.getElementById('otherCountry');
    otherInput.style.display = countrySelect.value === 'Other' ? 'block' : 'none';
}

function validateForm() {
    const email = document.getElementById("email").value.trim();
    const phone = document.getElementById("phone").value.trim();
    const password = document.getElementById("password").value.trim();
    const name = document.getElementById("name").value.trim();

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const phonePattern = /^\d{10}$/;
    const passwordPattern = /^(?=.*[A-Z])(?=.*\d).{8,}$/;

    if (!name) {
        alert("Name is required.");
        return false;
    }

    if (!emailPattern.test(email)) {
        alert("Invalid email format.");
        return false;
    }

    if (!phonePattern.test(phone)) {
        alert("Phone must be 10 digits.");
        return false;
    }

    if (!passwordPattern.test(password)) {
        alert("Password must be at least 8 characters, include 1 uppercase letter and 1 number.");
        return false;
    }

    return true;
}
</script>

</body>
</html>
