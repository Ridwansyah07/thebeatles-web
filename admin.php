<?php
require_once __DIR__ . '/koneksi.php';

// Ambil data admin
$query = mysqli_query($koneksi, "
    SELECT id_admin, username
    FROM admin
    ORDER BY id_admin DESC
");

include 'header.php';
include 'sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h2>Data Admin</h2>
        </div>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-header">
                <a href="admin_form.php?aksi=tambah" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Admin
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Username</th>
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
                                <td><?= htmlspecialchars($data['username']); ?></td>
                                <td>
                                    <a href="admin_form.php?aksi=edit&id=<?= $data['id_admin']; ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="admin_form.php?aksi=hapus&id=<?= $data['id_admin']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus admin ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                            <?php if (mysqli_num_rows($query) == 0) : ?>
                            <tr>
                                <td colspan="3" class="text-center">Belum ada data admin.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
