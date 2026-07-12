<?php
session_start();
require '../../conn.php';
$area = $_SESSION['area'];

// ======================
// CEK LOGIN
// ======================
if (!isset($_SESSION['role'])) {
    header("Location: ../../login.php");
    exit;
}

if (
    $_SESSION['role'] != 'REPAIRMAN'
) {
    header("Location: ../../login.php");
    exit;
}

// Button Save
if (isset($_POST['btn_save'])) {

    $product_id   = mysqli_real_escape_string($conn, $_POST['product_id']);
    $model_code   = mysqli_real_escape_string($conn, $_POST['model_code']);
    $defect_id    = mysqli_real_escape_string($conn, $_POST['defect_id']);
    $category_id  = mysqli_real_escape_string($conn, $_POST['category_id']);
    $rootcause_id = mysqli_real_escape_string($conn, $_POST['rootcause_id']);
    $action_id    = mysqli_real_escape_string($conn, $_POST['action_id']);
    $remark       = mysqli_real_escape_string($conn, $_POST['remark']);

    $username = $_SESSION['username'];
    $name     = $_SESSION['name'];

    // Upload evidence photo (optional)
    $evidence_photo = '';

    if (
        isset($_FILES['evidence_photo']) &&
        $_FILES['evidence_photo']['error'] == 0
    ) {

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        $ext = strtolower(
            pathinfo(
                $_FILES['evidence_photo']['name'],
                PATHINFO_EXTENSION
            )
        );

        if (in_array($ext, $allowed)) {

            $filename =
                'LD_' .
                date('YmdHis') .
                '_' .
                rand(1000, 9999) .
                '.' .
                $ext;

            $upload_dir = '../../uploads/';

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            if (
                move_uploaded_file(
                    $_FILES['evidence_photo']['tmp_name'],
                    $upload_dir . $filename
                )
            ) {
                $evidence_photo = $filename;
            }
        }
    }

    mysqli_query(
        $conn,
        "INSERT INTO line_drop_transaction
        (
            product_id,
            model_code,
            area,
            defect_id,
            category_id,
            rootcause_id,
            action_id,
            remark,
            evidence_photo,
            created_by,
            created_name
        )
        VALUES
        (
            '$product_id',
            '$model_code',
            '$area',
            '$defect_id',
            '$category_id',
            '$rootcause_id',
            '$action_id',
            '$remark',
            '$evidence_photo',
            '$username',
            '$name'
        )"
    );

    echo "<script>
            alert('Data Line Drop berhasil disimpan');
            window.location='index.php';
          </script>";
}

// ======================
// MASTER DATA
// ======================
$modelData = mysqli_query(
    $conn,
    "SELECT * FROM model_master WHERE area = '$area' ORDER BY model_name"
);

$defectData = mysqli_query(
    $conn,
    "SELECT * FROM defect_master WHERE area = '$area' ORDER BY defect_name"
);

$categoryData = mysqli_query(
    $conn,
    "SELECT * FROM category_master ORDER BY category_name"
);

$rootcauseData = mysqli_query(
    $conn,
    "SELECT * FROM rootcause_master WHERE area = '$area' ORDER BY rootcause_name"
);

$actionData = mysqli_query(
    $conn,
    "SELECT * FROM action_master WHERE area = '$area' ORDER BY action_name"
);

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Operator Line Drop</title>
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
    <!-- html5-qrcode -->
    <script src="https://unpkg.com/html5-qrcode"></script>
</head>

<body class="bg-light">
    <!-- NAVBAR -->
    <nav class="navbar navbar-dark bg-primary shadow">
        <div class="container-fluid">
            <span class="navbar-brand">
                OPERATOR PANEL
            </span>
            <div class="text-white">
                <?= $_SESSION['name']; ?>
                |
                <a href="../../logout.php"
                    class="btn btn-light btn-sm">
                    Logout
                </a>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            Scan Product ID
                        </h4>
                    </div>
                    <div class="card-body">
                        <!-- BUTTON SCAN -->
                        <div class="mb-3">
                            <button type="button"
                                onclick="startScan()"
                                class="btn btn-primary">
                                Start Scan
                            </button>
                            <button type="button"
                                onclick="stopScan()"
                                class="btn btn-danger">
                                Stop Camera
                            </button>
                            <a href="history.php"
                                class="btn btn-dark">
                                History Line Drop
                            </a>
                        </div>
                        <!-- CAMERA -->
                        <div id="reader"
                            class="mb-3">
                        </div>
                        <!-- FORM -->
                        <form method="POST" enctype="multipart/form-data">
                            <!-- PRODUCT ID -->
                            <div class="mb-3">
                                <label class="form-label">
                                    Product ID
                                </label>
                                <input type="text"
                                    id="product_id"
                                    name="product_id"
                                    class="form-control form-control-lg"
                                    readonly
                                    required>
                            </div>
                            <!-- MODEL -->
                            <div class="mb-3">
                                <label class="form-label">
                                    Model Code
                                </label>
                                <input type="text"
                                    id="model_code"
                                    name="model_code"
                                    class="form-control"
                                    readonly
                                    required>
                            </div>
                            <!-- DEFECT -->
                            <div class="mb-3">
                                <label class="form-label">
                                    Defect
                                </label>
                                <select name="defect_id"
                                    class="form-select"
                                    required>
                                    <option value="">
                                        -- Select Defect --
                                    </option>
                                    <?php while ($d = mysqli_fetch_assoc($defectData)) : ?>
                                        <option value="<?= $d['defect_id']; ?>">
                                            <?= $d['defect_name']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <!-- CATEGORY -->
                            <div class="mb-3">
                                <label class="form-label">
                                    Category
                                </label>
                                <select name="category_id"
                                    class="form-select"
                                    required>
                                    <option value="">
                                        -- Select Category --
                                    </option>
                                    <?php while ($c = mysqli_fetch_assoc($categoryData)) : ?>
                                        <option value="<?= $c['category_id']; ?>">
                                            <?= $c['category_name']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <!-- ROOTCAUSE -->
                            <div class="mb-3">
                                <label class="form-label">
                                    Root Cause
                                </label>
                                <select name="rootcause_id"
                                    class="form-select"
                                    required>
                                    <option value="">
                                        -- Select Root Cause --
                                    </option>
                                    <?php while ($r = mysqli_fetch_assoc($rootcauseData)) : ?>
                                        <option value="<?= $r['rootcause_id']; ?>">
                                            <?= $r['rootcause_name']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <!-- ACTION -->
                            <div class="mb-3">
                                <label class="form-label">
                                    Action Production
                                </label>
                                <select name="action_id"
                                    class="form-select"
                                    required>
                                    <option value="">
                                        -- Select Action --
                                    </option>
                                    <?php while ($a = mysqli_fetch_assoc($actionData)) : ?>
                                        <option value="<?= $a['action_id']; ?>">
                                            <?= $a['action_name']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <!-- REMARK -->
                            <div class="mb-3">
                                <label class="form-label">
                                    Remark
                                </label>
                                <textarea name="remark"
                                    class="form-control"
                                    rows="3"></textarea>
                            </div>
                            <!-- EVIDENCE PHOTO -->
                            <div class="mb-3">
                                <input type="file"
                                    id="evidence_photo"
                                    name="evidence_photo"
                                    class="form-control"
                                    accept="image/*"
                                    capture="environment">

                                <small id="photo_info" class="text-success"></small>
                            </div>
                            <!-- BUTTON -->
                            <div class="d-grid">
                                <button type="submit"
                                    name="btn_save"
                                    class="btn btn-success btn-lg">
                                    SAVE LINE DROP
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../../js/browser-image-compression.js"></script>
    <script>
        let html5QrCode;
        // START SCAN
        function startScan() {
            html5QrCode = new Html5Qrcode("reader");
            html5QrCode.start({
                    facingMode: "environment"
                }, {
                    fps: 10,
                    qrbox: {
                        width: 250,
                        height: 250
                    }
                },
                function(decodedText) {
                    // Isi Product ID
                    document.getElementById("product_id").value = decodedText;
                    // Ambil 8 digit terakhir
                    let modelCode = decodedText.slice(-8);
                    // Isi model code
                    document.getElementById("model_code").value = modelCode;
                    // Stop camera
                    stopScan();
                },
                function(errorMessage) {
                    // ignore
                }
            ).catch((err) => {
                alert("Gagal membuka kamera : " + err);
            });
        }

        // STOP CAMERA
        function stopScan() {
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                });
            }
        }
    </script>
    <script>
        document.getElementById('evidence_photo').addEventListener(
            'change',
            async function(e) {

                const file = e.target.files[0];

                if (!file) return;

                try {

                    const options = {
                        maxSizeMB: 0.2,
                        maxWidthOrHeight: 1000,
                        initialQuality: 0.6,
                        useWebWorker: true
                    };

                    const compressedBlob =
                        await imageCompression(file, options);

                    const compressedFile =
                        new File(
                            [compressedBlob],
                            file.name, {
                                type: compressedBlob.type,
                                lastModified: Date.now()
                            }
                        );

                    const dt = new DataTransfer();

                    dt.items.add(compressedFile);

                    this.files = dt.files;

                    console.log(
                        'Original:',
                        (file.size / 1024 / 1024).toFixed(2),
                        'MB'
                    );

                    console.log(
                        'Compressed:',
                        (compressedFile.size / 1024 / 1024).toFixed(2),
                        'MB'
                    );

                } catch (err) {

                    console.error(err);

                }

            }
        );
    </script>
</body>

</html>