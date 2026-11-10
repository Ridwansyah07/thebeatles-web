<?php
require 'koneksi.php';
include 'header.php';
include 'sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <h2>Data Merchandise</h2>
    </div>
  </section>

  <section class="content">
    <div class="card">
      <div class="card-header">
        <a href="merchandise_form.php?aksi=tambah" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Merchandise</a>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th width="5%">No</th>
                <th>Nama Produk</th>
                <th>Terkait Album</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Status</th>
                <th width="15%">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $no = 1;
              $query = mysqli_query($koneksi, "SELECT merchandise.*, albums.judul AS judul_album FROM merchandise LEFT JOIN albums ON merchandise.id_album = albums.id_album ORDER BY merchandise.id_merch DESC");
              while ($data = mysqli_fetch_assoc($query)) {
              ?>
              <tr>
                <td><?= $no++; ?></td>
                <td>
                  <div class="d-flex align-items-center">
                    <?php if (!empty($data['gambar_url'])) : ?>
                        <!-- jika foto gambar_url ada di database -->
                        <img src="img/<?= htmlspecialchars($data['gambar_url']); ?>"
                             alt="Cover"
                             class="rounded-circle mr-3"
                             style="width: 45px; height: 45px; object-fit: cover;">
                    <?php else : ?>
                        <!-- jika foto kosong akan tampil inisial -->
                        <div class="rounded-circle mr-3 d-flex align-items-center justify-content-center bg-secondary text-white"
                             style="width: 45px; height: 45px; font-size: 20px; font-weight: bold;">
                            <?= strtoupper(substr($data['nama_produk'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    <?= htmlspecialchars($data['nama_produk']); ?>
                  </div>
                </td>
                <td><?= htmlspecialchars($data['judul_album'] ?? 'Umum (Tanpa Album)'); ?></td>
                <td>Rp <?= number_format($data['harga'], 0, ',', '.'); ?></td>
                <td><?= htmlspecialchars($data['stok']); ?></td>
                <td>
                  <?php if($data['label_status'] == 'available') { ?>
                      <span class="badge badge-success">Available</span>
                  <?php } else { ?>
                      <span class="badge badge-danger"><?= htmlspecialchars($data['label_status']); ?></span>
                  <?php } ?>
                </td>
                <td>
                  <a href="merchandise_form.php?aksi=edit&id=<?= $data['id_merch']; ?>" class="btn btn-warning btn-sm mb-1"><i class="fas fa-edit"></i> Edit</a>
                  <a href="merchandise_form.php?aksi=hapus&id=<?= $data['id_merch']; ?>" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Hapus merchandise ini?')"><i class="fas fa-trash"></i> Hapus</a>
                </td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>