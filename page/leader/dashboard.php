<?php
session_start();
require '../../conn.php';
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
// FILTER
// ======================
$area = $_SESSION['area'] ?? '';
$filter_type = $_GET['filter_type'] ?? 'month';
$product_id = trim($_GET['product_id'] ?? '');
$month = $_GET['month'] ?? date('m');
$year  = $_GET['year'] ?? date('Y');
$date_from = $_GET['date_from'] ?? date('Y-m-01');
$date_to   = $_GET['date_to'] ?? date('Y-m-d');
$where = [];
// ======================
// FILTER AREA
// ======================
if (!empty($area)) {
    $area_safe = mysqli_real_escape_string(
        $conn,
        $area
    );
    $where[] = "area = '$area_safe'";
}
// ======================
// FILTER BULAN
// ======================
if ($filter_type == 'month') {
    $where[] = "MONTH(created_at)='$month'";
    $where[] = "YEAR(created_at)='$year'";
}
// ======================
// FILTER CUSTOM DATE
// ======================
if ($filter_type == 'custom') {
    $where[] = "
        DATE(created_at)
        BETWEEN '$date_from'
        AND '$date_to'
    ";
}
// ======================
// FILTER PRODUCT ID
// ======================
if ($product_id != '') {
    $product_id_safe = mysqli_real_escape_string(
        $conn,
        $product_id
    );
    $where[] = "
        product_id LIKE '%$product_id_safe%'
    ";
}
// ======================
// BUILD WHERE SQL
// ======================
if (count($where) > 0) {
    $where_sql = implode(
        ' AND ',
        $where
    );
} else {
    $where_sql = "1";
}
// ======================
// WHERE UNTUK ALIAS t
// ======================
$where_t = $where_sql;
$where_t = str_replace(
    "area",
    "t.area",
    $where_t
);
$where_t = str_replace(
    "created_at",
    "t.created_at",
    $where_t
);
$where_t = str_replace(
    "product_id",
    "t.product_id",
    $where_t
);
// ======================
// TOTAL LINE DROP
// ======================
$totalQuery = mysqli_query(
    $conn,
    "
    SELECT COUNT(*) total
    FROM line_drop_transaction
    WHERE $where_sql
    "
);
$totalData = mysqli_fetch_assoc($totalQuery);
// ======================
// TOTAL MODEL
// ======================
$modelQuery = mysqli_query(
    $conn,
    "
    SELECT
        COUNT(DISTINCT model_code) total
    FROM line_drop_transaction
    WHERE $where_sql
    "
);
$modelData = mysqli_fetch_assoc($modelQuery);
// ======================
// DEFECT
// ======================
$defectLabel = [];
$defectTotal = [];
$defectQuery = mysqli_query(
    $conn,
    "
    SELECT
        d.defect_name,
        COUNT(*) total
    FROM line_drop_transaction t
    LEFT JOIN defect_master d
        ON d.defect_id=t.defect_id
    WHERE $where_t
    GROUP BY d.defect_name
    ORDER BY total DESC
    "
);
while ($row = mysqli_fetch_assoc($defectQuery)) {
    $defectLabel[] = $row['defect_name'];
    $defectTotal[] = $row['total'];
}
// ======================
// CATEGORY
// ======================
$categoryLabel = [];
$categoryTotal = [];
$categoryQuery = mysqli_query(
    $conn,
    "
    SELECT
        c.category_name,
        COUNT(*) total
    FROM line_drop_transaction t
    LEFT JOIN category_master c
        ON c.category_id=t.category_id
    WHERE $where_t
    GROUP BY c.category_name
    ORDER BY total DESC
    "
);
while ($row = mysqli_fetch_assoc($categoryQuery)) {
    $categoryLabel[] = $row['category_name'];
    $categoryTotal[] = $row['total'];
}
// ======================
// ROOTCAUSE
// ======================
$rootLabel = [];
$rootTotal = [];
$rootQuery = mysqli_query(
    $conn,
    "
    SELECT
        r.rootcause_name,
        COUNT(*) total
    FROM line_drop_transaction t
    LEFT JOIN rootcause_master r
        ON r.rootcause_id=t.rootcause_id
    WHERE $where_t
    GROUP BY r.rootcause_name
    ORDER BY total DESC
    "
);
while ($row = mysqli_fetch_assoc($rootQuery)) {
    $rootLabel[] = $row['rootcause_name'];
    $rootTotal[] = $row['total'];
}
// ======================
// TREND
// ======================
$trendLabel = [];
$trendData = [];
$trendQuery = mysqli_query(
    $conn,
    "
    SELECT
        DATE(created_at) tgl,
        COUNT(*) total
    FROM line_drop_transaction
    WHERE $where_sql
    GROUP BY DATE(created_at)
    ORDER BY DATE(created_at)
    "
);
while ($row = mysqli_fetch_assoc($trendQuery)) {
    $trendLabel[] = date(
        'd M',
        strtotime($row['tgl'])
    );
    $trendData[] = $row['total'];
}
// ======================
// HISTORY
// ======================
$historyQuery = mysqli_query(
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
        ON d.defect_id=t.defect_id
    LEFT JOIN category_master c
        ON c.category_id=t.category_id
    LEFT JOIN rootcause_master r
        ON r.rootcause_id=t.rootcause_id
    LEFT JOIN action_master a
        ON a.action_id=t.action_id
    WHERE $where_t
    ORDER BY t.transaction_id DESC
    "
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
        <span class="navbar-brand">
        </span>
        <div class="text-white">
            <?= $_SESSION['name']; ?>
            |
            <a href="../../logout.php"
                class="btn btn-danger btn-sm mx-2">
                Logout
            </a>
        </div>
        </div>
    </nav>
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between mb-3">
            <h3>Dashboard Line Drop</h3>
            <a href="index.php" class="btn btn-secondary">
                Back
            </a>
        </div>
        <div class="row mb-4">
            <!-- FILTER MONTH -->
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white py-2">
                        Filter By Month
                    </div>
                    <div class="card-body">
                        <form method="GET">
                            <input type="hidden"
                                name="filter_type"
                                value="month">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="small fw-bold">
                                        Month
                                    </label>
                                    <select
                                        name="month"
                                        class="form-select form-select-sm">
                                        <?php
                                        for ($m = 1; $m <= 12; $m++) :
                                            $val = sprintf('%02d', $m);
                                        ?>
                                            <option
                                                value="<?= $val ?>"
                                                <?= ($month == $val) ? 'selected' : '' ?>>
                                                <?= date(
                                                    'M',
                                                    mktime(0, 0, 0, $m, 1)
                                                ) ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="small fw-bold">
                                        Year
                                    </label>
                                    <select
                                        name="year"
                                        class="form-select form-select-sm">
                                        <?php
                                        for (
                                            $y = date('Y');
                                            $y >= 2024;
                                            $y--
                                        ) :
                                        ?>
                                            <option
                                                value="<?= $y ?>"
                                                <?= ($year == $y) ? 'selected' : '' ?>>
                                                <?= $y ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="small fw-bold">
                                        Product ID
                                    </label>
                                    <input
                                        type="text"
                                        name="product_id"
                                        class="form-control form-control-sm"
                                        value="<?= htmlspecialchars($product_id) ?>">
                                </div>
                                <div class="col-12">
                                    <button
                                        class="btn btn-primary btn-sm w-100">
                                        Filter Month
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- FILTER CUSTOM -->
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-success text-white py-2">
                        Filter Custom Date
                    </div>
                    <div class="card-body">
                        <form method="GET">
                            <input type="hidden"
                                name="filter_type"
                                value="custom">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="small fw-bold">
                                        Date From
                                    </label>
                                    <input
                                        type="date"
                                        name="date_from"
                                        class="form-control form-control-sm"
                                        value="<?= $date_from ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="small fw-bold">
                                        Date To
                                    </label>
                                    <input
                                        type="date"
                                        name="date_to"
                                        class="form-control form-control-sm"
                                        value="<?= $date_to ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="small fw-bold">
                                        Product ID
                                    </label>
                                    <input
                                        type="text"
                                        name="product_id"
                                        class="form-control form-control-sm"
                                        value="<?= htmlspecialchars($product_id) ?>">
                                </div>
                                <div class="col-12">
                                    <button
                                        class="btn btn-success btn-sm w-100">
                                        Filter Custom
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="alert alert-info">
            <strong>Filter :</strong>
            <?= date('d M Y', strtotime($date_from)); ?>
            -
            <?= date('d M Y', strtotime($date_to)); ?>
            <?php if ($product_id != '') : ?>
                |
                Product ID :
                <strong><?= htmlspecialchars($product_id) ?></strong>
            <?php endif; ?>
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
                            <th width="130px">Revision</th>
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
                                <td><?= $h['remark']; ?></td>
                                <td class="text-center">
                                    <?php
                                    $canEdit = (time() - strtotime($h['created_at'])) <= 86400;
                                    if ($canEdit):
                                    ?>
                                        <a
                                            href="revision/edit_line_drop.php?id=<?= $h['transaction_id']; ?>"
                                            class="btn btn-warning btn-sm">
                                            Edit
                                        </a>
                                        <button
                                            class="btn btn-danger btn-sm"
                                            onclick="if(confirm('Are you sure to delete this record?')){ window.location.href='revision/delete_line_drop.php?id=<?= $h['transaction_id']; ?>'; }">
                                            Delete
                                        </button>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">
                                            Expired
                                        </span>
                                    <?php endif; ?>
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
        function copyReport() {
            let date =
                document.getElementById('report_date').value;
            fetch(
                    'copy_report.php?date=' + date
                )
                .then(res => res.text())
                .then(text => {
                    navigator.clipboard.writeText(text);
                    alert(
                        'Data berhasil dicopy.\n\nPaste ke Excel dengan CTRL + V'
                    );
                });
        }
    </script>
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