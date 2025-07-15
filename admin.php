<?php
session_start();
require 'config.php';
$conn = getDBConnection();

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Pagination setup
$limit = 2;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search and gender filter
$search = trim($_GET['search'] ?? '');
$genderFilter = $_GET['gender'] ?? '';

$sql = "SELECT * FROM contacts";
$where = [];
$params = [];
$types = '';

if ($search !== '') {
    $where[] = "(name LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm; $params[] = $searchTerm; $params[] = $searchTerm;
    $types .= 'sss';
}

if ($genderFilter !== '') {
    $where[] = "gender = ?";
    $params[] = $genderFilter;
    $types .= 's';
}

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

// Count total for pagination
$countSql = $sql;
$countStmt = $conn->prepare($countSql);
if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalResults = $countStmt->get_result()->num_rows;
$countStmt->close();

// Add order, limit and offset
$sql .= " ORDER BY id DESC LIMIT $limit OFFSET $offset";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$contacts = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Count totals
$totalCountQuery = $conn->query("SELECT COUNT(*) as total FROM contacts");
$totalCount = $totalCountQuery->fetch_assoc()['total'];
$maleCount = $conn->query("SELECT COUNT(*) as total FROM contacts WHERE gender = 'Male'")->fetch_assoc()['total'];
$femaleCount = $conn->query("SELECT COUNT(*) as total FROM contacts WHERE gender = 'Female'")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - All Contacts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="text-primary">📋 All Contacts (Admin View)</h3>
        <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
    </div>

    <div class="mb-3 d-flex gap-3">
        <span class="badge bg-dark p-2">Total Contacts: <?= $totalCount ?></span>
        <span class="badge bg-primary p-2">Males: <?= $maleCount ?></span>
        <span class="badge bg-warning text-dark p-2">Females: <?= $femaleCount ?></span>
    </div>

    <form method="GET" class="mb-3 d-flex flex-wrap gap-2">
        <input type="text" name="search" class="form-control" placeholder="Search by name, email or phone" value="<?= htmlspecialchars($search) ?>">
        <select name="gender" class="form-select">
            <option value="">All Genders</option>
            <option value="Male" <?= $genderFilter === 'Male' ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= $genderFilter === 'Female' ? 'selected' : '' ?>>Female</option>
            <option value="Other" <?= $genderFilter === 'Other' ? 'selected' : '' ?>>Other</option>
        </select>
        <button type="submit" class="btn btn-outline-primary">Search</button>
        <?php if ($search !== '' || $genderFilter !== ''): ?>
            <a href="admin.php" class="btn btn-outline-secondary">Reset</a>
        <?php endif; ?>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-hover bg-white">
            <thead class="table-dark">
                <tr>
                    <th>S.No.</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Gender</th>
                    <th>Hobbies</th>
                    <th>Country</th>
                    <th>Profile</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($contacts): $i = $offset + 1; ?>
                <?php foreach ($contacts as $row): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><?= htmlspecialchars($row['gender']) ?></td>
                        <td><?= htmlspecialchars($row['hobbies']) ?></td>
                        <td><?= htmlspecialchars($row['country']) ?></td>
                        <td>
                            <?php if (!empty($row['profile_pic']) && file_exists($row['profile_pic'])): ?>
                                <img src="<?= $row['profile_pic'] ?>" width="50" height="50" class="rounded-circle">
                            <?php else: ?>
                                <span class="text-muted">No image</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this contact?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center">No contacts found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <nav>
        <ul class="pagination justify-content-center">
            <?php
            $totalPages = ceil($totalResults / $limit);
            for ($p = 1; $p <= $totalPages; $p++):
                $url = "?page=$p";
                if ($search) $url .= "&search=" . urlencode($search);
                if ($genderFilter) $url .= "&gender=$genderFilter";
            ?>
                <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                    <a class="page-link" href="<?= $url ?>"><?= $p ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>
</body>
</html>
