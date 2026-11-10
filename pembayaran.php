<?php
require_once __DIR__ . '/koneksi.php';

// Ambil data pembayaran
$query = mysqli_query($koneksi, "
    SELECT pb.*, t.kode_transaksi, a.username
    FROM pembayaran pb
    LEFT JOIN transaksi t ON pb.id_transaksi = t.id_transaksi
    LEFT JOIN admin a ON pb.diverifikasi_oleh = a.id_admin
    ORDER BY pb.id_pembayaran DESC
");

include 'header.php';
include 'sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h2>Data Pembayaran</h2>
        </div>
    </section>
    <section class="content">
        <div class="card">
            <div class="card-header">
                <a href="pembayaran_form.php?aksi=tambah" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Pembayaran
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Pembayaran</th>
                                <th>Kode Transaksi</th>
                                <th>Metode Pembayaran</th>
                                <th>Jumlah Bayar</th>
                                <th>Tanggal Pembayaran</th>
                                <th>Status</th>
                                <th>Diverifikasi Oleh</th>
                                <th>Tanggal Verifikasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            while ($data = mysqli_fetch_assoc($query)) {
                            ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $data['id_pembayaran']; ?></td>
                                    <td><?= htmlspecialchars($data['kode_transaksi'] ?? '-'); ?></td>
                                    <td><?= htmlspecialchars($data['metode_pembayaran']); ?></td>
                                    <td>Rp <?= number_format($data['jumlah_bayar'], 0, ',', '.'); ?></td>
                                    <td><?= date('d-m-Y H:i', strtotime($data['tanggal_pembayaran'])); ?></td>
                                    <td>
                                        <?php
                                        $warna = 'warning';
                                        if ($data['status_pembayaran'] == 'berhasil') $warna = 'success';
                                        elseif ($data['status_pembayaran'] == 'ditolak') $warna = 'danger';
                                        ?>
                                        <span class="badge badge-<?= $warna; ?>">
                                            <?= ucfirst($data['status_pembayaran']); ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($data['username'] ?? '-'); ?></td>
                                    <td>
                                        <?= $data['tanggal_verifikasi'] ? date('d-m-Y H:i', strtotime($data['tanggal_verifikasi'])) : '-'; ?>
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="pembayaran_form.php?aksi=edit&id=<?= $data['id_pembayaran']; ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="pembayaran_form.php?aksi=hapus&id=<?= $data['id_pembayaran']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus pembayaran ini?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if (mysqli_num_rows($query) == 0) : ?>
                                <tr>
                                    <td colspan="10" class="text-center">Belum ada data pembayaran.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>