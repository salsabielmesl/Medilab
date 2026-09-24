<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>DASHMIN - Bootstrap Admin Template</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="medilab.png" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <!-- <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet"> -->
    <!-- <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" /> -->

 
    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style1.css"/>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs" defer></script>

    <!-- Include ApexCharts library -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    

    <!-- FullCalendar CSS -->
<!-- <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css" rel="stylesheet"> -->

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.js"></script>




<!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap CSS -->
<link href="css/bootstrap.min.css" rel="stylesheet">
   <!-- DataTables CSS -->
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet"/>


 <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap.min.css" rel="stylesheet"> -->

<!-- jQuery (required by DataTables) -->
 

<style>
  
 .table th,
  .table td,
  .table, .table th, .table td {
  border: 2px solid #dee2e6 !important; /* Bootstrap's default light gray */
} 
  /* Set height for chart containers */
  #availableBeds,
  #patients,
  #treatment,
  #claims,
  #genderAge,
  #sparkline1,
  #sparkline2,
  #sparkline3,
  #sparkline4 {
    height: 300px; /* Adjust as needed */
  }
  
  /* For sparklines, smaller height */
  #sparkline1,
  #sparkline2,
  #sparkline3,
  #sparkline4 {
    height: 60px;
  }
 body {
      background-color: #f8f9fa;
    }

    .doctor-img {
      width: 40px;
      height: 40px;
      object-fit: cover;
      border-radius: 50%;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .table td,
    .table th {
      vertical-align: middle;
    }

    .table-hover tbody tr:hover {
      background-color: #f1f3f5;
      transition: background-color 0.3s ease;
    }

    /* Modal header red bg */
    .modal-header.bg-danger {
      background-color: #dc3545 !important;
    }

    .btn-close.btn-close-white {
      filter: invert(1);
      opacity: 1;
      9
    }
    /* --- General card and layout tweaks --- */
.app-body {
  padding: 1.5rem;
  background-color: #f8f9fa; /* light gray background */
  min-height: 100vh;
}

.card {
  border-radius: 0.75rem;
  box-shadow: 0 0.125rem 0.25rem rgb(0 0 0 / 0.075);
}

.card-header {
  background-color: #fff;
  border-bottom: 1px solid #e9ecef;
  font-weight: 600;
  font-size: 1.125rem;
  padding: 1rem 1.5rem;
  border-top-left-radius: 0.75rem;
  border-top-right-radius: 0.75rem;
}

.card-body {
  padding: 1.25rem 1.5rem;
}

/* --- Chart containers --- */
.chart-height-lg {
  height: 280px;
}

/* --- Table Styling --- */
.table {
  font-size: 0.95rem;
  color: #495057;
}

.table thead th {
  border-bottom: 2px solid #dee2e6;
  font-weight: 600;
}

.table tbody tr:hover {
  background-color: #f1f3f5;
  transition: background-color 0.15s ease-in-out;
}

/* --- Stacked images for doctors list --- */
.stacked-images {
  display: flex;
  align-items: center;
  position: relative;
}

.stacked-images img {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  border: 2px solid #fff;
  box-shadow: 0 0 5px rgba(0,0,0,0.1);
  margin-left: -10px;
  transition: transform 0.2s ease;
}

.stacked-images img:first-child {
  margin-left: 0;
}

.stacked-images img:hover {
  z-index: 10;
  transform: scale(1.2);
  box-shadow: 0 0 8px rgba(0,0,0,0.25);
}

/* Plus badge */
.stacked-images .plus {
  display: inline-flex;
  justify-content: center;
  align-items: center;
  margin-left: 6px;
  width: 34px;
  height: 34px;
  border-radius: 50%;
  font-size: 0.75rem;
  font-weight: 600;
  color: #fff;
  box-shadow: 0 0 4px rgba(0,0,0,0.15);
  user-select: none;
}

.plus.bg-primary { background-color: #0d6efd; }
.plus.bg-danger { background-color: #dc3545; }
.plus.bg-success { background-color: #198754; }
.plus.bg-warning { background-color: #ffc107; color: #212529; }
.plus.bg-info { background-color: #0dcaf0; color: #212529; }

/* --- Image shadows and sizing for department heads --- */
.img-shadow {
  box-shadow: 0 0 6px rgba(0,0,0,0.12);
  vertical-align: middle;
  object-fit: cover;
}

.img-2x {
  width: 40px;
  height: 40px;
}

/* --- Buttons --- */
.btn-outline-danger,
.btn-outline-success {
  padding: 0.25rem 0.6rem;
  font-size: 0.875rem;
}

.btn-outline-danger:hover {
  color: #fff;
  background-color: #dc3545;
  border-color: #dc3545;
}

.btn-outline-success:hover {
  color: #fff;
  background-color: #198754;
  border-color: #198754;
}

/* Rounded buttons */
.btn-sm.rounded-5 {
  border-radius: 1.25rem;
}

/* Modal styles */
.modal-content {
  border-radius: 0.75rem;
  padding: 1rem;
}

.modal-header {
  border-bottom: none;
  padding-bottom: 0;
}

.modal-footer {
  border-top: none;
  padding-top: 0;
}

/* Tooltip fix (Bootstrap 5 default is fine, but ensure this for your icons) */
[data-bs-toggle="tooltip"] {
  cursor: pointer;
}

/* Responsive tweaks */
@media (max-width: 575.98px) {
  .stacked-images img {
    width: 28px;
    height: 28px;
  }
  .plus {
    width: 28px;
    height: 28px;
    font-size: 0.65rem;
  }
}
.chart-height-lg {
  height: 280px;
}
.card {
  border-radius: 1rem;
  box-shadow: 0 0.5rem 1.2rem rgba(0, 0, 0, 0.05);
  border: none;
}

/* Card title */
.card-title {
  font-weight: 600;
  font-size: 1.2rem;
  color: #0d6efd;
}

/* Form controls */
.form-control, .form-select {
  border-radius: 0.5rem;
  min-height: 44px;
  font-size: 0.95rem;
  padding: 0.6rem 0.75rem;
}

/* Radio buttons spacing */
.form-check-inline {
  margin-right: 1.5rem;
}

/* Form labels */
.form-label {
  font-weight: 500;
  margin-bottom: 0.4rem;
  color: #333;
}

/* Button styling */
.btn-primary {
  background-color: #0d6efd;
  border-color: #0d6efd;
  font-weight: 500;
  padding: 0.5rem 1.5rem;
}

.btn-outline-secondary {
  padding: 0.5rem 1.5rem;
}

/* Footer */
.app-footer {
  padding: 1rem 1.5rem;
  font-size: 0.875rem;
  border-top: 1px solid #eee;
  color: #6c757d;
}

/* Responsive enhancements */
@media (max-width: 767.98px) {
  .form-control, .form-select {
    font-size: 1rem;
  }
}

    
</style>


</head>