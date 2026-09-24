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
  select[disabled] {
    pointer-events: none;
    background-color: #e9ecef; /* matches Bootstrap disabled look */
    color: #495057;
    border: 1px solid #ced4da;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: none !important;
  }
</style>
<?php
include 'dp.php';
$departments = $conn->query("SELECT DepID, DepName FROM department");
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

<div class="container-fluid py-2 px-3">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-2">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item active">Dashboard</li>
    </ol>
  </nav>
</div>

<div class="container mt-4">
  <div class="row mb-3">
    <div class="col-md-4">
      <label for="DepID" class="form-label">Department</label>
      <select id="DepID" class="form-select" required>
        <option value="">-- Select Department --</option>
        <?php while ($dept = $departments->fetch_assoc()) : ?>
          <option value="<?= htmlspecialchars($dept['DepID']) ?>">
            <?= htmlspecialchars($dept['DepName']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-4">
      <label for="DocID" class="form-label">Doctor</label>
      <select id="DocID" class="form-select" required>
        <option value="">-- Select Doctor --</option>
      </select>
    </div>
  </div>

  <div id="calendar"></div>
</div>

<!-- Modal -->
<div class="modal fade" id="appointmentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="appointmentForm">
        <div class="modal-header" style="background-color: #818CF8; color: white;">
          <h5 class="modal-title">Doctor Schedule</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="ScheduleID" name="ScheduleID" />
          <input type="hidden" id="DayOfWeek" name="DayOfWeek" />

          <div class="mb-3">
            <label class="form-label">Department</label>
            <select id="DepID_modal" class="form-select" disabled>
              <option value="">-- Select Department --</option>
              <?php
              $departments->data_seek(0);
              while ($dept = $departments->fetch_assoc()) : ?>
                <option value="<?= htmlspecialchars($dept['DepID']) ?>">
                  <?= htmlspecialchars($dept['DepName']) ?>
                </option>
              <?php endwhile; ?>
            </select>
            <input type="hidden" id="DepID_modal_hidden" name="DepID" />
          </div>

          <div class="mb-3" id="doctorDiv_modal">
            <label class="form-label">Doctor</label>
            <select id="DocID_modal" class="form-select" disabled>
              <option value="">-- Select Doctor --</option>
            </select>
            <input type="hidden" id="DocID_modal_hidden" name="DocID" />
          </div>

          <div class="mb-3">
            <label class="form-label">Start Time</label>
            <input type="time" id="StartTime" name="StartTime" class="form-control" required />
          </div>

          <div class="mb-3">
            <label class="form-label">End Time</label>
            <input type="time" id="EndTime" name="EndTime" class="form-control" required />
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" id="deleteBtn" class="btn btn-danger d-none">Delete</button>
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
          <button class="btn btn-indigo-400" type="submit">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' rel='stylesheet' />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js'></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const calendarEl = document.getElementById("calendar");
  const modalEl = document.getElementById("appointmentModal");
  const modal = new bootstrap.Modal(modalEl);
  const form = document.getElementById("appointmentForm");

  const depSelect = document.getElementById("DepID");
  const docSelect = document.getElementById("DocID");
  const depSelectModal = document.getElementById("DepID_modal");
  const docSelectModal = document.getElementById("DocID_modal");
  const depHidden = document.getElementById("DepID_modal_hidden");
  const docHidden = document.getElementById("DocID_modal_hidden");

  const dayOfWeekInput = document.getElementById("DayOfWeek");
  const deleteBtn = document.getElementById("deleteBtn");
  const scheduleIdInput = document.getElementById("ScheduleID");

  let calendar = null;

  function loadDoctors(depID, selectElement) {
    if (!depID) {
      selectElement.innerHTML = '<option value="">-- Select Doctor --</option>';
      return Promise.resolve();
    }
    return fetch("getdoctorsfront.php?DepID=" + encodeURIComponent(depID))
      .then(res => res.text())
      .then(data => {
        selectElement.innerHTML = data;
      });
  }

  function loadCalendar(docID) {
    if (!docID) {
      if (calendar) {
        calendar.destroy();
        calendar = null;
      }
      return;
    }

    if (calendar) {
      calendar.destroy();
      calendar = null;
    }

    calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: "dayGridMonth",
      selectable: true,
      editable: false,
      events: function(fetchInfo, successCallback, failureCallback) {
  fetch("fetchdocschedule.php?DocID=" + encodeURIComponent(docID) + 
        "&start=" + encodeURIComponent(fetchInfo.startStr) + 
        "&end=" + encodeURIComponent(fetchInfo.endStr))
    .then(res => res.json())
    .then(events => successCallback(events))
    .catch(err => failureCallback(err));
},

      dateClick: function (info) {
        if (!depSelect.value || !docSelect.value) {
          Swal.fire({
            icon: 'warning',
            title: 'Please select',
            text: 'Please select a department and doctor first.',
            confirmButtonText: 'OK'
          });
          return;
        }

        form.reset();
        scheduleIdInput.value = "";
        dayOfWeekInput.value = info.date.toLocaleDateString('en-US', { weekday: 'long' });

        depSelectModal.value = depSelect.value;
        depHidden.value = depSelect.value;

        loadDoctors(depSelect.value, docSelectModal).then(() => {
          docSelectModal.value = docSelect.value;
          docHidden.value = docSelect.value;
        });

        deleteBtn.classList.add("d-none");
        modal.show();
      },
      eventClick: function (info) {
        const event = info.event;
        const props = event.extendedProps;

        form.reset();
        scheduleIdInput.value = event.id;
        dayOfWeekInput.value = new Date(event.start).toLocaleDateString('en-US', { weekday: 'long' });

        depSelectModal.value = props.depID;
        depHidden.value = props.depID;

        loadDoctors(props.depID, docSelectModal).then(() => {
          docSelectModal.value = props.docID;
          docHidden.value = props.docID;
        });

        document.getElementById("StartTime").value = new Date(event.start).toISOString().substring(11, 16);
        document.getElementById("EndTime").value = new Date(event.end).toISOString().substring(11, 16);

        deleteBtn.classList.remove("d-none");
        modal.show();
      }
    });

    calendar.render();
  }

  depSelect.addEventListener("change", () => {
  loadDoctors(depSelect.value, docSelect).then(() => {
    const firstRealDoctor = [...docSelect.options].find(opt => opt.value);
    if (firstRealDoctor) {
      docSelect.value = firstRealDoctor.value;
      loadCalendar(firstRealDoctor.value);
    } else {
      if (calendar) {
        calendar.destroy();
        calendar = null;
      }
    }
  });
});

  docSelect.addEventListener("change", () => {
    loadCalendar(docSelect.value);
  });

  form.addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(form);

    if (!formData.get("DayOfWeek")) {
      formData.set('DayOfWeek', dayOfWeekInput.value);
    }

    fetch("saveschedule.php", {
      method: "POST",
      body: formData,
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        modal.hide();
        loadCalendar(docSelect.value);
        Swal.fire({
          icon: 'success',
          title: 'Success',
          text: 'Schedule saved successfully.',
          confirmButtonText: 'OK'
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Failed to save schedule: ' + (data.message || 'Unknown error'),
          confirmButtonText: 'OK'
        });
      }
    })
    .catch(() => {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Network error while saving schedule.',
        confirmButtonText: 'OK'
      });
    });
  });

  deleteBtn.addEventListener("click", function () {
    const id = scheduleIdInput.value;
    if (!id) return;

    Swal.fire({
      title: 'Are you sure?',
      text: "This schedule will be deleted!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, delete it!',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        fetch("deleteschedule.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: "ScheduleID=" + encodeURIComponent(id),
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            modal.hide();
            loadCalendar(docSelect.value);
            Swal.fire({
              icon: 'success',
              title: 'Deleted',
              text: 'Schedule deleted successfully.',
              confirmButtonText: 'OK'
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Delete failed: ' + (data.message || 'Unknown error'),
              confirmButtonText: 'OK'
            });
          }
        })
        .catch(() => {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Network error while deleting schedule.',
            confirmButtonText: 'OK'
          });
        });
      }
    });
  });
});
</script>

<?php require("common/footer.php"); ?>
<?php require("common/javascript.php"); ?>
</div>
</body>
</html>
