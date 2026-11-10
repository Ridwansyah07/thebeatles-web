<?php
require_once __DIR__ . '/koneksi.php';

$aksi = $_GET['aksi'] ?? 'tambah';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$data = null;

if (!in_array($aksi, ['tambah', 'edit', 'hapus'])) {
    $aksi = 'tambah';
}

// Proses hapus
if ($aksi == 'hapus') {
    $stmt = mysqli_prepare($koneksi, "DELETE FROM admin WHERE id_admin=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: admin.php");
    exit;
}

// Ambil data untuk edit
if ($aksi == 'edit') {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM admin WHERE id_admin=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($hasil);
    mysqli_stmt_close($stmt);

    if (!$data) {
        header("Location: admin.php");
        exit;
    }
}

// Proses simpan
if (isset($_POST['simpan'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($username == '') {
        $pesan = "Username wajib diisi.";
    } elseif ($aksi == 'tambah' && $password == '') {
        $pesan = "Password wajib diisi.";
    } else {
        // Periksa username ganda
        $stmt = mysqli_prepare($koneksi, "
            SELECT id_admin FROM admin
            WHERE username=? AND id_admin != ?
        ");
        mysqli_stmt_bind_param($stmt, "si", $username, $id);
        mysqli_stmt_execute($stmt);
        $hasil = mysqli_stmt_get_result($stmt);
        $duplikat = mysqli_num_rows($hasil) > 0;
        mysqli_stmt_close($stmt);

        if ($duplikat) {
            $pesan = "Username sudah digunakan.";
        } else {
            if ($aksi == 'tambah') {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = mysqli_prepare($koneksi, "
                    INSERT INTO admin (username, password)
                    VALUES (?, ?)
                ");
                mysqli_stmt_bind_param($stmt, "ss", $username, $password_hash);
            } else {
                if ($password != '') {
                    $password_hash = password_hash($password, PASSWORD_DEFAULT);

                    $stmt = mysqli_prepare($koneksi, "
                        UPDATE admin
                        SET username=?, password=?
                        WHERE id_admin=?
                    ");
                    mysqli_stmt_bind_param($stmt, "ssi", $username, $password_hash, $id);
                } else {
                    $stmt = mysqli_prepare($koneksi, "
                        UPDATE admin SET username=?
                        WHERE id_admin=?
                    ");
                    mysqli_stmt_bind_param($stmt, "si", $username, $id);
                }
            }

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("Location: admin.php");
                exit;
            } else {
                $pesan = "Data admin gagal disimpan.";
                mysqli_stmt_close($stmt);
            }
        }
    }
}

include 'header.php';
include 'sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h2><?= $aksi == 'edit' ? 'Edit Admin' : 'Tambah Admin Baru'; ?></h2>
        </div>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-body">
                <?php if (isset($pesan)) : ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($pesan); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control"
                            value="<?= htmlspecialchars($_POST['username'] ?? $data['username'] ?? ''); ?>"
                            placeholder="Masukkan username admin" required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control"
                            placeholder="<?= $aksi == 'edit' ? 'Kosongkan jika tidak ingin mengubah password' : 'Masukkan password'; ?>"
                            <?= $aksi == 'tambah' ? 'required' : ''; ?>>
                    </div>

                    <button type="submit" name="simpan" class="btn btn-success">
                        <i class="fas fa-save"></i>
                        <?= $aksi == 'edit' ? 'Perbarui Data' : 'Simpan Data'; ?>
                    </button>
                    <a href="admin.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                </form>
            </div>
        </div>
    </section>
</div>
