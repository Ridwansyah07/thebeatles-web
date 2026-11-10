<?php
require 'koneksi.php';

// Pilih aksi
$aksi = $_GET['aksi'] ?? 'tambah';
if (!in_array($aksi, ['tambah', 'edit', 'hapus'])) {
    $aksi = 'tambah';
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$data = null;

// Fungsi upload foto (dipakai oleh tambah & edit)
function uploadFoto($nama, $foto_lama = "")
{
    $target_dir = "img/";

    if (!isset($_FILES["foto"]) || $_FILES["foto"]["error"] == 4) {
        return $foto_lama;
    }

    if ($_FILES["foto"]["error"] != 0) {
        echo "<script>alert('Terjadi Kesalahan saat Mengunggah Foto.'); window.history.back();</script>";
        exit;
    }

    $file_name = basename($_FILES["foto"]["name"]);
    $imageFileType = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Membuat nama file berdasarkan nama member
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nama), '-'));
    $new_filename = $slug . "_" . time() . "." . $imageFileType;
    $target_file = $target_dir . $new_filename;

    // Cek apakah benar-benar gambar
    if (getimagesize($_FILES["foto"]["tmp_name"]) === false) {
        echo "<script>alert('File Bukan Berupa Gambar.'); window.history.back();</script>";
        exit;
    }

    // Cek ukuran file (maksimal 2MB)
    if ($_FILES["foto"]["size"] > 2000000) {
        echo "<script>alert('Ukuran File Terlalu Besar. Maksimal 2MB.'); window.history.back();</script>";
        exit;
    }

    // Cek format file
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
        echo "<script>alert('Harap Unggah File Dengan Ekstensi JPG, JPEG, dan PNG.'); window.history.back();</script>";
        exit;
    }

    // Pindahkan file
    if (!move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
        echo "<script>alert('Terjadi Kesalahan saat Mengunggah Foto.'); window.history.back();</script>";
        exit;
    }

    return $new_filename;
}

// Hapus data
if ($aksi == 'hapus') {
    $stmt = mysqli_prepare($koneksi, "DELETE FROM members WHERE id_member = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Data member berhasil dihapus!'); window.location='members.php';</script>";
    } else {
        echo "<script>alert('Data member gagal dihapus!'); window.location='members.php';</script>";
    }
    exit;
}

// Edit data
if ($aksi == 'edit') {
    $stmt_select = mysqli_prepare($koneksi, "SELECT * FROM members WHERE id_member = ?");
    mysqli_stmt_bind_param($stmt_select, "i", $id);
    mysqli_stmt_execute($stmt_select);
    $result = mysqli_stmt_get_result($stmt_select);
    $data = mysqli_fetch_assoc($result);

    if (!$data) {
        echo "<script>alert('Data member tidak ditemukan!'); window.location='members.php';</script>";
        exit;
    }
}

// Submit form
if (isset($_POST['simpan'])) {
    $nama = trim($_POST['nama']);
    $info_kelahiran = trim($_POST['info_kelahiran']);
    $bio = trim($_POST['bio']);

    // Tambah data
    if ($aksi == 'tambah') {
        $nama_foto = uploadFoto($nama);

        $stmt = mysqli_prepare($koneksi, "INSERT INTO members (nama, info_kelahiran, foto_url, bio) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $nama, $info_kelahiran, $nama_foto, $bio);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Data member berhasil ditambahkan!'); window.location='members.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan data member!'); window.location='members_form.php?aksi=tambah';</script>";
        }
        exit;
    }

    // Edit data
    if ($aksi == 'edit') {
        $nama_foto = uploadFoto($nama, $data['foto_url']);

        $stmt = mysqli_prepare($koneksi, "UPDATE members SET nama=?, info_kelahiran=?, foto_url=?, bio=? WHERE id_member=?");
        mysqli_stmt_bind_param($stmt, "ssssi", $nama, $info_kelahiran, $nama_foto, $bio, $id);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Data member berhasil diperbarui!'); window.location='members.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui data member!'); window.location='members_form.php?aksi=edit&id=$id';</script>";
        }
        exit;
    }
}

// Nilai awal form
$v = fn($key) => $data ? htmlspecialchars($data[$key] ?? '') : '';

include 'header.php';
include 'sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h2><?= $aksi == 'edit' ? 'Edit Data Member' : 'Tambah Member Baru'; ?></h2>
        </div>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Nama Member</label>
                        <input type="text" name="nama" class="form-control"
                               value="<?= $v('nama'); ?>"
                               placeholder="Masukkan nama member"
                               maxlength="100" required>
                    </div>

                    <div class="form-group">
                        <label>Informasi Kelahiran</label>
                        <input type="text" name="info_kelahiran" class="form-control"
                               value="<?= $v('info_kelahiran'); ?>"
                               placeholder="Contoh: Liverpool, 9 Oktober 1940"
                               maxlength="150">
                    </div>

                    <div class="form-group">
                        <label>
                            <?= $aksi == 'edit' ? 'Update Foto Member (Biarkan kosong jika tidak ingin ganti)' : 'Upload Foto Member'; ?>
                        </label><br>
                        <?php if ($aksi == 'edit' && !empty($data['foto_url'])) : ?>
                            <img src="img/<?= htmlspecialchars($data['foto_url']); ?>" width="80" class="mb-2 rounded">
                        <?php endif; ?>
                        <input type="file" name="foto" class="form-control" accept="image/png, image/jpeg, image/jpg">
                        <small class="text-muted">Format yang diizinkan: JPG, JPEG, PNG.</small>
                    </div>

                    <div class="form-group">
                        <label>Biografi</label>
                        <textarea name="bio" class="form-control" rows="5"
                                  placeholder="Tuliskan biografi member..."><?= $v('bio'); ?></textarea>
                    </div>

                    <?php if ($aksi == 'edit') : ?>
                        <button type="submit" name="simpan" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Update Data
                        </button>
                    <?php else : ?>
                        <button type="submit" name="simpan" class="btn btn-success">
                            <i class="fas fa-save"></i> Simpan Data
                        </button>
                    <?php endif; ?>

                    <a href="members.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                </form>
            </div>
        </div>
    </section>
</div>
