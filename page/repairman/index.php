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

    // ===================================
    // SAVE PHOTO FROM CAMERA (BASE64)
    // ===================================

    $evidence_photo = '';

    if (!empty($_POST['photo_base64'])) {

        $image = $_POST['photo_base64'];

        // Hapus header base64
        $image = preg_replace('#^data:image/\w+;base64,#i', '', $image);

        // Ganti spasi menjadi +
        $image = str_replace(' ', '+', $image);

        $imageData = base64_decode($image);

        if ($imageData !== false) {

            $upload_dir = '../../uploads/';

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $filename =
                'LD_' .
                date('YmdHis') .
                '_' .
                rand(1000, 9999) .
                '.jpg';

            file_put_contents(
                $upload_dir . $filename,
                $imageData
            );

            $evidence_photo = $filename;
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
                                <label class="form-label fw-bold">
                                    Evidence Photo
                                </label>
                                <div class="border rounded p-2 bg-light">
                                    <video
                                        id="camera"
                                        autoplay
                                        playsinline
                                        muted
                                        class="w-100 rounded"
                                        style="max-height:350px;display:none;">
                                    </video>
                                    <canvas
                                        id="canvas"
                                        style="display:none;">
                                    </canvas>
                                    <img
                                        id="preview"
                                        class="img-fluid rounded mt-2"
                                        style="display:none;">
                                    <input
                                        type="hidden"
                                        id="photo_base64"
                                        name="photo_base64">
                                    <div class="mt-3 d-flex gap-2">
                                        <button
                                            type="button"
                                            class="btn btn-primary"
                                            onclick="openCamera()">
                                            Open Camera
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-success"
                                            onclick="capturePhoto()">
                                            Capture Photo
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-danger"
                                            onclick="stopCamera()">
                                            Close Camera
                                        </button>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        Setelah Capture Photo, klik SAVE LINE DROP.
                                    </small>
                                </div>
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
        let html5QrCode = null;
        let cameraStream = null;

        // =======================
        // START QR SCAN
        // =======================
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
                    document.getElementById("product_id").value = decodedText;
                    let modelCode = decodedText.slice(-8);
                    document.getElementById("model_code").value = modelCode;
                    stopScan();
                    openCamera();
                },
                function(errorMessage) {}
            ).catch(function(err) {
                alert("Camera Error : " + err);
            });
        }
        // STOP QR SCAN
        function stopScan() {
            if (html5QrCode) {
                html5QrCode.stop().then(function() {
                    html5QrCode.clear();
                });
            }
        }
        // OPEN CAMERA
        async function openCamera() {
            try {
                cameraStream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: "environment"
                    }
                });
                document.getElementById("camera").srcObject = cameraStream;
                document.getElementById("camera").style.display = "block";
            } catch (err) {
                alert("Tidak bisa membuka kamera.");
            }
        }

        // CAPTURE PHOTO
        function capturePhoto() {
            const video = document.getElementById("camera");
            const canvas = document.getElementById("canvas");
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext("2d");
            ctx.drawImage(video, 0, 0);
            const image = canvas.toDataURL("image/jpeg", 0.8);
            // Simpan ke input hidden
            document.getElementById("photo_base64").value = image;
            // Preview hasil foto
            document.getElementById("preview").src = image;
            document.getElementById("preview").style.display = "block";
            // Tutup kamera otomatis
            stopCamera();
            alert("Photo berhasil diambil.");
        }

        // STOP CAMERA
        function stopCamera() {
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                document.getElementById("camera").srcObject = null;
                cameraStream = null;
            }
        }
        // SAVE FORM
        document.querySelector("form").addEventListener("submit", function(e) {
            if (document.getElementById("photo_base64").value == "") {
                e.preventDefault();
                alert("Silakan Capture Photo terlebih dahulu.");
                return false;
            }
        });
        window.onbeforeunload = function() {
            stopCamera();
        };
    </script>
</body>

</html>