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
  .bg-indigo {
    background-color: #818CF8 !important;
    color: white !important;
  }
  .modal-body {
    max-height: 70vh;
    overflow-y: auto;
  }
  textarea.form-control {
    resize: vertical;
    max-height: 200px;
  }
</style>

<?php
session_start();
$docID = $_SESSION['DocID'] ?? null;
if (!$docID) {
    die("No doctor logged in. Please login.");
}

require('dp.php');

require('common/head.php');

// === Dynamic KPI Queries ===

// Patients count (distinct patients for this doctor)
$queryPatients = "
    SELECT COUNT(DISTINCT p.PatientID) 
    FROM patient p
    INNER JOIN appointments a ON p.PatientID = a.PatientID
    WHERE a.DocID = ?
";
$stmtPatients = $conn->prepare($queryPatients);
$stmtPatients->bind_param("i", $docID);
$stmtPatients->execute();
$patientsCount = $stmtPatients->get_result()->fetch_row()[0];

// Reports count for this doctor
$queryReports = "
    SELECT COUNT(r.ReportID)
    FROM reports r
    INNER JOIN appointments a ON r.AppID = a.AppID
    WHERE a.DocID = ?
";
$stmtReports = $conn->prepare($queryReports);
$stmtReports->bind_param("i", $docID);
$stmtReports->execute();
$reportsCount = $stmtReports->get_result()->fetch_row()[0];

// Income sum for this doctor
$queryIncome = "
    SELECT IFNULL(SUM(a.PaymentAmount), 0)
    FROM appointments a
    WHERE a.DocID = ?
";
$stmtIncome = $conn->prepare($queryIncome);
$stmtIncome->bind_param("i", $docID);
$stmtIncome->execute();
$incomeSum = $stmtIncome->get_result()->fetch_row()[0];

// === Upcoming Appointments (not yet accepted) ===
$queryUpcoming = "
  SELECT a.AppID, p.FullName, p.DOB, a.AppDate, a.StartTime
  FROM appointments a
  INNER JOIN patient p ON a.PatientID = p.PatientID
  WHERE a.DocID = ? AND a.Status = 'Appointed' AND (a.Approved IS NULL OR a.Approved = 0)
  ORDER BY a.AppDate ASC, a.StartTime ASC
";
$stmtUpcoming = $conn->prepare($queryUpcoming);
$stmtUpcoming->bind_param("i", $docID);
$stmtUpcoming->execute();
$resultUpcoming = $stmtUpcoming->get_result();

// === Accepted Appointments with report existence check ===
$queryAccepted = "
  SELECT a.AppID, p.FullName, p.DOB, a.AppDate, a.StartTime,
         (SELECT COUNT(*) FROM reports r WHERE r.AppID = a.AppID) AS HasReport
  FROM appointments a
  INNER JOIN patient p ON a.PatientID = p.PatientID
  WHERE a.DocID = ? AND a.Status = 'Appointed' AND a.Approved = 1
  ORDER BY a.AppDate DESC, a.StartTime DESC
";
$stmtAccepted = $conn->prepare($queryAccepted);
$stmtAccepted->bind_param("i", $docID);
$stmtAccepted->execute();
$resultAccepted = $stmtAccepted->get_result();
?>

<body>
<?php require('common/sidebar.php'); ?>
<div class="content">
<?php require('common/navbar.php'); ?>

<div class="app-body">
  <!-- KPI Cards -->
  <div class="row mt-4 gx-3">
    <div class="col-xxl-2 col-sm-4">
      <div class="card mb-3 text-center">
        <div class="card-body mh-230">
          <div class="icon-box xl bg-primary-subtle rounded-5 mb-2">
            <i class="ri-empathize-line fs-1 text-primary"></i>
          </div>
          <h1 class="text-primary"><?= number_format($patientsCount) ?></h1>
          <h6>Patients</h6>
          
        </div>
      </div>
    </div>
    <div class="col-xxl-2 col-sm-4">
      <div class="card mb-3 text-center">
        <div class="card-body mh-230">
          <div class="icon-box xl bg-danger-subtle rounded-5 mb-2">
            <i class="ri-lungs-line fs-1 text-danger"></i>
          </div>
          <h1 class="text-danger"><?= number_format($reportsCount) ?></h1>
          <h6>Reports</h6>
          
        </div>
      </div>
    </div>
    <div class="col-xxl-2 col-sm-4">
      <div class="card mb-3 text-center">
        <div class="card-body mh-230">
          <div class="icon-box xl bg-success-subtle rounded-5 mb-2">
            <i class="ri-money-dollar-circle-line fs-1 text-success"></i>
          </div>
          <h1 class="text-success">$<?= number_format($incomeSum) ?></h1>
<h6>Income</h6>


        </div>
      </div>
    </div>
  </div>

  <!-- Upcoming Appointments Section -->
  <div class="row gx-3">
    <div class="col-xxl-6 col-sm-12">
      <div class="card mb-3">
        <div class="card-header"><h5 class="card-title">Upcoming Appointments</h5></div>
        <div class="card-body" style="max-height:300px; overflow-y:auto;">
          <?php if ($resultUpcoming->num_rows > 0): ?>
            <?php while ($row = $resultUpcoming->fetch_assoc()): ?>
              <div class="d-flex flex-column p-3 border rounded-2 mb-3">
                <div class="d-flex align-items-center mb-3">
                  <div>
                    <h6 class="mb-1">Patient: <strong><?= htmlspecialchars($row['FullName']) ?></strong></h6>
                    <p class="mb-1"><strong>DOB:</strong> <?= htmlspecialchars($row['DOB']) ?></p>
                    <p class="mb-1">
                      <strong>Date:</strong>
                      <?= date('F j, Y', strtotime($row['AppDate'])) ?> – <?= date('h:i A', strtotime($row['StartTime'])) ?>
                    </p>
                    <div class="mt-2">
                      <form method="post" action="handle_appointment_status.php" class="d-inline">
                        <input type="hidden" name="AppID" value="<?= $row['AppID'] ?>">
                        <input type="hidden" name="action" value="accept">
                        <button type="submit" class="btn btn-indigo-400 btn-sm">Accept</button>
                      </form>
                      <form method="post" action="handle_appointment_status.php" class="d-inline">
                        <input type="hidden" name="AppID" value="<?= $row['AppID'] ?>">
                        <input type="hidden" name="action" value="decline">
                        <button type="submit" class="btn btn-danger btn-sm">Decline</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <p>No upcoming appointments found.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Accepted Appointments Section -->
  <div class="row gx-3 mt-3">
    <div class="col-xxl-6 col-sm-12">
      <div class="card mb-3">
        <div class="card-header"><h5 class="card-title">Appointments</h5></div>
        <div class="card-body" style="max-height:300px; overflow-y:auto;">
          <?php if ($resultAccepted->num_rows > 0): ?>
            <?php while ($row = $resultAccepted->fetch_assoc()): ?>
              <div class="d-flex align-items-center mb-3">
                <div>
                  <h6 class="mb-1">Patient: <strong><?= htmlspecialchars($row['FullName']) ?></strong></h6>
                  <p class="mb-1"><strong>DOB:</strong> <?= htmlspecialchars($row['DOB']) ?></p>
                  <p class="mb-1">
                    <strong>Date:</strong>
                    <?= date('F j, Y', strtotime($row['AppDate'])) ?> – <?= date('h:i A', strtotime($row['StartTime'])) ?>
                  </p>
                  <div class="mt-2">
                    <button
                      class="btn btn-indigo-400 btn-sm report-btn"
                      data-bs-toggle="modal"
                      data-bs-target="#reportModal"
                      data-appid="<?= $row['AppID'] ?>"
                      data-mode="edit"
                    >
                      Report
                    </button>

                    <?php if ($row['HasReport'] > 0): ?>
                      <button
                        class="btn btn-secondary btn-sm view-report-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#reportModal"
                        data-appid="<?= $row['AppID'] ?>"
                        data-mode="view"
                      >
                        View Report
                      </button>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <p>No appointments found.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

</div> <!-- End .app-body -->

<!-- Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <form id="reportForm" method="post" action="savereport.php">
        <div class="modal-header bg-indigo">
          <h5 class="modal-title text-white">Fill Medical Report</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

          <input type="hidden" name="AppID" id="reportAppID" />

          <!-- Visit Info -->
          <h6 class="text-primary fw-bold mb-3">Visit Info</h6>
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Doctor’s Name</label>
              <input type="text" class="form-control" name="DoctorName" readonly>
            </div>
            <div class="col-md-6">
              <label class="form-label">Visit Date</label>
              <input type="date" class="form-control" name="VisitDate" readonly>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Specialization</label>
              <input type="text" class="form-control" name="Specialization" readonly>
            </div>
          </div>

          <!-- Patient Info -->
          <h6 class="text-primary fw-bold mb-3">Patient Info</h6>
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Full Name</label>
              <input type="text" class="form-control" id="patientName" readonly>
            </div>
            <div class="col-md-6">
              <label class="form-label">Birth Date</label>
              <input type="text" class="form-control" id="patientDOB" readonly>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Phone</label>
              <input type="text" class="form-control" name="Phone" readonly>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="Email" readonly>
            </div>
          </div>

          <!-- Assessment -->
          <h6 class="text-primary fw-bold mb-2">Assessment</h6>
          <div class="mb-3">
            <textarea class="form-control" name="Assessment" rows="4" placeholder="Write assessment here..." required></textarea>
          </div>

          <!-- Diagnosis -->
          <h6 class="text-primary fw-bold mb-2">Diagnosis</h6>
          <div class="mb-3">
            <textarea class="form-control" name="Diagnosis" rows="4" placeholder="Write diagnosis here..." required></textarea>
          </div>

          <!-- Prescription -->
          <h6 class="text-primary fw-bold mb-2">Prescription</h6>
          <div class="mb-3">
            <textarea class="form-control" name="Prescription" rows="4" placeholder="Write prescription here..." required></textarea>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-indigo-400">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require('common/footer.php'); ?>
<?php require('common/javascript.php'); ?>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var reportModal = document.getElementById('reportModal');

    function setFormReadOnly(readOnly) {
      const inputs = reportModal.querySelectorAll('input, textarea');
      inputs.forEach(input => {
        if (readOnly) {
          input.setAttribute('readonly', 'readonly');
          input.setAttribute('disabled', 'disabled'); // disables inputs/buttons if any
        } else {
          input.removeAttribute('readonly');
          input.removeAttribute('disabled');
        }
      });

      // Except hidden input for AppID
      var appIDInput = reportModal.querySelector('input[name="AppID"]');
      appIDInput.removeAttribute('disabled');
      appIDInput.setAttribute('readonly', 'readonly');

      // Show/hide Save button
      var saveBtn = reportModal.querySelector('button[type="submit"]');
      if (readOnly) {
        saveBtn.style.display = 'none';
      } else {
        saveBtn.style.display = 'inline-block';
      }
    }

    reportModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      var appID = button.getAttribute('data-appid');
      var mode = button.getAttribute('data-mode'); // 'edit' or 'view'

      document.getElementById('reportAppID').value = appID;

      // Clear previous values
      const fields = ['DoctorName', 'VisitDate', 'Specialization', 'Phone', 'Email'];
      fields.forEach(field => {
        const input = document.querySelector(`input[name="${field}"]`);
        if (input) input.value = '';
      });
      document.getElementById('patientName').value = '';
      document.getElementById('patientDOB').value = '';

      ['Assessment', 'Diagnosis', 'Prescription'].forEach(name => {
        const ta = reportModal.querySelector(`textarea[name="${name}"]`);
        if (ta) ta.value = '';
      });

      // Fetch visit, patient and report info
      fetch('fetchreport.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'AppID=' + encodeURIComponent(appID)
      })
      .then(response => response.json())
      .then(data => {
        if (data.error) {
          alert(data.error);
          return;
        }

        // Fill visit and patient info
        document.querySelector('input[name="DoctorName"]').value = data.DoctorName || '';
        document.querySelector('input[name="VisitDate"]').value = data.AppDate || '';
        document.querySelector('input[name="Specialization"]').value = data.Specialization || '';
        document.getElementById('patientName').value = data.PatientName || '';
        document.getElementById('patientDOB').value = data.DOB || '';
        document.querySelector('input[name="Phone"]').value = data.Phone || '';
        document.querySelector('input[name="Email"]').value = data.Email || '';

        // Fill report fields if exists
        if (data.Report) {
          document.querySelector('textarea[name="Assessment"]').value = data.Report.Assessment || '';
          document.querySelector('textarea[name="Diagnosis"]').value = data.Report.Diagnosis || '';
          document.querySelector('textarea[name="Prescription"]').value = data.Report.Prescription || '';
        }

        // Set readonly or editable
        if (mode === 'view') {
          setFormReadOnly(true);
        } else {
          setFormReadOnly(false);
        }
      })
      .catch(error => {
        console.error('Error fetching report data:', error);
      });
    });
  });
</script>
</div>
</body>
</html>
