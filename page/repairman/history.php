<?php
session_start();
require '../../conn.php';

// =========================
// CEK LOGIN
// =========================
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

$username = $_SESSION['username'];
$area = $_SESSION['area'];

// =========================
// UPDATE DATA + LOG
// =========================
if (isset($_POST['btn_update'])) {

    date_default_timezone_set('Asia/Jakarta');


    $id            = (int)$_POST['transaction_id'];
    $defect_id     = (int)$_POST['defect_id'];
    $category_id   = (int)$_POST['category_id'];
    $rootcause_id  = (int)$_POST['rootcause_id'];
    $action_id     = (int)$_POST['action_id'];
    $remark        = mysqli_real_escape_string($conn, $_POST['remark']);



    // =========================
    // AMBIL DATA LAMA
    // =========================

    $oldQuery = mysqli_query(
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
            ON t.defect_id=d.defect_id

        LEFT JOIN category_master c
            ON t.category_id=c.category_id

        LEFT JOIN rootcause_master r
            ON t.rootcause_id=r.rootcause_id

        LEFT JOIN action_master a
            ON t.action_id=a.action_id

        WHERE t.transaction_id='$id'
        AND t.created_by='$username'
        AND DATE(t.created_at)=CURDATE()
        "
    );


    if (mysqli_num_rows($oldQuery) == 0) {

        echo "
        <script>
        alert('Data tidak ditemukan');
        window.location='history.php';
        </script>";

        exit;
    }


    $old = mysqli_fetch_assoc($oldQuery);



    // =========================
    // FOTO UPDATE
    // =========================

    $update_photo = "";


    if (
        isset($_FILES['evidence_photo']) &&
        $_FILES['evidence_photo']['error'] == 0
    ) {


        if (
            !empty($old['evidence_photo']) &&
            file_exists('../../uploads/' . $old['evidence_photo'])
        ) {

            unlink(
                '../../uploads/' . $old['evidence_photo']
            );
        }



        $ext = strtolower(
            pathinfo(
                $_FILES['evidence_photo']['name'],
                PATHINFO_EXTENSION
            )
        );


        $filename =
            "LD_" .
            date('YmdHis') .
            "_" .
            rand(1000, 9999) .
            "." . $ext;


        move_uploaded_file(
            $_FILES['evidence_photo']['tmp_name'],
            '../../uploads/' . $filename
        );


        $update_photo =
            ", evidence_photo='$filename'";
    }



    // =========================
    // LOG EDIT
    // =========================


    $log = "";

    $log .= "====================================================\n";

    $log .= "DATE EDIT    : " . date("Y-m-d H:i:s") . "\n";

    $log .= "USER         : " . $_SESSION['username'] .
        " (" . $_SESSION['name'] . ")\n";

    $log .= "ROLE         : REPAIRMAN\n";

    $log .= "IP ADDRESS   : " . $_SERVER['REMOTE_ADDR'] . "\n";

    $log .= "ACTION       : UPDATE\n";


    $log .= "TRANSACTION  : " . $old['transaction_id'] . "\n";

    $log .= "PRODUCT ID   : " . $old['product_id'] . "\n";

    $log .= "MODEL        : " . $old['model_code'] . "\n";

    $log .= "AREA         : " . $old['area'] . "\n\n";



    $log .= "----- BEFORE -----\n";

    $log .= "DEFECT       : " . $old['defect_name'] . "\n";

    $log .= "CATEGORY     : " . $old['category_name'] . "\n";

    $log .= "ROOTCAUSE    : " . $old['rootcause_name'] . "\n";

    $log .= "ACTION       : " . $old['action_name'] . "\n";

    $log .= "REMARK       : " . $old['remark'] . "\n";

    $log .= "PHOTO        : " . $old['evidence_photo'] . "\n\n";



    // DATA BARU

    $newDefect = mysqli_fetch_assoc(
        mysqli_query(
            $conn,
            "
            SELECT defect_name
            FROM defect_master
            WHERE defect_id='$defect_id'
            "
        )
    );


    $newCategory = mysqli_fetch_assoc(
        mysqli_query(
            $conn,
            "
            SELECT category_name
            FROM category_master
            WHERE category_id='$category_id'
            "
        )
    );


    $newRoot = mysqli_fetch_assoc(
        mysqli_query(
            $conn,
            "
            SELECT rootcause_name
            FROM rootcause_master
            WHERE rootcause_id='$rootcause_id'
            "
        )
    );


    $newAction = mysqli_fetch_assoc(
        mysqli_query(
            $conn,
            "
            SELECT action_name
            FROM action_master
            WHERE action_id='$action_id'
            "
        )
    );



    $log .= "----- AFTER -----\n";

    $log .= "DEFECT       : " . $newDefect['defect_name'] . "\n";

    $log .= "CATEGORY     : " . $newCategory['category_name'] . "\n";

    $log .= "ROOTCAUSE    : " . $newRoot['rootcause_name'] . "\n";

    $log .= "ACTION       : " . $newAction['action_name'] . "\n";

    $log .= "REMARK       : " . $remark . "\n";


    if ($update_photo != "") {

        $log .= "PHOTO        : " . $filename . "\n";
    } else {

        $log .= "PHOTO        : No Change\n";
    }


    $log .= "====================================================\n\n";




    // =========================
    // SIMPAN LOG
    // =========================

    $log_folder = "../../logs/";


    if (!is_dir($log_folder)) {
        mkdir($log_folder, 0777, true);
    }



    file_put_contents(
        $log_folder . "edit_line_drop_repairman.log",
        $log,
        FILE_APPEND | LOCK_EX
    );




    // =========================
    // UPDATE DATABASE
    // =========================


    mysqli_query(
        $conn,
        "
        UPDATE line_drop_transaction SET

            defect_id='$defect_id',
            category_id='$category_id',
            rootcause_id='$rootcause_id',
            action_id='$action_id',
            remark='$remark'

            $update_photo


        WHERE transaction_id='$id'

        AND created_by='$username'

        AND DATE(created_at)=CURDATE()
        "
    );


    echo "
    <script>
    alert('Data berhasil diupdate');
    window.location='history.php';
    </script>";
}


// =========================
// MASTER DATA
// =========================
$defectData = mysqli_query(
    $conn,
    "SELECT * FROM defect_master
     ORDER BY defect_name"
);

$categoryData = mysqli_query(
    $conn,
    "SELECT * FROM category_master
     ORDER BY category_name"
);

$rootcauseData = mysqli_query(
    $conn,
    "SELECT * FROM rootcause_master
     ORDER BY rootcause_name"
);

$actionData = mysqli_query(
    $conn,
    "SELECT * FROM action_master
     ORDER BY action_name"
);


// =========================
// HISTORY HARI INI SAJA
// =========================
$query = "
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

WHERE
    t.created_by = '$username'
    AND DATE(t.created_at) = CURDATE()

ORDER BY t.transaction_id DESC
";

$data = mysqli_query($conn, $query);

?>

<!doctype html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>History Line Drop</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>

<body class="bg-light">

    <!-- NAVBAR -->
    <nav class="navbar navbar-dark bg-primary shadow">

        <div class="container-fluid">

            <span class="navbar-brand">
                HISTORY LINE DROP TODAY
            </span>

            <div class="text-white">

                <?= $_SESSION['name']; ?>

                |

                <a href="index.php"
                    class="btn btn-secondary">

                    Back

                </a>

                |

                <a href="../../logout.php"
                    class="btn btn-danger">

                    Logout

                </a>

            </div>

        </div>

    </nav>

    <div class="container-fluid mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">
                    History Hari Ini :
                    <?= date('d-m-Y'); ?>
                </h5>
            </div>

            <div class="card-body table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-primary text-center">

                        <tr>

                            <th>No</th>

                            <th>Time</th>

                            <th>Product ID</th>

                            <th>Model</th>

                            <th>Defect</th>

                            <th>Category</th>

                            <th>Root Cause</th>

                            <th>Action</th>

                            <th>Remark</th>
                            <th>Evidence</th>

                            <th width="120">
                                Action
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php
                        $no = 1;

                        while ($row = mysqli_fetch_assoc($data)) :
                        ?>

                            <tr>

                                <td class="text-center">
                                    <?= $no++; ?>
                                </td>

                                <td class="text-center">

                                    <?= date(
                                        'H:i:s',
                                        strtotime($row['created_at'])
                                    ); ?>

                                </td>

                                <td>
                                    <?= $row['product_id']; ?>
                                </td>

                                <td class="text-center">
                                    <?= $row['model_code']; ?>
                                </td>

                                <td>
                                    <?= $row['defect_name']; ?>
                                </td>

                                <td>
                                    <?= $row['category_name']; ?>
                                </td>

                                <td>
                                    <?= $row['rootcause_name']; ?>
                                </td>

                                <td>
                                    <?= $row['action_name']; ?>
                                </td>

                                <td>
                                    <?= $row['remark']; ?>
                                </td>

                                <td class="text-center">

                                    <?php if (!empty($row['evidence_photo'])) : ?>

                                        <a href="../../uploads/<?= $row['evidence_photo']; ?>"
                                            target="_blank">

                                            <img src="../../uploads/<?= $row['evidence_photo']; ?>"
                                                width="80"
                                                class="img-thumbnail">

                                        </a>

                                    <?php else : ?>

                                        -

                                    <?php endif; ?>

                                </td>

                                <td class="text-center">

                                    <button class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#edit<?= $row['transaction_id']; ?>">

                                        Edit

                                    </button>

                                </td>

                            </tr>


                            <!-- MODAL EDIT -->
                            <div class="modal fade"
                                id="edit<?= $row['transaction_id']; ?>"
                                tabindex="-1">

                                <div class="modal-dialog modal-lg">

                                    <div class="modal-content">

                                        <form method="POST" enctype="multipart/form-data">

                                            <div class="modal-header bg-warning">

                                                <h5 class="modal-title">
                                                    Edit Line Drop
                                                </h5>

                                                <button type="button"
                                                    class="btn-close"
                                                    data-bs-dismiss="modal"></button>

                                            </div>

                                            <div class="modal-body">

                                                <input type="hidden"
                                                    name="transaction_id"
                                                    value="<?= $row['transaction_id']; ?>">

                                                <!-- PRODUCT -->
                                                <div class="mb-3">

                                                    <label>
                                                        Product ID
                                                    </label>

                                                    <input type="text"
                                                        class="form-control"
                                                        value="<?= $row['product_id']; ?>"
                                                        readonly>

                                                </div>

                                                <!-- DEFECT -->
                                                <div class="mb-3">

                                                    <label>
                                                        Defect
                                                    </label>

                                                    <select name="defect_id"
                                                        class="form-select"
                                                        required>

                                                        <?php
                                                        mysqli_data_seek($defectData, 0);

                                                        while ($d = mysqli_fetch_assoc($defectData)) :
                                                        ?>

                                                            <option value="<?= $d['defect_id']; ?>"
                                                                <?= ($row['defect_id'] == $d['defect_id']) ? 'selected' : ''; ?>>

                                                                <?= $d['defect_name']; ?>

                                                            </option>

                                                        <?php endwhile; ?>

                                                    </select>

                                                </div>

                                                <!-- CATEGORY -->
                                                <div class="mb-3">

                                                    <label>
                                                        Category
                                                    </label>

                                                    <select name="category_id"
                                                        class="form-select"
                                                        required>

                                                        <?php
                                                        mysqli_data_seek($categoryData, 0);

                                                        while ($c = mysqli_fetch_assoc($categoryData)) :
                                                        ?>

                                                            <option value="<?= $c['category_id']; ?>"
                                                                <?= ($row['category_id'] == $c['category_id']) ? 'selected' : ''; ?>>

                                                                <?= $c['category_name']; ?>

                                                            </option>

                                                        <?php endwhile; ?>

                                                    </select>

                                                </div>

                                                <!-- ROOTCAUSE -->
                                                <div class="mb-3">

                                                    <label>
                                                        Root Cause
                                                    </label>

                                                    <select name="rootcause_id"
                                                        class="form-select"
                                                        required>

                                                        <?php
                                                        mysqli_data_seek($rootcauseData, 0);

                                                        while ($r = mysqli_fetch_assoc($rootcauseData)) :
                                                        ?>

                                                            <option value="<?= $r['rootcause_id']; ?>"
                                                                <?= ($row['rootcause_id'] == $r['rootcause_id']) ? 'selected' : ''; ?>>

                                                                <?= $r['rootcause_name']; ?>

                                                            </option>

                                                        <?php endwhile; ?>

                                                    </select>

                                                </div>

                                                <!-- ACTION -->
                                                <div class="mb-3">

                                                    <label>
                                                        Action
                                                    </label>

                                                    <select name="action_id"
                                                        class="form-select"
                                                        required>

                                                        <?php
                                                        mysqli_data_seek($actionData, 0);

                                                        while ($a = mysqli_fetch_assoc($actionData)) :
                                                        ?>

                                                            <option value="<?= $a['action_id']; ?>"
                                                                <?= ($row['action_id'] == $a['action_id']) ? 'selected' : ''; ?>>

                                                                <?= $a['action_name']; ?>

                                                            </option>

                                                        <?php endwhile; ?>

                                                    </select>

                                                </div>

                                                <!-- REMARK -->
                                                <div class="mb-3">

                                                    <label>
                                                        Remark
                                                    </label>

                                                    <textarea name="remark"
                                                        class="form-control"
                                                        rows="3"><?= $row['remark']; ?></textarea>

                                                </div>
                                                <!-- EVIDENCE PHOTO -->
                                                <div class="mb-3">

                                                    <label>Evidence Photo</label>

                                                    <?php if (!empty($row['evidence_photo'])) : ?>

                                                        <div class="mb-2">
                                                            <a href="../../uploads/<?= $row['evidence_photo']; ?>"
                                                                target="_blank">

                                                                <img src="../../uploads/<?= $row['evidence_photo']; ?>"
                                                                    class="img-thumbnail"
                                                                    style="max-width:200px;">

                                                            </a>
                                                        </div>

                                                    <?php endif; ?>

                                                    <input type="file"
                                                        name="evidence_photo"
                                                        class="form-control"
                                                        accept="image/*"
                                                        capture="environment">

                                                    <small class="text-muted">
                                                        Kosongkan jika tidak ingin mengganti foto
                                                    </small>

                                                </div>

                                            </div>

                                            <div class="modal-footer">

                                                <button type="button"
                                                    class="btn btn-secondary"
                                                    data-bs-dismiss="modal">

                                                    Close

                                                </button>

                                                <button type="submit"
                                                    name="btn_update"
                                                    class="btn btn-warning">

                                                    Update

                                                </button>

                                            </div>

                                        </form>

                                    </div>

                                </div>

                            </div>

                        <?php endwhile; ?>

                        <?php if (mysqli_num_rows($data) == 0) : ?>

                            <tr>

                                <td colspan="10"
                                    class="text-center text-muted">

                                    Belum ada data line drop hari ini

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>