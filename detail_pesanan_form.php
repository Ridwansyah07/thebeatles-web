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
    $stmt = mysqli_prepare($koneksi, "DELETE FROM detail_pesanan WHERE id_detail=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: detail_pesanan.php");
    exit;
}

// Ambil data untuk edit
if ($aksi == 'edit') {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM detail_pesanan WHERE id_detail=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($hasil);
    mysqli_stmt_close($stmt);

    if (!$data) {
        header("Location: detail_pesanan.php");
        exit;
    }
}

// Proses simpan
if (isset($_POST['simpan'])) {
    $id_pesanan = (int) $_POST['id_pesanan'];
    $id_merch = (int) $_POST['id_merch'];
    $jumlah = (int) $_POST['jumlah'];
    $harga_satuan = (float) $_POST['harga_satuan'];
    $subtotal = $jumlah * $harga_satuan;

    if ($id_pesanan <= 0 || $id_merch <= 0 || $jumlah <= 0 || $harga_satuan < 0) {
        $pesan = "Semua data wajib diisi dengan benar.";
    } else {
        if ($aksi == 'tambah') {
            $stmt = mysqli_prepare($koneksi, "
                INSERT INTO detail_pesanan
                (id_pesanan, id_merch, jumlah, harga_satuan, subtotal)
                VALUES (?, ?, ?, ?, ?)
            ");
            mysqli_stmt_bind_param($stmt, "iiidd", $id_pesanan, $id_merch, $jumlah, $harga_satuan, $subtotal);
        } else {
            $stmt = mysqli_prepare($koneksi, "
                UPDATE detail_pesanan
                SET id_pesanan=?, id_merch=?, jumlah=?, harga_satuan=?, subtotal=?
                WHERE id_detail=?
            ");
            mysqli_stmt_bind_param($stmt, "iiiddi", $id_pesanan, $id_merch, $jumlah, $harga_satuan, $subtotal, $id);
        }

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: detail_pesanan.php");
            exit;
        } else {
            $pesan = "Data detail pesanan gagal disimpan. Periksa pilihan pesanan dan merchandise.";
            mysqli_stmt_close($stmt);
        }
    }
}

// Ambil pilihan pesanan
$daftar_pesanan = mysqli_query($koneksi, "
    SELECT id_pesanan, kode_pesanan
    FROM pesanan
    ORDER BY id_pesanan DESC
");

// Ambil pilihan merchandise
$daftar_merch = mysqli_query($koneksi, "
    SELECT id_merch
    FROM merchandise
    ORDER BY id_merch DESC
");

include 'header.php';
include 'sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h2><?= $aksi == 'edit' ? 'Edit Detail Pesanan' : 'Tambah Detail Pesanan'; ?></h2>
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
                        <label>Pesanan</label>
                        <select name="id_pesanan" class="form-control" required>
                            <option value="">-- Pilih Pesanan --</option>
                            <?php while ($pesanan = mysqli_fetch_assoc($daftar_pesanan)) : ?>
                                <option value="<?= $pesanan['id_pesanan']; ?>"
                                    <?= (($_POST['id_pesanan'] ?? $data['id_pesanan'] ?? '') == $pesanan['id_pesanan']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($pesanan['kode_pesanan']); ?>
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
                    <div class="form-group">
                        <label>Harga Satuan (Rp)</label>
                        <input type="number" name="harga_satuan" class="form-control"
                            value="<?= htmlspecialchars($_POST['harga_satuan'] ?? $data['harga_satuan'] ?? ''); ?>"
                            min="0" step="0.01" placeholder="Masukkan harga satuan" required>
                    </div>
                    <button type="submit" name="simpan" class="btn btn-success">
                        <i class="fas fa-save"></i>
                        <?= $aksi == 'edit' ? 'Perbarui Data' : 'Simpan Data'; ?>
                    </button>
                    <a href="detail_pesanan.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                </form>
            </div>
        </div>
    </section>
</div>