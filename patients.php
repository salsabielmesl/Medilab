
<?php
  session_start(); // make sure this is at the top
   'Role: ' . ($_SESSION['role'] ?? 'Not Set');
?>
<?php require('common/head.php') ?>
<style>
  .dataTables_wrapper .dataTables_paginate .paginate_button.page-item.active > a.page-link {
  background-color: #818CF8 !important;
  border-color: #818CF8 !important;
  color: white !important;
  box-shadow: none !important;
}
  #patientTable tbody tr:hover { cursor: pointer; }

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

  thead.table-indigo th {
    background-color: #818cf8 !important;
    color: white !important;
  }

  .modal-body label.form-label {
    text-align: left;
    display: block;
  }

  .action-buttons {
    display: flex;
    gap: 8px;
    justify-content: center;
  }

  /* DataTables controls alignment */
  .dataTables_length {
    float: left !important;
    text-align: left !important;
  }
  .dataTables_filter {
    float: right !important;
    text-align: right !important;
  }
  .dataTables_wrapper .row {
    clear: both;
  }
</style>

<body>
<?php require('common/sidebar.php') ?>
<div class="content">
  <?php require('common/navbar.php') ?>

  <div class="app-body">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title m-0">Patient List</h5>
          <button class="btn btn-indigo-400" data-bs-toggle="modal" data-bs-target="#addPatientModal">
            <i class="bi bi-plus-circle me-1"></i> Add Patient
          </button>
        </div>
        <div class="card-body">
          <div class="bg-light rounded p-4 border-bottom">
            <div class="table-responsive">
              <table id="patientTable" class="table table-hover table-bordered table-striped align-middle m-0">
                <thead class="table-indigo text-center">
                  <tr>
                    <th>Patient Name</th>
                    <th>Gender</th>
                    <th>Date of Birth</th>
                    <th>Phone Number</th>
                    <th class="text-center">Actions</th>
                  </tr>
                </thead>
                <tbody>
  <?php
  include 'dp.php';
  $query = $conn->query("SELECT * FROM patient ORDER BY FullName ASC");
  if (!$query) {
    echo "<tr><td colspan='5' class='text-center text-danger'>Error: " . $conn->error . "</td></tr>";
  } else {
    while ($row = $query->fetch_assoc()):
  ?>
    <tr class="text-center">
      <td><?= htmlspecialchars($row['FullName'] ?? '') ?></td>
      <td><?= htmlspecialchars($row['Gender'] ?? '') ?></td>
      <td><?= htmlspecialchars($row['DOB'] ?? '') ?></td>
      <td><?= htmlspecialchars($row['Phone'] ?? '') ?></td>
      <td>
        <div class="action-buttons">
          <button 
            class="btn btn-sm btn-success edit-btn" 
            data-id="<?= $row['PatientID'] ?>"
            data-name="<?= htmlspecialchars($row['FullName'] ?? '') ?>"
            data-gender="<?= $row['Gender'] ?? '' ?>"
            data-dob="<?= $row['DOB'] ?? '' ?>"
            data-phone="<?= htmlspecialchars($row['Phone'] ?? '') ?>"
            data-bs-toggle="modal" 
            data-bs-target="#editPatientModal"
            title="Edit Patient"
          >
            <i class="bi bi-pencil"></i>
          </button>
          <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $row['PatientID'] ?>" title="Delete Patient">
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </td>
    </tr>
  <?php endwhile; } ?>
</tbody>

              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add Patient Modal -->
  <div class="modal fade" id="addPatientModal" tabindex="-1" aria-labelledby="addPatientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form action="addpatientbutton.php" method="POST">
        <div class="modal-content">
          <div class="modal-header" style="background-color: #818CF8; color: white;">
            <h5 class="modal-title">Add Patient</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Patient Name</label>
              <input type="text" class="form-control" name="FullName" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Gender</label><br>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="Gender" value="Male" required>
                <label class="form-check-label">Male</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="Gender" value="Female" required>
                <label class="form-check-label">Female</label>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Date of Birth</label>
              <input type="date" class="form-control" name="DOB" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Phone Number</label>
              <input type="tel" class="form-control" name="Phone" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-indigo-400">Save</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Patient Modal -->
  <div class="modal fade" id="editPatientModal" tabindex="-1" aria-labelledby="editPatientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <form action="addpatientbutton.php" method="POST">
        <div class="modal-content">
          <div class="modal-header" style="background-color: #818CF8; color: white;">
            <h5 class="modal-title">Edit Patient</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="PatientID" id="editPatientID">
            <div class="mb-3">
              <label class="form-label">Patient Name</label>
              <input type="text" class="form-control" name="FullName" id="editFullName" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Gender</label><br>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="Gender" id="editGenderMale" value="Male" required>
                <label class="form-check-label" for="editGenderMale">Male</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="Gender" id="editGenderFemale" value="Female" required>
                <label class="form-check-label" for="editGenderFemale">Female</label>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Date of Birth</label>
              <input type="date" class="form-control" name="DOB" id="editDOB" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Phone Number</label>
              <input type="tel" class="form-control" name="Phone" id="editPhone" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-indigo-400">Update</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <?php require('common/footer.php') ?>
  <?php require('common/javascript.php') ?>

  <?php if (isset($_GET['success'])): ?>
  <script>
    Swal.fire({
      icon: 'success',
      title: 'Patient Added',
      text: 'The patient was added successfully!',
      confirmButtonColor: '#3085d6'
    });
  </script>
  <?php elseif (isset($_GET['updated'])): ?>
  <script>
    Swal.fire({
      icon: 'success',
      title: 'Patient Updated',
      text: 'The patient information has been updated!',
      confirmButtonColor: '#28a745'
    });
  </script>
  <?php elseif (isset($_GET['deleted'])): ?>
  <script>
    Swal.fire({
      icon: 'success',
      title: 'Deleted!',
      text: 'Patient was deleted successfully.',
      confirmButtonColor: '#3085d6'
    });
  </script>
  <?php elseif (isset($_GET['error'])): ?>
  <script>
    Swal.fire({
      icon: 'error',
      title: 'Oops!',
      text: 'Something went wrong. Please try again.',
      confirmButtonColor: '#d33'
    });
  </script>
  <?php endif; ?>

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
    $(document).ready(function () {
      $('#patientTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [5, 10, 25, 50],
        "order": [],
        "language": {
          "search": "Search Patients:",
          "lengthMenu": "Show _MENU_ entries",
          "info": "Showing _START_ to _END_ of _TOTAL_ patients",
          "infoEmpty": "No patients available",
          "infoFiltered": "(filtered from _MAX_ total patients)",
          "zeroRecords": "No matching patients found",
          "paginate": {
            "previous": "&laquo;",
            "next": "&raquo;"
          }
        }
      });

      // Edit button fills modal fields
      $('.edit-btn').on('click', function () {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const gender = $(this).data('gender');
        const dob = $(this).data('dob');
        const phone = $(this).data('phone');

        $('#editPatientID').val(id);
        $('#editFullName').val(name);
        $('#editDOB').val(dob);
        $('#editPhone').val(phone);

        if (gender === 'Male') {
          $('#editGenderMale').prop('checked', true);
        } else if (gender === 'Female') {
          $('#editGenderFemale').prop('checked', true);
        }
      });

      // Delete confirmation
      $('.delete-btn').on('click', function (e) {
        e.preventDefault();
        const id = $(this).data('id');

        Swal.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = 'deletepatient.php?id=' + id;
          }
        });
      });
    });
  </script>
</div>
</body>
</html>
