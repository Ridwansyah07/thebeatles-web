<?php
require_once __DIR__ . '/koneksi.php';
include 'header.php';
include 'sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <h2>Data Album</h2>
    </div>
  </section>

  <section class="content">
    <div class="card">
      <div class="card-header">
        <a href="album_form.php?aksi=tambah" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Album</a>
      </div>
      <div class="card-body">
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th width="5%">No</th>
              <th>Judul Album</th>
              <th>Slug</th>
              <th>Tahun Rilis</th>
              <th>Views</th>
              <th>Deskripsi Singkat</th>
              <th width="15%">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            $query = mysqli_query($koneksi, "SELECT * FROM albums ORDER BY id_album DESC");
            while ($data = mysqli_fetch_assoc($query)) {
            ?>
            <tr>
              <td><?= $no++; ?></td>
              <td>
                <div class="d-flex align-items-center">
                    <?php if (!empty($data['cover_url'])) : ?>
                        <!-- jika foto cover_url ada di database -->
                        <img src="img/<?= htmlspecialchars($data['cover_url']); ?>"
                             alt="Cover"
                             class="rounded-circle mr-3"
                             style="width: 45px; height: 45px; object-fit: cover;">
                    <?php else : ?>
                        <!-- jika foto kosong akan tampil inisial -->
                        <div class="rounded-circle mr-3 d-flex align-items-center justify-content-center bg-secondary text-white"
                             style="width: 45px; height: 45px; font-size: 20px; font-weight: bold;">
                            <?= strtoupper(substr($data['judul'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    <?= htmlspecialchars($data['judul']); ?>
                </div>
              </td>
              <td><?= htmlspecialchars($data['slug']); ?></td>
              <td><?= htmlspecialchars($data['tahun_rilis']); ?></td>
              <td><?= htmlspecialchars($data['jumlah_views']); ?></td>
              <td><?= htmlspecialchars($data['deskripsi']); ?></td>
              <td>
                <a href="album_form.php?aksi=edit&id=<?= $data['id_album']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                <a href="album_form.php?aksi=hapus&id=<?= $data['id_album']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus album ini?')"><i class="fas fa-trash"></i> Hapus</a>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>