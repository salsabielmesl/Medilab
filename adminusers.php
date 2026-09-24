<?php
include 'dp.php'; // DB connection
session_start(); // must be before any output

// Handle edit form submission (update user)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editUser'])) {
    $id = intval($_POST['id'] ?? 0);
    $username = trim($_POST['username'] ?? '');
    $role = trim($_POST['role'] ?? '');

    if ($id > 0 && $username !== '' && $role !== '') {
        $stmt = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
        $stmt->bind_param("ssi", $username, $role, $id);
        if ($stmt->execute()) {
            // Redirect with success flag
            header("Location: adminusers.php?updated=1");
            exit;
        } else {
            // Redirect with error flag
            header("Location: adminusers.php?error=1");
            exit;
        }
    } else {
        header("Location: adminusers.php?error=1");
        exit;
    }
}

// Fetch admin users AFTER handling POST
$usersQuery = "SELECT * FROM users";
$result = $conn->query($usersQuery);
?>
<?php require('common/head.php') ?>

<style>
    .dataTables_wrapper .dataTables_paginate .paginate_button.page-item.active > a.page-link {
  background-color: #818CF8 !important;
  border-color: #818CF8 !important;
  color: white !important;
  box-shadow: none !important;
}
  /* (Your existing styles here, unchanged) */
  #basicExample tbody tr:hover { cursor: pointer; }
  .action-buttons { display: flex; gap: 8px; justify-content: center; }
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
  .dataTables_length { float: left !important; text-align: left !important; }
  .dataTables_filter { float: right !important; text-align: right !important; }
  .dataTables_wrapper .row { clear: both; }
</style>

<body>
<?php require('common/sidebar.php'); ?>
<div class="content">
  <?php require('common/navbar.php'); ?>

  <div class="container-fluid py-2 px-3">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Admin Users</li>
      </ol>
    </nav>
  </div>

  <div class="app-body">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title">Admin Users List</h5>
          <button class="btn btn-indigo-400 ms-auto" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-plus-circle me-1"></i> Add User
          </button>
        </div>
        <div class="card-body">
          <div class="bg-light rounded p-4">
            <div class="table-responsive">
              <table id="basicExample" class="table table-hover table-bordered table-striped align-middle m-0">
                <thead class="text-center" style="background-color: #818cf8; color:white;">
                  <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th class="text-center">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                      <tr class="text-center">
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['role']) ?></td>
                        <td>
                          <div class="action-buttons">
                            <!-- Edit triggers modal -->
                            <a href="#" 
                               class="btn btn-sm btn-success edit-user-btn" 
                               data-id="<?= $row['id'] ?>"
                               data-username="<?= htmlspecialchars($row['username']) ?>"
                               data-role="<?= htmlspecialchars($row['role']) ?>"
                               title="Edit" 
                               data-bs-toggle="modal" 
                               data-bs-target="#editUserModal">
                               <i class="bi bi-pencil"></i>
                            </a>
                            <a href="deleteuser.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger delete-link" title="Delete">
                              <i class="bi bi-trash"></i>
                            </a>
                          </div>
                        </td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr><td colspan="4" class="text-center text-muted">No users found.</td></tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add User Modal (existing, unchanged) -->
  <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
      <form class="modal-content" action="adduser.php" method="POST">
        <div class="modal-header" style="background-color: #818CF8; color: white;">
          <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Your Add User fields here -->
          <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select class="form-select" id="role" name="role" required>
              <option value="">-- Select Role --</option>
              <option value="admin">Admin</option>
              <option value="doctor">Doctor</option>
              <option value="receptionist">Receptionist</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Username</label>

            <!-- Dropdown for doctor/receptionist -->
            <select class="form-select d-none" id="usernameSelect" name="username">
              <option value="">-- Select Username --</option>
            </select>

            <!-- Input for admin -->
            <input type="text" class="form-control d-none mt-2" id="usernameInput" name="username" placeholder="Enter username manually">
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-indigo-400">Save</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit User Modal -->
  <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
      <!-- Note the hidden input and submit button named 'editUser' to detect this form submission -->
      <form class="modal-content" id="editUserForm" method="POST" action="adminusers.php">
        <div class="modal-header" style="background-color: #818CF8; color: white;">
          <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="editUserId" name="id">

          <div class="mb-3">
            <label for="editRole" class="form-label">Role</label>
            <select class="form-select" id="editRole" name="role" required>
              <option value="">-- Select Role --</option>
              <option value="admin">Admin</option>
              <option value="doctor">Doctor</option>
              <option value="receptionist">Receptionist</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="editUsername" class="form-label">Username</label>
            <input type="text" id="editUsername" name="username" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="editUser" class="btn btn-indigo-400">Update</button>
        </div>
      </form>
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
  document.addEventListener('DOMContentLoaded', function () {
    $('#basicExample').DataTable({
      "pageLength": 10,
      "lengthMenu": [5, 10, 25, 50],
      "order": [],
      "language": {
        "search": "Search Users:",
        "lengthMenu": "Show _MENU_ entries",
        "info": "Showing _START_ to _END_ of _TOTAL_ users",
        "infoEmpty": "No users available",
        "infoFiltered": "(filtered from _MAX_ total users)",
        "zeroRecords": "No matching users found",
        "paginate": {
          "previous": "&laquo;",
          "next": "&raquo;"
        }
      }
    });

    // Delete confirmation
    document.querySelectorAll('.delete-link').forEach(link => {
      link.addEventListener('click', e => {
        e.preventDefault();
        const href = link.getAttribute('href');
        Swal.fire({
          title: 'Are you sure?',
          text: 'This user will be deleted!',
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
    });

    // Alerts for actions
    const params = new URLSearchParams(window.location.search);
    if (params.has('success'))
      Swal.fire({ icon: 'success', title: 'User Added', text: 'User added successfully!' });
    if (params.has('updated'))
      Swal.fire({ icon: 'success', title: 'User Updated', text: 'User updated successfully!' });
    if (params.has('deleted'))
      Swal.fire({ icon: 'success', title: 'Deleted!', text: 'User deleted successfully.' });
    if (params.has('error'))
      Swal.fire({ icon: 'error', title: 'Oops!', text: 'Something went wrong.' });

    // Role change handler for username input/dropdown toggle (Add User Modal)
    const roleSelect = document.getElementById('role');
    const usernameSelect = document.getElementById('usernameSelect');
    const usernameInput = document.getElementById('usernameInput');

    if(roleSelect){
      roleSelect.addEventListener('change', function () {
        const role = this.value;

        if (role === 'doctor' || role === 'receptionist') {
          usernameSelect.classList.remove('d-none');
          usernameSelect.disabled = false;

          usernameInput.classList.add('d-none');
          usernameInput.disabled = true;
          usernameInput.removeAttribute('required');

          usernameSelect.setAttribute('required', 'required');

          usernameSelect.innerHTML = '<option value="">Loading...</option>';
          fetch('get_usernames.php?role=' + role)
            .then(res => res.json())
            .then(data => {
              usernameSelect.innerHTML = '<option value="">-- Select Username --</option>';
              data.forEach(name => {
                const opt = document.createElement('option');
                opt.value = name;
                opt.textContent = name;
                usernameSelect.appendChild(opt);
              });
            })
            .catch(() => {
              usernameSelect.innerHTML = '<option value="">Error loading usernames</option>';
            });

        } else if (role === 'admin') {
          usernameInput.classList.remove('d-none');
          usernameInput.disabled = false;
          usernameInput.setAttribute('required', 'required');

          usernameSelect.classList.add('d-none');
          usernameSelect.disabled = true;
          usernameSelect.removeAttribute('required');

        } else {
          usernameInput.classList.add('d-none');
          usernameInput.disabled = true;
          usernameInput.removeAttribute('required');

          usernameSelect.classList.add('d-none');
          usernameSelect.disabled = true;
          usernameSelect.removeAttribute('required');
        }
      });
    }

    // Fill Edit User modal with data from clicked edit button
    document.querySelectorAll('.edit-user-btn').forEach(button => {
      button.addEventListener('click', function () {
        const id = this.getAttribute('data-id');
        const username = this.getAttribute('data-username');
        const role = this.getAttribute('data-role');

        // Set modal form fields
        document.getElementById('editUserId').value = id;
        document.getElementById('editUsername').value = username;
        document.getElementById('editRole').value = role;
      });
    });
  });
</script>
</div>
</body>
</html>
