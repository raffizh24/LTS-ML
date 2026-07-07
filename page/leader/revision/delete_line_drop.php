<?php
session_start();
require '../../../conn.php';

// ======================
// CEK LOGIN
// ======================
if (!isset($_SESSION['role'])) {
    header("Location: ../../index.php");
    exit;
}

if ($_SESSION['role'] != 'LEADER') {
    header("Location: ../../index.php");
    exit;
}

// ======================
// CEK ID
// ======================
if (!isset($_GET['id'])) {
    header("Location: ../dashboard.php");
    exit;
}

$id = (int) $_GET['id'];

// ======================
// AMBIL DATA
// ======================
$query = mysqli_query(
    $conn,
    "SELECT transaction_id, created_at
     FROM line_drop_transaction
     WHERE transaction_id = '$id'"
);

if (mysqli_num_rows($query) == 0) {
    echo "<script>
            alert('Data tidak ditemukan!');
            window.location='../dashboard.php';
          </script>";
    exit;
}

$data = mysqli_fetch_assoc($query);

// ======================
// VALIDASI 24 JAM
// ======================
$selisih = time() - strtotime($data['created_at']);

if ($selisih > 86400) {

    echo "<script>
            alert('Data sudah lebih dari 24 jam, tidak dapat dihapus!');
            window.location='../dashboard.php';
          </script>";
    exit;
}

// ======================
// HAPUS DATA
// ======================
$delete = mysqli_query(
    $conn,
    "DELETE FROM line_drop_transaction
     WHERE transaction_id = '$id'"
);

if ($delete) {

    echo "<script>
            alert('Data berhasil dihapus.');
            window.location='../dashboard.php';
          </script>";
} else {

    echo "<script>
            alert('Gagal menghapus data.');
            window.location='../dashboard.php';
          </script>";
}
