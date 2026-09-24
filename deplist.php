<?php
require_once 'dp.php';

// Fetch departments
$query = "SELECT * FROM department";
$result = mysqli_query($conn, $query);
?>
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
  #basicExample tbody tr:hover {
    cursor: pointer;
  }
  .action-buttons {
    display: flex;
    gap: 8px;
    justify-content: center;
  }
  .department-image {
    height: 50px;
    width: auto;
    object-fit: contain;
    border-radius: 5px;
  }
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

  /* Align 'Show entries' dropdown to the left */
  .dataTables_length {
    float: left !important;
    text-align: left !important;
  }

  /* Keep search box on the right */
  .dataTables_filter {
    float: right !important;
    text-align: right !important;
  }

  /* Clear floats after table controls */
  .dataTables_wrapper .row {
    clear: both;
  }
</style>

</head>
<body>
<?php require('common/sidebar.php') ?>

<div class="content">
<?php require('common/navbar.php') ?>
  <!-- Breadcrumb Navigation -->
<div class="container-fluid py-2 px-3">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-2">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
    </ol>
  </nav>
</div>

<div class="app-body">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title">Department List</h5>
        <button class="btn btn-indigo-400 ms-auto" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
          <i class="bi bi-plus-circle me-1"></i> Add Department
        </button>
      </div>

      <div class="card-body">
        <div class="bg-light rounded p-4">
          <div class="table-responsive">
            <table id="basicExample" class="table table-hover table-bordered table-striped align-middle m-0">
              <thead class="text-center" style="background-color: #818cf8; color: white;">
                <tr>
                  <th>Image</th>
                  <th>Department</th>
                  <th>Description</th>
                  <th class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                  <?php while ($row = mysqli_fetch_assoc($result)): ?>
                  <tr class="text-center">
                    <td>
                      <?php if (!empty($row['Image'])): ?>
                        <img src="uploads/departments/<?= htmlspecialchars($row['Image']) ?>" alt="Department Image" class="department-image" loading="lazy">
                      <?php else: ?>
                        <span class="text-muted">No Image</span>
                      <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['DepName']) ?></td>
                    <td class="text-start"><?= htmlspecialchars($row['Description']) ?></td>
                    <td>
                      <div class="action-buttons">
                        <button class="btn btn-sm btn-success edit-btn"
                          data-id="<?= $row['DepID'] ?>"
                          data-name="<?= htmlspecialchars($row['DepName'], ENT_QUOTES) ?>"
                          data-desc="<?= htmlspecialchars($row['Description'], ENT_QUOTES) ?>"
                          data-image="<?= htmlspecialchars($row['Image'], ENT_QUOTES) ?>"
                          data-bs-toggle="modal"
                          data-bs-target="#editDepartmentModal"
                          title="Edit"
                        >
                          <i class="bi bi-pencil"></i>
                        </button>
                        <a href="deletedepartment.php?id=<?= $row['DepID'] ?>" 
                           class="btn btn-sm btn-danger delete-link" 
                           title="Delete"
                        >
                          <i class="bi bi-trash"></i>
                        </a>
                      </div>
                    </td>
                  </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="4" class="text-center text-muted">No departments found.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Add Department Modal -->
        <div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-labelledby="addDepartmentModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-md modal-dialog-centered">
            <form action="adddepbutton.php" method="POST" enctype="multipart/form-data" class="modal-content">
              <input type="hidden" name="action" value="add">
              <div class="modal-header" style="background-color: #818CF8; color: white;">
                <h5 class="modal-title" id="addDepartmentModalLabel">Add Department</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label for="DepName" class="form-label">Department Name</label>
                  <input type="text" class="form-control" id="DepName" name="DepName" required>
                </div>
                <div class="mb-3">
                  <label for="Description" class="form-label">Description</label>
                  <textarea class="form-control" id="Description" name="Description" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                  <label for="DepImage" class="form-label">Image</label>
                  <input type="file" class="form-control" id="DepImage" name="DepImage" accept="image/*" required>
                </div>
              </div>
              <div class="modal-footer">
                <button class="btn btn-danger" data-bs-dismiss="modal" type="button">Cancel</button>
                <button class="btn btn-indigo-400" type="submit">Save</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Edit Department Modal -->
        <div class="modal fade" id="editDepartmentModal" tabindex="-1" aria-labelledby="editDepartmentModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-md modal-dialog-centered">
            <form action="adddepbutton.php" method="POST" enctype="multipart/form-data" class="modal-content">
              <input type="hidden" name="action" value="edit">
              <input type="hidden" name="DepID" id="editDepID">
              <div class="modal-header" style="background-color: #818CF8; color: white;">
                <h5 class="modal-title" id="editDepartmentModalLabel">Edit Department</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label for="editDepName" class="form-label">Department Name</label>
                  <input type="text" class="form-control" id="editDepName" name="DepName" required>
                </div>
                <div class="mb-3">
                  <label for="editDescription" class="form-label">Description</label>
                  <textarea class="form-control" id="editDescription" name="Description" rows="3" required></textarea>
                </div>
                <div class="mb-3 text-center">
                  <label class="form-label d-block">Current Image</label>
                  <img id="currentDepImage" src="" alt="Current Department Image" 
                       style="max-height: 220px; max-width: 100%; object-fit: contain; display:none; margin: 0 auto;">
                </div>
                <div class="mb-3">
                  <label for="editDepImage" class="form-label">Change Image</label>
                  <input type="file" class="form-control" id="editDepImage" name="DepImage" accept="image/*">
                  <small class="text-muted">Leave blank to keep current image.</small>
                </div>
              </div>
              <div class="modal-footer">
                <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-indigo-400">Update</button>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<?php require('common/footer.php') ?>
</div>

<?php require('common/javascript.php') ?>

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
  $('#basicExample').DataTable({
    // Optional: you can customize DataTables options here
    "pageLength": 10,
    "lengthMenu": [5, 10, 25, 50],
    "order": [],
    "language": {
      "search": "Search Departments:",
      "lengthMenu": "Show _MENU_ entries",
      "info": "Showing _START_ to _END_ of _TOTAL_ departments",
      "infoEmpty": "No departments available",
      "infoFiltered": "(filtered from _MAX_ total departments)",
      "zeroRecords": "No matching departments found",
      "paginate": {
        "previous": "&laquo;",
        "next": "&raquo;"
      }
    }
  });

  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.forEach(function (tooltipTriggerEl) {
    new bootstrap.Tooltip(tooltipTriggerEl);
  });

  document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', () => {
      const id = button.getAttribute('data-id');
      const name = button.getAttribute('data-name');
      const desc = button.getAttribute('data-desc');
      const image = button.getAttribute('data-image');

      document.getElementById('editDepID').value = id;
      document.getElementById('editDepName').value = name;
      document.getElementById('editDescription').value = desc;

      const imgElem = document.getElementById('currentDepImage');
      if (image) {
        imgElem.src = 'uploads/departments/' + image;
        imgElem.style.display = 'block';
      } else {
        imgElem.style.display = 'none';
        imgElem.src = '';
      }
    });
  });

  // SweetAlert delete confirmation
  document.querySelectorAll('.delete-link').forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      const href = this.getAttribute('href');

      Swal.fire({
        title: 'Are you sure?',
        text: "This department will be deleted!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete!',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = href;
        }
      });
    });
  });

  // SweetAlert for Add/Edit Success
  const urlParams = new URLSearchParams(window.location.search);
  const status = urlParams.get('status');
  if (status === 'added') {
    Swal.fire({
      icon: 'success',
      title: 'Department Added',
      text: 'The department has been successfully added!',
      confirmButtonColor: '#556ee6'
    });
  }
  if (status === 'edited') {
    Swal.fire({
      icon: 'success',
      title: 'Department Updated',
      text: 'The department has been successfully updated!',
      confirmButtonColor: '#556ee6'
    });
  }

  // Clean URL after alerts
  if (status === 'added' || status === 'edited') {
    if (window.history.replaceState) {
      const cleanUrl = window.location.origin + window.location.pathname;
      window.history.replaceState(null, null, cleanUrl);
    }
  }
});
</script>

<script>
  const params = new URLSearchParams(window.location.search);

  if (params.has('success')) {
    Swal.fire({
      icon: 'success',
      title: 'Department Added',
      text: 'The department was added successfully!'
    });
  }

  if (params.has('updated')) {
    Swal.fire({
      icon: 'success',
      title: 'Department Updated',
      text: 'The department was updated successfully!'
    });
  }

  if (params.has('deleted')) {
    Swal.fire({
      icon: 'success',
      title: 'Deleted!',
      text: 'Department was deleted successfully.',
      confirmButtonColor: '#3085d6'
    });
  }

  if (params.has('error')) {
    Swal.fire({
      icon: 'error',
      title: 'Oops!',
      text: 'Something went wrong. Please try again.',
      confirmButtonColor: '#d33'
    });
  }
</script>

</body>
</html>
