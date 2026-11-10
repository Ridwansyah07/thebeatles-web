<?php
require 'koneksi.php';
include 'header.php';
include 'sidebar.php';

// Warna badge untuk tiap status pesanan
$warna_status = [
    'pending'    => 'warning',
    'diproses'   => 'info',
    'dikirim'    => 'primary',
    'selesai'    => 'success',
    'dibatalkan' => 'danger',
];
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <h2>Data Pesanan</h2>
    </div>
  </section>

  <section class="content">
    <div class="card">
      <div class="card-header">
        <a href="pesanan_form.php?aksi=tambah" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Pesanan</a>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th width="5%">No</th>
                <th>Kode Pesanan</th>
                <th>Pelanggan</th>
                <th>Alamat Pengiriman</th>
                <th>Tanggal</th>
                <th>Ongkos Kirim</th>
                <th>Total Harga</th>
                <th>Status</th>
                <th width="15%">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $no = 1;
              $query = mysqli_query($koneksi, "SELECT * FROM pesanan ORDER BY id_pesanan DESC");
              while ($data = mysqli_fetch_assoc($query)) {
                  $warna = $warna_status[$data['status_pesanan']] ?? 'secondary';
              ?>
              <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($data['kode_pesanan']); ?></td>
                <td>
                  <?= htmlspecialchars($data['nama_pelanggan']); ?><br>
                  <small class="text-muted"><?= htmlspecialchars($data['email']); ?></small><br>
                  <small class="text-muted"><?= htmlspecialchars($data['no_telepon']); ?></small>
                </td>
                <td><?= nl2br(htmlspecialchars($data['alamat_pengiriman'])); ?></td>
                <td><?= date('d/m/Y H:i', strtotime($data['tanggal_pesanan'])); ?></td>
                <td>Rp <?= number_format($data['ongkos_kirim'], 0, ',', '.'); ?></td>
                <td>Rp <?= number_format($data['total_harga'], 0, ',', '.'); ?></td>
                <td><span class="badge badge-<?= $warna; ?>"><?= htmlspecialchars(ucfirst($data['status_pesanan'])); ?></span></td>
                <td>
                  <a href="pesanan_form.php?aksi=edit&id=<?= $data['id_pesanan']; ?>" class="btn btn-warning btn-sm mb-1"><i class="fas fa-edit"></i> Edit</a>
                  <a href="pesanan_form.php?aksi=hapus&id=<?= $data['id_pesanan']; ?>" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Hapus pesanan ini?')"><i class="fas fa-trash"></i> Hapus</a>
                </td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>