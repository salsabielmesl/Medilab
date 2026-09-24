<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Upload Image</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5">

  <h3>Upload Profile Picture</h3>
  <form action="upload.php" method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <input type="file" name="profile" class="form-control" required>
    </div>
    <button type="submit" name="submit" class="btn btn-primary">Upload</button>
  </form>

  <?php
    if (isset($_GET['image'])) {
      $image = $_GET['image'];
      echo "<h5 class='mt-4'>Uploaded Image:</h5>";
      echo "<img src='uploads/$image' width='200' class='border rounded'>";
    }
  ?>

</body>
</html>
