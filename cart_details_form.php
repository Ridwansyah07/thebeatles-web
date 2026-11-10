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
    $stmt = mysqli_prepare($koneksi, "DELETE FROM cart_details WHERE id_cart_detail=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: cart_details.php");
    exit;
}

// Ambil data untuk edit
if ($aksi == 'edit') {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM cart_details WHERE id_cart_detail=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($hasil);
    mysqli_stmt_close($stmt);

    if (!$data) {
        header("Location: cart_details.php");
        exit;
    }
}

// Proses simpan
if (isset($_POST['simpan'])) {
    $id_cart = (int) $_POST['id_cart'];
    $id_merch = (int) $_POST['id_merch'];
    $jumlah = (int) $_POST['jumlah'];

    if ($id_cart <= 0 || $id_merch <= 0 || $jumlah <= 0) {
        $pesan = "Semua data wajib diisi dengan benar.";
    } else {
        if ($aksi == 'tambah') {
            $stmt = mysqli_prepare($koneksi, "
                INSERT INTO cart_details (id_cart, id_merch, jumlah)
                VALUES (?, ?, ?)
            ");
            mysqli_stmt_bind_param($stmt, "iii", $id_cart, $id_merch, $jumlah);
        } else {
            $stmt = mysqli_prepare($koneksi, "
                UPDATE cart_details
                SET id_cart=?, id_merch=?, jumlah=?
                WHERE id_cart_detail=?
            ");
            mysqli_stmt_bind_param($stmt, "iiii", $id_cart, $id_merch, $jumlah, $id);
        }

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: cart_details.php");
            exit;
        } else {
            $pesan = "Data detail cart gagal disimpan. Periksa pilihan cart dan merchandise.";
            mysqli_stmt_close($stmt);
        }
    }
}

// Ambil pilihan cart
$daftar_cart = mysqli_query($koneksi, "
    SELECT id_cart, session_id FROM cart ORDER BY id_cart DESC
");

// Ambil pilihan merchandise
$daftar_merch = mysqli_query($koneksi, "
    SELECT id_merch FROM merchandise ORDER BY id_merch DESC
");

include 'header.php';
include 'sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h2><?= $aksi == 'edit' ? 'Edit Detail Cart' : 'Tambah Detail Cart'; ?></h2>
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
                        <label>Cart</label>
                        <select name="id_cart" class="form-control" required>
                            <option value="">-- Pilih Cart --</option>
                            <?php while ($cart = mysqli_fetch_assoc($daftar_cart)) : ?>
                                <option value="<?= $cart['id_cart']; ?>"
                                    <?= (($_POST['id_cart'] ?? $data['id_cart'] ?? '') == $cart['id_cart']) ? 'selected' : ''; ?>>
                                    Cart #<?= $cart['id_cart']; ?> - <?= htmlspecialchars($cart['session_id']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Merchandise</label>
                        <select name="id_merch" class="form-control" required>
                            <option value="">-- Pilih Merchandise --</option>
                            <?php while ($merch = mysqli_fetch_assoc($daftar_merch)) : ?>
                                <option value="<?= $merch['id_merch']; ?>"
                                    <?= (($_POST['id_merch'] ?? $data['id_merch'] ?? '') == $merch['id_merch']) ? 'selected' : ''; ?>>
                                    Merchandise #<?= $merch['id_merch']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jumlah</label>
                        <input type="number" name="jumlah" class="form-control"
                            value="<?= htmlspecialchars($_POST['jumlah'] ?? $data['jumlah'] ?? '1'); ?>"
                            min="1" required>
                    </div>
                    <button type="submit" name="simpan" class="btn btn-success">
                        <i class="fas fa-save"></i>
                        <?= $aksi == 'edit' ? 'Perbarui Data' : 'Simpan Data'; ?>
                    </button>
                    <a href="cart_details.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                </form>
            </div>
        </div>
    </section>
</div>
