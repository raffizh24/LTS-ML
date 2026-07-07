<?php
session_start();
require '../../../conn.php';
// ======================
// CEK LOGIN
// ======================
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'LEADER') {
    header("Location: ../../login.php");
    exit;
}

// ======================
// GET DATA
// ======================
$area = $_SESSION['area'];
if (!isset($_GET['id'])) {
    header("Location: ../dashboard.php");
    exit;
}
$transaction_id = (int)$_GET['id'];
$q = mysqli_query($conn, "SELECT * FROM line_drop_transaction WHERE transaction_id='$transaction_id' AND area='$area'");
if (mysqli_num_rows($q) == 0) {
    echo "<script>alert('Data tidak ditemukan');location='../dashboard.php';</script>";
    exit;
}
$data = mysqli_fetch_assoc($q);
if ((time() - strtotime($data['created_at'])) > 86400) {
    echo "<script>alert('Data sudah lebih dari 24 jam');location='../dashboard.php';</script>";
    exit;
}
$defectData = mysqli_query($conn, "SELECT * FROM defect_master WHERE area='$area' ORDER BY defect_name");
$categoryData = mysqli_query($conn, "SELECT * FROM category_master ORDER BY category_name");
$rootcauseData = mysqli_query($conn, "SELECT * FROM rootcause_master WHERE area='$area' ORDER BY rootcause_name");
$actionData = mysqli_query($conn, "SELECT * FROM action_master WHERE area='$area' ORDER BY action_name");

// ======================
// UPDATE DATA
// ======================
if (isset($_POST['btn_update'])) {
    $transaction_id = (int)$_POST['transaction_id'];
    $defect_id = (int)$_POST['defect_id'];
    $category_id = (int)$_POST['category_id'];
    $rootcause_id = (int)$_POST['rootcause_id'];
    $action_id = (int)$_POST['action_id'];
    $remark = mysqli_real_escape_string($conn, $_POST['remark']);
    // Cek apakah data masih ada dan sesuai area
    $cek = mysqli_query(
        $conn,
        "
        SELECT created_at
        FROM line_drop_transaction
        WHERE transaction_id='$transaction_id'
        AND area='$area'
        "
    );

    if (mysqli_num_rows($cek) == 0) {
        echo "
        <script>
            alert('Data tidak ditemukan!');
            window.location='../dashboard.php';
        </script>
        ";
        exit;
    }
    $row = mysqli_fetch_assoc($cek);
    // Cek maksimal 24 jam
    if ((time() - strtotime($row['created_at'])) > 86400) {
        echo "
        <script>
            alert('Data sudah lebih dari 24 jam dan tidak dapat diedit!');
            window.location='../dashboard.php';
        </script>
        ";
        exit;
    }
    // Update
    $update = mysqli_query(
        $conn,
        "
        UPDATE line_drop_transaction
        SET
            defect_id = '$defect_id',
            category_id = '$category_id',
            rootcause_id = '$rootcause_id',
            action_id = '$action_id',
            remark = '$remark'
        WHERE
            transaction_id = '$transaction_id'
            AND area = '$area'
        "
    );
    if ($update) {
        echo "
        <script>
            alert('Data berhasil diupdate.');
            window.location='../dashboard.php';
        </script>
        ";
    } else {
        echo "
        <script>
            alert('Gagal mengupdate data.');
        </script>
        ";
    }
    exit;
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Edit Line Drop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary">
        <div class="container-fluid">
            <span class="navbar-brand">EDIT LINE DROP</span>
            <div class="text-white"><?= $_SESSION['name']; ?> | <a href="../dashboard.php" class="btn btn-light btn-sm">Back</a></div>
        </div>
    </nav>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-warning">
                        <h4 class="mb-0">Edit Line Drop</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="transaction_id" value="<?= $data['transaction_id']; ?>">
                            <div class="mb-3">
                                <label class="form-label">Product ID</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($data['product_id']); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Model Code</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($data['model_code']); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Defect</label>
                                <select name="defect_id" class="form-select" required>
                                    <?php while ($d = mysqli_fetch_assoc($defectData)): ?>
                                        <option value="<?= $d['defect_id']; ?>" <?= $data['defect_id'] == $d['defect_id'] ? 'selected' : ''; ?>><?= $d['defect_name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select" required>
                                    <?php while ($c = mysqli_fetch_assoc($categoryData)): ?>
                                        <option value="<?= $c['category_id']; ?>" <?= $data['category_id'] == $c['category_id'] ? 'selected' : ''; ?>><?= $c['category_name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Root Cause</label>
                                <select name="rootcause_id" class="form-select" required>
                                    <?php while ($r = mysqli_fetch_assoc($rootcauseData)): ?>
                                        <option value="<?= $r['rootcause_id']; ?>" <?= $data['rootcause_id'] == $r['rootcause_id'] ? 'selected' : ''; ?>><?= $r['rootcause_name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Action Production</label>
                                <select name="action_id" class="form-select" required>
                                    <?php while ($a = mysqli_fetch_assoc($actionData)): ?>
                                        <option value="<?= $a['action_id']; ?>" <?= $data['action_id'] == $a['action_id'] ? 'selected' : ''; ?>><?= $a['action_name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Remark</label>
                                <textarea name="remark" class="form-control" rows="3"><?= htmlspecialchars($data['remark']); ?></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" name="btn_update" class="btn btn-warning btn-lg">UPDATE LINE DROP</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>