<style>
  .gallery-card {
  width: 100%;
  height: 200px;
  overflow: hidden;
  border-radius: 10px;
}

.gallery-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 10px;
  transition: transform 0.3s ease;
}

.gallery-img:hover {
  transform: scale(1.05);
}


  .gallery-container {
    display: flex;
    overflow-x: auto;
    gap: 1rem;
    scroll-behavior: smooth;
    padding-bottom: 1rem;
    -webkit-overflow-scrolling: touch; 
  }
  .gallery-item {
    flex: 0 0 auto; 
    width: 300px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    cursor: pointer;
    transition: transform 0.3s ease;
  }
  .gallery-item:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
  }
  .gallery-item img {
    width: 100%;
    height: auto;
    display: block;
  }
  #hero-carousel {
  width: 100%;
}

#hero-carousel .carousel-inner {
  height: 400px; 
}

#hero-carousel .carousel-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.carousel-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

</style>
<?php
include 'dp.php';

// Fetch all departments
$departments = $conn->query("SELECT * FROM department");

// Set default department ID for doctors dropdown (first department if available)
$defaultDepID = 0;
if ($departments && $departments->num_rows > 0) {
    $firstDep = $departments->fetch_assoc();
    $defaultDepID = (int)$firstDep['DepID'];
    $departments->data_seek(0); // Reset pointer
}

// Fetch doctors for the default department
$doctorsResult = $conn->query("
    SELECT d.Name, d.ProfilePic, dep.DepName
    FROM doctor d
    JOIN department dep ON d.DepID = dep.DepID
    WHERE d.DepID = $defaultDepID
    ORDER BY d.Name ASC
");

// Fetch carousel slides
$slides = [];
$result = $conn->query("SELECT * FROM carousel ORDER BY id ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $slides[] = $row;
    }
}

// Fetch active About Us content
$aboutResult = $conn->query("SELECT * FROM aboutus WHERE active = TRUE");
$about = $aboutResult ? $aboutResult->fetch_assoc() : null;
$image = $about['image'] ?? 'default_about.jpg';
$content = $about['content'] ?? 'About us information is not available.';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <title>landing - Medilab Bootstrap Template</title>

  <link href="assets/img/favicon1.png" rel="icon" />
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon" />
  <link href="https://fonts.googleapis.com" rel="preconnect" />
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&family=Poppins:wght@400;600&display=swap"
    rel="stylesheet" />
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" />
  <link href="assets/vendor/aos/aos.css" rel="stylesheet" />
  <link href="assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" />
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet" />
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet" />
  <link href="assets/css/main.css" rel="stylesheet" />
  <style>
    .doctor-card .card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      cursor: pointer;
    }

    .doctor-card .card:hover {
      transform: translateY(-15px) scale(1.05);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
      z-index: 10;
    }
    .department-img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 10px;
}

  </style>
</head>

<body class="index-page">
  <!-- ======= Header ======= -->
  <header id="header" class="header sticky-top">
    <div class="branding d-flex align-items-center">
      <div class="container position-relative d-flex align-items-center justify-content-end">
        <a href="landingpage.php" class="logo d-flex align-items-center me-auto">
          <img src="assets/img/favicon1.png" alt="Medilab Logo" style="width: 80px; height: 80px" />
        </a>

        <nav id="navmenu" class="navmenu">
          <ul>
            <li><a href="#hero" class="active">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#departments">Departments</a></li>
            <li><a href="#doctors">Doctors</a></li>
            <li><a href="#gallery">Gallery</a></li>
          </ul>
          <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

        <a class="cta-btn" href="../login.php">Login</a>
      </div>
    </div>
  </header>

  <main class="main">
    <!-- hero section -->
<form action="carousel.php" method="POST" enctype="multipart/form-data">
  <div id="hero-carousel" class="carousel slide carousel-fade w-100" data-bs-ride="carousel" data-bs-interval="5000">

    
    <div class="carousel-indicators">
      <?php
      $result = $conn->query("SELECT * FROM carousel ORDER BY id ASC");
      $count = 0;
      while ($row = $result->fetch_assoc()) {
          $active = $count === 0 ? 'active' : '';
          echo '<button type="button" data-bs-target="#hero-carousel" data-bs-slide-to="' . $count . '" class="' . $active . '" aria-label="Slide ' . ($count + 1) . '"></button>';
          $count++;
      }
      if ($count === 0) {
          echo '<button type="button" data-bs-target="#hero-carousel" data-bs-slide-to="0" class="active" aria-label="Slide 1"></button>';
      }
      ?>
    </div>

    <div class="carousel-inner" style="height: 400px;">
      <?php
      $result = $conn->query("SELECT * FROM carousel ORDER BY id ASC");
      $count = 0;
      if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              $active = $count === 0 ? 'active' : '';

              $image = !empty($row['image']) ? '../uploads/aboutus/' . htmlspecialchars($row['image']) : '';
              $title = htmlspecialchars($row['title']);
              $desc = nl2br(htmlspecialchars($row['description']));

              echo '<div class="carousel-item ' . $active . '">';
              if ($image) {
                  echo '<img src="' . $image . '" class="d-block w-100 carousel-img" alt="' . $title . '" />';
              } else {
                  echo '<div style="height:400px; background:#ddd; display:flex; align-items:center; justify-content:center;">';
                  echo '<span class="text-muted">No image available</span>';
                  echo '</div>';
              }
              echo '<div class="container">';
              echo '<div class="carousel-caption">';
              echo '<h2>' . $title . '</h2>';
              echo '<p class="lead">' . $desc . '</p>';
              echo '<a href="#about" class="btn-get-started">Read More</a>';
              echo '</div>';
              echo '</div>';
              echo '</div>';

              $count++;
          }
      } else {
          echo '<div class="carousel-item active">';
          echo '<img src="../uploads/aboutus/hero-carousel-1.jpg" class="d-block w-100" style="height: 400px; object-fit: cover;" alt="Welcome to Medilab" />';
          echo '<div class="container">';
          echo '<div class="carousel-caption">';
          echo '<h2>Welcome to Medilab</h2>';
          echo '<p class="lead">Trusted care for your family’s health—where compassion meets advanced medical expertise.</p>';
          echo '<a href="#about" class="btn-get-started">Read More</a>';
          echo '</div>';
          echo '</div>';
          echo '</div>';
      }
      ?>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#hero-carousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon  " aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#hero-carousel" data-bs-slide="next">
      <span class="carousel-control-next-icon " aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>
</form>


    <!-- End Hero Section -->



    <!-- End Hero Section -->

   <!-- ======= About Section ======= -->
<?php
// Fetch the active About Us version
$aboutResult = $conn->query("SELECT * FROM aboutus WHERE active = TRUE ");
$about = $aboutResult ? $aboutResult->fetch_assoc() : null;

$image = $about['image'] ?? 'default_about.jpg';
$content = $about['content'] ?? 'About us information is not available.';
?>
<section id="about" class="about section" data-aos="fade-up">
  <div class="container section-title" data-aos="fade-up">
    <h2>About Us</h2>
  </div>
  <div class="container">
    <div class="row gy-4">
      <div class="col-lg-6 position-relative align-self-start" data-aos="fade-up" data-aos-delay="100">
        <img src="../uploads/aboutus/<?= htmlspecialchars($image) ?>" alt="About Us Image" class="img-fluid" style="max-width: 450px; height: auto;" />
        <!-- Removed duplicated content here -->
      </div>
      <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="200">
        <h3>Welcome to Medilab Clinic</h3>
        <p class="fst-italic"><?= nl2br(htmlspecialchars($content)) ?></p>
      </div>
    </div>
  </div>
</section>


    <!-- End About Section -->

    

    <!-- ======= Departments Section ======= -->
    <section id="departments" class="tabs section" data-aos="fade-up">
      <div class="container section-title" data-aos="fade-up">
        <h2>Departments</h2>
      </div>
      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row">
          <div class="col-lg-3">
            <ul class="nav nav-tabs flex-column">
              <?php
              $activeSet = false;
              if ($departments && $departments->num_rows > 0):
                foreach ($departments as $index => $row):
                  $depID = 'tabs-tab-' . $row['DepID'];
                  $isActive = !$activeSet ? 'active show' : '';
                  $activeSet = true;
              ?>
              <li class="nav-item">
                <a class="nav-link <?= $isActive ?>" data-bs-toggle="tab" href="#<?= $depID ?>">
                  <?= htmlspecialchars($row['DepName']) ?>
                </a>
              </li>
              <?php endforeach; endif; ?>
            </ul>   
          </div>

          <div class="col-lg-9 mt-4 mt-lg-0">
            <div class="tab-content">
              <?php
              $departments->data_seek(0);
              $activeSet = false;
              foreach ($departments as $row):
                $depID = 'tabs-tab-' . $row['DepID'];
                $isActive = !$activeSet ? 'active show' : '';
                $activeSet = true;
              ?>
              <div class="tab-pane <?= $isActive ?>" id="<?= $depID ?>">
                <div class="row">
                  <div class="col-lg-8 details order-2 order-lg-1">
                    <h3><?= htmlspecialchars($row['DepName']) ?></h3>
                    <p class="fst-italic"><?= nl2br(htmlspecialchars($row['Description'])) ?></p>
                  </div>
                  <div class="col-lg-4 text-center order-1 order-lg-2">
                    <?php if (!empty($row['Image'])): ?>
                      <img src="../uploads/departments/<?= htmlspecialchars($row['Image']) ?>" alt="<?= htmlspecialchars($row['DepName']) ?>" class="department-img" />
                    <?php else: ?>
                      <img src="assets/img/default-department.jpg" alt="Default Department" class="img-fluid" />
                    <?php endif; ?>
                  </div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- End Departments Section -->

    <!-- ======= Doctors Section ======= -->
    <section id="doctors" class="doctors section-bg py-5">
      <div class="container">
        <div class="section-title text-center mb-4">
          <h2>Doctors</h2>
          <p>Our professional medical experts</p>
        </div>

        <!-- Department Dropdown -->
<div class="mb-4 d-flex justify-content-end align-items-center">
  <label for="departmentSelect" class="form-label fw-bold me-3 mb-0">Select Department:</label>
  <select id="departmentSelect" class="form-select w-auto">
    <?php if ($departments && $departments->num_rows > 0): ?>
      <?php
      $departments->data_seek(0);
      foreach ($departments as $dep): ?>
        <option value="<?= htmlspecialchars($dep['DepID']) ?>" <?= ($dep['DepID'] == $defaultDepID) ? 'selected' : '' ?>>
          <?= htmlspecialchars($dep['DepName']) ?>
        </option>
      <?php endforeach; ?>
    <?php else: ?>
      <option disabled>No Departments Available</option>
    <?php endif; ?>
  </select>
</div>


        <!-- Doctors Cards Container -->
        <div class="row" id="doctorsContainer">
          <?php
          $delay = 0;
          if ($doctorsResult && $doctorsResult->num_rows > 0):
            while ($doc = $doctorsResult->fetch_assoc()):
          ?>
            <div
              class="col-lg-4 col-md-6 d-flex align-items-stretch mb-4 doctor-card"
              style="animation-delay: <?= $delay ?>s;"
            >
              <div class="card shadow-sm border-0 w-100">
                <div class="card-img-top overflow-hidden" style="height: 280px;">
                  <?php if (!empty($doc['ProfilePic'])): ?>
                    <img
                      src="../uploads/<?= htmlspecialchars($doc['ProfilePic']) ?>"
                      class="img-fluid h-100 w-100 object-fit-cover"
                      alt="Doctor Profile"
                    >
                  <?php else: ?>
                    <img
                      src="assets/img/default-doctor.jpg"
                      class="img-fluid h-100 w-100 object-fit-cover"
                      alt="Default Doctor"
                    >
                  <?php endif; ?>
                </div>
                <div class="card-body text-center d-flex flex-column justify-content-center">
                  <h5 class="card-title mb-2"><?= htmlspecialchars($doc['Name']) ?></h5>
                  <p class="card-text text-muted mb-3"><?= htmlspecialchars($doc['DepName']) ?></p>
                  
                </div>
              </div>
            </div>
          <?php
            $delay += 0.2;
            endwhile;
          else:
          ?>
            <p class="text-center">No doctors available for this department.</p>
          <?php endif; ?>
        </div>
      </div>
    </section>
    <!-- End Doctors Section -->

    <!-- ======= Gallery Section ======= -->
<section id="gallery" class="gallery section">
  <div class="container section-title" data-aos="fade-up">
    <h2>Gallery</h2>
    <p>Some photos from our clinic and events</p>
  </div>

  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="gallery-container d-flex flex-wrap gap-3 justify-content-center">
      <?php 
      $result = $conn->query("SELECT * FROM gallery ORDER BY uploaded_at DESC");
      while ($row = $result->fetch_assoc()):
          $imgPath = preg_replace('#^medicio/#', '', $row['image']);
      ?>
      <div class="gallery-item card shadow-sm" style="width: 250px; height: 180px; overflow: hidden; border-radius: 10px;">
        <a href="<?= htmlspecialchars($imgPath) ?>" class="glightbox" data-gallery="gallery">
          <img src="<?= htmlspecialchars($imgPath) ?>" class="w-100 h-100 object-cover" alt="Gallery Image">
        </a>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>


<!-- End Gallery Section -->



<script>
  // Initialize GLightbox for gallery images
  const lightbox = GLightbox({
    selector: '.glightbox',
    loop: true,
    zoomable: true,
  });
</script>

  </main>

  <!-- ======= Footer ======= -->
 <footer id="footer" class="footer">
  <div class="container d-flex justify-content-between align-items-center">
    <div class="copyright">
      &copy; Copyright <strong><span>Medilab</span></strong>. All Rights Reserved
    </div>
    <div class="credits text-end">
      Designed by Medilab Team
    </div>
  </div>
</footer>

  <!-- End Footer -->

  <!-- Scroll to Top Button -->
  <a href="#" class="scroll-top d-flex align-items-center justify-content-center">
    <i class="bi bi-arrow-up-short"></i>
  </a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

  <script>
    // AJAX load doctors on department change
    document.getElementById('departmentSelect').addEventListener('change', function () {
      const depID = this.value;
      const container = document.getElementById('doctorsContainer');
      container.innerHTML = `<p class="text-center">Loading doctors...</p>`;

      fetch(`../getdocs.php?depID=${depID}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            if (data.doctors.length === 0) {
              container.innerHTML = `<p class="text-center">No doctors available for this department.</p>`;
              return;
            }
            let html = '';
            let delay = 0;
            data.doctors.forEach(doc => {
              html += `
                <div class="col-lg-4 col-md-6 d-flex align-items-stretch mb-4 doctor-card" style="animation-delay: ${delay}s;">
                  <div class="card shadow-sm border-0 w-100">
                    <div class="card-img-top overflow-hidden" style="height: 280px;">
                      <img src="${doc.ProfilePic ? '../uploads/' + doc.ProfilePic : 'assets/img/default-doctor.jpg'}" alt="Doctor Profile" class="img-fluid h-100 w-100 object-fit-cover" />
                    </div>
                    <div class="card-body text-center d-flex flex-column justify-content-center">
                      <h5 class="card-title mb-2">${doc.Name}</h5>
                      <p class="card-text text-muted mb-3">${doc.DepName}</p>
                      
                    </div>
                  </div>
                </div>
              `;
              delay += 0.2;
            });
            container.innerHTML = html;
          } else {
            container.innerHTML = `<p class="text-center text-danger">Failed to load doctors.</p>`;
          }
        })
        .catch(() => {
          container.innerHTML = `<p class="text-center text-danger">Error loading doctors.</p>`;
        });
    });
  </script>
</body>

</html>
