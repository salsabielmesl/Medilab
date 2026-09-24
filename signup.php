<?php require_once 'authController.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Register</h4>
          </div>
          <div class="card-body">
            <form action="signup.php" method="POST">

            <?php if(count($errors)>0):?>
            <div class="alert alert-danger">
            <?php foreach($errors as $error):?>
            <li><?php echo $error;?> </li>
            <?php endforeach;?>
            </div>
            
            <?php endif; ?>

              <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" value="<?php echo $username;?> "placeholder="Enter your username" required>
              </div>

              <div class="mb-3">
                <label for="Email_Address" class="form-label">Email Address</label>
                <input type="email" id="Email_Address" name="Email_Address" class="form-control"value="<?php echo $Email_Address;?>" placeholder="Enter your email" required>
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" value="<?php echo $password;?>" placeholder="Enter your password" required>
              </div>

              <div class="mb-3">
                <label for="DateofBirth" class="form-label">Date of Birth</label>
                <input type="date" id="DateofBirth" name="DateofBirth" value="<?php echo $DateofBirth?>" class="form-control" required>
              </div>

              <div class="mb-3">
                <label for="Gender" class="form-label">Gender</label>
                <select id="Gender" name="Gender" value="<?php echo $Gender?>"  class="form-select" required>
                  <option value="">Select</option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                  <option value="other">Other</option>
                </select>
              </div>

              <div class="d-grid">
                <button type="submit" name="signup-btn" class="btn btn-primary">Submit</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
