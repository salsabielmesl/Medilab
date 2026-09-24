<style>
  .gallery-preview img {
    max-height: 150px;
    margin: 10px;
    object-fit: cover;
    border-radius: 8px;
  }

  .btn-indigo-400 {
    background-color: #818CF8;
    color: white;
    border: none;
  }

  .btn-indigo-400:hover {
    background-color: #6366F1;
  }

  .text-indigo {
    color: #4F46E5;
  }

  .delete-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    background-color: rgba(220, 53, 69, 0.9);
    border: none;
    color: white;
    padding: 2px 6px;
    border-radius: 50%;
    font-weight: bold;
    cursor: pointer;
  }

  .delete-btn:hover {
    background-color: rgba(200, 35, 51, 1);
  }
</style>

<?php
include 'dp.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['gallery_images'])) {
    $uploadDir = 'medicio/assets/img/gallery/';
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

    foreach ($_FILES['gallery_images']['tmp_name'] as $index => $tmpName) {
        if ($_FILES['gallery_images']['error'][$index] === UPLOAD_ERR_OK) {
            $fileType = mime_content_type($tmpName);
            if (in_array($fileType, $allowedTypes)) {
                $fileName = basename($_FILES['gallery_images']['name'][$index]);
                $uniqueName = time() . '_' . $fileName;
                $targetPath = $uploadDir . $uniqueName;

                if (move_uploaded_file($tmpName, $targetPath)) {
                    $stmt = $conn->prepare("INSERT INTO gallery (image) VALUES (?)");
                    $stmt->bind_param("s", $targetPath);
                    $stmt->execute();
                }
            }
        }
    }

    header("Location: editgallery.php?success=1");
    exit();
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

  <?php if (isset($_GET['success'])): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Uploaded!',
        text: 'Images uploaded successfully.'
      });
    </script>
  <?php endif; ?>

  <?php if (isset($_GET['deleted'])): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Deleted!',
        text: 'Image deleted successfully.'
      });
    </script>
  <?php endif; ?>

  <form action="editgallery.php" method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm bg-white">
    <div class="mb-3">
      <input type="file" class="form-control" name="gallery_images[]" multiple required accept="image/*">
    </div>
    <button type="submit" class=" btn-indigo-400">Upload</button>
  </form>

  <hr class="my-5">

  <h4 class="mb-3">Current Gallery Images</h4>
  <div class="gallery-preview d-flex flex-wrap">
    <?php
    $galleryFolder = 'medicio/assets/img/gallery/';
    $images = glob($galleryFolder . '*.{jpg,jpeg,png,webp}', GLOB_BRACE);
    foreach ($images as $imgPath): ?>
      <div class="position-relative me-3 mb-3">
        <img src="<?= $imgPath ?>" alt="" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover; border-radius: 8px;">
        <form class="delete-form" method="POST" action="deletegallery.php" style="position: absolute; top: 0; right: 0;">
          <input type="hidden" name="image_path" value="<?= $imgPath ?>">
          <button type="button" class="delete-btn">✕</button>
        </form>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php require('common/footer.php') ?>
<?php require('common/javascript.php') ?>

<script>
  // SweetAlert delete confirmation
  document.querySelectorAll('.delete-form .delete-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      const form = this.closest('form');
      Swal.fire({
        title: 'Are you sure?',
        text: "This image will be permanently deleted.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
  });
</script>
</div>
</body>
</html>
