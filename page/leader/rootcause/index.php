<?php
session_start();
require '../../../conn.php';

if (!isset($_SESSION['role'])) {
    header("Location: ../../../login.php");
    exit;
}

if ($_SESSION['role'] != 'LEADER' && $_SESSION['role'] != 'ADMIN') {
    header("Location: ../../../login.php");
    exit;
}

$table = "rootcause_master";
$field = "rootcause_name";


// CREATE
if (isset($_POST['btn_save'])) {

    $name = $_POST['name'];

    mysqli_query(
        $conn,
        "INSERT INTO $table($field)
         VALUES('$name')"
    );

    echo "<script>
            alert('Data berhasil ditambahkan');
            window.location='index.php';
          </script>";
}


// DELETE
if (isset($_GET['delete'])) {

    $id = $_GET['delete'];

    mysqli_query(
        $conn,
        "DELETE FROM $table
         WHERE rootcause_id='$id'"
    );

    echo "<script>
            alert('Data berhasil dihapus');
            window.location='index.php';
          </script>";
}


// UPDATE
if (isset($_POST['btn_update'])) {

    $id   = $_POST['id'];
    $name = $_POST['name'];

    mysqli_query(
        $conn,
        "UPDATE $table
         SET $field='$name'
         WHERE rootcause_id='$id'"
    );

    echo "<script>
            alert('Data berhasil diupdate');
            window.location='index.php';
          </script>";
}

$data = mysqli_query(
    $conn,
    "SELECT * FROM $table
     ORDER BY rootcause_id DESC"
);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>Master Root Cause</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-4">

        <div class="d-flex justify-content-between mb-3">

            <h3>Master Root Cause</h3>

            <a href="../index.php" class="btn btn-secondary">
                Back
            </a>

        </div>

        <!-- FORM -->
        <div class="card shadow-sm mb-4">

            <div class="card-body">

                <form method="POST">

                    <div class="row">

                        <div class="col-md-10">
                            <input type="text"
                                name="name"
                                class="form-control"
                                placeholder="Input root cause..."
                                required>
                        </div>

                        <div class="col-md-2 d-grid">
                            <button type="submit"
                                name="btn_save"
                                class="btn btn-primary">
                                Save
                            </button>
                        </div>

                    </div>

                </form>

            </div>

        </div>

        <!-- TABLE -->
        <div class="card shadow-sm">

            <div class="card-body table-responsive">

                <table class="table table-bordered">

                    <thead class="table-primary">

                        <tr>
                            <th width="80">No</th>
                            <th>Root Cause Name</th>
                            <th width="200">Action</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php
                        $no = 1;

                        while ($row = mysqli_fetch_assoc($data)) :
                        ?>

                            <tr>

                                <td><?= $no++; ?></td>

                                <td><?= $row[$field]; ?></td>

                                <td>

                                    <button class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#edit<?= $row['rootcause_id']; ?>">
                                        Edit
                                    </button>

                                    <a href="?delete=<?= $row['rootcause_id']; ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Hapus data?')">
                                        Delete
                                    </a>

                                </td>

                            </tr>

                            <!-- MODAL EDIT -->
                            <div class="modal fade"
                                id="edit<?= $row['rootcause_id']; ?>">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <form method="POST">

                                            <div class="modal-header">
                                                <h5>Edit Root Cause</h5>
                                            </div>

                                            <div class="modal-body">

                                                <input type="hidden"
                                                    name="id"
                                                    value="<?= $row['rootcause_id']; ?>">

                                                <input type="text"
                                                    name="name"
                                                    class="form-control"
                                                    value="<?= $row[$field]; ?>"
                                                    required>

                                            </div>

                                            <div class="modal-footer">

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

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>