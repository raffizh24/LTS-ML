<?php
session_start();
require '../../conn.php';

// ======================
// CEK LOGIN
// ======================
if (!isset($_SESSION['role'])) {
    header("Location: ../../login.php");
    exit;
}

if (
    $_SESSION['role'] != 'LEADER' &&
    $_SESSION['role'] != 'ADMIN'
) {
    header("Location: ../../login.php");
    exit;
}


// ======================
// FILTER
// ======================
$month = isset($_GET['month'])
    ? $_GET['month']
    : date('m');

$year = isset($_GET['year'])
    ? $_GET['year']
    : date('Y');


// ======================
// TOTAL LINE DROP
// ======================
$totalQuery = mysqli_query(
    $conn,
    "SELECT COUNT(*) total
     FROM line_drop_transaction
     WHERE MONTH(created_at) = '$month'
     AND YEAR(created_at) = '$year'"
);

$totalData = mysqli_fetch_assoc($totalQuery);


// ======================
// DEFECT DATA
// ======================
$defectQuery = mysqli_query(
    $conn,
    "SELECT
        d.defect_name,
        COUNT(t.transaction_id) total

    FROM line_drop_transaction t

    LEFT JOIN defect_master d
        ON t.defect_id = d.defect_id

    WHERE
        MONTH(t.created_at) = '$month'
        AND YEAR(t.created_at) = '$year'

    GROUP BY d.defect_name

    ORDER BY total DESC"
);

$defectLabel = [];
$defectTotal = [];

while ($d = mysqli_fetch_assoc($defectQuery)) {

    $defectLabel[] = $d['defect_name'];
    $defectTotal[] = $d['total'];
}


// ======================
// CATEGORY DATA
// ======================
$categoryQuery = mysqli_query(
    $conn,
    "SELECT
        c.category_name,
        COUNT(t.transaction_id) total

    FROM line_drop_transaction t

    LEFT JOIN category_master c
        ON t.category_id = c.category_id

    WHERE
        MONTH(t.created_at) = '$month'
        AND YEAR(t.created_at) = '$year'

    GROUP BY c.category_name

    ORDER BY total DESC"
);

$categoryLabel = [];
$categoryTotal = [];

while ($c = mysqli_fetch_assoc($categoryQuery)) {

    $categoryLabel[] = $c['category_name'];
    $categoryTotal[] = $c['total'];
}


// ======================
// ROOTCAUSE DATA
// ======================
$rootcauseQuery = mysqli_query(
    $conn,
    "SELECT
        r.rootcause_name,
        COUNT(t.transaction_id) total

    FROM line_drop_transaction t

    LEFT JOIN rootcause_master r
        ON t.rootcause_id = r.rootcause_id

    WHERE
        MONTH(t.created_at) = '$month'
        AND YEAR(t.created_at) = '$year'

    GROUP BY r.rootcause_name

    ORDER BY total DESC"
);

$rootLabel = [];
$rootTotal = [];

while ($r = mysqli_fetch_assoc($rootcauseQuery)) {

    $rootLabel[] = $r['rootcause_name'];
    $rootTotal[] = $r['total'];
}


// ======================
// TREND HARIAN
// ======================
$trendQuery = mysqli_query(
    $conn,
    "SELECT
        DATE(created_at) tgl,
        COUNT(*) total

    FROM line_drop_transaction

    WHERE
        MONTH(created_at) = '$month'
        AND YEAR(created_at) = '$year'

    GROUP BY DATE(created_at)

    ORDER BY DATE(created_at)"
);

$trendLabel = [];
$trendData  = [];

while ($t = mysqli_fetch_assoc($trendQuery)) {

    $trendLabel[] = date(
        'd M',
        strtotime($t['tgl'])
    );

    $trendData[] = $t['total'];
}


// ======================
// HISTORY
// ======================
$historyQuery = mysqli_query(
    $conn,
    "SELECT
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
        MONTH(t.created_at) = '$month'
        AND YEAR(t.created_at) = '$year'

    ORDER BY t.transaction_id DESC"
);
?>

<!doctype html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Dashboard Line Drop</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
        }

        .card {
            border: none;
            border-radius: 12px;
        }

        .card-header {
            font-weight: bold;
        }

        canvas {
            width: 100% !important;
            height: 100% !important;
        }
    </style>

</head>

<body>


    <!-- NAVBAR -->
    <nav class="navbar navbar-dark bg-primary shadow">

        <div class="container-fluid">

            <span class="navbar-brand">

                DASHBOARD LINE DROP

            </span>

            <div class="text-white">

                <?= $_SESSION['name']; ?>

                |

                <a href="master.php"
                    class="btn btn-light btn-sm">

                    Data Master

                </a>

                |

                <a href="../../logout.php"
                    class="btn btn-danger btn-sm">

                    Logout

                </a>

            </div>

        </div>

    </nav>


    <div class="container-fluid mt-4">


        <!-- FILTER -->
        <div class="card shadow-sm mb-4">

            <div class="card-body">

                <form method="GET">

                    <div class="row align-items-end">

                        <!-- MONTH -->
                        <div class="col-md-2">

                            <label class="form-label">
                                Month
                            </label>

                            <select name="month"
                                class="form-select">

                                <?php for ($m = 1; $m <= 12; $m++) : ?>

                                    <option value="<?= sprintf('%02d', $m); ?>"
                                        <?= ($month == sprintf('%02d', $m))
                                            ? 'selected'
                                            : ''; ?>>

                                        <?= date(
                                            'F',
                                            mktime(0, 0, 0, $m, 1)
                                        ); ?>

                                    </option>

                                <?php endfor; ?>

                            </select>

                        </div>

                        <!-- YEAR -->
                        <div class="col-md-2">

                            <label class="form-label">
                                Year
                            </label>

                            <select name="year"
                                class="form-select">

                                <?php
                                for ($y = date('Y'); $y >= 2024; $y--) :
                                ?>

                                    <option value="<?= $y; ?>"
                                        <?= ($year == $y)
                                            ? 'selected'
                                            : ''; ?>>

                                        <?= $y; ?>

                                    </option>

                                <?php endfor; ?>

                            </select>

                        </div>

                        <!-- BUTTON -->
                        <div class="col-md-2 d-grid">

                            <button type="submit"
                                class="btn btn-primary">

                                Filter

                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>


        <!-- SUMMARY -->
        <div class="row mb-4">

            <div class="col-md-12">

                <div class="card shadow-sm">

                    <div class="card-body text-center">

                        <h6 class="text-muted">
                            TOTAL LINE DROP
                        </h6>

                        <h1 class="fw-bold text-danger">

                            <?= $totalData['total']; ?>

                        </h1>

                    </div>

                </div>

            </div>

        </div>


        <!-- CHARTS -->
        <div class="row">

            <!-- DEFECT -->
            <div class="col-lg-6 mb-4">

                <div class="card shadow-sm h-100">

                    <div class="card-header bg-danger text-white">
                        Grafik Defect
                    </div>

                    <div class="card-body">

                        <div style="height:300px;">
                            <canvas id="defectChart"></canvas>
                        </div>

                    </div>

                </div>

            </div>


            <!-- CATEGORY -->
            <div class="col-lg-6 mb-4">

                <div class="card shadow-sm h-100">

                    <div class="card-header bg-warning">
                        Grafik Category
                    </div>

                    <div class="card-body">

                        <div style="height:300px;">
                            <canvas id="categoryChart"></canvas>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        <div class="row">

            <!-- ROOTCAUSE -->
            <div class="col-lg-6 mb-4">

                <div class="card shadow-sm h-100">

                    <div class="card-header bg-info text-white">
                        Grafik Root Cause
                    </div>

                    <div class="card-body">

                        <div style="height:300px;">
                            <canvas id="rootChart"></canvas>
                        </div>

                    </div>

                </div>

            </div>


            <!-- TREND -->
            <div class="col-lg-6 mb-4">

                <div class="card shadow-sm h-100">

                    <div class="card-header bg-success text-white">
                        Trend Line Drop
                    </div>

                    <div class="card-body">

                        <div style="height:300px;">
                            <canvas id="trendChart"></canvas>
                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- HISTORY -->
        <div class="card shadow-sm mb-5">

            <div class="card-header bg-dark text-white">

                HISTORY LINE DROP

            </div>

            <div class="card-body table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-primary text-center">

                        <tr>

                            <th>No</th>

                            <th>Date</th>

                            <th>Operator</th>

                            <th>Product ID</th>

                            <th>Model</th>

                            <th>Defect</th>

                            <th>Category</th>

                            <th>Root Cause</th>

                            <th>Action</th>

                            <th>Remark</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php
                        $no = 1;

                        while ($h = mysqli_fetch_assoc($historyQuery)) :
                        ?>

                            <tr>

                                <td class="text-center">
                                    <?= $no++; ?>
                                </td>

                                <td>
                                    <?= $h['created_at']; ?>
                                </td>

                                <td>
                                    <?= $h['created_name']; ?>
                                </td>

                                <td>
                                    <?= $h['product_id']; ?>
                                </td>

                                <td class="text-center">
                                    <?= $h['model_code']; ?>
                                </td>

                                <td>
                                    <?= $h['defect_name']; ?>
                                </td>

                                <td>
                                    <?= $h['category_name']; ?>
                                </td>

                                <td>
                                    <?= $h['rootcause_name']; ?>
                                </td>

                                <td>
                                    <?= $h['action_name']; ?>
                                </td>

                                <td>
                                    <?= $h['remark']; ?>
                                </td>

                            </tr>

                        <?php endwhile; ?>

                        <?php if (mysqli_num_rows($historyQuery) == 0) : ?>

                            <tr>

                                <td colspan="10"
                                    class="text-center text-muted">

                                    Tidak ada data

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <!-- CHART JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // =====================
        // DEFECT BAR
        // =====================
        new Chart(document.getElementById('defectChart'), {

            type: 'bar',

            data: {

                labels: <?= json_encode($defectLabel); ?>,

                datasets: [{

                    label: 'Total Defect',

                    data: <?= json_encode($defectTotal); ?>,

                    borderWidth: 1

                }]
            },

            options: {

                responsive: true,
                maintainAspectRatio: false,

                scales: {

                    y: {
                        beginAtZero: true
                    }

                }

            }

        });


        // =====================
        // CATEGORY PIE
        // =====================
        new Chart(document.getElementById('categoryChart'), {

            type: 'pie',

            data: {

                labels: <?= json_encode($categoryLabel); ?>,

                datasets: [{

                    data: <?= json_encode($categoryTotal); ?>

                }]
            },

            options: {

                responsive: true,
                maintainAspectRatio: false

            }

        });


        // =====================
        // ROOTCAUSE DOUGHNUT
        // =====================
        new Chart(document.getElementById('rootChart'), {

            type: 'doughnut',

            data: {

                labels: <?= json_encode($rootLabel); ?>,

                datasets: [{

                    data: <?= json_encode($rootTotal); ?>

                }]
            },

            options: {

                responsive: true,
                maintainAspectRatio: false

            }

        });


        // =====================
        // TREND LINE
        // =====================
        new Chart(document.getElementById('trendChart'), {

            type: 'line',

            data: {

                labels: <?= json_encode($trendLabel); ?>,

                datasets: [{

                    label: 'Line Drop',

                    data: <?= json_encode($trendData); ?>,

                    fill: false,

                    tension: 0.3

                }]
            },

            options: {

                responsive: true,
                maintainAspectRatio: false,

                scales: {

                    y: {
                        beginAtZero: true
                    }

                }

            }

        });
    </script>

</body>

</html>