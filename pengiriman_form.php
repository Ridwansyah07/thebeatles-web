<?php
require_once __DIR__ . '/koneksi.php';

// Pilih aksi
$aksi = $_GET['aksi'] ?? 'tambah';
if (!in_array($aksi, ['tambah', 'edit', 'hapus'])) {
    $aksi = 'tambah';
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$data = null;

// Hapus data
if ($aksi == 'hapus') {
    $stmt = mysqli_prepare($koneksi, "DELETE FROM pengiriman WHERE id_pengiriman = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Data pengiriman berhasil dihapus!'); window.location='pengiriman.php';</script>";
    } else {
        echo "<script>alert('Data pengiriman gagal dihapus!'); window.location='pengiriman.php';</script>";
    }
    exit;
}

// Ambil data untuk diedit
if ($aksi == 'edit') {
    $stmt_select = mysqli_prepare($koneksi, "SELECT * FROM pengiriman WHERE id_pengiriman = ?");
    mysqli_stmt_bind_param($stmt_select, "i", $id);
    mysqli_stmt_execute($stmt_select);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_select));

    if (!$data) {
        echo "<script>alert('Data pengiriman tidak ditemukan!'); window.location='pengiriman.php';</script>";
        exit;
    }
}

// Simpan data
if (isset($_POST['simpan'])) {
    $id_pesanan = (int) $_POST['id_pesanan'];
    $nama_ekspedisi = trim($_POST['nama_ekspedisi']);
    $layanan_pengiriman = trim($_POST['layanan_pengiriman']);
    $nomor_resi = !empty($_POST['nomor_resi']) ? trim($_POST['nomor_resi']) : NULL;
    $tanggal_pengiriman = !empty($_POST['tanggal_pengiriman']) ? date('Y-m-d H:i:s', strtotime($_POST['tanggal_pengiriman'])) : NULL;
    $tanggal_diterima = !empty($_POST['tanggal_diterima']) ? date('Y-m-d H:i:s', strtotime($_POST['tanggal_diterima'])) : NULL;
    $status_pengiriman = $_POST['status_pengiriman'];

    // Validasi status pengiriman
    $daftar_status = ['belum_dikirim', 'dikirim', 'diterima', 'gagal'];
    if (!in_array($status_pengiriman, $daftar_status)) {
        echo "<script>alert('Status pengiriman tidak valid!'); window.history.back();</script>";
        exit;
    }

    // Periksa apakah pesanan sudah memiliki data pengiriman
    $stmt_cek = mysqli_prepare($koneksi, "SELECT id_pengiriman FROM pengiriman WHERE id_pesanan = ? AND id_pengiriman != ?");
    mysqli_stmt_bind_param($stmt_cek, "ii", $id_pesanan, $id);
    mysqli_stmt_execute($stmt_cek);
    $cek = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_cek));

    if ($cek) {
        echo "<script>alert('Pesanan ini sudah memiliki data pengiriman!'); window.history.back();</script>";
        exit;
    }

    // Tambah data
    if ($aksi == 'tambah') {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO pengiriman (id_pesanan, nama_ekspedisi, layanan_pengiriman, nomor_resi, tanggal_pengiriman, tanggal_diterima, status_pengiriman) VALUES (?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "issssss", $id_pesanan, $nama_ekspedisi, $layanan_pengiriman, $nomor_resi, $tanggal_pengiriman, $tanggal_diterima, $status_pengiriman);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Data pengiriman berhasil ditambahkan!'); window.location='pengiriman.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan data pengiriman!'); window.location='pengiriman_form.php?aksi=tambah';</script>";
        }
        exit;
    }

    // Perbarui data
    if ($aksi == 'edit') {
        $stmt = mysqli_prepare($koneksi, "UPDATE pengiriman SET id_pesanan=?, nama_ekspedisi=?, layanan_pengiriman=?, nomor_resi=?, tanggal_pengiriman=?, tanggal_diterima=?, status_pengiriman=? WHERE id_pengiriman=?");
        mysqli_stmt_bind_param($stmt, "issssssi", $id_pesanan, $nama_ekspedisi, $layanan_pengiriman, $nomor_resi, $tanggal_pengiriman, $tanggal_diterima, $status_pengiriman, $id);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Data pengiriman berhasil diperbarui!'); window.location='pengiriman.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui data pengiriman!'); window.location='pengiriman_form.php?aksi=edit&id=$id';</script>";
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
            <h2><?= $aksi == 'edit' ? 'Edit Data Pengiriman' : 'Tambah Pengiriman Baru'; ?></h2>
        </div>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-body">
                <form action="" method="POST">
                    <div class="form-group">
                        <label><?= $aksi == 'edit' ? 'Pesanan' : 'Pilih Pesanan'; ?></label>
                        <select name="id_pesanan" class="form-control" required>
                            <option value="">-- Pilih Pesanan --</option>
                            <?php
                            $q_pesanan = mysqli_prepare($koneksi, "
                                SELECT p.id_pesanan, p.kode_pesanan
                                FROM pesanan p
                                LEFT JOIN pengiriman pg ON p.id_pesanan = pg.id_pesanan
                                WHERE pg.id_pengiriman IS NULL OR pg.id_pengiriman = ?
                                ORDER BY p.id_pesanan DESC
                            ");
                            mysqli_stmt_bind_param($q_pesanan, "i", $id);
                            mysqli_stmt_execute($q_pesanan);
                            $result_pesanan = mysqli_stmt_get_result($q_pesanan);

                            while ($pesanan = mysqli_fetch_assoc($result_pesanan)) {
                                $selected = ($data && $pesanan['id_pesanan'] == $data['id_pesanan']) ? 'selected' : '';
                                echo "<option value='" . $pesanan['id_pesanan'] . "' $selected>" . htmlspecialchars($pesanan['kode_pesanan']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Nama Ekspedisi</label>
                        <input type="text" name="nama_ekspedisi" class="form-control"
                               value="<?= $v('nama_ekspedisi'); ?>"
                               placeholder="Contoh: JNE, J&T, SiCepat"
                               maxlength="100" required>
                    </div>

                    <div class="form-group">
                        <label>Layanan Pengiriman</label>
                        <input type="text" name="layanan_pengiriman" class="form-control"
                               value="<?= $v('layanan_pengiriman'); ?>"
                               placeholder="Contoh: REGULAR, EKONOMI, EXPRESS"
                               maxlength="100" required>
                    </div>

                    <div class="form-group">
                        <label>Nomor Resi</label>
                        <input type="text" name="nomor_resi" class="form-control"
                               value="<?= $v('nomor_resi'); ?>"
                               placeholder="Masukkan nomor resi"
                               maxlength="50">
                    </div>

                    <div class="form-group">
                        <label>Tanggal Pengiriman</label>
                        <input type="datetime-local" name="tanggal_pengiriman" class="form-control"
                               value="<?= $data && $data['tanggal_pengiriman'] ? date('Y-m-d\TH:i', strtotime($data['tanggal_pengiriman'])) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Tanggal Diterima</label>
                        <input type="datetime-local" name="tanggal_diterima" class="form-control"
                               value="<?= $data && $data['tanggal_diterima'] ? date('Y-m-d\TH:i', strtotime($data['tanggal_diterima'])) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Status Pengiriman</label>
                        <select name="status_pengiriman" class="form-control" required>
                            <option value="belum_dikirim" <?= ($data['status_pengiriman'] ?? 'belum_dikirim') == 'belum_dikirim' ? 'selected' : ''; ?>>Belum Dikirim</option>
                            <option value="dikirim" <?= ($data['status_pengiriman'] ?? '') == 'dikirim' ? 'selected' : ''; ?>>Dalam Pengiriman</option>
                            <option value="diterima" <?= ($data['status_pengiriman'] ?? '') == 'diterima' ? 'selected' : ''; ?>>Diterima</option>
                            <option value="gagal" <?= ($data['status_pengiriman'] ?? '') == 'gagal' ? 'selected' : ''; ?>>Gagal Dikirim</option>
                        </select>
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

                    <a href="pengiriman.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                </form>
            </div>
        </div>
    </section>
</div>
