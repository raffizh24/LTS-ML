<?php
session_start();
require '../../../conn.php';

if (!isset($_SESSION['role'])) {
    header("Location: ../../../login.php");
    exit;
}

if ($_SESSION['role'] != 'LEADER') {
    header("Location: ../../../login.php");
    exit;
}

$table = "category_master";
$field = "category_name";


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
         WHERE category_id='$id'"
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
         WHERE category_id='$id'"
    );

    echo "<script>
            alert('Data berhasil diupdate');
            window.location='index.php';
          </script>";
}

$data = mysqli_query(
    $conn,
    "SELECT * FROM $table
     ORDER BY category_id DESC"
);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>Master Category</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-4">

        <div class="d-flex justify-content-between mb-3">

            <h3>Master Category</h3>

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
                                placeholder="Input category..."
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
                            <th>Category Name</th>
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
                                        data-bs-target="#edit<?= $row['category_id']; ?>">
                                        Edit
                                    </button>

                                    <a href="?delete=<?= $row['category_id']; ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Hapus data?')">
                                        Delete
                                    </a>

                                </td>

                            </tr>

                            <!-- MODAL EDIT -->
                            <div class="modal fade"
                                id="edit<?= $row['category_id']; ?>">

                                <div class="modal-dialog">

                                    <div class="modal-content">

                                        <form method="POST">

                                            <div class="modal-header">
                                                <h5>Edit Category</h5>
                                            </div>

                                            <div class="modal-body">

                                                <input type="hidden"
                                                    name="id"
                                                    value="<?= $row['category_id']; ?>">

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