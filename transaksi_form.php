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
    $stmt = mysqli_prepare($koneksi, "DELETE FROM transaksi WHERE id_transaksi=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: transaksi.php");
    exit;
}

// Ambil data untuk edit
if ($aksi == 'edit') {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM transaksi WHERE id_transaksi=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($hasil);
    mysqli_stmt_close($stmt);

    if (!$data) {
        header("Location: transaksi.php");
        exit;
    }
}

// Proses simpan
if (isset($_POST['simpan'])) {
    $id_pesanan = (int) $_POST['id_pesanan'];
    $kode_transaksi = trim($_POST['kode_transaksi']);
    $total_transaksi = (float) $_POST['total_transaksi'];
    $status_transaksi = $_POST['status_transaksi'];

    $status_valid = ['pending', 'berhasil', 'gagal', 'dibatalkan'];

    if ($id_pesanan <= 0 || $kode_transaksi == '' || $total_transaksi < 0 || !in_array($status_transaksi, $status_valid)) {
        $pesan = "Semua data wajib diisi dengan benar.";
    } else {
        // Periksa kode transaksi dan pesanan ganda
        $stmt = mysqli_prepare($koneksi, "
            SELECT id_transaksi FROM transaksi
            WHERE (kode_transaksi=? OR id_pesanan=?)
            AND id_transaksi != ?
        ");
        mysqli_stmt_bind_param($stmt, "sii", $kode_transaksi, $id_pesanan, $id);
        mysqli_stmt_execute($stmt);
        $hasil = mysqli_stmt_get_result($stmt);
        $duplikat = mysqli_num_rows($hasil) > 0;
        mysqli_stmt_close($stmt);

        if ($duplikat) {
            $pesan = "Kode transaksi atau pesanan tersebut sudah memiliki transaksi.";
        } else {
            if ($aksi == 'tambah') {
                $stmt = mysqli_prepare($koneksi, "
                    INSERT INTO transaksi
                    (id_pesanan, kode_transaksi, total_transaksi, status_transaksi)
                    VALUES (?, ?, ?, ?)
                ");
                mysqli_stmt_bind_param($stmt, "isds", $id_pesanan, $kode_transaksi, $total_transaksi, $status_transaksi);
            } else {
                $stmt = mysqli_prepare($koneksi, "
                    UPDATE transaksi
                    SET id_pesanan=?, kode_transaksi=?, total_transaksi=?, status_transaksi=?
                    WHERE id_transaksi=?
                ");
                mysqli_stmt_bind_param($stmt, "isdsi", $id_pesanan, $kode_transaksi, $total_transaksi, $status_transaksi, $id);
            }

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("Location: transaksi.php");
                exit;
            } else {
                $pesan = "Data transaksi gagal disimpan. Periksa kembali data pesanan.";
                mysqli_stmt_close($stmt);
            }
        }
    }
}

// Ambil pilihan pesanan
$daftar_pesanan = mysqli_query($koneksi, "
    SELECT id_pesanan, kode_pesanan
    FROM pesanan
    ORDER BY id_pesanan DESC
");

include 'header.php';
include 'sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h2><?= $aksi == 'edit' ? 'Edit Transaksi' : 'Tambah Transaksi'; ?></h2>
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
                        <label>Kode Transaksi</label>
                        <input type="text" name="kode_transaksi" class="form-control"
                            value="<?= htmlspecialchars($_POST['kode_transaksi'] ?? $data['kode_transaksi'] ?? ''); ?>"
                            maxlength="30" placeholder="Contoh: TRX001" required>
                    </div>
                    <div class="form-group">
                        <label>Total Transaksi (Rp)</label>
                        <input type="number" name="total_transaksi" class="form-control"
                            value="<?= htmlspecialchars($_POST['total_transaksi'] ?? $data['total_transaksi'] ?? ''); ?>"
                            min="0" step="0.01" placeholder="Masukkan total transaksi" required>
                    </div>
                    <div class="form-group">
                        <label>Status Transaksi</label>
                        <select name="status_transaksi" class="form-control" required>
                            <?php
                            $status_pilihan = ['pending', 'berhasil', 'gagal', 'dibatalkan'];
                            $status_terpilih = $_POST['status_transaksi'] ?? $data['status_transaksi'] ?? 'pending';
                            foreach ($status_pilihan as $status) :
                            ?>
                                <option value="<?= $status; ?>" <?= $status_terpilih == $status ? 'selected' : ''; ?>>
                                    <?= ucfirst($status); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="simpan" class="btn btn-success">
                        <i class="fas fa-save"></i>
                        <?= $aksi == 'edit' ? 'Perbarui Data' : 'Simpan Data'; ?>
                    </button>
                    <a href="transaksi.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                </form>
            </div>
        </div>
    </section>
</div>