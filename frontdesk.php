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
</style>
<?php
include 'dp.php';

$departments = $conn->query("SELECT DepID, DepName FROM department");

// Fetch patients for patient dropdown
$patients = $conn->query("SELECT PatientID, FullName FROM patient ORDER BY FullName ASC");
if (!$patients) {
    die("Patient query failed: " . $conn->error);
}
?>
<?php
  session_start(); // make sure this is at the top
   'Role: ' . ($_SESSION['role'] ?? 'Not Set');
?>

<?php require('common/head.php'); ?>
<body>
<?php require('common/sidebar.php'); ?>
<div class="content">
<?php require('common/navbar.php'); ?>

<div class="container">
  <h2 class="text-center my-4">Front Desk Calendar</h2>
  <div id="calendar"></div>
</div>

<!-- Appointment Modal -->
<div class="modal fade" id="appointmentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="appointmentForm">
        <div class="modal-header" style="background-color: #818CF8; color: white;">
          <h5 class="modal-title">Appointment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="AppID" name="AppID">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <select id="PatientID" name="PatientID" class="form-select" required>
                  <option value="">-- Select Patient --</option>
                  <?php while ($patient = $patients->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($patient['PatientID']) ?>"><?= htmlspecialchars($patient['FullName']) ?></option>
                  <?php endwhile; ?>
                </select>
              </div>
              <div class="mb-3">
                <label>Department</label>
                <select id="DepID" name="DepID" class="form-select" required>
                  <option value="">-- Select Department --</option>
                  <?php
                  $departments->data_seek(0);
                  while ($dept = $departments->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($dept['DepID']) ?>"><?= htmlspecialchars($dept['DepName']) ?></option>
                  <?php endwhile; ?>
                </select>
              </div>
              <div class="mb-3">
                <label>Doctor</label>
                <select id="DocID" name="DocID" class="form-select" required>
                  <option value="">-- Select Doctor --</option>
                </select>
              </div>
              <div class="mb-3">
                <label>Date</label>
                <input type="date" id="appDate" name="AppDate" class="form-control" required />
              </div>
              <div class="mb-3">
                <label>Start Time</label>
                <input type="time" id="startTime" name="StartTime" class="form-control" required />
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label>Price</label>
                <input type="number" id="price" name="Price" class="form-control" step="0.01" required />
              </div>
              <div class="mb-3">
                <label>Discount</label>
                <input type="number" id="discount" name="Discount" class="form-control" step="0.01" value="0" />
              </div>
              <div class="mb-3">
                <label>Total</label>
                <input type="number" id="total" name="Total" class="form-control" step="0.01" readonly />
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          
          
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
          <button type="button" id="deleteBtn" class="btn btn-danger d-none">Delete</button>
          <button type="submit" class="btn btn-indigo-400">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- View-Only Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <form id="paymentForm">
        <div class="modal-header" style="background-color: #818CF8; color: white;">
          <h5 class="modal-title">Record Payment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="paymentAppID" name="AppID">
          <div class="row g-2 align-items-center mb-2">
            <div class="col-4"><label class="col-form-label">Full Name:</label></div>
            <div class="col-8"><p id="paymentFullName" class="form-control-plaintext mb-0"></p></div>
          </div>
          <div class="row g-2 align-items-center mb-2">
            <div class="col-4"><label class="col-form-label">Department:</label></div>
            <div class="col-8"><p id="paymentDepartment" class="form-control-plaintext mb-0"></p></div>
          </div>
          <div class="row g-2 align-items-center mb-2">
            <div class="col-4"><label class="col-form-label">Doctor:</label></div>
            <div class="col-8"><p id="paymentDoctor" class="form-control-plaintext mb-0"></p></div>
          </div>
          <div class="row g-2 align-items-center mb-2">
            <div class="col-4"><label class="col-form-label">Date:</label></div>
            <div class="col-8"><p id="paymentDate" class="form-control-plaintext mb-0"></p></div>
          </div>
          <div class="row g-2 align-items-center mb-2">
            <div class="col-4"><label class="col-form-label">Time:</label></div>
            <div class="col-8"><p id="paymentStartTime" class="form-control-plaintext mb-0"></p></div>
          </div>
          <div class="row g-2 align-items-center mb-2">
            <div class="col-4"><label class="col-form-label">Status:</label></div>
            <div class="col-8"><p id="paymentStatus" class="form-control-plaintext text-capitalize mb-0"></p></div>
          </div>
          <!-- Moved Price ABOVE Payment -->
          <div class="row g-2 align-items-center mb-2">
            <div class="col-4"><label class="col-form-label">Price:</label></div>
            <div class="col-8"><p id="paymentPriceText" class="form-control-plaintext mb-0"></p></div>
          </div>
          <div class="row g-2 align-items-center mb-2">
            <div class="col-4"><label class="col-form-label">Payment:</label></div>
            <div class="col-8"><p id="paymentAmountText" class="form-control-plaintext mb-0"></p></div>
          </div>
        </div>
        <div class="modal-footer">
          
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
          <button type="button" id="editAppointmentBtn" class="btn btn-secondary">Edit</button>
          <button type="button" id="makePaymentBtn" class="btn btn-indigo-400">Payment</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Enter Payment Modal -->
<div class="modal fade" id="enterPaymentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="enterPaymentForm">
        <div class="modal-header" style="background-color: #818CF8; color: white;">
          <h5 class="modal-title">Enter Payment</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="enterPaymentAppID" name="AppID">

          <div class="row mb-3 align-items-center">
            <label for="paymentFullNameInput" class="col-sm-3 col-form-label">Full Name</label>
            <div class="col-sm-9">
              <input type="text" readonly class="form-control" id="paymentFullNameInput" name="FullName" />
            </div>
          </div>

          <!-- Phone number block REMOVED here -->

          <div class="row mb-3 align-items-center">
            <label for="paymentPriceInput" class="col-sm-3 col-form-label">Price</label>
            <div class="col-sm-9">
              <input type="number" readonly class="form-control" id="paymentPriceInput" name="Price" step="0.01" />
            </div>
          </div>

          <div class="row mb-3 align-items-center">
            <label for="enterPaymentAmount" class="col-sm-3 col-form-label">Payment Amount</label>
            <div class="col-sm-9">
              <input type="number" id="enterPaymentAmount" name="PaymentAmount" class="form-control" step="0.01" required />
            </div>
          </div>

          <div class="row mb-3 align-items-center">
            <label for="remainingAmount" class="col-sm-3 col-form-label">Remaining Amount</label>
            <div class="col-sm-9">
              <input type="number" id="remainingAmount" name="RemainingAmount" class="form-control" step="0.01" readonly />
            </div>
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


<script>
document.addEventListener('DOMContentLoaded', function () {
  const calendarEl = document.getElementById('calendar');
  const appointmentModal = new bootstrap.Modal(document.getElementById('appointmentModal'));
  const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
  const enterPaymentModal = new bootstrap.Modal(document.getElementById('enterPaymentModal'));
  const appointmentForm = document.getElementById('appointmentForm');
  const deleteBtn = document.getElementById('deleteBtn');
  const enterPaymentForm = document.getElementById('enterPaymentForm');

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    selectable: true,
    editable: false,
    events: 'fetchapp.php',

    eventDisplay: 'block',
    eventDidMount: function(info) {
      info.el.style.backgroundColor = info.event.backgroundColor;
      info.el.style.borderColor = info.event.borderColor;
    },

    dateClick(info) {
      appointmentForm.reset();
      document.getElementById('AppID').value = '';
      document.getElementById('DocID').innerHTML = '<option value="">-- Select Doctor --</option>';
      document.getElementById('appDate').value = info.dateStr;
      deleteBtn.classList.add('d-none');
      appointmentModal.show();
    },

    eventClick(info) {
      const data = info.event.extendedProps;

      // Fill Payment Modal (readonly)
      document.getElementById('paymentAppID').value = info.event.id;
      document.getElementById('paymentFullName').textContent = info.event.title;
      document.getElementById('paymentDepartment').textContent = data.departmentName;
      document.getElementById('paymentDoctor').textContent = data.doctorName;
      document.getElementById('paymentDate').textContent = data.date;
      document.getElementById('paymentStartTime').textContent = data.start;
      document.getElementById('paymentStatus').textContent = data.status || 'appointed';
      // Note the switched order here matches the modal layout now:
      document.getElementById('paymentPriceText').textContent = data.price ? parseFloat(data.price).toFixed(2) + ' $' : 'N/A';
      document.getElementById('paymentAmountText').textContent = data.payment ? parseFloat(data.payment).toFixed(2) + ' $' : 'N/A';


      const status = (data.status || '').toLowerCase();
      const paymentAmount = parseFloat(data.payment) || 0;
      const totalAmount = parseFloat(data.total) || 0;

      const makePaymentBtn = document.getElementById('makePaymentBtn');
      const editAppointmentBtn = document.getElementById('editAppointmentBtn');

      if (status === 'cancel') {
        // Cancelled: hide both buttons
        makePaymentBtn.style.display = 'none';
        editAppointmentBtn.style.display = 'none';
      } else if (paymentAmount > 0) {
        // Partially or fully paid: hide edit button
        editAppointmentBtn.style.display = 'none';

        // Show or hide payment button based on whether fully paid
        if (paymentAmount >= totalAmount && totalAmount > 0) {
          makePaymentBtn.style.display = 'none'; // Fully paid, no payment button
        } else {
          makePaymentBtn.style.display = 'inline-block'; // Partially paid, allow payment
        }
      } else {
        // No payment: show both buttons
        makePaymentBtn.style.display = 'inline-block';
        editAppointmentBtn.style.display = 'inline-block';
      }

      paymentModal.show();
    }
  });

  document.getElementById('DepID').addEventListener('change', function () {
    const depID = this.value;
    fetch('getdoctorsfront.php?DepID=' + depID)
      .then(res => res.text())
      .then(data => {
        document.getElementById('DocID').innerHTML = '<option value="">-- Select Doctor --</option>' + data;
      });
  });

  function updateTotal() {
    const price = parseFloat(document.getElementById('price').value) || 0;
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const total = price - discount;
    document.getElementById('total').value = total >= 0 ? total.toFixed(2) : 0;
  }
  document.getElementById('price').addEventListener('input', updateTotal);
  document.getElementById('discount').addEventListener('input', updateTotal);

  document.getElementById('makePaymentBtn').addEventListener('click', function () {
    const paymentAppID = document.getElementById('paymentAppID').value;
    if (!paymentAppID) return;

    document.getElementById('enterPaymentAppID').value = paymentAppID;

    const event = calendar.getEventById(paymentAppID);
    if (!event) return;

    const data = event.extendedProps;

    document.getElementById('paymentFullNameInput').value = event.title || '';
    
    document.getElementById('paymentPriceInput').value = parseFloat(data.price || 0).toFixed(2);
    

    const totalAmount = parseFloat(data.total) || 0;
    const paymentSoFar = parseFloat(data.payment) || 0;
    const remaining = totalAmount - paymentSoFar;

    document.getElementById('remainingAmount').value = remaining.toFixed(2);
    document.getElementById('enterPaymentAmount').value = '';

    paymentModal.hide();
    enterPaymentModal.show();
  });

  document.getElementById('enterPaymentAmount').addEventListener('input', () => {
    const totalRemaining = parseFloat(document.getElementById('remainingAmount').value) + (parseFloat(document.getElementById('enterPaymentAmount').value) || 0);
    const paymentInput = parseFloat(document.getElementById('enterPaymentAmount').value) || 0;
    const remaining = totalRemaining - paymentInput;
    document.getElementById('remainingAmount').value = remaining >= 0 ? remaining.toFixed(2) : '0.00';
  });

  enterPaymentForm.addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(enterPaymentForm);
    fetch('savepayment.php', {
      method: 'POST',
      body: formData
    }).then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: 'Success',
            text: 'Payment saved successfully.',
            showConfirmButton: true
          });
          enterPaymentModal.hide();
          calendar.refetchEvents();
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.message || 'Error saving payment.',
            showConfirmButton: true
          });
        }
      }).catch(() => {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Network error while saving payment.',
          showConfirmButton: true
        });
      });
  });

  appointmentForm.addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(appointmentForm);
    formData.append('action', document.getElementById('AppID').value ? 'update' : 'add');
    fetch('saveapp.php', {
      method: 'POST',
      body: formData
    }).then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: 'Success',
            text: data.message || 'Appointment saved.',
            showConfirmButton: true
          });
          appointmentModal.hide();
          calendar.refetchEvents();
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.message || 'Failed to save appointment.',
            showConfirmButton: true
          });
        }
      }).catch(() => {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Network error while saving appointment.',
          showConfirmButton: true
        });
      });
  });

  deleteBtn.addEventListener('click', () => {
    Swal.fire({
      title: 'Are you sure?',
      text: "Do you really want to delete this appointment?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
        const id = document.getElementById('AppID').value;
        fetch('saveapp.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({ action: 'delete', AppID: id })
        }).then(res => res.json())
          .then(data => {
            if (data.status === 'success') {
              appointmentModal.hide();
              Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                text: data.message || 'Appointment deleted successfully.',
                showConfirmButton: true
              });
              calendar.refetchEvents();
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Error deleting appointment.',
                showConfirmButton: true
              });
            }
          }).catch(() => {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Network error while deleting appointment.',
              showConfirmButton: true
            });
          });
      }
    });
  });

  document.getElementById('editAppointmentBtn').addEventListener('click', function () {
  const appID = document.getElementById('paymentAppID').value;
  const fullName = document.getElementById('paymentFullName').textContent;
  const depName = document.getElementById('paymentDepartment').textContent;
  const docName = document.getElementById('paymentDoctor').textContent;

  document.getElementById('AppID').value = appID;

  const patientSelect = document.getElementById('PatientID');
  for (let option of patientSelect.options) {
    if (option.text === fullName) {
      patientSelect.value = option.value;
      break;
    }
  }

  document.getElementById('appDate').value = document.getElementById('paymentDate').textContent;
  document.getElementById('startTime').value = document.getElementById('paymentStartTime').textContent;

  const depSelect = document.getElementById('DepID');
  for (let option of depSelect.options) {
    if (option.text === depName) {
      depSelect.value = option.value;
      break;
    }
  }

  // Fetch event data
  const event = calendar.getEventById(appID);
  const eventData = event.extendedProps;

  // Set Price, Discount, and Total
  document.getElementById('price').value = parseFloat(eventData.price || 0).toFixed(2);
  document.getElementById('discount').value = parseFloat(eventData.discount || 0).toFixed(2);
  const total = (parseFloat(eventData.price || 0) - parseFloat(eventData.discount || 0)).toFixed(2);
  document.getElementById('total').value = total >= 0 ? total : '0.00';

  // Load doctors and select the right one
  fetch('getdoctorsfront.php?DepID=' + depSelect.value)
    .then(res => res.text())
    .then(data => {
      const docSelect = document.getElementById('DocID');
      docSelect.innerHTML = '<option value="">-- Select Doctor --</option>' + data;
      for (let option of docSelect.options) {
        if (option.text === docName) {
          docSelect.value = option.value;
          break;
        }
      }
    });

  deleteBtn.classList.remove('d-none');
  paymentModal.hide();
  appointmentModal.show();
});


  calendar.render();
});
</script>

<?php require('common/footer.php'); ?>
<?php require('common/javascript.php'); ?>
</div>
</body>
</html>
