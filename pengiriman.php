<?php
require 'koneksi.php';

//Ambil data pengiriman
$query = mysqli_query($koneksi, "
    SELECT pengiriman.*, pesanan.kode_pesanan
    FROM pengiriman
    INNER JOIN pesanan ON pengiriman.id_pesanan = pesanan.id_pesanan
    ORDER BY pengiriman.id_pengiriman DESC
");

include 'header.php';
include 'sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h2>Data Pengiriman</h2>
        </div>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-header">
                <a href="pengiriman_form.php?aksi=tambah" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Pengiriman
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Pesanan</th>
                                <th>Nama Ekspedisi</th>
                                <th>Layanan Pengiriman</th>
                                <th>Nomor Resi</th>
                                <th>Tanggal Pengiriman</th>
                                <th>Tanggal Diterima</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            while ($data = mysqli_fetch_assoc($query)) {
                                $status = !empty($data['status_pengiriman'])
                                ? $data['status_pengiriman']
                                : 'belum_dikirim';

                            $warna = 'secondary';

                            if ($status == 'belum_dikirim') {
                                $warna = 'warning';
                            } elseif ($status == 'dikirim') {
                                $warna = 'primary';
                            } elseif ($status == 'diterima') {
                                $warna = 'success';
                            } elseif ($status == 'gagal') {
                                $warna = 'danger';
                            }

                            $nama_status = [
                                'belum_dikirim' => 'Belum Dikirim',
                                'dikirim' => 'Dalam Pengiriman',
                                'diterima' => 'Diterima',
                                'gagal' => 'Gagal Dikirim'
                            ];
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($data['kode_pesanan']); ?></td>
                                <td><?= htmlspecialchars($data['nama_ekspedisi']); ?></td>
                                <td><?= htmlspecialchars($data['layanan_pengiriman']); ?></td>
                                <td><?= !empty($data['nomor_resi']) ? htmlspecialchars($data['nomor_resi']) : '-'; ?></td>
                                <td><?= !empty($data['tanggal_pengiriman']) ? date('d/m/Y H:i', strtotime($data['tanggal_pengiriman'])) : '-'; ?></td>
                                <td><?= !empty($data['tanggal_diterima']) ? date('d/m/Y H:i', strtotime($data['tanggal_diterima'])) : '-'; ?></td>
                                <td>
                                    <span class="badge badge-<?= $warna; ?>">
                                        <?= htmlspecialchars(ucwords(str_replace('_', ' ', $status))); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="pengiriman_form.php?aksi=edit&id=<?= $data['id_pengiriman']; ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="pengiriman_form.php?aksi=hapus&id=<?= $data['id_pengiriman']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data pengiriman ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                            <?php if (mysqli_num_rows($query) == 0) : ?>
                            <tr>
                                <td colspan="9" class="text-center">Belum ada data pengiriman.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
