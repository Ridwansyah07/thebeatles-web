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
    $stmt = mysqli_prepare($koneksi, "DELETE FROM pembayaran WHERE id_pembayaran=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: pembayaran.php");
    exit;
}

// Ambil data untuk edit
if ($aksi == 'edit') {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM pembayaran WHERE id_pembayaran=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $hasil = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($hasil);
    mysqli_stmt_close($stmt);

    if (!$data) {
        header("Location: pembayaran.php");
        exit;
    }
}

// Proses simpan
if (isset($_POST['simpan'])) {
    $id_transaksi = (int) $_POST['id_transaksi'];
    $metode_pembayaran = trim($_POST['metode_pembayaran']);
    $jumlah_bayar = (float) $_POST['jumlah_bayar'];
    $bukti_pembayaran = trim($_POST['bukti_pembayaran']);
    $status_pembayaran = $_POST['status_pembayaran'];
    $diverifikasi_oleh = $_POST['diverifikasi_oleh'] !== '' ? (int) $_POST['diverifikasi_oleh'] : null;

    $status_valid = ['pending', 'berhasil', 'ditolak'];

    if ($id_transaksi <= 0 || $metode_pembayaran == '' || $jumlah_bayar <= 0 || !in_array($status_pembayaran, $status_valid)) {
        $pesan = "Semua data wajib diisi dengan benar.";
    } elseif ($status_pembayaran != 'pending' && $diverifikasi_oleh === null) {
        $pesan = "Pilih admin yang memverifikasi pembayaran.";
    } else {
        // Tentukan tanggal verifikasi
        $tanggal_verifikasi = $status_pembayaran == 'pending' ? null : date('Y-m-d H:i:s');
        $bukti_pembayaran = $bukti_pembayaran == '' ? null : $bukti_pembayaran;

        if ($aksi == 'tambah') {
            $stmt = mysqli_prepare($koneksi, "
                INSERT INTO pembayaran
                (id_transaksi, metode_pembayaran, jumlah_bayar, bukti_pembayaran, status_pembayaran, diverifikasi_oleh, tanggal_verifikasi)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            mysqli_stmt_bind_param($stmt, "isdssis", $id_transaksi, $metode_pembayaran, $jumlah_bayar, $bukti_pembayaran, $status_pembayaran, $diverifikasi_oleh, $tanggal_verifikasi);
        } else {
            $stmt = mysqli_prepare($koneksi, "
                UPDATE pembayaran
                SET id_transaksi=?, metode_pembayaran=?, jumlah_bayar=?, bukti_pembayaran=?, status_pembayaran=?, diverifikasi_oleh=?, tanggal_verifikasi=?
                WHERE id_pembayaran=?
            ");
            mysqli_stmt_bind_param($stmt, "isdssisi", $id_transaksi, $metode_pembayaran, $jumlah_bayar, $bukti_pembayaran, $status_pembayaran, $diverifikasi_oleh, $tanggal_verifikasi, $id);
        }

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: pembayaran.php");
            exit;
        } else {
            $pesan = "Data pembayaran gagal disimpan. Periksa kembali transaksi dan admin.";
            mysqli_stmt_close($stmt);
        }
    }
}

// Ambil pilihan transaksi
$daftar_transaksi = mysqli_query($koneksi, "
    SELECT id_transaksi, kode_transaksi
    FROM transaksi
    ORDER BY id_transaksi DESC
");

// Ambil pilihan admin
$daftar_admin = mysqli_query($koneksi, "
    SELECT id_admin, username
    FROM admin
    ORDER BY username ASC
");

include 'header.php';
include 'sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h2><?= $aksi == 'edit' ? 'Edit Pembayaran' : 'Tambah Pembayaran'; ?></h2>
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
                        <label>Transaksi</label>
                        <select name="id_transaksi" class="form-control" required>
                            <option value="">-- Pilih Transaksi --</option>
                            <?php while ($transaksi = mysqli_fetch_assoc($daftar_transaksi)) : ?>
                                <option value="<?= $transaksi['id_transaksi']; ?>"
                                    <?= (($_POST['id_transaksi'] ?? $data['id_transaksi'] ?? '') == $transaksi['id_transaksi']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($transaksi['kode_transaksi']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Metode Pembayaran</label>
                        <input type="text" name="metode_pembayaran" class="form-control"
                            value="<?= htmlspecialchars($_POST['metode_pembayaran'] ?? $data['metode_pembayaran'] ?? ''); ?>"
                            maxlength="50" placeholder="Contoh: Transfer Bank" required>
                    </div>
                    <div class="form-group">
                        <label>Jumlah Bayar (Rp)</label>
                        <input type="number" name="jumlah_bayar" class="form-control"
                            value="<?= htmlspecialchars($_POST['jumlah_bayar'] ?? $data['jumlah_bayar'] ?? ''); ?>"
                            min="0.01" step="0.01" placeholder="Masukkan jumlah pembayaran" required>
                    </div>
                    <div class="form-group">
                        <label>Bukti Pembayaran (URL atau nama file)</label>
                        <input type="text" name="bukti_pembayaran" class="form-control"
                            value="<?= htmlspecialchars($_POST['bukti_pembayaran'] ?? $data['bukti_pembayaran'] ?? ''); ?>"
                            maxlength="255" placeholder="Masukkan lokasi bukti pembayaran (opsional)">
                    </div>
                    <div class="form-group">
                        <label>Status Pembayaran</label>
                        <select name="status_pembayaran" class="form-control" required>
                            <?php
                            $status_pilihan = ['pending', 'berhasil', 'ditolak'];
                            $status_terpilih = $_POST['status_pembayaran'] ?? $data['status_pembayaran'] ?? 'pending';
                            foreach ($status_pilihan as $status) :
                            ?>
                                <option value="<?= $status; ?>" <?= $status_terpilih == $status ? 'selected' : ''; ?>>
                                    <?= ucfirst($status); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Diverifikasi Oleh</label>
                        <select name="diverifikasi_oleh" class="form-control">
                            <option value="">-- Belum Diverifikasi --</option>
                            <?php while ($admin = mysqli_fetch_assoc($daftar_admin)) : ?>
                                <option value="<?= $admin['id_admin']; ?>"
                                    <?= (($_POST['diverifikasi_oleh'] ?? $data['diverifikasi_oleh'] ?? '') == $admin['id_admin']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($admin['username']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <button type="submit" name="simpan" class="btn btn-success">
                        <i class="fas fa-save"></i>
                        <?= $aksi == 'edit' ? 'Perbarui Data' : 'Simpan Data'; ?>
                    </button>
                    <a href="pembayaran.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                </form>
            </div>
        </div>
    </section>
</div>