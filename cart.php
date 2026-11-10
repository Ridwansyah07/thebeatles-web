<?php
require_once __DIR__ . '/koneksi.php';

// Ambil data cart
$query = mysqli_query($koneksi, "
    SELECT * FROM cart
    ORDER BY id_cart DESC
");

include 'header.php';
include 'sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h2>Data Cart</h2>
        </div>
    </section>
    <section class="content">
        <div class="card">
            <div class="card-header">
                <a href="cart_form.php?aksi=tambah" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Cart
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Cart</th>
                                <th>Session ID</th>
                                <th>Dibuat</th>
                                <th>Diperbarui</th>
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
                                <td><?= $data['id_cart']; ?></td>
                                <td><?= htmlspecialchars($data['session_id']); ?></td>
                                <td><?= date('d-m-Y H:i:s', strtotime($data['created_at'])); ?></td>
                                <td><?= date('d-m-Y H:i:s', strtotime($data['updated_at'])); ?></td>
                                <td>
                                    <a href="cart_form.php?aksi=edit&id=<?= $data['id_cart']; ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="cart_form.php?aksi=hapus&id=<?= $data['id_cart']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus cart ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                            <?php if (mysqli_num_rows($query) == 0) : ?>
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data cart.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
