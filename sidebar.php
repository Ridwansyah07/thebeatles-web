<?php
$halaman = basename($_SERVER['PHP_SELF']);
?>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="index.php" class="brand-link">
    <span class="brand-text font-weight-light">The Beatles Admin</span>
  </a>

  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
          <a href="index.php" class="nav-link <?= $halaman == 'index.php' ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>
        <li class="nav-header">DATA MASTER</li>
        <li class="nav-item">
          <a href="album.php" class="nav-link <?= in_array($halaman, ['album.php', 'album_form.php']) ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-record-vinyl"></i>
            <p>Data Album</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="songs.php" class="nav-link <?= in_array($halaman, ['songs.php', 'songs_form.php']) ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-record-vinyl"></i>
            <p>Data Lagu</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="merchandise.php" class="nav-link <?= in_array($halaman, ['merchandise.php', 'merchandise_form.php']) ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-tshirt"></i>
            <p>Merchandise</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="admin.php" class="nav-link <?= in_array($halaman, ['admin.php', 'admin_form.php']) ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-user-shield"></i>
            <p>Data Admin</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="members.php" class="nav-link <?= in_array($halaman, ['members.php', 'members_form.php']) ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-users"></i>
            <p>Data Member</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="roles.php" class="nav-link <?= in_array($halaman, ['roles.php', 'roles_form.php']) ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-user-tag"></i>
            <p>Data Roles</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="member_roles.php" class="nav-link <?= in_array($halaman, ['member_roles.php', 'member_roles_form.php']) ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-user-tag"></i>
            <p>Member Roles</p>
          </a>
        </li>
        <li class="nav-header">TRANSAKSI</li>
        <li class="nav-item">
          <a href="cart.php" class="nav-link <?= in_array($halaman, ['cart.php', 'cart_form.php']) ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-shopping-basket"></i>
            <p>Data Cart</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="cart_details.php" class="nav-link <?= in_array($halaman, ['cart_details.php', 'cart_details_form.php']) ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-list-alt"></i>
            <p>Detail Cart</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="detail_pesanan.php" class="nav-link <?= in_array($halaman, ['detail_pesanan.php', 'detail_pesanan_form.php']) ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-file-invoice"></i>
            <p>Detail Pesanan</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="transaksi.php" class="nav-link <?= in_array($halaman, ['transaksi.php', 'transaksi_form.php']) ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-money-bill-wave"></i>
            <p>Data Transaksi</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="pembayaran.php" class="nav-link <?= in_array($halaman, ['pembayaran.php', 'pembayaran_form.php']) ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-credit-card"></i>
            <p>Data Pembayaran</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="pesanan.php" class="nav-link <?= in_array($halaman, ['pesanan.php', 'pesanan_form.php']) ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-shopping-cart"></i>
            <p>Data Pesanan</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="pengiriman.php" class="nav-link <?= in_array($halaman, ['pengiriman.php', 'pengiriman_form.php']) ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-truck"></i>
            <p>Data Pengiriman</p>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</aside>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.main-sidebar .sidebar');
    if (!sidebar) return;

    const posisiScroll = sessionStorage.getItem('sidebarScroll');

    if (posisiScroll !== null) {
      sidebar.scrollTop = parseInt(posisiScroll, 10);
    }

    sidebar.addEventListener('scroll', function() {
      sessionStorage.setItem('sidebarScroll', sidebar.scrollTop);
    });
  });
</script>