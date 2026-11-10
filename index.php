<?php
require_once __DIR__ . '/koneksi.php';

// Menghitung total data
$query_album = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM albums");
$total_album = mysqli_fetch_assoc($query_album)['total'];

$query_merch = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM merchandise");
$total_merch = mysqli_fetch_assoc($query_merch)['total'];

$query_songs = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM songs");
$total_songs = mysqli_fetch_assoc($query_songs)['total'];

$query_pesanan = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pesanan");
$total_pesanan = mysqli_fetch_assoc($query_pesanan)['total'];

$query_member = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM members");
$total_member = mysqli_fetch_assoc($query_member)['total'];

$query_transaksi = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM transaksi");
$total_transaksi = mysqli_fetch_assoc($query_transaksi)['total'];

$query_pending = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total
    FROM pembayaran
    WHERE status_pembayaran = 'pending'
");
$total_pending = mysqli_fetch_assoc($query_pending)['total'];

// Mengambil 5 pesanan terbaru
$query_terbaru = mysqli_query($koneksi, "
    SELECT kode_pesanan, nama_pelanggan, total_harga,
           status_pesanan, tanggal_pesanan
    FROM pesanan
    ORDER BY tanggal_pesanan DESC
    LIMIT 5
");

include 'header.php';
include 'sidebar.php';
?>

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <h1 class="m-0">Dashboard Utama</h1>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <!-- Total Album -->
        <div class="col-lg-3 col-6">
          <div class="small-box bg-info">
            <div class="inner">
              <h3><?= $total_album; ?></h3>
              <p>Total Album</p>
            </div>
            <div class="icon">
              <i class="fas fa-record-vinyl"></i>
            </div>
            <a href="album.php" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <!-- Total Lagu -->
        <div class="col-lg-3 col-6">
          <div class="small-box bg-primary">
            <div class="inner">
              <h3><?= $total_songs; ?></h3>
              <p>Total Lagu</p>
            </div>
            <div class="icon">
              <i class="fas fa-music"></i>
            </div>
            <a href="songs.php" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <!-- Total Merchandise -->
        <div class="col-lg-3 col-6">
          <div class="small-box bg-success">
            <div class="inner">
              <h3><?= $total_merch; ?></h3>
              <p>Total Merchandise</p>
            </div>
            <div class="icon">
              <i class="fas fa-tshirt"></i>
            </div>
            <a href="merchandise.php" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <!-- Total Member -->
        <div class="col-lg-3 col-6">
          <div class="small-box bg-secondary">
            <div class="inner">
              <h3><?= $total_member; ?></h3>
              <p>Total Member</p>
            </div>
            <div class="icon">
              <i class="fas fa-users"></i>
            </div>
            <a href="members.php" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <!-- Total Pesanan -->
        <div class="col-lg-3 col-6">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3><?= $total_pesanan; ?></h3>
              <p>Total Pesanan</p>
            </div>
            <div class="icon">
              <i class="fas fa-shopping-cart"></i>
            </div>
            <a href="pesanan.php" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <!-- Total Transaksi -->
        <div class="col-lg-3 col-6">
          <div class="small-box bg-purple">
            <div class="inner">
              <h3><?= $total_transaksi; ?></h3>
              <p>Total Transaksi</p>
            </div>
            <div class="icon">
              <i class="fas fa-money-bill-wave"></i>
            </div>
            <a href="transaksi.php" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <!-- Pembayaran Pending -->
        <div class="col-lg-3 col-6">
          <div class="small-box bg-danger">
            <div class="inner">
              <h3><?= $total_pending; ?></h3>
              <p>Pembayaran Pending</p>
            </div>
            <div class="icon">
              <i class="fas fa-clock"></i>
            </div>
            <a href="pembayaran.php" class="small-box-footer">Detail <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>

      <!-- Tabel Pesanan Terbaru -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-shopping-cart mr-2"></i>
            Pesanan Terbaru
          </h3>
        </div>
        <div class="card-body table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>No</th>
                <th>Kode Pesanan</th>
                <th>Nama Pelanggan</th>
                <th>Total Harga</th>
                <th>Tanggal Pesanan</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $no = 1;
              while ($data = mysqli_fetch_assoc($query_terbaru)) {
                $warna = 'warning';
                if ($data['status_pesanan'] == 'selesai') {
                  $warna = 'success';
                } elseif ($data['status_pesanan'] == 'dikirim') {
                  $warna = 'info';
                } elseif ($data['status_pesanan'] == 'diproses') {
                  $warna = 'primary';
                }
              ?>
                <tr>
                  <td><?= $no++; ?></td>
                  <td><?= htmlspecialchars($data['kode_pesanan']); ?></td>
                  <td><?= htmlspecialchars($data['nama_pelanggan']); ?></td>
                  <td>Rp <?= number_format($data['total_harga'], 0, ',', '.'); ?></td>
                  <td><?= date('d-m-Y H:i', strtotime($data['tanggal_pesanan'])); ?></td>
                  <td>
                    <span class="badge badge-<?= $warna; ?>">
                      <?= ucfirst($data['status_pesanan']); ?>
                    </span>
                  </td>
                </tr>
              <?php } ?>

              <?php if (mysqli_num_rows($query_terbaru) == 0) : ?>
                <tr>
                  <td colspan="6" class="text-center">Belum ada pesanan.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <div class="card-footer">
          <a href="pesanan.php" class="btn btn-primary btn-sm">
            Lihat Semua Pesanan
          </a>
        </div>
      </div>
    </div>
  </section>
</div>