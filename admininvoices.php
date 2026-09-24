<?php
  session_start(); // make sure this is at the top
   'Role: ' . ($_SESSION['role'] ?? 'Not Set');
?>
<?php require('common/head.php'); ?>
<style>
  .avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 600;
    font-size: 0.9rem;
    margin-right: 15px;
    flex-shrink: 0;
  }
  .row-item {
    background: #fff;
    border-bottom: 1px solid #dee2e6;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .patient-info {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
  }
  .patient-details small {
    color: #6c757d;
    font-size: 0.85rem;
  }
  .status-time {
    display: flex;
    align-items: center;
    gap: 30px;
    min-width: 200px;
    justify-content: flex-end;
  }
  .status-badge {
    font-size: 0.8rem;
  }
  h5.section-title {
    margin-top: 2rem;
    margin-bottom: 1rem;
    font-weight: 600;
  }
</style>

<body>
<?php require('common/sidebar.php'); ?>
<div class="content">
<?php require('common/navbar.php'); ?>

<div class="container-fluid py-2 px-3">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-2">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active" aria-current="page">Invoices</li>
    </ol>
  </nav>
</div>

<div class="container">
  <h5 class="section-title">Invoices</h5>

  <!-- Filter Row -->
  <form id="filterForm" class="row g-3 mb-4" method="get">
    <div class="col-md-1 dropdown">
      <button class="btn btn-outline-secondary w-100 dropdown-toggle" type="button" data-bs-toggle="dropdown">📅</button>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#" data-filter="daterange" data-value="">All Time</a></li>
        <li><a class="dropdown-item" href="#" data-filter="daterange" data-value="this_week">This Week</a></li>
        <li><a class="dropdown-item" href="#" data-filter="daterange" data-value="this_month">This Month</a></li>
      </ul>
    </div>
    <div class="col-md-1 dropdown">
      <button class="btn btn-outline-secondary w-100 dropdown-toggle" type="button" data-bs-toggle="dropdown">📌</button>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#" data-filter="status" data-value="">All Statuses</a></li>
        <li><a class="dropdown-item" href="#" data-filter="status" data-value="appointed">Appointed</a></li>
        <li><a class="dropdown-item" href="#" data-filter="status" data-value="paid">Paid</a></li>
        <li><a class="dropdown-item" href="#" data-filter="status" data-value="cancel">Cancelled</a></li>
      </ul>
    </div>
    <div class="col-md-3">
      <input type="text" name="name" id="nameInput" class="form-control form-control-sm" placeholder="Search by name" value="<?= htmlspecialchars($_GET['name'] ?? '') ?>">
    </div>
    <input type="hidden" name="daterange" id="daterangeField" value="<?= htmlspecialchars($_GET['daterange'] ?? '') ?>">
    <input type="hidden" name="status" id="statusField" value="<?= htmlspecialchars($_GET['status'] ?? '') ?>">
  </form>

  <?php
  require_once 'dp.php';

  $conditions = [];
  $params = [];
  $types = '';

  $limit = 5;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

  $offset = ($page - 1) * $limit;

  if (!empty($_GET['name'])) {
    $conditions[] = "p.FullName LIKE ?";
    $params[] = "%" . $_GET['name'] . "%";
    $types .= 's';
  }

  if (!empty($_GET['daterange'])) {
    $today = date('Y-m-d');
    if ($_GET['daterange'] === 'this_week') {
      $monday = date('Y-m-d', strtotime('monday this week'));
      $conditions[] = "a.AppDate BETWEEN ? AND ?";
      $params[] = $monday;
      $params[] = $today;
      $types .= 'ss';
    } elseif ($_GET['daterange'] === 'this_month') {
      $first = date('Y-m-01');
      $conditions[] = "a.AppDate BETWEEN ? AND ?";
      $params[] = $first;
      $params[] = $today;
      $types .= 'ss';
    }
  }

  if (!empty($_GET['status'])) {
    $conditions[] = "a.Status = ?";
    $params[] = $_GET['status'];
    $types .= 's';
  }

  $whereSQL = count($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

  // Count query for pagination
  $countQuery = "SELECT COUNT(*) as total FROM appointments a JOIN patient p ON a.PatientID = p.PatientID $whereSQL";
  $countStmt = $conn->prepare($countQuery);
  if ($countStmt) {
    if (!empty($params)) $countStmt->bind_param($types, ...$params);
    $countStmt->execute();
    $totalRows = $countStmt->get_result()->fetch_assoc()['total'] ?? 0;
    $countStmt->close();
  }
  $totalPages = max(1, ceil($totalRows / $limit));

  // Main query
  $query = "
    SELECT a.AppID, p.FullName, a.Price, a.Discount, a.Total, a.Status, a.AppDate
    FROM appointments a
    JOIN patient p ON a.PatientID = p.PatientID
    $whereSQL
    ORDER BY a.AppDate DESC
    LIMIT ? OFFSET ?
  ";
  $params[] = $limit;
  $params[] = $offset;
  $types .= 'ii';

  $stmt = $conn->prepare($query);
  if ($stmt) {
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
      echo "<div class='alert alert-warning'>No invoices found.</div>";
    } else {
      while ($row = $result->fetch_assoc()):
        $name = $row['FullName'];
        $appID = $row['AppID'];
        $total = number_format($row['Total'], 2);
        $price = number_format($row['Price'], 2);
        $discount = number_format($row['Discount'], 2);
        $date = date("d M Y", strtotime($row['AppDate']));
        $status = strtolower($row['Status']);
        $initials = strtoupper(substr($name, 0, 1)) . (str_contains($name, ' ') ? strtoupper(substr(strrchr($name, ' '), 1, 1)) : '');
        $statusBadge = match ($status) {
          'paid' => "<span class='text-success status-badge'>💰 Paid</span>",
          'cancel' => "<span class='text-danger status-badge'>❌ Cancelled</span>",
          default => "<span class='text-secondary status-badge'>🕒 Appointed</span>",
        };
  ?>
      <div class="row-item">
        <div class="patient-info">
          <div class="avatar bg-primary"><?= $initials ?></div>
          <div class="patient-details">
            <strong><?= htmlspecialchars($name) ?></strong> • Invoice #INV-<?= $appID ?><br />
            <small>Service Price: $<?= $price ?> | Discount: $<?= $discount ?> | Total: $<?= $total ?></small>
          </div>
        </div>
        <div class="status-time">
          <?= $statusBadge ?>
          <span><?= $date ?></span>
        </div>
      </div>
  <?php endwhile;
    }
    $stmt->close();
  } else {
    echo "<div class='alert alert-danger'>Query error: " . $conn->error . "</div>";
  }
  ?>

  <!-- Pagination Links -->
  <?php if ($totalPages > 1): ?>
    <nav class="mt-4">
      <ul class="pagination justify-content-center">
        <?php
          $urlParams = $_GET;
          for ($i = 1; $i <= $totalPages; $i++):
            $urlParams['page'] = $i;
            $link = '?' . http_build_query($urlParams);
        ?>
          <li class="page-item <?= $i == $page ? 'active' : '' ?>">
            <a class="page-link" href="<?= $link ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
  <?php endif; ?>
</div>

<?php require('common/footer.php'); ?>
<?php require('common/javascript.php'); ?>

<script>
  document.querySelectorAll('.dropdown-item').forEach(item => {
    item.addEventListener('click', function (e) {
      e.preventDefault();
      const filter = this.dataset.filter;
      const value = this.dataset.value;
      const url = new URL(window.location.href);
      if (value) {
        url.searchParams.set(filter, value);
      } else {
        url.searchParams.delete(filter);
      }
      url.searchParams.delete('page'); // Reset to page 1
      window.location.href = url.toString();
    });
  });

  let debounce;
  const nameInput = document.getElementById('nameInput');
  nameInput.addEventListener('input', function () {
    clearTimeout(debounce);
    debounce = setTimeout(() => {
      const url = new URL(window.location.href);
      const value = nameInput.value.trim();
      if (value) {
        url.searchParams.set('name', value);
      } else {
        url.searchParams.delete('name');
      }
      url.searchParams.delete('page');
      window.location.href = url.toString();
    }, 400);
  });
</script>

</div>
</body>
</html>
