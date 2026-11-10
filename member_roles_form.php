<?php
require_once __DIR__ . '/koneksi.php';

// Pilih aksi
$aksi = $_GET['aksi'] ?? 'tambah';
$id_member_lama = isset($_GET['id_member']) ? (int) $_GET['id_member'] : 0;
$id_role_lama = isset($_GET['id_role']) ? (int) $_GET['id_role'] : 0;
$data = null;

// Validasi aksi
if (!in_array($aksi, ['tambah', 'edit', 'hapus'])) {
    $aksi = 'tambah';
}

// Hapus data
if ($aksi == 'hapus') {
    $stmt = mysqli_prepare($koneksi, "DELETE FROM member_roles WHERE id_member=? AND id_role=?");
    mysqli_stmt_bind_param($stmt, "ii", $id_member_lama, $id_role_lama);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Data member role berhasil dihapus!'); window.location='member_roles.php';</script>";
    } else {
        echo "<script>alert('Data member role gagal dihapus!'); window.location='member_roles.php';</script>";
    }
    exit;
}

// Ambil data untuk diedit
if ($aksi == 'edit') {
    $stmt = mysqli_prepare($koneksi, "SELECT * FROM member_roles WHERE id_member=? AND id_role=?");
    mysqli_stmt_bind_param($stmt, "ii", $id_member_lama, $id_role_lama);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    if (!$data) {
        echo "<script>alert('Data member role tidak ditemukan!'); window.location='member_roles.php';</script>";
        exit;
    }
}

// Simpan data
if (isset($_POST['simpan'])) {
    $id_member = (int) $_POST['id_member'];
    $id_role = (int) $_POST['id_role'];
    $urutan_tampil = (int) $_POST['urutan_tampil'];

    if ($id_member <= 0 || $id_role <= 0 || $urutan_tampil <= 0) {
        echo "<script>alert('Semua data harus diisi dengan benar!');</script>";
    } else {
        // Periksa apakah kombinasi member dan role sudah ada
        if ($aksi == 'tambah') {
            $cek = mysqli_prepare($koneksi, "SELECT * FROM member_roles WHERE id_member=? AND id_role=?");
            mysqli_stmt_bind_param($cek, "ii", $id_member, $id_role);
        } else {
            $cek = mysqli_prepare($koneksi, "SELECT * FROM member_roles WHERE id_member=? AND id_role=? AND NOT (id_member=? AND id_role=?)");
            mysqli_stmt_bind_param($cek, "iiii", $id_member, $id_role, $id_member_lama, $id_role_lama);
        }

        mysqli_stmt_execute($cek);
        $hasil = mysqli_stmt_get_result($cek);

        if (mysqli_num_rows($hasil) > 0) {
            echo "<script>alert('Role tersebut sudah dimiliki oleh member ini!');</script>";
        } else {
            // Tambah data
            if ($aksi == 'tambah') {
                $stmt = mysqli_prepare($koneksi, "INSERT INTO member_roles (id_member, id_role, urutan_tampil) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "iii", $id_member, $id_role, $urutan_tampil);

                if (mysqli_stmt_execute($stmt)) {
                    echo "<script>alert('Data member role berhasil ditambahkan!'); window.location='member_roles.php';</script>";
                } else {
                    echo "<script>alert('Data member role gagal ditambahkan!');</script>";
                }
                exit;
            }

            // Perbarui data
            if ($aksi == 'edit') {
                $stmt = mysqli_prepare($koneksi, "UPDATE member_roles SET id_member=?, id_role=?, urutan_tampil=? WHERE id_member=? AND id_role=?");
                mysqli_stmt_bind_param($stmt, "iiiii", $id_member, $id_role, $urutan_tampil, $id_member_lama, $id_role_lama);

                if (mysqli_stmt_execute($stmt)) {
                    echo "<script>alert('Data member role berhasil diperbarui!'); window.location='member_roles.php';</script>";
                } else {
                    echo "<script>alert('Data member role gagal diperbarui!');</script>";
                }
                exit;
            }
        }
    }
}

// Ambil pilihan member dan role
$members = mysqli_query($koneksi, "SELECT * FROM members ORDER BY nama ASC");
$roles = mysqli_query($koneksi, "SELECT * FROM roles ORDER BY nama_role ASC");

include 'header.php';
include 'sidebar.php';
?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h2><?= $aksi == 'edit' ? 'Edit Member Role' : 'Tambah Member Role Baru'; ?></h2>
        </div>
    </section>

    <section class="content">
        <div class="card">
            <div class="card-body">
                <form action="" method="POST">
                    <div class="form-group">
                        <label>Nama Member</label>
                        <select name="id_member" class="form-control" required>
                            <option value="">-- Pilih Member --</option>
                            <?php while ($member = mysqli_fetch_assoc($members)) : ?>
                                <option value="<?= $member['id_member']; ?>"
                                    <?= ($data && $data['id_member'] == $member['id_member']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($member['nama']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Nama Role</label>
                        <select name="id_role" class="form-control" required>
                            <option value="">-- Pilih Role --</option>
                            <?php while ($role = mysqli_fetch_assoc($roles)) : ?>
                                <option value="<?= $role['id_role']; ?>"
                                    <?= ($data && $data['id_role'] == $role['id_role']) ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($role['nama_role']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Urutan Tampil</label>
                        <input type="number" name="urutan_tampil" class="form-control"
                               value="<?= $data ? htmlspecialchars($data['urutan_tampil']) : '1'; ?>"
                               min="1" placeholder="Masukkan urutan tampil" required>
                    </div>

                    <?php if ($aksi == 'edit') : ?>
                        <button type="submit" name="simpan" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Perbarui Data
                        </button>
                    <?php else : ?>
                        <button type="submit" name="simpan" class="btn btn-success">
                            <i class="fas fa-save"></i> Simpan Data
                        </button>
                    <?php endif; ?>

                    <a href="member_roles.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Batal
                    </a>
                </form>
            </div>
        </div>
    </section>
</div>
