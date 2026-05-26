<?php
session_start();

if (!isset($_SESSION['role'])) {
    header("Location: ../../login.php");
    exit;
}

if ($_SESSION['role'] != 'LEADER' && $_SESSION['role'] != 'ADMIN') {
    header("Location: ../../login.php");
    exit;
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Leader Menu</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <!-- NAVBAR -->
    <nav class="navbar navbar-dark bg-primary shadow">

        <div class="container-fluid">

            <span class="navbar-brand">

                DATA MASTER LINE DROP

            </span>

            <div class="text-white">

                <?= $_SESSION['name']; ?>

                |

                <a href="index.php"
                    class="btn btn-light btn-sm">

                    Dashboard

                </a>

                |

                <a href="../../logout.php"
                    class="btn btn-danger btn-sm">

                    Logout

                </a>

            </div>

        </div>

    </nav>

    <div class="container mt-4">

        <div class="row g-4">

            <div class="col-md-4">
                <a href="model.php" class="text-decoration-none">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h4>Model AC</h4>
                            <p>Master Model Production</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="defect.php" class="text-decoration-none">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h4>Defect Line Drop</h4>
                            <p>Master Defect</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="category.php" class="text-decoration-none">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h4>Category</h4>
                            <p>Master Category</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="rootcause.php" class="text-decoration-none">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h4>Root Cause</h4>
                            <p>Master Root Cause</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="action.php" class="text-decoration-none">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h4>Action Production</h4>
                            <p>Master Action</p>
                        </div>
                    </div>
                </a>
            </div>

        </div>

    </div>

</body>

</html>