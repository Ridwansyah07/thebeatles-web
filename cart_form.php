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
    $stmt = mysqli_prepare($koneksi, "DELETE FROM cart WHERE id_cart=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: cart.php");
    exit;
}

// Ambil data untuk edit
if ($aksi == 'edit') {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM cart WHERE id_cart=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($hasil);
    mysqli_stmt_close($stmt);

    if (!$data) {
        header("Location: cart.php");
        exit;
    }
}

// Proses simpan
if (isset($_POST['simpan'])) {
    $session_id = trim($_POST['session_id']);

    if ($session_id == '') {
        $pesan = "Session ID wajib diisi.";
    } else {
        if ($aksi == 'tambah') {
            $stmt = mysqli_prepare($koneksi, "
                INSERT INTO cart (session_id)
                VALUES (?)
            ");
            mysqli_stmt_bind_param($stmt, "s", $session_id);
        } else {
            $stmt = mysqli_prepare($koneksi, "
                UPDATE cart SET session_id=?
                WHERE id_cart=?
            ");
            mysqli_stmt_bind_param($stmt, "si", $session_id, $id);
        }

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: cart.php");
            exit;
        } else {
            $pesan = "Data cart gagal disimpan.";
            mysqli_stmt_close($stmt);
        }
    }
}

include 'header.php';
include 'sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h2><?= $aksi == 'edit' ? 'Edit Cart' : 'Tambah Cart Baru'; ?></h2>
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
                        <label>Session ID</label>
                        <input type="text" name="session_id" class="form-control"
                            value="<?= htmlspecialchars($_POST['session_id'] ?? $data['session_id'] ?? ''); ?>"
                            placeholder="Masukkan session ID" required>
                    </div>
                    <button type="submit" name="simpan" class="btn btn-success">
                        <i class="fas fa-save"></i>
                        <?= $aksi == 'edit' ? 'Perbarui Data' : 'Simpan Data'; ?>
                    </button>
                    <a href="cart.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                </form>
            </div>
        </div>
    </section>
</div>
