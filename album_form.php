<?php
require_once __DIR__ . '/koneksi.php';

//pilih aksi
$aksi = $_GET['aksi'] ?? 'tambah';
if (!in_array($aksi, ['tambah', 'edit', 'hapus'])) {
    $aksi = 'tambah';
}

$id   = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$data = null;

//fungsi upload foto (dipakai oleh tambah & edit).
//mengembalikan nama file baru jika sukses, atau $foto_lama jika tidak ada file diunggah.
 
function uploadFoto($slug, $foto_lama = "")
{
    $target_dir = "img/";

    if (!isset($_FILES["foto"]) || $_FILES["foto"]["error"] != 0) {
        return $foto_lama;
    }

    $file_name     = basename($_FILES["foto"]["name"]);
    $imageFileType = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $new_filename  = $slug . "_" . time() . "." . $imageFileType;
    $target_file   = $target_dir . $new_filename;

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
    $stmt = mysqli_prepare($koneksi, "DELETE FROM albums WHERE id_album = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Data berhasil dihapus!'); window.location='album.php';</script>";
    } else {
        echo "<script>alert('Data gagal dihapus!'); window.location='album.php';</script>";
    }
    exit;
}

//Edit
if ($aksi == 'edit') {
    $stmt_select = mysqli_prepare($koneksi, "SELECT * FROM albums WHERE id_album = ?");
    mysqli_stmt_bind_param($stmt_select, "i", $id);
    mysqli_stmt_execute($stmt_select);
    $result = mysqli_stmt_get_result($stmt_select);
    $data   = mysqli_fetch_assoc($result);

    if (!$data) {
        echo "<script>alert('Data album tidak ditemukan!'); window.location='album.php';</script>";
        exit;
    }
}

//Submit Form
if (isset($_POST['simpan'])) {
    $slug         = $_POST['slug'];
    $judul        = $_POST['judul'];
    $tahun_rilis  = $_POST['tahun_rilis'];
    $jumlah_views = $_POST['jumlah_views'];
    $deskripsi    = $_POST['deskripsi'];

    if ($aksi == 'tambah') {
        $nama_foto = uploadFoto($slug);

        $stmt = mysqli_prepare($koneksi, "INSERT INTO albums (slug, judul, tahun_rilis, jumlah_views, deskripsi, cover_url) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssssss", $slug, $judul, $tahun_rilis, $jumlah_views, $deskripsi, $nama_foto);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Data album berhasil ditambahkan!'); window.location='album.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan data!'); window.location='album_form.php?aksi=tambah';</script>";
        }
        exit;
    }

    if ($aksi == 'edit') {
        $nama_foto = uploadFoto($slug, $data['cover_url']);

        $stmt = mysqli_prepare($koneksi, "UPDATE albums SET slug=?, judul=?, tahun_rilis=?, jumlah_views=?, deskripsi=?, cover_url=? WHERE id_album=?");
        mysqli_stmt_bind_param($stmt, "ssssssi", $slug, $judul, $tahun_rilis, $jumlah_views, $deskripsi, $nama_foto, $id);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Data album berhasil diperbarui!'); window.location='album.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui data!'); window.location='album_form.php?aksi=edit&id=$id';</script>";
        }
        exit;
    }
}

//Nilai awal form (kosong untuk tambah, terisi untuk edit)
$v = fn($key) => $data ? htmlspecialchars($data[$key] ?? '') : '';

include 'header.php';
include 'sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <h2><?= $aksi == 'edit' ? 'Edit Data Album' : 'Tambah Album Baru'; ?></h2>
    </div>
  </section>

  <section class="content">
    <div class="card">
      <div class="card-body">
        <form action="" method="POST" enctype="multipart/form-data">
          <div class="form-group">
            <label>Judul Album</label>
            <input type="text" name="judul" class="form-control" value="<?= $v('judul'); ?>" required placeholder="Masukkan judul album">
          </div>
          <div class="form-group">
            <label>Slug (URL Ramah)</label>
            <input type="text" name="slug" class="form-control" value="<?= $v('slug'); ?>" required placeholder="contoh: abbey-road">
          </div>
          <div class="form-group">
            <label>Tahun Rilis</label>
            <input type="number" name="tahun_rilis" class="form-control" value="<?= $v('tahun_rilis'); ?>" required placeholder="Contoh: 1969" min="1900" max="2100">
          </div>
          <div class="form-group">
            <label>Jumlah Views</label>
            <input type="number" name="jumlah_views" class="form-control" value="<?= $v('jumlah_views'); ?>" required placeholder="Contoh: 1000000">
          </div>
          <div class="form-group">
            <label>Deskripsi Album</label>
            <textarea name="deskripsi" class="form-control" rows="4" placeholder="Tambahkan deskripsi..."><?= $v('deskripsi'); ?></textarea>
          </div>

          <div class="form-group">
            <label>
              <?= $aksi == 'edit' ? 'Update Foto Album (Biarkan kosong jika tidak ingin ganti)' : 'Upload Foto Album'; ?>
            </label><br>
            <?php if ($aksi == 'edit' && !empty($data['cover_url'])) : ?>
                <img src="img/<?= htmlspecialchars($data['cover_url']); ?>" width="80" class="mb-2 rounded">
            <?php endif; ?>
            <input type="file" name="foto" class="form-control" accept="image/png, image/jpeg, image/jpg">
            <small class="text-muted">Format yang diizinkan: JPG, JPEG, PNG.</small>
          </div>

          <?php if ($aksi == 'edit') : ?>
            <button type="submit" name="simpan" class="btn btn-warning"><i class="fas fa-edit"></i> Update Data</button>
          <?php else : ?>
            <button type="submit" name="simpan" class="btn btn-success"><i class="fas fa-save"></i> Simpan Data</button>
          <?php endif; ?>
          <a href="album.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
        </form>
      </div>
    </div>
  </section>
</div>