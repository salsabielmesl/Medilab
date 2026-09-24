<style>
  .carousel-preview img {
    max-height: 150px;
    object-fit: cover;
  }
  .bg-indigo-400 {
    background-color: #818CF8 !important;
    color: white !important;
  }
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

$TOTAL_SLIDES = 3;

$result = $conn->query("SELECT * FROM carousel ORDER BY id ASC");
$carousel = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $carousel[] = $row;
    }
}

// Pad with empty slides if less than $TOTAL_SLIDES
while (count($carousel) < $TOTAL_SLIDES) {
    $carousel[] = [
        'id' => 0,
        'image' => '',
        'title' => '',
        'description' => '',
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['title'] as $index => $title) {
        $id = (int)($_POST['id'][$index] ?? 0);
        $desc = $_POST['description'][$index] ?? '';
        $existingImage = $_POST['existing_image'][$index] ?? '';

        $imageName = $existingImage;

        // Handle image upload
        if (!empty($_FILES['image']['name'][$index]) && $_FILES['image']['error'][$index] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['image']['tmp_name'][$index];
            $origName = basename($_FILES['image']['name'][$index]);
            $ext = pathinfo($origName, PATHINFO_EXTENSION);
            $imageName = 'carousel_' . time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($tmpName, "uploads/aboutus/" . $imageName);
        }

        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE carousel SET title=?, description=?, image=? WHERE id=?");
            $stmt->bind_param("sssi", $title, $desc, $imageName, $id);
            $stmt->execute();
        } else {
            if ($title !== '' || $desc !== '' || $imageName !== '') {
                $stmt = $conn->prepare("INSERT INTO carousel (title, description, image) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $title, $desc, $imageName);
                $stmt->execute();
            }
        }
    }

    header("Location: editcarousel.php?updated=1");
    exit;
}
?>
<?php
  session_start(); // make sure this is at the top
   'Role: ' . ($_SESSION['role'] ?? 'Not Set');
?>
<?php require('common/head.php') ?>
<body class="bg-light">
<?php require('common/sidebar.php') ?>
<div class="content">
<?php require('common/navbar.php') ?>

<div class="container py-5">
    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-success">Carousel updated successfully!</div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <?php foreach ($carousel as $index => $item): ?>
            <div class="card mb-4">
                <div class="card-header bg-indigo-400">
                    Slide <?= $index + 1 ?>
                </div>
                <div class="card-body row g-3">
                    <input type="hidden" name="id[]" value="<?= (int)$item['id'] ?>">
                    <input type="hidden" name="existing_image[]" value="<?= htmlspecialchars($item['image']) ?>">

                    <div class="col-md-4 carousel-preview">
                        <label class="form-label">Current Image:</label>
                        <?php if ($item['image']): ?>
                            <img src="uploads/aboutus/<?= htmlspecialchars($item['image']) ?>" class="img-fluid rounded border" alt="Slide Image">
                        <?php else: ?>
                            <div class="border rounded d-flex align-items-center justify-content-center text-muted" style="height:150px;">
                                No image uploaded
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control mt-2" name="image[]">
                    </div>

                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title[]" value="<?= htmlspecialchars($item['title']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description[]" rows="3"><?= htmlspecialchars($item['description']) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

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
