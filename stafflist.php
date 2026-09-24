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
  /* Hover effect */
  #receptionistTable tbody tr:hover { cursor: pointer; }

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

  /* Clear floats after the table controls */
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
        <h5 class="card-title">Receptionist List</h5>
        <button class="btn btn-indigo-400 ms-auto" data-bs-toggle="modal" data-bs-target="#addReceptionistModal">
          <i class="bi bi-plus-circle me-1"></i> Add Receptionist
        </button>
      </div>
      <div class="card-body">
        <div class="bg-light rounded p-4">
          <div class="table-responsive">
            <table id="receptionistTable" class="table table-hover table-bordered table-striped align-middle m-0">
              <thead class="text-center" style="background-color: #818cf8; color: white;">
                <tr>
                  <th>Name</th>
                  <th>Phone</th>
                  <th>DOB</th>
                  <th class="text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                require_once 'dp.php';
                $query = "SELECT * FROM receptionist";
                $result = mysqli_query($conn, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                  while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr class='text-center'>";
                    echo "<td>" . htmlspecialchars($row['FullName']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['PhoneNum']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['DOB']) . "</td>";
                    echo "<td>";
                    echo "<div class='action-buttons'>";
                    echo "<button 
                            class='btn btn-sm btn-success edit-btn' 
                            data-id='" . $row['RecepID'] . "'
                            data-name='" . htmlspecialchars($row['FullName'], ENT_QUOTES) . "'
                            data-phone='" . htmlspecialchars($row['PhoneNum'], ENT_QUOTES) . "'
                            data-dob='" . $row['DOB'] . "'
                            data-bs-toggle='modal'
                            data-bs-target='#editReceptionistModal'
                            title='Edit'>
                            <i class='bi bi-pencil'></i>
                          </button>";
                    echo "<a href='deletestaff.php?id=" . $row['RecepID'] . "' 
                             class='btn btn-sm btn-danger delete-link' 
                             title='Delete'>
                            <i class='bi bi-trash'></i>
                          </a>";
                    echo "</div>";
                    echo "</td>";
                    echo "</tr>";
                  }
                } else {
                  echo "<tr><td colspan='4' class='text-center text-muted'>No receptionists found.</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add Receptionist Modal -->
  <div class="modal fade" id="addReceptionistModal" tabindex="-1" aria-labelledby="addReceptionistLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
      <form class="modal-content" action="addreceptionistbutton.php" method="POST">
        <input type="hidden" name="action" value="add">
        <div class="modal-header" style="background-color: #818CF8; color: white;">
          <h5 class="modal-title" id="addReceptionistLabel">Add Receptionist</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label for="FullName" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="FullName" name="FullName" required>
          </div>
          <div class="mb-3">
            <label for="Phone" class="form-label">Phone Number</label>
            <input type="text" class="form-control" id="Phone" name="PhoneNum" required>
          </div>
          <div class="mb-3">
            <label for="DOB" class="form-label">Date of Birth</label>
            <input type="date" class="form-control" id="DOB" name="DOB" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-indigo-400">Save</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Receptionist Modal -->
  <div class="modal fade" id="editReceptionistModal" tabindex="-1" aria-labelledby="editReceptionistLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
      <form class="modal-content" action="addreceptionistbutton.php" method="POST">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="RecepID" id="editRecepID">
        <div class="modal-header" style="background-color: #818CF8; color: white;">
          <h5 class="modal-title" id="editReceptionistLabel">Edit Receptionist</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label for="editFullName" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="editFullName" name="FullName" required>
          </div>
          <div class="mb-3">
            <label for="editPhone" class="form-label">Phone Number</label>
            <input type="text" class="form-control" id="editPhone" name="PhoneNum" required>
          </div>
          <div class="mb-3">
            <label for="editDOB" class="form-label">Date of Birth</label>
            <input type="date" class="form-control" id="editDOB" name="DOB" required>
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

<?php require('common/footer.php') ?>
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
  $(document).ready(function () {
    $('#receptionistTable').DataTable({
      "pageLength": 10,
      "lengthMenu": [5, 10, 25, 50],
      "order": [],
      "language": {
        "search": "Search Receptionists:",
        "lengthMenu": "Show _MENU_ entries",
        "info": "Showing _START_ to _END_ of _TOTAL_ receptionists",
        "infoEmpty": "No receptionists available",
        "infoFiltered": "(filtered from _MAX_ total receptionists)",
        "zeroRecords": "No matching receptionists found",
        "paginate": {
          "previous": "&laquo;",
          "next": "&raquo;"
        }
      }
    });
  });
</script>

<script>
  document.querySelectorAll('.edit-btn').forEach(button => {
    button.addEventListener('click', () => {
      document.getElementById('editRecepID').value = button.getAttribute('data-id');
      document.getElementById('editFullName').value = button.getAttribute('data-name');
      document.getElementById('editPhone').value = button.getAttribute('data-phone');
      document.getElementById('editDOB').value = button.getAttribute('data-dob');
    });
  });

  document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);

    if (params.has('success')) {
      Swal.fire({
        icon: 'success',
        title: 'Receptionist Added',
        text: 'The receptionist was added successfully!',
        confirmButtonText: 'OK'
      });
    }

    if (params.has('updated')) {
      Swal.fire({
        icon: 'success',
        title: 'Receptionist Updated',
        text: 'The receptionist was updated successfully!',
        confirmButtonText: 'OK'
      });
    }

    document.querySelectorAll('.delete-link').forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        const href = this.getAttribute('href');
        Swal.fire({
          title: 'Are you sure?',
          text: "This receptionist will be deleted!",
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
  });
</script>

<script>
  const params = new URLSearchParams(window.location.search);

  if (params.has('success')) {
    Swal.fire({
      icon: 'success',
      title: 'Receptionist Added',
      text: 'The Receptionist was added successfully!'
    });
  }

  if (params.has('updated')) {
    Swal.fire({
      icon: 'success',
      title: 'Receptionist Updated',
      text: 'The Receptionist was updated successfully!'
    });
  }

  if (params.has('deleted')) {
    Swal.fire({
      icon: 'success',
      title: 'Deleted!',
      text: 'Receptionist was deleted successfully.',
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
