<?php
session_start();
require '../../../conn.php';

date_default_timezone_set('Asia/Jakarta');


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
    "
    SELECT 
        t.*,
        d.defect_name,
        c.category_name,
        r.rootcause_name,
        a.action_name

    FROM line_drop_transaction t

    LEFT JOIN defect_master d
        ON t.defect_id = d.defect_id

    LEFT JOIN category_master c
        ON t.category_id = c.category_id

    LEFT JOIN rootcause_master r
        ON t.rootcause_id = r.rootcause_id

    LEFT JOIN action_master a
        ON t.action_id = a.action_id

    WHERE t.transaction_id='$id'
    "
);


if (!$query) {

    die("SQL ERROR : " . mysqli_error($conn));
}


if (mysqli_num_rows($query) == 0) {

    echo "
    <script>
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

    echo "
    <script>
    alert('Data sudah lebih dari 24 jam, tidak dapat dihapus!');
    window.location='../dashboard.php';
    </script>";

    exit;
}



// ======================
// BUAT LOG
// ======================

$username = $_SESSION['username'] ?? '-';
$name     = $_SESSION['name'] ?? '-';


$ip = $_SERVER['REMOTE_ADDR'] ?? '-';


$log  = "====================================================\n";
$log .= "Tanggal      : " . date("Y-m-d H:i:s") . "\n";
$log .= "User         : " . $username . " (" . $name . ")\n";
$log .= "IP Address   : " . $ip . "\n";
$log .= "Action       : DELETE\n";

$log .= "Transaction  : " . $data['transaction_id'] . "\n";
$log .= "Product ID   : " . $data['product_id'] . "\n";
$log .= "Model        : " . $data['model_code'] . "\n";
$log .= "Area         : " . $data['area'] . "\n";

$log .= "Defect       : " . $data['defect_name'] . "\n";
$log .= "Category     : " . $data['category_name'] . "\n";
$log .= "Root Cause   : " . $data['rootcause_name'] . "\n";
$log .= "Action       : " . $data['action_name'] . "\n";

$log .= "Remark       : " . $data['remark'] . "\n";

$log .= "Created By   : " . $data['created_by'] . "\n";
$log .= "Created Name : " . $data['created_name'] . "\n";
$log .= "Created At   : " . $data['created_at'] . "\n";

$log .= "====================================================\n\n";



// ======================
// TULIS LOG (SEBELUM DELETE)
// ======================

file_put_contents(
    "logfile.txt",
    $log,
    FILE_APPEND | LOCK_EX
);



// ======================
// HAPUS DATA
// ======================

$delete = mysqli_query(
    $conn,
    "
    DELETE FROM line_drop_transaction
    WHERE transaction_id='$id'
    "
);


if ($delete) {

    echo "
    <script>
    alert('Data berhasil dihapus.');
    window.location='../dashboard.php';
    </script>";
} else {

    echo "
    <script>
    alert('Gagal menghapus data.');
    window.location='../dashboard.php';
    </script>";
}
