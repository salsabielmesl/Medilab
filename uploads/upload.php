<?php
if (isset($_POST['submit'])) {
  $file = $_FILES['profile'];

  $fileName = $file['name'];
  $fileTmpName = $file['tmp_name'];
  $fileError = $file['error'];

  if ($fileError === 0) {
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($fileExt, $allowed)) {
      $newFileName = uniqid("IMG_", true) . '.' . $fileExt;
      $destination = 'uploads/' . $newFileName;

      move_uploaded_file($fileTmpName, $destination);

      // Redirect back to form with image name
      header("Location: form.php?image=$newFileName");
      exit();
    } else {
      echo "File type not allowed.";
    }
  } else {
    echo "There was an error uploading your file.";
  }
}
?>
