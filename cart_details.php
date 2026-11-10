<?php
require_once __DIR__ . '/koneksi.php';

// Ambil data detail cart
$query = mysqli_query($koneksi, "
    SELECT cd.*, c.session_id
    FROM cart_details cd
    LEFT JOIN cart c ON cd.id_cart = c.id_cart
    ORDER BY cd.id_cart_detail DESC
");

include 'header.php';
include 'sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h2>Detail Cart</h2>
        </div>
    </section>
    <section class="content">
        <div class="card">
            <div class="card-header">
                <a href="cart_details_form.php?aksi=tambah" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Detail Cart
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Detail Cart</th>
                                <th>ID Cart</th>
                                <th>Session ID</th>
                                <th>ID Merchandise</th>
                                <th>Jumlah</th>
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
                                <td><?= $data['id_cart_detail']; ?></td>
                                <td><?= $data['id_cart']; ?></td>
                                <td><?= htmlspecialchars($data['session_id'] ?? '-'); ?></td>
                                <td><?= $data['id_merch']; ?></td>
                                <td><?= $data['jumlah']; ?></td>
                                <td>
                                    <a href="cart_details_form.php?aksi=edit&id=<?= $data['id_cart_detail']; ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="cart_details_form.php?aksi=hapus&id=<?= $data['id_cart_detail']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus detail cart ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                            <?php if (mysqli_num_rows($query) == 0) : ?>
                            <tr>
                                <td colspan="7" class="text-center">Belum ada detail cart.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
