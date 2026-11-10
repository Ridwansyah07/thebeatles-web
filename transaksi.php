<?php
require_once __DIR__ . '/koneksi.php';

// Ambil data transaksi
$query = mysqli_query($koneksi, "
    SELECT t.*, p.kode_pesanan
    FROM transaksi t
    LEFT JOIN pesanan p ON t.id_pesanan = p.id_pesanan
    ORDER BY t.id_transaksi DESC
");

include 'header.php';
include 'sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h2>Data Transaksi</h2>
        </div>
    </section>
    <section class="content">
        <div class="card">
            <div class="card-header">
                <a href="transaksi_form.php?aksi=tambah" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Transaksi
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Transaksi</th>
                                <th>Kode Transaksi</th>
                                <th>Kode Pesanan</th>
                                <th>Tanggal Transaksi</th>
                                <th>Total Transaksi</th>
                                <th>Status</th>
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
                                    <td><?= $data['id_transaksi']; ?></td>
                                    <td><?= htmlspecialchars($data['kode_transaksi']); ?></td>
                                    <td><?= htmlspecialchars($data['kode_pesanan'] ?? '-'); ?></td>
                                    <td><?= date('d-m-Y H:i', strtotime($data['tanggal_transaksi'])); ?></td>
                                    <td>Rp <?= number_format($data['total_transaksi'], 0, ',', '.'); ?></td>
                                    <td>
                                        <?php
                                        $warna = 'secondary';
                                        if ($data['status_transaksi'] == 'pending') $warna = 'warning';
                                        elseif ($data['status_transaksi'] == 'berhasil') $warna = 'success';
                                        elseif ($data['status_transaksi'] == 'gagal') $warna = 'danger';
                                        elseif ($data['status_transaksi'] == 'dibatalkan') $warna = 'dark';
                                        ?>
                                        <span class="badge badge-<?= $warna; ?>">
                                            <?= ucfirst($data['status_transaksi']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="transaksi_form.php?aksi=edit&id=<?= $data['id_transaksi']; ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="transaksi_form.php?aksi=hapus&id=<?= $data['id_transaksi']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus transaksi ini?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if (mysqli_num_rows($query) == 0) : ?>
                                <tr>
                                    <td colspan="8" class="text-center">Belum ada data transaksi.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>