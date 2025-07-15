<?php
session_start();
require 'config.php';
$conn = getDBConnection();

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit();
}

// Fetch contact data
$stmt = $conn->prepare("SELECT * FROM contacts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$contact = $result->fetch_assoc();
$stmt->close();

if (!$contact) {
    echo "Contact not found.";
    exit();
}

// Retrieve validation errors (if any)
$errors = $_SESSION['form_errors'] ?? [];
$old = $_SESSION['form_old'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['form_old']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Contact</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
<h2 class="mb-4 text-primary">Edit Contact</h2>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="update.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $contact['id'] ?>">

    <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($old['name'] ?? $contact['name']) ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($old['email'] ?? $contact['email']) ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control" required value="<?= htmlspecialchars($old['phone'] ?? $contact['phone']) ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Gender</label><br>
        <?php foreach (['Male', 'Female', 'Other'] as $g): ?>
            <div class="form-check form-check-inline">
                <input type="radio" name="gender" class="form-check-input" value="<?= $g ?>" 
                    <?= ($old['gender'] ?? $contact['gender']) === $g ? 'checked' : '' ?>>
                <label class="form-check-label"><?= $g ?></label>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mb-3">
        <label class="form-label">Country</label>
        <select name="country" class="form-select" onchange="document.getElementById('otherCountry').style.display = this.value == 'Other' ? 'block' : 'none';" required>
            <option value="">Select</option>
            <option value="India" <?= ($old['country'] ?? $contact['country']) == 'India' ? 'selected' : '' ?>>India</option>
            <option value="USA" <?= ($old['country'] ?? $contact['country']) == 'USA' ? 'selected' : '' ?>>USA</option>
            <option value="Other" <?= ($old['country'] ?? $contact['country']) == 'Other' ? 'selected' : '' ?>>Other</option>
        </select>
        <input type="text" name="other_country" id="otherCountry" class="form-control mt-2" placeholder="Enter Country"
            style="display: <?= ($old['country'] ?? $contact['country']) == 'Other' ? 'block' : 'none' ?>;"
            value="<?= $old['other_country'] ?? '' ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($old['username'] ?? $contact['username']) ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Hobbies</label><br>
        <?php
        $allHobbies = ['Reading', 'Gaming', 'Sports', 'Music', 'Travel', 'Coding'];
        $selectedHobbies = explode(", ", $old['hobbies'] ?? $contact['hobbies']);
        foreach ($allHobbies as $hobby): ?>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="hobbies[]" value="<?= $hobby ?>"
                       <?= in_array($hobby, $selectedHobbies) ? 'checked' : '' ?>>
                <label class="form-check-label"><?= $hobby ?></label>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mb-3">
        <label class="form-label">Profile Picture</label><br>
        <?php if (!empty($contact['profile_pic']) && file_exists($contact['profile_pic'])): ?>
            <img src="<?= $contact['profile_pic'] ?>" width="80" class="mb-2 rounded"><br>
        <?php endif; ?>
        <input type="file" name="profile_pic" class="form-control">
    </div>

    <button type="submit" class="btn btn-primary">Update Contact</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>
</body>
</html>
