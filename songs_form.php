<?php
require 'koneksi.php';

//Pilih aksi
$aksi = $_GET['aksi'] ?? 'tambah';
if (!in_array($aksi, ['tambah', 'edit', 'hapus'])) {
    $aksi = 'tambah';
}

$id   = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$data = null;

//Hapus
if ($aksi == 'hapus') {
    $stmt = mysqli_prepare($koneksi, "DELETE FROM songs WHERE id_song = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Lagu berhasil dihapus!'); window.location='songs.php';</script>";
    } else {
        echo "<script>alert('Lagu gagal dihapus!'); window.location='songs.php';</script>";
    }
    exit;
}

//Edit
if ($aksi == 'edit') {
    $stmt_select = mysqli_prepare($koneksi, "SELECT * FROM songs WHERE id_song = ?");
    mysqli_stmt_bind_param($stmt_select, "i", $id);
    mysqli_stmt_execute($stmt_select);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_select));

    if (!$data) {
        echo "<script>alert('Data lagu tidak ditemukan!'); window.location='songs.php';</script>";
        exit;
    }
}

//Submit
if (isset($_POST['simpan'])) {
    $id_album       = $_POST['id_album'];
    $judul_lagu     = $_POST['judul_lagu'];
    $nomor_track    = $_POST['nomor_track'];
    $is_recommended = $_POST['is_recommended'];
    $youtube_id     = !empty($_POST['youtube_id']) ? $_POST['youtube_id'] : NULL;
    $spotify_url    = !empty($_POST['spotify_url']) ? $_POST['spotify_url'] : NULL;

    if ($aksi == 'tambah') {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO songs (id_album, judul_lagu, nomor_track, is_recommended, youtube_id, spotify_url) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "isisss", $id_album, $judul_lagu, $nomor_track, $is_recommended, $youtube_id, $spotify_url);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Lagu berhasil ditambahkan!'); window.location='songs.php';</script>";
        } else {
            echo "<script>alert('Gagal menambahkan lagu!'); window.location='songs_form.php?aksi=tambah';</script>";
        }
        exit;
    }

    if ($aksi == 'edit') {
        $stmt = mysqli_prepare($koneksi, "UPDATE songs SET id_album=?, judul_lagu=?, nomor_track=?, is_recommended=?, youtube_id=?, spotify_url=? WHERE id_song=?");
        mysqli_stmt_bind_param($stmt, "isisssi", $id_album, $judul_lagu, $nomor_track, $is_recommended, $youtube_id, $spotify_url, $id);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Lagu berhasil diperbarui!'); window.location='songs.php';</script>";
        } else {
            echo "<script>alert('Gagal memperbarui lagu!'); window.location='songs_form.php?aksi=edit&id=$id';</script>";
        }
        exit;
    }
}

//Nilai awal form (kosong untuk tambah, terisi untuk edit)
$v = fn($key) => $data ? htmlspecialchars($data[$key] ?? '') : '';

include 'header.php';
include 'sidebar.php';
?>

<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <h2><?= $aksi == 'edit' ? 'Edit Data Lagu' : 'Tambah Lagu Baru'; ?></h2>
    </div>
  </section>

  <section class="content">
    <div class="card">
      <div class="card-body">
        <form action="" method="POST">
          <div class="form-group">
            <label>Judul Lagu</label>
            <input type="text" name="judul_lagu" class="form-control" value="<?= $v('judul_lagu'); ?>" required>
          </div>
          <div class="form-group">
            <label><?= $aksi == 'edit' ? 'Album' : 'Pilih Album'; ?></label>
            <select name="id_album" class="form-control" required>
              <option value="">-- Pilih Album --</option>
              <?php
              $q_album = mysqli_query($koneksi, "SELECT id_album, judul FROM albums ORDER BY judul ASC");
              while ($album = mysqli_fetch_assoc($q_album)) {
                  $selected = ($data && $album['id_album'] == $data['id_album']) ? 'selected' : '';
                  echo "<option value='{$album['id_album']}' {$selected}>" . htmlspecialchars($album['judul']) . "</option>";
              }
              ?>
            </select>
          </div>
          <div class="form-group">
            <label>Nomor Track</label>
            <input type="number" name="nomor_track" class="form-control" value="<?= $v('nomor_track'); ?>" required>
          </div>
          <div class="form-group">
            <label>Rekomendasi?</label>
            <select name="is_recommended" class="form-control" required>
              <option value="N" <?= ($data['is_recommended'] ?? 'N') == 'N' ? 'selected' : ''; ?>>Tidak (N)</option>
              <option value="Y" <?= ($data['is_recommended'] ?? 'N') == 'Y' ? 'selected' : ''; ?>>Ya (Y)</option>
            </select>
          </div>
          <div class="form-group">
            <label>ID YouTube <?= $aksi == 'tambah' ? '(Opsional)' : ''; ?></label>
            <input type="text" name="youtube_id" class="form-control" value="<?= $v('youtube_id'); ?>" placeholder="Contoh: dQw4w9WgXcQ">
          </div>
          <div class="form-group">
            <label>URL Spotify <?= $aksi == 'tambah' ? '(Opsional)' : ''; ?></label>
            <input type="text" name="spotify_url" class="form-control" value="<?= $v('spotify_url'); ?>" placeholder="Contoh: https://open.spotify.com/track/...">
          </div>

          <?php if ($aksi == 'edit') : ?>
            <button type="submit" name="simpan" class="btn btn-warning"><i class="fas fa-edit"></i> Update Data</button>
          <?php else : ?>
            <button type="submit" name="simpan" class="btn btn-success"><i class="fas fa-save"></i> Simpan Data</button>
          <?php endif; ?>
          <a href="songs.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
        </form>
      </div>
    </div>
  </section>
</div>