<?php
include 'dp.php'; // DB connection

// Fetch departments for dropdown
$departmentsQuery = "SELECT DepID, DepName FROM department";
$departmentsResult = $conn->query($departmentsQuery);

// Fetch doctors list with department name
$doctorsQuery = "
   SELECT doctor.*, department.DepName 
   FROM doctor 
   INNER JOIN department ON doctor.DepID = department.DepID
";
$result = $conn->query($doctorsQuery);
?>
<?php
  session_start(); // make sure this is at the top
   'Role: ' . ($_SESSION['role'] ?? 'Not Set');
?>
<?php require('common/head.php'); ?>

<style>
  .dataTables_wrapper .dataTables_paginate .paginate_button.page-item.active > a.page-link {
  background-color: #818CF8 !important;
  border-color: #818CF8 !important;
  color: white !important;
  box-shadow: none !important;
}
  #doctorTable tbody tr:hover { cursor: pointer; }
  .action-buttons { display: flex; gap: 8px; justify-content: center; }
  .doctor-pic { height: 50px; width: 50px; object-fit: cover; border-radius: 5px; }
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
  .modal-body label.form-label {
    text-align: left;
    display: block;
  }
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
<?php require('common/sidebar.php'); ?>
<div class="content">
  <?php require('common/navbar.php'); ?>

  <div class="container-fluid py-2 px-3">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Doctors</li>
      </ol>
    </nav>
  </div>

  <div class="app-body">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title">Doctors List</h5>
          <button class="btn btn-indigo-400 ms-auto" data-bs-toggle="modal" data-bs-target="#addDoctorModal">
            <i class="bi bi-plus-circle me-1"></i> Add Doctor
          </button>
        </div>
        <div class="card-body">
          <div class="bg-light rounded p-4 border-bottom">
            <div class="table-responsive">
              <table id="doctorTable" class="table table-hover table-bordered table-striped align-middle m-0">
                <thead class="text-center" style="background-color: #818cf8; color:white;">
                  <tr>
                    <th>Picture</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>DOB</th>
                    <th class="text-center">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                      <tr class="text-center">
                        <td>
                          <?php if (!empty($row['ProfilePic'])): ?>
                            <img src="uploads/<?= htmlspecialchars($row['ProfilePic']) ?>" alt="Profile" class="doctor-pic" loading="lazy">
                          <?php else: ?>
                            <span class="text-muted">N/A</span>
                          <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['Name']) ?></td>
                        <td><?= htmlspecialchars($row['DepName']) ?></td>
                        <td><?= htmlspecialchars($row['DOB']) ?></td>
                        <td>
                          <div class="action-buttons">
                            <button class="btn btn-sm btn-success edit-btn" 
                              data-id="<?= $row['DocID'] ?>"
                              data-name="<?= htmlspecialchars($row['Name'], ENT_QUOTES) ?>"
                              data-dob="<?= $row['DOB'] ?>"
                              data-depid="<?= $row['DepID'] ?>"
                              data-pic="<?= htmlspecialchars($row['ProfilePic'], ENT_QUOTES) ?>"
                              data-bs-toggle="modal"
                              data-bs-target="#editDoctorModal">
                              <i class="bi bi-pencil"></i>
                            </button>
                            <a href="deletedoctor.php?id=<?= $row['DocID'] ?>" class="btn btn-sm btn-danger delete-link">
                              <i class="bi bi-trash"></i>
                            </a>
                          </div>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr><td colspan="5" class="text-center text-muted">No doctors found.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Doctor Modal -->
    <div class="modal fade" id="addDoctorModal" tabindex="-1">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content" action="adddocbutton.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="action" value="add">
          <div class="modal-header" style="background-color: #818CF8; color: white;">
            <h5 class="modal-title">Add Doctor</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Full Name</label>
              <input type="text" name="Name" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Date of Birth</label>
              <input type="date" name="DOB" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Profile Picture</label>
              <input type="file" name="ProfilePic" class="form-control" accept=".jpg,.jpeg,.png">
            </div>
            <div class="mb-3">
              <label class="form-label">Department</label>
              <select name="DepID" class="form-select" required>
                <option value="">-- Select Department --</option>
                <?php
                  $departmentsResult->data_seek(0);
                  while ($dept = $departmentsResult->fetch_assoc()) {
                    echo '<option value="' . $dept['DepID'] . '">' . htmlspecialchars($dept['DepName']) . '</option>';
                  }
                ?>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-indigo-400">Save</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Edit Doctor Modal -->
    <div class="modal fade" id="editDoctorModal" tabindex="-1">
      <div class="modal-dialog modal-md modal-dialog-centered">
        <form class="modal-content" action="adddocbutton.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="action" value="edit">
          <input type="hidden" name="DocID" id="editDocID">
          <div class="modal-header" style="background-color: #818CF8; color: white;">
            <h5 class="modal-title">Edit Doctor</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Full Name</label>
              <input type="text" name="Name" id="editName" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Date of Birth</label>
              <input type="date" name="DOB" id="editDOB" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Department</label>
              <select name="DepID" id="editDepID" class="form-select" required>
                <option value="">-- Select Department --</option>
                <?php
                  $departmentsResult->data_seek(0);
                  while ($dept = $departmentsResult->fetch_assoc()) {
                    echo '<option value="' . $dept['DepID'] . '">' . htmlspecialchars($dept['DepName']) . '</option>';
                  }
                ?>
              </select>
            </div>
            <div class="mb-3 text-center">
              <label class="form-label d-block">Current Picture</label>
              <img id="editPicPreview" src="#" alt="Current Picture" style="width:120px;height:120px;object-fit:cover;border-radius:8px;" class="mb-3 mx-auto d-block">
              <input type="file" name="ProfilePic" class="form-control mt-2" style="max-width:300px;">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-indigo-400">Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>

<?php require('common/footer.php'); ?>
<?php require('common/javascript.php'); ?>

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
    $('#doctorTable').DataTable({
      pageLength: 10,
      lengthMenu: [5, 10, 25, 50],
      order: [],
      language: {
        search: "Search:",
        lengthMenu: "Show _MENU_ entries",
        info: "Showing _START_ to _END_ of _TOTAL_ doctors",
        infoEmpty: "No doctors available",
        infoFiltered: "(filtered from _MAX_ total doctors)",
        zeroRecords: "No matching records found",
        paginate: {
          previous: "&laquo;",
          next: "&raquo;"
        }
      }
    });

    $('.edit-btn').on('click', function () {
      $('#editDocID').val($(this).data('id'));
      $('#editName').val($(this).data('name'));
      $('#editDOB').val($(this).data('dob'));
      $('#editDepID').val($(this).data('depid'));
      const pic = $(this).data('pic');
      $('#editPicPreview').attr('src', pic ? 'uploads/' + pic : '').toggle(!!pic);
    });

    $('.delete-link').on('click', function (e) {
      e.preventDefault();
      const href = $(this).attr('href');
      Swal.fire({
        title: 'Are you sure?',
        text: 'This doctor will be deleted!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete!',
        reverseButtons: true
      }).then(result => {
        if (result.isConfirmed) window.location.href = href;
      });
    });

    const params = new URLSearchParams(window.location.search);
    if (params.has('success')) Swal.fire({ icon: 'success', title: 'Doctor Added', text: 'The doctor was added successfully!' });
    if (params.has('updated')) Swal.fire({ icon: 'success', title: 'Doctor Updated', text: 'The doctor was updated successfully!' });
    if (params.has('deleted')) Swal.fire({ icon: 'success', title: 'Deleted!', text: 'Doctor was deleted successfully.', confirmButtonColor: '#3085d6' });
    if (params.has('error')) Swal.fire({ icon: 'error', title: 'Oops!', text: 'Something went wrong. Please try again.', confirmButtonColor: '#d33' });
  });
</script>

</div>
</body>
</html>
