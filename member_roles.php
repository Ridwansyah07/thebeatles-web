<?php
require_once __DIR__ . '/koneksi.php';

// Ambil data member roles
$query = mysqli_query($koneksi, "
    SELECT member_roles.*, members.nama, roles.nama_role
    FROM member_roles
    INNER JOIN members ON member_roles.id_member = members.id_member
    INNER JOIN roles ON member_roles.id_role = roles.id_role
    ORDER BY members.nama ASC, member_roles.urutan_tampil ASC
");

include 'header.php';
include 'sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h2>Data Member Roles</h2>
        </div>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-header">
                <a href="member_roles_form.php?aksi=tambah" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Member Role
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Member</th>
                                <th>Nama Role</th>
                                <th>Urutan Tampil</th>
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
                                <td><?= htmlspecialchars($data['nama']); ?></td>
                                <td><?= htmlspecialchars($data['nama_role']); ?></td>
                                <td><?= htmlspecialchars($data['urutan_tampil']); ?></td>
                                <td>
                                    <a href="member_roles_form.php?aksi=edit&id_member=<?= $data['id_member']; ?>&id_role=<?= $data['id_role']; ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="member_roles_form.php?aksi=hapus&id_member=<?= $data['id_member']; ?>&id_role=<?= $data['id_role']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data member role ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>

                            <?php if (mysqli_num_rows($query) == 0) : ?>
                            <tr>
                                <td colspan="5" class="text-center">Belum ada data member role.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
