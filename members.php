<?php
require_once __DIR__ . '/koneksi.php';
include 'header.php';
include 'sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <h2>Data Member</h2>
    </div>
  </section>

  <section class="content">
    <div class="card">
      <div class="card-header">
        <a href="members_form.php?aksi=tambah" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Member</a>
      </div>
      <div class="card-body">
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th width="5%">No</th>
              <th>Nama Member</th>
              <th>Informasi Kelahiran</th>
              <th>Biografi</th>
              <th width="15%">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            $query = mysqli_query($koneksi, "SELECT * FROM members ORDER BY id_member ASC");
            while ($data = mysqli_fetch_assoc($query)) {
            ?>
            <tr>
              <td><?= $no++; ?></td>
              <td>
                <div class="d-flex align-items-center">
                    <?php if (!empty($data['foto_url'])) : ?>
                        <!-- Jika foto member tersedia di database -->
                        <img src="img/<?= htmlspecialchars($data['foto_url']); ?>"
                             alt="Foto Member"
                             class="rounded-circle mr-3"
                             style="width: 45px; height: 45px; object-fit: cover;">
                    <?php else : ?>
                        <!-- Jika foto kosong, tampilkan inisial nama -->
                        <div class="rounded-circle mr-3 d-flex align-items-center justify-content-center bg-secondary text-white"
                             style="width: 45px; height: 45px; font-size: 20px; font-weight: bold;">
                            <?= strtoupper(substr($data['nama'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    <?= htmlspecialchars($data['nama']); ?>
                </div>
              </td>
              <td><?= htmlspecialchars($data['info_kelahiran']); ?></td>
              <td><?= htmlspecialchars($data['bio']); ?></td>
              <td>
                <a href="members_form.php?aksi=edit&id=<?= $data['id_member']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                <a href="members_form.php?aksi=hapus&id=<?= $data['id_member']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus member ini?')"><i class="fas fa-trash"></i> Hapus</a>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>
