<?php
session_start();

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
?>

<!doctype html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1">

    <title>Leader Menu</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
        }

        .menu-card {

            transition: .3s;
            border: none;
            border-radius: 15px;

        }

        .menu-card:hover {

            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, .15);

        }

        .menu-icon {

            font-size: 50px;

        }
    </style>

</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">

        <div class="container-fluid">

            <span class="navbar-brand">

                LINE DROP SYSTEM

            </span>

            <div class="text-white">

                <?= $_SESSION['name']; ?>

                |

                <?= $_SESSION['role']; ?>

                |

                <a href="../../logout.php"
                    class="btn btn-danger btn-sm">

                    Logout

                </a>

            </div>

        </div>

    </nav>


    <div class="container py-4">

        <div class="mb-4">

            <h3>

                Welcome,
                <?= $_SESSION['name']; ?>

            </h3>

            <p class="text-muted">

                Leader Management Menu

            </p>

        </div>


        <div class="row g-4">

            <!-- DASHBOARD -->
            <div class="col-md-4">

                <a href="dashboard.php"
                    class="text-decoration-none">

                    <div class="card menu-card shadow-sm">

                        <div class="card-body text-center">

                            <div class="menu-icon text-primary mb-3">

                                <i class="bi bi-bar-chart-line-fill"></i>

                            </div>

                            <h5>Dashboard</h5>

                            <small class="text-muted">

                                Monitoring Line Drop

                            </small>

                        </div>

                    </div>

                </a>

            </div>


            <!-- MODEL -->
            <div class="col-md-4">

                <a href="model/index.php"
                    class="text-decoration-none">

                    <div class="card menu-card shadow-sm">

                        <div class="card-body text-center">

                            <div class="menu-icon text-success mb-3">

                                <i class="bi bi-box-seam"></i>

                            </div>

                            <h5>Master Model</h5>

                            <small class="text-muted">

                                Manage AC Model

                            </small>

                        </div>

                    </div>

                </a>

            </div>


            <!-- DEFECT -->
            <div class="col-md-4">

                <a href="defect/index.php"
                    class="text-decoration-none">

                    <div class="card menu-card shadow-sm">

                        <div class="card-body text-center">

                            <div class="menu-icon text-danger mb-3">

                                <i class="bi bi-exclamation-triangle-fill"></i>

                            </div>

                            <h5>Master Defect</h5>

                            <small class="text-muted">

                                Manage Defect

                            </small>

                        </div>

                    </div>

                </a>

            </div>


            <!-- CATEGORY -->
            <div class="col-md-4">

                <a href="category/index.php"
                    class="text-decoration-none">

                    <div class="card menu-card shadow-sm">

                        <div class="card-body text-center">

                            <div class="menu-icon text-warning mb-3">

                                <i class="bi bi-tags-fill"></i>

                            </div>

                            <h5>Category</h5>

                            <small class="text-muted">

                                Manage Category

                            </small>

                        </div>

                    </div>

                </a>

            </div>


            <!-- ROOTCAUSE -->
            <div class="col-md-4">

                <a href="rootcause/index.php"
                    class="text-decoration-none">

                    <div class="card menu-card shadow-sm">

                        <div class="card-body text-center">

                            <div class="menu-icon text-info mb-3">

                                <i class="bi bi-search"></i>

                            </div>

                            <h5>Root Cause</h5>

                            <small class="text-muted">

                                Manage Root Cause

                            </small>

                        </div>

                    </div>

                </a>

            </div>


            <!-- ACTION -->
            <div class="col-md-4">

                <a href="action/index.php"
                    class="text-decoration-none">

                    <div class="card menu-card shadow-sm">

                        <div class="card-body text-center">

                            <div class="menu-icon text-secondary mb-3">

                                <i class="bi bi-tools"></i>

                            </div>

                            <h5>Action Production</h5>

                            <small class="text-muted">

                                Manage Countermeasure

                            </small>

                        </div>

                    </div>

                </a>

            </div>

        </div>

    </div>

</body>

</html>