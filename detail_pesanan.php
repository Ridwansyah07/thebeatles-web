<?php
require_once __DIR__ . '/koneksi.php';

// Ambil data detail pesanan
$query = mysqli_query($koneksi, "
    SELECT dp.*, p.kode_pesanan
    FROM detail_pesanan dp
    LEFT JOIN pesanan p ON dp.id_pesanan = p.id_pesanan
    ORDER BY dp.id_detail DESC
");

include 'header.php';
include 'sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h2>Detail Pesanan</h2>
        </div>
    </section>
    <section class="content">
        <div class="card">
            <div class="card-header">
                <a href="detail_pesanan_form.php?aksi=tambah" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Detail Pesanan
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Detail</th>
                                <th>Kode Pesanan</th>
                                <th>ID Merchandise</th>
                                <th>Jumlah</th>
                                <th>Harga Satuan</th>
                                <th>Subtotal</th>
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
                                    <td><?= $data['id_detail']; ?></td>
                                    <td><?= htmlspecialchars($data['kode_pesanan'] ?? '-'); ?></td>
                                    <td><?= $data['id_merch']; ?></td>
                                    <td><?= $data['jumlah']; ?></td>
                                    <td>Rp <?= number_format($data['harga_satuan'], 0, ',', '.'); ?></td>
                                    <td>Rp <?= number_format($data['subtotal'], 0, ',', '.'); ?></td>
                                    <td>
                                        <a href="detail_pesanan_form.php?aksi=edit&id=<?= $data['id_detail']; ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="detail_pesanan_form.php?aksi=hapus&id=<?= $data['id_detail']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus detail pesanan ini?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if (mysqli_num_rows($query) == 0) : ?>
                                <tr>
                                    <td colspan="8" class="text-center">Belum ada detail pesanan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>