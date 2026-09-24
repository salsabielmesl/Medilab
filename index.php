<?php
require_once 'dp.php'; // your DB connection

require 'auth.php';
if ($_SESSION['role'] !== 'admin') {
    header("Location: adminlogin.php");
    exit;
}

// Optimized single query to fetch all counts
$sql = "
  SELECT
    (SELECT COUNT(*) FROM appointments) AS appointmentsCount,
    (SELECT COUNT(*) FROM doctor) AS doctorsCount,
    (SELECT COUNT(*) FROM receptionist) AS receptionistsCount,
    (SELECT COUNT(*) FROM patient) AS patientsCount,
    (SELECT COUNT(*) FROM department) AS departmentsCount
";
$result = $conn->query($sql);
if ($result) {
    $counts = $result->fetch_assoc();
    $appointmentsCount = $counts['appointmentsCount'] ?? 0;
    $doctorsCount = $counts['doctorsCount'] ?? 0;
    $receptionistsCount = $counts['receptionistsCount'] ?? 0;
    $patientsCount = $counts['patientsCount'] ?? 0;
    $departmentsCount = $counts['departmentsCount'] ?? 0;
} else {
    $appointmentsCount = $doctorsCount = $receptionistsCount = $patientsCount = $departmentsCount = 0;
}

// Fetch appointment status counts
$appointmentStatusCounts = ['Appointed' => 0, 'Paid' => 0, 'Cancel' => 0];
$result = $conn->query("SELECT Status, COUNT(*) as cnt FROM appointments GROUP BY Status");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $status = $row['Status'];
        $count = (int)$row['cnt'];
        if (isset($appointmentStatusCounts[$status])) {
            $appointmentStatusCounts[$status] = $count;
        }
    }
}

// Fetch patients count by gender
$patientGenderCounts = ['Male' => 0, 'Female' => 0];
$result = $conn->query("SELECT Gender, COUNT(*) as cnt FROM patient GROUP BY Gender");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $gender = $row['Gender'];
        $count = (int)$row['cnt'];
        if (isset($patientGenderCounts[$gender])) {
            $patientGenderCounts[$gender] = $count;
        }
    }
}

// Total revenue (sum of 'Total' column in appointments where paid)
$result = $conn->query("SELECT IFNULL(SUM(Total), 0) AS total_revenue FROM appointments WHERE Status = 'Paid'");
$totalRevenue = 0;
if ($result) {
    $row = $result->fetch_assoc();
    $totalRevenue = (float)$row['total_revenue'];
}

// Revenue by department
$deptRevenue = [];
$sql = "
    SELECT d.DepName, IFNULL(SUM(a.Total), 0) AS revenue
    FROM department d
    LEFT JOIN appointments a ON a.DepID = d.DepID AND a.Status = 'Paid'
    GROUP BY d.DepID, d.DepName
    ORDER BY revenue DESC
";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $deptRevenue[] = [
            'name' => $row['DepName'],
            'revenue' => (float)$row['revenue']
        ];
    }
}

// Expected revenue static (you can make this dynamic if you want)
$expectedRevenue = 10000;

?>

<?php require('common/head.php') ?>

<style>
  .back-to-top {
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 9999;
  width: 50px;
  height: 50px;
  background-color: #556ee6; /* Indigo */
  color: white;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 8px rgba(0,0,0,0.15);
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.back-to-top:hover {
  background-color: #818cf8; /* lighter indigo */
  color: white;
}

.back-to-top i {
  font-size: 24px; /* make the arrow bigger */
  line-height: 1;
  vertical-align: middle;
}

  .icon-box.md {
    background: linear-gradient(135deg, #556ee6 0%, #34c38f 100%);
    transition: background 0.3s ease;
    color: white;
    border-radius: 0.75rem;
    padding: 1rem;
  }

  /* Card hover effect */
  .card:hover {
    background-color: #a5b4fc !important; /* Tailwind's indigo-300 */
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.12);
    transition: all 0.3s ease;
    cursor: pointer;
  }

  /* Icon box hover effect */
  .card:hover .icon-box.md {
    background: linear-gradient(135deg, #a5b4fc 0%, #818cf8 100%);
  }

  /*text contrast on hover */
  .card:hover h6,
  .card:hover h2 {
    color: white !important;
  }

  /* Make cards clickable*/
  .card-clickable {
    cursor: pointer;
  }
</style>

<body x-data="{ dashboardOpen: true }" class="position-relative">
  <div class="container-fluid bg-white d-flex p-0">
    <!-- Spinner -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center" style="z-index: 1050;">
      <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <!-- Sidebar -->
    <?php require('common/sidebar.php') ?>

    <!-- Content -->
    <div class="content">
      <?php require('common/navbar.php') ?>
      <div class="container mt-5">
    <h2>Welcome Admin: <?= htmlspecialchars($_SESSION['username']) ?></h2> 
</div>
      <!-- Breadcrumb Navigation -->
      <div class="container-fluid py-2 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-2">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
          </ol>
        </nav>

        <!-- Top Cards -->
        <div class="row gx-3">
          <?php
            $stats = [
              ['title' => 'Appointments', 'count' => $appointmentsCount, 'icon' => 'ri-verified-badge-line', 'link' => 'appointments.php'],
              ['title' => 'Doctors', 'count' => $doctorsCount, 'icon' => 'ri-stethoscope-line', 'link' => 'doctors.php'],
              ['title' => 'Receptionists', 'count' => $receptionistsCount, 'icon' => 'ri-briefcase-line', 'link' => 'receptionists.php'],
              ['title' => 'Patients', 'count' => $patientsCount, 'icon' => 'ri-team-line', 'link' => 'patient.php'],
              ['title' => 'Departments', 'count' => $departmentsCount, 'icon' => 'ri-building-line', 'link' => 'departments.php']
            ];
            foreach ($stats as $s): ?>
            <div class="col-xl-2 col-sm-6 col-12">
              <div
                class="card mb-3 card-clickable"
                data-type="<?= strtolower($s['title']) ?>"
                data-bs-toggle="modal"
                data-bs-target="#cardDetailsModal"
                style="cursor:pointer;"
              >
                <div class="card-body">
                  <div class="d-flex flex-column align-items-center" data-bs-toggle="tooltip" title="View <?= htmlspecialchars($s['title']) ?>">
                    <div class="icon-box md rounded-5 mb-3 p-3 text-white">
                      <i class="<?= htmlspecialchars($s['icon']) ?> fs-4 lh-1"></i>
                    </div>
                    <h6><?= htmlspecialchars($s['title']) ?></h6>
                    <h2 class="text-primary m-0"><?= number_format($s['count']) ?></h2>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Charts -->
        <div class="row gx-3">
          <div class="col-md-6 col-sm-12">
            <div class="card mb-3">
              <div class="card-header">
                <h5 class="card-title mb-0">Appointments status</h5>
              </div>
              <div class="card-body"><div id="claims"></div></div>
            </div>
          </div>
          <div class="col-md-6 col-sm-12">
            <div class="card mb-3">
              <div class="card-header">
                <h5 class="card-title mb-0">Patients by Gender</h5>
              </div>
              <div class="card-body"><div id="genderAge"></div></div>
            </div>
          </div>
        </div>

        <!-- Revenue and Deals Charts -->
        <div class="row gx-3">
          <div class="col-md-6 col-sm-12">
            <div class="card mb-3">
              <div class="card-header">
                <h5 class="card-title mb-0">Total revenue (yearly)</h5>
              </div>
              <div class="card-body text-center">
                <div id="revenueGauge" class="mb-3"></div>
                <div class="d-flex justify-content-around">
                  <div><strong>Expected</strong><br>$<?= number_format($expectedRevenue, 2) ?></div>
                  <div><strong>Realized</strong><br>$<?= number_format($totalRevenue, 2) ?></div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-md-6 col-sm-12">
            <div class="card mb-3">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Revenue By Department</h5>
              </div>
              <div class="card-body">
                <div id="dealsChart"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <a href="#" class="back-to-top" title="Back to top">
  <i class="bi bi-arrow-up"></i>
</a>

        <?php require('common/footer.php') ?>
      </div>
    </div>

    <!-- Modal for card details -->
    <div class="modal fade" id="cardDetailsModal" tabindex="-1" aria-labelledby="cardDetailsModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header" style="background-color: #818CF8; color: white;">
            <h5 class="modal-title" id="cardDetailsModalLabel">Details</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="cardDetailsContent">
            <div class="text-center">Loading...</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Scripts -->
    <?php require('common/javascript.php') ?>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
      document.addEventListener("DOMContentLoaded", function () {

        const spinner = document.getElementById('spinner');
        if (spinner) {
          spinner.style.transition = 'opacity 0.4s ease';
          spinner.style.opacity = '0';
          setTimeout(() => spinner.style.display = 'none', 400);
        }

        // Initialize Bootstrap tooltips
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el));

        // Charts data from PHP
        const appointmentStatusData = <?= json_encode(array_values($appointmentStatusCounts)) ?>;
        const appointmentStatusLabels = <?= json_encode(array_keys($appointmentStatusCounts)) ?>;
        const patientGenderCounts = <?= json_encode($patientGenderCounts) ?>;
        const expectedRevenue = <?= json_encode($expectedRevenue) ?>;
        const realizedRevenue = <?= json_encode($totalRevenue) ?>;
        const revenuePercent = Math.min(100, parseFloat(((realizedRevenue / expectedRevenue) * 100).toFixed(2)) || 0);
        const deptRevenueData = <?= json_encode(array_map(fn($d) => $d['revenue'], $deptRevenue)) ?>;
        const deptRevenueLabels = <?= json_encode(array_map(fn($d) => $d['name'], $deptRevenue)) ?>;

        // Charts
        new ApexCharts(document.querySelector("#claims"), {
          chart: { type: 'pie', height: 300 },
          series: appointmentStatusData,
          labels: appointmentStatusLabels,
          colors: ['#34c38f', '#556ee6', '#f46a6a'],
          legend: { position: 'bottom' }
        }).render();

        new ApexCharts(document.querySelector("#genderAge"), {
          chart: { type: 'bar', height: 300 },
          series: [
            { name: 'Male', data: [patientGenderCounts['Male'] || 0] },
            { name: 'Female', data: [patientGenderCounts['Female'] || 0] }
          ],
          xaxis: { categories: ['Total Patients'] },
          colors: ['#556ee6', '#f46a6a'],
          plotOptions: { bar: { columnWidth: '45%' } },
          legend: { position: 'top' }
        }).render();

        new ApexCharts(document.querySelector("#revenueGauge"), {
          chart: { type: 'radialBar', height: 300 },
          series: [revenuePercent],
          plotOptions: {
            radialBar: {
              hollow: { size: '70%' },
              dataLabels: {
                name: { show: false },
                value: {
                  fontSize: '24px',
                  fontWeight: 'bold',
                  formatter: val => val + '%'
                }
              }
            }
          },
          colors: ['#34c38f']
        }).render();

        new ApexCharts(document.querySelector("#dealsChart"), {
          chart: { type: 'bar', height: 300, toolbar: { show: false } },
          series: [{
            name: 'Revenue',
            data: deptRevenueData
          }],
          xaxis: {
            categories: deptRevenueLabels,
            labels: { rotate: -45 }
          },
          colors: ['#556ee6'],
          plotOptions: { bar: { columnWidth: '50%' } },
          dataLabels: {
            enabled: true,
            formatter: val => '$' + val.toLocaleString()
          },
          tooltip: {
            y: {
              formatter: val => '$' + val.toLocaleString()
            }
          },
          legend: { show: false }
        }).render();

        // Modal details loading
        const cards = document.querySelectorAll('.card-clickable');
        cards.forEach(card => {
          card.addEventListener('click', () => {
            const type = card.getAttribute('data-type');
            const modalTitle = document.getElementById('cardDetailsModalLabel');
            const modalBody = document.getElementById('cardDetailsContent');

            modalTitle.textContent = `Loading ${type.charAt(0).toUpperCase() + type.slice(1)} Details...`;
            modalBody.innerHTML = '<div class="text-center">Loading...</div>';

            // AJAX fetch details.php?type=...
            fetch(`appdetails.php?type=${encodeURIComponent(type)}`)
              .then(res => res.text())
              .then(html => {
                modalTitle.textContent = `${type.charAt(0).toUpperCase() + type.slice(1)} Details`;
                modalBody.innerHTML = html;
              })
              .catch(() => {
                modalTitle.textContent = `Error Loading ${type.charAt(0).toUpperCase() + type.slice(1)} Details`;
                modalBody.innerHTML = '<div class="text-danger text-center">Failed to load details. Please try again later.</div>';
              });
          });
        });
      });
    </script>
</body>
</html>
