<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login2.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $profile_pic = $user['profilepic'];

    if (isset($_FILES['profilepic']) && $_FILES['profilepic']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['profilepic']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];

        if (in_array($ext, $allowed)) {
            $newName = uniqid('user_', true) . '.' . $ext;
            move_uploaded_file($_FILES['profilepic']['tmp_name'], 'uploads/' . $newName);
            $profile_pic = $newName;

            // Update DB
            $stmt = $pdo->prepare("UPDATE users SET profilepic = ? WHERE id = ?");
            $stmt->execute([$profile_pic, $_SESSION['user_id']]);
            $_SESSION['profilepic'] = $profile_pic;

            header("Location: myprofile.php?success=1");
            exit;
        } else {
            $error = "Invalid file type. Allowed: jpg, jpeg, png, gif";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #ddd;
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card p-4 shadow">
        <h3 class="text-center mb-4">My Profile</h3>

        <?php if (!empty($_GET['success'])): ?>
            <div class="alert alert-success">Profile picture updated successfully.</div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="text-center mb-4">
            <?php
$profilePic = !empty($user['profilepic']) ? 'uploads/' . htmlspecialchars($user['profilepic']) : 'uploads/default.png';
?>
<img src="<?= $profilePic ?>" alt="Profile Picture" class="profile-img mb-2">

            <h5><?= htmlspecialchars($user['username']) ?></h5>
            <p>Role: <?= htmlspecialchars($user['role']) ?></p>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="profilepic" class="form-label">Change Profile Picture</label>
                <input type="file" class="form-control" name="profilepic" id="profilepic" accept="image/*" required>
            </div>
            <button class="btn btn-primary w-100" type="submit">Update Picture</button>
        </form>

        <div class="mt-3 text-center">
            <a href="logout2.php" class="btn btn-danger btn-sm">Logout</a>
            <a href="index.php" class="btn btn-secondary btn-sm">Dashboard</a>
        </div>
    </div>
</div>
</body>
</html>
