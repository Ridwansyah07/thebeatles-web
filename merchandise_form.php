<?php
require 'koneksi.php';

//pilih aki
$aksi = $_GET['aksi'] ?? 'tambah';
if (!in_array($aksi, ['tambah', 'edit', 'hapus'])) {
    $aksi = 'tambah';
}

$id   = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$data = null;

//Fungsi upload gambar (dipakai oleh tambah & edit).
//Mengembalikan nama file baru jika sukses, atau $gambar_lama jika tidak ada file diunggah.
function uploadGambar($nama_produk, $gambar_lama = null)
{
    $target_dir = "img/";

    if (!isset($_FILES["foto"]) || $_FILES["foto"]["error"] != 0) {
        return $gambar_lama;
    }

    $file_name     = basename($_FILES["foto"]["name"]);
    $imageFileType = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Nama file aman (spasi & simbol dari nama produk diganti "-")
    $nama_aman    = preg_replace('/[^a-zA-Z0-9_-]/', '-', strtolower($nama_produk));
    $new_filename = "merch_" . $nama_aman . "_" . time() . "." . $imageFileType;
    $target_file  = $target_dir . $new_filename;

    // Cek apakah benar-benar gambar
    if (getimagesize($_FILES["foto"]["tmp_name"]) === false) {
        echo "<script>alert('File Bukan Berupa Gambar.'); window.history.back();</script>";
        exit;
    }

    // Cek ukuran file (maks 2MB)
    if ($_FILES["foto"]["size"] > 2000000) {
        echo "<script>alert('Ukuran File Terlalu Besar. Maksimal 2MB.'); window.history.back();</script>";
        exit;
    }

    // Whitelist format
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
        echo "<script>alert('Harap Unggah File Dengan Ekstensi JPG, JPEG, dan PNG'); window.history.back();</script>";
        exit;
    }

    // Pindahkan file
    if (!move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
        echo "<script>alert('Terjadi Kesalahan saat Mengunggah Foto..'); window.history.back();</script>";
        exit;
    }

    return $new_filename;
}

//Hapus
if ($aksi == 'hapus') {
    $stmt = mysqli_prepare($koneksi, "DELETE FROM merchandise WHERE id_merch = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Merchandise berhasil dihapus!'); window.location='merchandise.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus merchandise!'); window.location='merchandise.php';</script>";
    }
    exit;
}

//Edit
if ($aksi == 'edit') {
    $stmt_select = mysqli_prepare($koneksi, "SELECT * FROM merchandise WHERE id_merch = ?");
    mysqli_stmt_bind_param($stmt_select, "i", $id);
    mysqli_stmt_execute($stmt_select);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_select));

    if (!$data) {
        echo "<script>alert('Data merchandise tidak ditemukan!'); window.location='merchandise.php';</script>";
        exit;
    }
}

//Submit Form
if (isset($_POST['simpan'])) {
    $id_album     = !empty($_POST['id_album']) ? $_POST['id_album'] : NULL;
    $nama_produk  = $_POST['nama_produk'];
    $deskripsi    = $_POST['deskripsi'];
    $harga        = $_POST['harga'];
    $label_status = $_POST['label_status'];
    $stok         = $_POST['stok'];

    if ($aksi == 'tambah') {
        $gambar_url = uploadGambar($nama_produk);

        $stmt = mysqli_prepare($koneksi, "INSERT INTO merchandise (id_album, nama_produk, deskripsi, harga, gambar_url, label_status, stok) VALUES (?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "issdssi", $id_album, $nama_produk, $deskripsi, $harga, $gambar_url, $label_status, $stok);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Merchandise berhasil ditambahkan!'); window.location='merchandise.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan merchandise!'); window.location='merchandise_form.php?aksi=tambah';</script>";
        }
        exit;
    }

    if ($aksi == 'edit') {
        $gambar_url = uploadGambar($nama_produk, $data['gambar_url']);

        $stmt = mysqli_prepare($koneksi, "UPDATE merchandise SET id_album=?, nama_produk=?, deskripsi=?, harga=?, gambar_url=?, label_status=?, stok=? WHERE id_merch=?");
        mysqli_stmt_bind_param($stmt, "issdssii", $id_album, $nama_produk, $deskripsi, $harga, $gambar_url, $label_status, $stok, $id);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Merchandise berhasil diperbarui!'); window.location='merchandise.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui merchandise!'); window.location='merchandise_form.php?aksi=edit&id=$id';</script>";
        }
        exit;
    }
}

//Nilai awal form (kosong untuk tambah, terisi untuk edit)
$v = fn($key, $default = '') => $data ? htmlspecialchars($data[$key] ?? $default) : $default;

include 'header.php';
include 'sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <h2><?= $aksi == 'edit' ? 'Edit Data Merchandise' : 'Tambah Merchandise Baru'; ?></h2>
    </div>
  </section>

  <section class="content">
    <div class="card">
      <div class="card-body">
        <form action="" method="POST" enctype="multipart/form-data">
          <div class="form-group">
            <label>Nama Produk</label>
            <input type="text" name="nama_produk" class="form-control" value="<?= $v('nama_produk'); ?>" required>
          </div>
          <div class="form-group">
            <label>Terkait Album <?= $aksi == 'tambah' ? '(Opsional)' : ''; ?></label>
            <select name="id_album" class="form-control">
              <option value="">-- Tidak ada album (Merchandise Umum) --</option>
              <?php
              $q_album = mysqli_query($koneksi, "SELECT id_album, judul FROM albums ORDER BY judul ASC");
              while ($album = mysqli_fetch_assoc($q_album)) {
                  $selected = ($data && $album['id_album'] == $data['id_album']) ? 'selected' : '';
                  echo "<option value='{$album['id_album']}' {$selected}>" . htmlspecialchars($album['judul']) . "</option>";
              }
              ?>
            </select>
          </div>
          <div class="form-group">
            <label>Deskripsi Produk</label>
            <textarea name="deskripsi" class="form-control" rows="3"><?= $v('deskripsi'); ?></textarea>
          </div>
          <div class="form-group">
            <label>Harga (Rp)</label>
            <input type="number" name="harga" class="form-control" step="0.01" value="<?= $data ? floatval($data['harga']) : ''; ?>" required>
          </div>

          <div class="form-group">
            <label>
              <?= $aksi == 'edit' ? 'Update Gambar Merchandise (Biarkan kosong jika tidak ganti)' : 'Upload Gambar Merchandise (Opsional)'; ?>
            </label><br>
            <?php if ($aksi == 'edit' && !empty($data['gambar_url'])) : ?>
                <img src="img/<?= htmlspecialchars($data['gambar_url']); ?>" width="80" class="mb-2 rounded border">
            <?php endif; ?>
            <input type="file" name="foto" class="form-control" accept="image/png, image/jpeg, image/jpg">
            <small class="text-muted">Format yang diizinkan: JPG, JPEG, PNG. Maksimal 2MB.</small>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Status</label>
                <select name="label_status" class="form-control" required>
                  <?php
                  $status_sekarang = $data['label_status'] ?? 'available';
                  $opsi_status = ['available' => 'Available (Tersedia)', 'sold out' => 'Sold Out (Habis)', 'pre-order' => 'Pre-Order'];
                  foreach ($opsi_status as $nilai => $teks) {
                      $sel = ($status_sekarang == $nilai) ? 'selected' : '';
                      echo "<option value='$nilai' $sel>$teks</option>";
                  }
                  ?>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label><?= $aksi == 'edit' ? 'Stok' : 'Stok Awal'; ?></label>
                <input type="number" name="stok" class="form-control" value="<?= $v('stok', '0'); ?>" required>
              </div>
            </div>
          </div>

          <?php if ($aksi == 'edit') : ?>
            <button type="submit" name="simpan" class="btn btn-warning"><i class="fas fa-edit"></i> Update Data</button>
          <?php else : ?>
            <button type="submit" name="simpan" class="btn btn-success"><i class="fas fa-save"></i> Simpan Data</button>
          <?php endif; ?>
          <a href="merchandise.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
        </form>
      </div>
    </div>
  </section>
</div>
