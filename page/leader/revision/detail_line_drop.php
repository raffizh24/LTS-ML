<?php
session_start();
require '../../../conn.php';

if (!isset($_SESSION['role'])) {
    header("Location: ../../../login.php");
    exit;
}

$id = $_GET['id'];


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
        ON d.defect_id=t.defect_id

    LEFT JOIN category_master c
        ON c.category_id=t.category_id

    LEFT JOIN rootcause_master r
        ON r.rootcause_id=t.rootcause_id

    LEFT JOIN action_master a
        ON a.action_id=t.action_id

    WHERE t.transaction_id='$id'
    "
);


$data = mysqli_fetch_assoc($query);

?>


<!doctype html>
<html>

<head>

    <title>
        Detail Line Drop
    </title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>


<body class="bg-light">


    <div class="container mt-4">


        <div class="card shadow">


            <div class="card-header bg-primary text-white">

                <h4>
                    Detail Line Drop
                </h4>


            </div>


            <div class="card-body">


                <div class="row">


                    <div class="col-md-9">


                        <table class="table table-bordered">

                            <a href="../dashboard.php"
                                class="btn btn-secondary mb-3">

                                Back

                            </a>
                            <tr>
                                <th>Product ID</th>
                                <td><?= $data['product_id']; ?></td>
                            </tr>


                            <tr>
                                <th>Model</th>
                                <td><?= $data['model_code']; ?></td>
                            </tr>


                            <tr>
                                <th>Area</th>
                                <td><?= $data['area']; ?></td>
                            </tr>


                            <tr>
                                <th>Defect</th>
                                <td><?= $data['defect_name']; ?></td>
                            </tr>


                            <tr>
                                <th>Category</th>
                                <td><?= $data['category_name']; ?></td>
                            </tr>


                            <tr>
                                <th>Root Cause</th>
                                <td><?= $data['rootcause_name']; ?></td>
                            </tr>


                            <tr>
                                <th>Action</th>
                                <td><?= $data['action_name']; ?></td>
                            </tr>


                            <tr>
                                <th>Remark</th>
                                <td><?= $data['remark']; ?></td>
                            </tr>


                            <tr>
                                <th>Created By</th>
                                <td><?= $data['created_name']; ?></td>
                            </tr>


                            <tr>
                                <th>Date</th>
                                <td><?= $data['created_at']; ?></td>
                            </tr>


                        </table>


                    </div>



                    <div class="col-md-3 mt-5 text-center">


                        <h5>
                            Evidence Photo
                        </h5>


                        <?php if (!empty($data['evidence_photo'])): ?>


                            <img src="../../../uploads/<?= $data['evidence_photo']; ?>"
                                class="img-fluid rounded shadow">


                        <?php else: ?>


                            <h5 class="text-muted mt-5">
                                No Photo
                            </h5>


                        <?php endif; ?>


                    </div>


                </div>





            </div>


        </div>


    </div>


</body>

</html>