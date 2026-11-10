<?php
require 'koneksi.php';

// Pilih aksi
$aksi = $_GET['aksi'] ?? 'tambah';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$data = null;

// Validasi aksi
if (!in_array($aksi, ['tambah', 'edit', 'hapus'])) {
    $aksi = 'tambah';
}

// Hapus data
if ($aksi == 'hapus') {
    $stmt = mysqli_prepare($koneksi, "DELETE FROM roles WHERE id_role=?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Data role berhasil dihapus!'); window.location='roles.php';</script>";
    } else {
        echo "<script>alert('Data role gagal dihapus!'); window.location='roles.php';</script>";
    }
    exit;
}

// Ambil data untuk diedit
if ($aksi == 'edit') {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM roles WHERE id_role=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    if (!$data) {
        echo "<script>alert('Data role tidak ditemukan!'); window.location='roles.php';</script>";
        exit;
    }
}

// Simpan data
if (isset($_POST['simpan'])) {
    $nama_role = trim($_POST['nama_role']);

    if (empty($nama_role)) {
        echo "<script>alert('Nama role harus diisi!');</script>";
    } else {
        // Tambah data
        if ($aksi == 'tambah') {
            $stmt = mysqli_prepare($koneksi, "INSERT INTO roles (nama_role) VALUES (?)");
            mysqli_stmt_bind_param($stmt, "s", $nama_role);

            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Data role berhasil ditambahkan!'); window.location='roles.php';</script>";
            } else {
                echo "<script>alert('Data role gagal ditambahkan!');</script>";
            }
            exit;
        }

        // Perbarui data
        if ($aksi == 'edit') {
            $stmt = mysqli_prepare($koneksi, "UPDATE roles SET nama_role=? WHERE id_role=?");
            mysqli_stmt_bind_param($stmt, "si", $nama_role, $id);

            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Data role berhasil diperbarui!'); window.location='roles.php';</script>";
            } else {
                echo "<script>alert('Data role gagal diperbarui!');</script>";
            }
            exit;
        }
    }
}

$v = fn($key) => htmlspecialchars($data[$key] ?? '', ENT_QUOTES, 'UTF-8');

include 'header.php';
include 'sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h2><?= $aksi == 'edit' ? 'Edit Data Role' : 'Tambah Role Baru'; ?></h2>
        </div>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-body">
                <form action="" method="POST">
                    <div class="form-group">
                        <label>Nama Role</label>
                        <input type="text" name="nama_role" class="form-control"
                               value="<?= $aksi == 'edit' ? $v('nama_role') : ''; ?>"
                               placeholder="Masukkan nama role"
                               maxlength="100" required>
                    </div>

                    <?php if ($aksi == 'edit') : ?>
                        <button type="submit" name="simpan" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Perbarui Data
                        </button>
                    <?php else : ?>
                        <button type="submit" name="simpan" class="btn btn-success">
                            <i class="fas fa-save"></i> Simpan Data
                        </button>
                    <?php endif; ?>

                    <a href="roles.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                </form>
            </div>
        </div>
    </section>
</div>
