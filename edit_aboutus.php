<style>
    .btn-indigo-400 {
    background-color: #818CF8 !important;
    color: white !important;
    border: none !important;
  }
  .btn-indigo-400:hover,
  .btn-indigo-400:focus {
    background-color: #5c7cfa !important;
    color: white !important;
  }
  </style>
<?php
include 'dp.php';

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = $_POST['content'] ?? '';
    $content = $conn->real_escape_string($content);

    $imageName = 'default_about.jpg'; // fallback

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileTmp = $_FILES['image']['tmp_name'];
        $fileName = basename($_FILES['image']['name']);
        $fileType = mime_content_type($fileTmp);

        if (!in_array($fileType, $allowedTypes)) {
            $message = 'Invalid image type. Only JPG, PNG, GIF allowed.';
        } else {
            $uploadDir = __DIR__ . '/uploads/aboutus/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $newFileName = 'aboutus_' . time() . '.' . $ext;
            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmp, $destination)) {
                $imageName = $newFileName;
            } else {
                $message = 'Failed to upload image.';
            }
        }
    }

    if (!$message) {
        // Deactivate all previous versions
        $conn->query("UPDATE aboutus SET active = FALSE");

        // Insert new version with active = TRUE
        $sql = "INSERT INTO aboutus (content, image, active) VALUES ('$content', '$imageName', TRUE)";
        if ($conn->query($sql)) {
            $message = 'New About Us version added and activated.';
        } else {
            $message = 'Database error: ' . $conn->error;
        }
    }
}

// Fetch current active About Us version for preview
$activeResult = $conn->query("SELECT * FROM aboutus WHERE active = TRUE");
$activeAbout = $activeResult ? $activeResult->fetch_assoc() : null;
?>
<?php
  session_start(); // make sure this is at the top
   'Role: ' . ($_SESSION['role'] ?? 'Not Set');
?>
<?php require('common/head.php') ?>
<body>
<?php require('common/sidebar.php') ?>
<div class="content">
<?php require('common/navbar.php') ?>
<div class="container mt-5">
   

    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($activeAbout): ?>
        <div class="mb-5">
            
            <img src="uploads/aboutus/<?= htmlspecialchars($activeAbout['image']) ?>" alt="Active About Us Image" style="max-width:200px; display:block; margin-bottom:10px;">
            <!-- <div style="white-space: pre-wrap;"><?= htmlspecialchars($activeAbout['content']) ?></div> -->
        </div>
        <hr />
    <?php endif; ?>

   
    <form method="post" enctype="multipart/form-data" class="mt-4">
        <div class="mb-3">
            <label for="image" class="form-label">Upload Image:</label>
            <input type="file" name="image" id="image" accept="image/*" class="form-control" />
            <small class="form-text text-muted">Uploading a new image is optional. If no image is uploaded, a default image will be used.</small>
        </div>

       <div class="mb-3">
        <label for="content" class="form-label">About Us Content:</label>
        <textarea name="content" id="content" rows="8" class="form-control" required><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
    </div>

    <div class="text-end">
        <button type="submit" class="btn btn-indigo-400">Save</button>
    </div>
    </form>
</div>
<?php require('common/footer.php') ?>
<?php require('common/javascript.php') ?>
</div>
</body>
</html>
