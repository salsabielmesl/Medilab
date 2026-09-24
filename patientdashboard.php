<?php
session_start();
include 'dp.php';

if (!isset($_SESSION['PatientID']) && isset($_COOKIE['rememberme'])) {
    $token = $_COOKIE['rememberme'];
    $now = time();

    $stmt = $conn->prepare("SELECT PatientID, FullName FROM patient WHERE remember_token = ? AND token_expiry > ?");
    $stmt->bind_param("si", $token, $now);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['PatientID'] = $user['PatientID'];
        $_SESSION['FullName'] = $user['FullName'];
    }

    $stmt->close();
}

$PatientID = $_SESSION['PatientID'] ?? null;
if (!$PatientID) {
    // Redirect or error - user not logged in
    header("Location: login.php");
    exit;
}

// Fetch departments for dropdowns
$departments = $conn->query("SELECT DepID, DepName FROM department");
if (!$departments) {
    die("Failed to fetch departments: " . $conn->error);
}

// Fetch reports count for reports card
$stmtReportsCount = $conn->prepare("
    SELECT COUNT(*) AS cnt FROM reports r
    JOIN appointments a ON r.AppID = a.AppID
    WHERE a.PatientID = ?
");
$stmtReportsCount->bind_param("i", $PatientID);
$stmtReportsCount->execute();
$resCount = $stmtReportsCount->get_result();
$reportCountRow = $resCount->fetch_assoc();
$reportCount = $reportCountRow['cnt'] ?? 0;
$stmtReportsCount->close();

// Fetch unpaid bills total (RemainingAmount) for the logged-in patient
$unpaidTotal = 0;
$stmtUnpaid = $conn->prepare("
  SELECT SUM(RemainingAmount) AS total_unpaid 
  FROM appointments 
  WHERE PatientID = ? AND Status != 'paid'
");
$stmtUnpaid->bind_param("i", $PatientID);
$stmtUnpaid->execute();
$resUnpaid = $stmtUnpaid->get_result();
if ($row = $resUnpaid->fetch_assoc()) {
  $unpaidTotal = $row['total_unpaid'] ?? 0;
}
$stmtUnpaid->close();

// Fetch count of upcoming appointments with Status = 'appointed' for the logged-in patient
$upcomingCount = 0;

$stmtUpcoming = $conn->prepare("
  SELECT COUNT(*) AS count 
  FROM appointments 
  WHERE PatientID = ? 
    AND AppDate >= CURDATE()
    AND Status = 'Appointed'
");
$stmtUpcoming->bind_param("i", $PatientID);
$stmtUpcoming->execute();
$resUpcoming = $stmtUpcoming->get_result();

if ($row = $resUpcoming->fetch_assoc()) {
  $upcomingCount = $row['count'] ?? 0;
}
$stmtUpcoming->close();

?>

<?php require('common/head.php') ?>

<style>
  /* Style the appointments table like stafflist.php receptionist table */
/* Active page number button background color with Bootstrap pagination classes */
.dataTables_wrapper .dataTables_paginate .paginate_button.page-item.active > a.page-link {
  background-color: #818CF8 !important;
  border-color: #818CF8 !important;
  color: white !important;
  box-shadow: none !important;
}

  /* Hover effect */
  #appointmentsTable tbody tr:hover { cursor: pointer; }

  /* Flex container for action buttons */
  .action-buttons {
    display: flex;
    gap: 8px;
    justify-content: center;
  }

  /* Indigo button style */
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

  /* Left align modal labels */
  .modal-body label.form-label {
    text-align: left;
    display: block;
  }
  
  .table td, .table th {
    vertical-align: middle;
  }

 /* Keep search box on the right */
  .dataTables_filter {
    float: right !important;
    text-align: right !important;
  }

  
.dataTables_length {
    float: left !important;
    text-align: left !important;
  }
  /* Clear floats after the table controls */
  .dataTables_wrapper .row {
    clear: both;
  }

  /* Indigo table header */
  #appointmentsTable thead {
    background-color: #818cf8;
    color: white;
  }

  /* Center the header text */
  #appointmentsTable thead th {
    text-align: center;
  }

  /* Center Actions column header */
  #appointmentsTable thead th.actions-column {
    text-align: center;
  }
</style>

<body>
<?php require('common/sidebar2.php') ?>
<div class="content">
<?php require('common/navbar2.php') ?>

<main class="app-body">
  <h2>Welcome, <?= htmlspecialchars($_SESSION['FullName'] ?? "Patient") ?></h2>
  <p class="text-muted">Your health at a glance</p>

  <div class="row mb-4 g-3">
    <!-- Upcoming Appointments Card -->
    <!-- Upcoming Appointments Card -->
<div class="col-md-4">
  <div class="card shadow-sm">
    <div class="card-body d-flex align-items-center">
      <i class="bi bi-calendar-check-fill card-icon me-3"></i>
      <div>
        <h6>Upcoming Appointments</h6>
        <h5><?= (int)$upcomingCount ?></h5>
      </div>
    </div>
  </div>


    </div>

    <!-- Reports Card (clickable, opens modal) -->
    <div class="col-md-4">
      <div class="card shadow-sm cursor-pointer" id="reportsCard" data-bs-toggle="modal" data-bs-target="#reportsModal">
        <div class="card-body d-flex align-items-center">
          <i class="bi bi-file-earmark-text-fill card-icon me-3"></i>
          <div>
            <h6>Reports</h6>
            <h5><?= (int)$reportCount ?></h5>
          </div>
        </div>
      </div>
    </div>

    <!-- Unpaid Bills Card -->
   <!-- Unpaid Bills Card -->
<div class="col-md-4">
  <div class="card shadow-sm">
    <div class="card-body d-flex align-items-center">
      <i class="bi bi-cash-coin card-icon me-3"></i>
      <div>
        <h6>Unpaid Bills</h6>
        <h5>$<?= number_format($unpaidTotal, 2) ?></h5>
      </div>
    </div>
  </div>
</div>

  </div>

   <!-- Recent Appointments Section with updated table styling -->
  <div class="app-body">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title">Recent Appointments</h5>
          <button class="btn btn-indigo-400 ms-auto" data-bs-toggle="modal" data-bs-target="#bookAppointmentModal">
            <i class="bi bi-plus-circle me-1"></i> Add Appointment
          </button>
        </div>

        <div class="bg-light rounded p-4">
          <div class="table-responsive">
            <table id="appointmentsTable" class="table table-hover table-bordered table-striped align-middle m-0">
              <thead>
                <tr>
                  <th>Department</th>
                  <th>Doctor</th>
                  <th>Status</th>
                  <th>Message</th>
                  <th>Date</th>
                  <th>Time</th>
                  <th class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $stmt = $conn->prepare("
                    SELECT a.AppID, a.AppDate, a.StartTime, a.Status, a.message, a.DepID, a.DocID, d.Name AS DoctorName, dept.DepName
                    FROM appointments a
                    JOIN doctor d ON a.DocID = d.DocID
                    JOIN department dept ON a.DepID = dept.DepID
                    WHERE a.PatientID = ?
                    ORDER BY a.AppDate DESC, a.StartTime DESC
                ");
                if (!$stmt) {
                    die("Prepare failed: " . $conn->error);
                }
                $stmt->bind_param("i", $PatientID);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows === 0) {
                    echo "<tr><td colspan='7' class='text-center text-muted'>No appointments found.</td></tr>";
                } else {
                    while ($row = $result->fetch_assoc()):
                ?>
                  <tr class="text-center">
                    <td><?= htmlspecialchars($row['DepName']) ?></td>
                    <td><?= htmlspecialchars($row['DoctorName']) ?></td>
                    <td><?= htmlspecialchars($row['Status']) ?></td>
                    <td><?= htmlspecialchars($row['message']) ?></td>
                    <td><?= htmlspecialchars($row['AppDate']) ?></td>
                    <td><?= htmlspecialchars($row['StartTime']) ?></td>
                    <td>
                      <div class="action-buttons">
                        <button 
                          class="btn btn-sm btn-success edit-btn" 
                          data-appid="<?= $row['AppID'] ?>"
                          data-depid="<?= htmlspecialchars($row['DepID']) ?>"
                          data-docid="<?= htmlspecialchars($row['DocID']) ?>"
                          data-appdate="<?= htmlspecialchars($row['AppDate']) ?>"
                          data-starttime="<?= htmlspecialchars($row['StartTime']) ?>"
                          data-status="<?= htmlspecialchars($row['Status']) ?>"
                          data-message="<?= htmlspecialchars($row['message']) ?>"
                          title="Edit"
                        >
                          <i class="bi bi-pencil"></i>
                        </button>
                        <button
                          class="btn btn-sm btn-danger delete-btn"
                          data-appid="<?= $row['AppID'] ?>"
                          title="Delete"
                        >
                          <i class="bi bi-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                <?php endwhile; } 
                $stmt->close();
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- Book Appointment Modal -->
<div class="modal fade" id="bookAppointmentModal" tabindex="-1" aria-labelledby="bookAppointmentLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="bookAppointmentForm" class="modal-content">

      <input type="hidden" name="action" value="add">
      <input type="hidden" name="PatientID" value="<?= htmlspecialchars($PatientID) ?>">

      <div class="modal-header" style="background-color: #818CF8; color: white;">
        <h5 class="modal-title">Add Appointment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Department</label>
          <select id="DepID" name="DepID" class="form-select" required>
            <option value="">-- Select Department --</option>
            <?php $departments->data_seek(0); while ($dept = $departments->fetch_assoc()): ?>
              <option value="<?= htmlspecialchars($dept['DepID']) ?>"><?= htmlspecialchars($dept['DepName']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Doctor</label>
          <select id="DocID" name="DocID" class="form-select" required>
            <option value="">-- Select Doctor --</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Date</label>
          <input type="date" name="AppDate" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Time</label>
          <input type="time" name="StartTime" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Message</label>
          <textarea name="message" class="form-control"></textarea>
        </div>

        <input type="hidden" name="Price" value="0">
        <input type="hidden" name="Discount" value="0">
        <input type="hidden" name="Total" value="0">
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-indigo-400">Save</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Appointment Modal -->
<div class="modal fade" id="editAppointmentModal" tabindex="-1" aria-labelledby="editAppointmentLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="saveapp.php">
      <input type="hidden" name="action" value="update">
      <input type="hidden" name="AppID" id="editAppID">
      <input type="hidden" name="PatientID" id="editPatientID" value="<?= htmlspecialchars($PatientID) ?>">

      <div class="modal-header" style="background-color: #818CF8; color: white;">
        <h5 class="modal-title">Edit Appointment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Department</label>
          <select id="editDepID" name="DepID" class="form-select" required>
            <option value="">-- Select Department --</option>
            <?php
            $departments->data_seek(0);
            while ($dept = $departments->fetch_assoc()): ?>
              <option value="<?= htmlspecialchars($dept['DepID']) ?>"><?= htmlspecialchars($dept['DepName']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Doctor</label>
          <select id="editDocID" name="DocID" class="form-select" required>
            <option value="">-- Select Doctor --</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Date</label>
          <input type="date" id="editAppDate" name="AppDate" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Time</label>
          <input type="time" id="editStartTime" name="StartTime" class="form-control" required>
        </div>
        
        <div class="mb-3">
          <label class="form-label">Message</label>
          <textarea id="editMessage" name="message" class="form-control"></textarea>
        </div>

        <input type="hidden" name="Price" value="0">
        <input type="hidden" name="Discount" value="0">
        <input type="hidden" name="Total" value="0">
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-indigo-400">Update</button>
      </div>
    </form>
  </div>
</div>

<!-- Reports Modal -->
<div class="modal fade" id="reportsModal" tabindex="-1" aria-labelledby="reportsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #818CF8; color: white;">
        <h5 class="modal-title" id="reportsModalLabel">Your Medical Reports</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="reportsContent">
          <p>Loading reports...</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-indigo-400" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<?php require('common/footer.php')?>
<?php require('common/javascript.php')?>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap CSS -->
<link href="css/bootstrap.min.css" rel="stylesheet">

<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet"/>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    $('#appointmentsTable').DataTable({
      "pageLength": 10,
      "lengthMenu": [5, 10, 25, 50],
      "order": [[4, 'desc']],
      "language": {
        "search": "Search Appointments:",
        "lengthMenu": "Show _MENU_ entries",
        "info": "Showing _START_ to _END_ of _TOTAL_ appointments",
        "infoEmpty": "No appointments available",
        "infoFiltered": "(filtered from _MAX_ total appointments)",
        "zeroRecords": "No matching appointments found",
        "paginate": {
          "previous": "&laquo;",
          "next": "&raquo;"
        }
      }
    });
  });

  // Fill Edit modal with data from button
  $('#editAppointmentModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var modal = $(this);

    modal.find('#editAppointmentID').val(button.data('id'));
    modal.find('#editDepartment').val(button.data('department'));
    modal.find('#editDoctor').val(button.data('doctor'));
    modal.find('#editStatus').val(button.data('status'));
    modal.find('#editMessage').val(button.data('message'));
    modal.find('#editDate').val(button.data('date'));
    modal.find('#editTime').val(button.data('time'));
  });




  // Fill edit modal with clicked appointment data
  document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', () => {
      document.getElementById('editAppointmentID').value = button.getAttribute('data-id');
      document.getElementById('editDepartment').value = button.getAttribute('data-department');
      document.getElementById('editDoctor').value = button.getAttribute('data-doctor');
      document.getElementById('editStatus').value = button.getAttribute('data-status');
      document.getElementById('editMessage').value = button.getAttribute('data-message');
      document.getElementById('editDate').value = button.getAttribute('data-date');
      document.getElementById('editTime').value = button.getAttribute('data-time');
    });
  });


  // Your edit button click handler here, if you have one, to populate the Edit modal
// Book Appointment modal: load doctors dynamically when department changes
  // Book Appointment modal: load doctors dynamically when department changes
  document.getElementById('DepID').addEventListener('change', function () {
    const depID = this.value;
    const doctorSelect = document.getElementById('DocID');

    doctorSelect.innerHTML = '<option value="">Loading...</option>';

    if (depID) {
      fetch('getdocs.php?DepID=' + encodeURIComponent(depID))
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            doctorSelect.innerHTML = '<option value="">-- Select Doctor --</option>';
            data.doctors.forEach(doctor => {
              const option = document.createElement('option');
              option.value = doctor.DocID;
              option.textContent = doctor.Name;
              doctorSelect.appendChild(option);
            });
          } else {
            doctorSelect.innerHTML = '<option value="">No doctors available</option>';
          }
        })
        .catch(() => {
          doctorSelect.innerHTML = '<option value="">-- Select Doctor --</option>';
        });
    } else {
      doctorSelect.innerHTML = '<option value="">-- Select Doctor --</option>';
    }
  });

  // Edit Appointment modal: open modal and populate fields
  document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', () => {
      const appID = button.dataset.appid;
      const depID = button.dataset.depid;
      const docID = button.dataset.docid;
      const appDate = button.dataset.appdate;
      const startTime = button.dataset.starttime;
      const status = button.dataset.status;
      const message = button.dataset.message;

      // Set values in the modal
      document.getElementById('editAppID').value = appID;
      document.getElementById('editDepID').value = depID;
      document.getElementById('editAppDate').value = appDate;
      document.getElementById('editStartTime').value = startTime;
      document.getElementById('editMessage').value = message;

      // Set PatientID hidden input value
      document.getElementById('editPatientID').value = '<?= htmlspecialchars($PatientID) ?>';

      // Load doctors dynamically for edit modal department
      const doctorSelect = document.getElementById('editDocID');
      doctorSelect.innerHTML = '<option value="">Loading...</option>';

      if (depID) {
        fetch('getdocs.php?DepID=' + encodeURIComponent(depID))
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              doctorSelect.innerHTML = '<option value="">-- Select Doctor --</option>';
              data.doctors.forEach(doc => {
                const opt = document.createElement('option');
                opt.value = doc.DocID;
                opt.textContent = doc.Name;
                if (doc.DocID == docID) opt.selected = true;
                doctorSelect.appendChild(opt);
              });
            } else {
              doctorSelect.innerHTML = '<option value="">No doctors available</option>';
            }
          })
          .catch(() => {
            doctorSelect.innerHTML = '<option value="">-- Select Doctor --</option>';
          });
      } else {
        doctorSelect.innerHTML = '<option value="">-- Select Doctor --</option>';
      }

      // Show the modal
      const editModal = new bootstrap.Modal(document.getElementById('editAppointmentModal'));
      editModal.show();
    });
  });

  // Edit modal: update doctors when department changes
  document.getElementById('editDepID').addEventListener('change', function () {
    const depID = this.value;
    const doctorSelect = document.getElementById('editDocID');

    doctorSelect.innerHTML = '<option value="">Loading...</option>';

    if (depID) {
      fetch('getdocs.php?DepID=' + encodeURIComponent(depID))
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            doctorSelect.innerHTML = '<option value="">-- Select Doctor --</option>';
            data.doctors.forEach(doc => {
              const opt = document.createElement('option');
              opt.value = doc.DocID;
              opt.textContent = doc.Name;
              doctorSelect.appendChild(opt);
            });
          } else {
            doctorSelect.innerHTML = '<option value="">No doctors available</option>';
          }
        })
        .catch(() => {
          doctorSelect.innerHTML = '<option value="">-- Select Doctor --</option>';
        });
    } else {
      doctorSelect.innerHTML = '<option value="">-- Select Doctor --</option>';
    }
  });

  // Delete confirmation with SweetAlert2
  document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', e => {
      e.preventDefault();
      const appID = btn.getAttribute('data-appid');

      Swal.fire({
        title: 'Are you sure?',
        text: "This appointment will be deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = 'deleteapppatient.php?AppID=' + appID;
        }
      });
    });
  });

  // Show success alerts based on URL params with OK button
  document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    
    if (params.has('status') && params.get('status') === 'success' && params.has('action')) {
      let action = params.get('action');
      let title = 'Success!';
      let text = '';

      switch (action) {
        case 'add':
          text = 'Appointment added successfully.';
          break;
        case 'update':
          text = 'Appointment updated successfully.';
          break;
        case 'delete':
          text = 'Appointment deleted successfully.';
          break;
        default:
          text = 'Action completed successfully.';
      }

      Swal.fire({
        icon: 'success',
        title: title,
        text: text,
        confirmButtonText: 'OK',
        allowOutsideClick: false,
        allowEscapeKey: false,
      });
    }
  });

  // Reports modal: fetch reports data when modal is opened
  document.getElementById('reportsModal').addEventListener('show.bs.modal', function () {
    const container = document.getElementById('reportsContent');
    container.innerHTML = '<p>Loading reports...</p>';

    fetch('fetchpatientreports.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ PatientID: <?= json_encode($PatientID) ?> })
    })
    .then(response => response.json())
    .then(data => {
      if (!data || data.length === 0) {
        container.innerHTML = '<p class="text-muted">No reports found.</p>';
        return;
      }
      let html = '<div class="list-group">';
data.forEach(report => {
  html += `
    <div class="list-group-item mb-3 border rounded">
      
      <p><strong>Appointment Date:</strong> ${report.AppDate}</p>
      <p><strong>Doctor:</strong> ${report.DoctorName}</p>
      <p><strong>Department:</strong> ${report.Department}</p>
      <p><strong>Assessment:</strong> ${report.Assessment}</p>
      <p><strong>Diagnosis:</strong> ${report.Diagnosis}</p>
      <p><strong>Prescription:</strong> ${report.Prescription}</p>
    </div>
  `;
});
html += '</div>';
document.getElementById('reportsContent').innerHTML = html;

    })
    .catch(err => {
      container.innerHTML = '<p class="text-danger">Failed to load reports. Please try again later.</p>';
      console.error(err);
    });
  });
  // Intercept Edit Appointment form submit via AJAX
  document.querySelector('#editAppointmentModal form').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    fetch('saveapp.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.status === 'success') {
        Swal.fire({
          icon: 'success',
          title: 'Updated!',
          text: data.message,
          confirmButtonText: 'OK'
        }).then(() => {
          // Hide the modal
          const modal = bootstrap.Modal.getInstance(document.getElementById('editAppointmentModal'));
          modal.hide();

          // Optionally reload the page or update table here
          location.reload(); // OR manually update the table row
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: data.message || 'Something went wrong.'
        });
      }
    })
    .catch(error => {
      console.error('AJAX error:', error);
      Swal.fire({
        icon: 'error',
        title: 'AJAX Error',
        text: 'Could not update appointment.'
      });
    });
  });
// Intercept Book Appointment form submit via AJAX
document.querySelector('#bookAppointmentForm').addEventListener('submit', function (e) {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);

  fetch('saveapp.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.status === 'success') {
      Swal.fire({
        icon: 'success',
        title: 'Appointment Booked!',
        text: data.message || 'Your appointment has been added successfully.',
        confirmButtonColor: '#6366F1'
      }).then(() => {
        // Close modal, reset form, reload
        const modal = bootstrap.Modal.getInstance(document.getElementById('bookAppointmentModal'));
        modal.hide();
        form.reset();
        location.reload(); // OR dynamically update the appointments table
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: data.message || 'Something went wrong.'
      });
    }
  })
  .catch(error => {
    console.error('AJAX error:', error);
    Swal.fire({
      icon: 'error',
      title: 'AJAX Error',
      text: 'Could not book appointment.'
    });
  });
});

</script>

</body>
</html>
