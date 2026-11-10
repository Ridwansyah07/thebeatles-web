<?php
require 'koneksi.php';

//Pilih aksi
$aksi = $_GET['aksi'] ?? 'tambah';
if (!in_array($aksi, ['tambah', 'edit', 'hapus'])) {
    $aksi = 'tambah';
}

$id   = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$data = null;

//Hapus
if ($aksi == 'hapus') {
    $stmt = mysqli_prepare($koneksi, "DELETE FROM pesanan WHERE id_pesanan = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Pesanan berhasil dihapus!'); window.location='pesanan.php';</script>";
    } else {
        echo "<script>alert('Pesanan gagal dihapus!'); window.location='pesanan.php';</script>";
    }
    exit;
}

//Edit
if ($aksi == 'edit') {
    $stmt_select = mysqli_prepare($koneksi, "SELECT * FROM pesanan WHERE id_pesanan = ?");
    mysqli_stmt_bind_param($stmt_select, "i", $id);
    mysqli_stmt_execute($stmt_select);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_select));

    if (!$data) {
        echo "<script>alert('Data pesanan tidak ditemukan!'); window.location='pesanan.php';</script>";
        exit;
    }
}

//Submit form
if (isset($_POST['simpan'])) {
    $kode_pesanan      = $_POST['kode_pesanan'];
    $nama_pelanggan    = $_POST['nama_pelanggan'];
    $email             = $_POST['email'];
    $no_telepon        = $_POST['no_telepon'];
    $alamat_pengiriman = $_POST['alamat_pengiriman'];
    $tanggal_pesanan   = date('Y-m-d H:i:s', strtotime($_POST['tanggal_pesanan']));
    $ongkos_kirim      = $_POST['ongkos_kirim'];
    $total_harga       = $_POST['total_harga'];
    $status_pesanan    = $_POST['status_pesanan'];

    //Kode pesanan harus unik (kecuali milik pesanan ini sendiri saat edit)
    $stmt_cek = mysqli_prepare($koneksi, "SELECT id_pesanan FROM pesanan WHERE kode_pesanan = ? AND id_pesanan != ?");
    mysqli_stmt_bind_param($stmt_cek, "si", $kode_pesanan, $id);
    mysqli_stmt_execute($stmt_cek);
    if (mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_cek))) {
        echo "<script>alert('Kode pesanan sudah dipakai. Gunakan kode lain.'); window.history.back();</script>";
        exit;
    }

    if ($aksi == 'tambah') {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO pesanan (kode_pesanan, nama_pelanggan, email, no_telepon, alamat_pengiriman, tanggal_pesanan, ongkos_kirim, total_harga, status_pesanan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssssssdds", $kode_pesanan, $nama_pelanggan, $email, $no_telepon, $alamat_pengiriman, $tanggal_pesanan, $ongkos_kirim, $total_harga, $status_pesanan);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Pesanan berhasil ditambahkan!'); window.location='pesanan.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan pesanan!'); window.location='pesanan_form.php?aksi=tambah';</script>";
        }
        exit;
    }

    if ($aksi == 'edit') {
        $stmt = mysqli_prepare($koneksi, "UPDATE pesanan SET kode_pesanan=?, nama_pelanggan=?, email=?, no_telepon=?, alamat_pengiriman=?, tanggal_pesanan=?, ongkos_kirim=?, total_harga=?, status_pesanan=? WHERE id_pesanan=?");
        mysqli_stmt_bind_param($stmt, "ssssssddsi", $kode_pesanan, $nama_pelanggan, $email, $no_telepon, $alamat_pengiriman, $tanggal_pesanan, $ongkos_kirim, $total_harga, $status_pesanan, $id);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Pesanan berhasil diperbarui!'); window.location='pesanan.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui pesanan!'); window.location='pesanan_form.php?aksi=edit&id=$id';</script>";
        }
        exit;
    }
}

//Nilai awal form (kosong untuk tambah, terisi untuk edit)
$v = fn($key, $default = '') => $data ? htmlspecialchars($data[$key] ?? $default) : $default;

//Nilai bawaan khusus: kode otomatis & waktu sekarang untuk pesanan baru
$kode_awal    = $data ? htmlspecialchars($data['kode_pesanan']) : 'PSN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
$tanggal_awal = date('Y-m-d\TH:i', $data ? strtotime($data['tanggal_pesanan']) : time());

include 'header.php';
include 'sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <h2><?= $aksi == 'edit' ? 'Edit Data Pesanan' : 'Tambah Pesanan Baru'; ?></h2>
    </div>
  </section>

  <section class="content">
    <div class="card">
      <div class="card-body">
        <form action="" method="POST">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Kode Pesanan</label>
                <input type="text" name="kode_pesanan" class="form-control" value="<?= $kode_awal; ?>" maxlength="30" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Tanggal Pesanan</label>
                <input type="datetime-local" name="tanggal_pesanan" class="form-control" value="<?= $tanggal_awal; ?>" required>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label>Nama Pelanggan</label>
            <input type="text" name="nama_pelanggan" class="form-control" value="<?= $v('nama_pelanggan'); ?>" maxlength="100" required placeholder="Masukkan nama pelanggan">
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= $v('email'); ?>" maxlength="100" required placeholder="Contoh: nama@email.com">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>No. Telepon</label>
                <input type="text" name="no_telepon" class="form-control" value="<?= $v('no_telepon'); ?>" maxlength="20" required placeholder="Contoh: 081234567890">
              </div>
            </div>
          </div>

          <div class="form-group">
            <label>Alamat Pengiriman</label>
            <textarea name="alamat_pengiriman" class="form-control" rows="3" required placeholder="Tuliskan alamat lengkap..."><?= $v('alamat_pengiriman'); ?></textarea>
          </div>

          <div class="row">
            <div class="col-md-4">
              <div class="form-group">
                <label>Ongkos Kirim (Rp)</label>
                <input type="number" name="ongkos_kirim" class="form-control" step="0.01" min="0" value="<?= $data ? floatval($data['ongkos_kirim']) : '0'; ?>" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Total Harga (Rp)</label>
                <input type="number" name="total_harga" class="form-control" step="0.01" min="0" value="<?= $data ? floatval($data['total_harga']) : '0'; ?>" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>Status Pesanan</label>
                <select name="status_pesanan" class="form-control" required>
                  <?php
                  $status_sekarang = $data['status_pesanan'] ?? 'pending';
                  $opsi_status = [
                      'pending'    => 'Pending',
                      'diproses'   => 'Diproses',
                      'dikirim'    => 'Dikirim',
                      'selesai'    => 'Selesai',
                      'dibatalkan' => 'Dibatalkan',
                  ];
                  foreach ($opsi_status as $nilai => $teks) {
                      $sel = ($status_sekarang == $nilai) ? 'selected' : '';
                      echo "<option value='$nilai' $sel>$teks</option>";
                  }
                  ?>
                </select>
              </div>
            </div>
          </div>

          <?php if ($aksi == 'edit') : ?>
            <button type="submit" name="simpan" class="btn btn-warning"><i class="fas fa-edit"></i> Update Data</button>
          <?php else : ?>
            <button type="submit" name="simpan" class="btn btn-success"><i class="fas fa-save"></i> Simpan Data</button>
          <?php endif; ?>
          <a href="pesanan.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
        </form>
      </div>
    </div>
  </section>
</div>