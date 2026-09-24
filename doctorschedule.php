<?php
include 'dp.php'; // Your DB connection
session_start();
$docID = $_SESSION['DocID'] ?? null;
if (!$docID) {
    die("No doctor logged in. Please login.");
}
?>
<?php require('common/head.php'); ?>
<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js"></script>

<body>
<?php require('common/sidebar.php'); ?>
<div class="content">
<?php require('common/navbar.php'); ?>

<div class="container mt-4">
  <div id="calendar"></div>
</div>

<!-- Modal: View Schedule -->
<div class="modal fade" id="viewScheduleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #818CF8; color: white;">
        <h5 class="modal-title">Schedule Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Start Time</label>
          <input type="time" id="startTime" class="form-control" readonly>
        </div>
        <div class="mb-3">
          <label class="form-label">End Time</label>
          <input type="time" id="endTime" class="form-control" readonly>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-indigo-400" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const calendarEl = document.getElementById("calendar");
  const modalEl = document.getElementById("viewScheduleModal");
  const modal = new bootstrap.Modal(modalEl);
  const startTimeInput = document.getElementById("startTime");
  const endTimeInput = document.getElementById("endTime");

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: "dayGridMonth",
    events: "fetchdocschedule.php",
    // Disable adding new events by doctor:
    // dateClick: () => {},

    eventClick: function(info) {
      const event = info.event;

      // Format start and end times as HH:mm
      const startISO = event.start.toISOString();
      const endISO = event.end ? event.end.toISOString() : null;

      startTimeInput.value = startISO.substring(11,16);
      endTimeInput.value = endISO ? endISO.substring(11,16) : "";

      modal.show();
    }
  });

  calendar.render();
});
</script>

<?php require("common/footer.php"); ?>
<?php require("common/javascript.php"); ?>
</div>
</body>
</html>
