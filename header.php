<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard | The Beatles</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Theme style (AdminLTE) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

  <style>
    /* Transisi menu sidebar */
    .main-sidebar .nav-sidebar .nav-link {
      border-radius: 8px;
      margin: 3px 8px;
      width: calc(100% - 16px);
      transition: all 0.25s ease-in-out;
      position: relative;
      overflow: hidden;
    }

    /* Efek ketika mouse diarahkan ke menu */
    .main-sidebar .nav-sidebar .nav-link:not(.active):hover {
      background-color: rgba(255, 255, 255, 0.10);
      color: #ffffff;
      transform: translateX(3px);
    }

    /* Menu yang sedang dibuka */
    .main-sidebar .nav-sidebar .nav-link.active {
      background: linear-gradient(110deg, #1677e8, #3498f6);
      color: #ffffff;
      box-shadow: 0 3px 10px rgba(22, 119, 232, 0.28);
      font-weight: 600;
    }

    /* Ikon pada menu terpilih */
    .main-sidebar .nav-sidebar .nav-link.active .nav-icon {
      color: #ffffff;
    }

    /* Efek hover pada menu terpilih */
    .main-sidebar .nav-sidebar .nav-link.active:hover {
      background: linear-gradient(110deg, #126bd4, #2589ed);
      color: #ffffff;
      transform: translateX(2px);
    }

    /* Warna tulisan menu */
    .main-sidebar .nav-sidebar .nav-link p {
      transition: color 0.25s ease;
    }

    /* Judul kelompok menu */
    .main-sidebar .nav-header {
      color: #aeb7c2;
      font-weight: 600;
      letter-spacing: 0.5px;
      padding-top: 15px;
    }
  </style>

</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">