<?php
require 'koneksi.php';
include 'header.php';
include 'sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <h2>Data Lagu (Songs)</h2>
    </div>
  </section>

  <section class="content">
    <div class="card">
      <div class="card-header">
        <a href="songs_form.php?aksi=tambah" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Lagu</a>
      </div>
      <div class="card-body">
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th width="5%">No</th>
              <th>Judul Lagu</th>
              <th>Album</th>
              <th>Track</th>
              <th>Rekomendasi</th>
              <th width="15%">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            $query = mysqli_query($koneksi, "SELECT songs.*, albums.judul AS judul_album FROM songs LEFT JOIN albums ON songs.id_album = albums.id_album ORDER BY songs.id_song DESC");
            while ($data = mysqli_fetch_assoc($query)) {
            ?>
            <tr>
              <td><?= $no++; ?></td>
              <td><?= htmlspecialchars($data['judul_lagu']); ?></td>
              <td><?= htmlspecialchars($data['judul_album'] ?? '-'); ?></td>
              <td><?= htmlspecialchars($data['nomor_track']); ?></td>
              <td>
                <?php if($data['is_recommended'] == 'Y') { ?>
                    <span class="badge badge-success">Ya</span>
                <?php } else { ?>
                    <span class="badge badge-secondary">Tidak</span>
                <?php } ?>
              </td>
              <td>
                <a href="songs_form.php?aksi=edit&id=<?= $data['id_song']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                <a href="songs_form.php?aksi=hapus&id=<?= $data['id_song']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus lagu ini?')"><i class="fas fa-trash"></i> Hapus</a>
              </td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>