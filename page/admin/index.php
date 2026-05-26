<?php
session_start();
require '../../conn.php';

// =====================
// CEK SESSION
// =====================
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'ADMIN') {
    header("Location: ../../index.php");
    exit;
}

// =====================
// CREATE USER
// =====================
if (isset($_POST['btn_save'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $name     = $_POST['name'];
    $area     = $_POST['area'];
    $process  = $_POST['process'];
    $role     = $_POST['role'];

    $query = "INSERT INTO user_master 
                (username, password, name, area, process, role)
              VALUES
                ('$username','$password','$name','$area','$process','$role')";

    mysqli_query($conn, $query);

    echo "<script>
            alert('User berhasil ditambahkan');
            window.location='index.php';
          </script>";
}

// =====================
// DELETE USER
// =====================
if (isset($_GET['delete'])) {

    $id = $_GET['delete'];

    mysqli_query($conn, "DELETE FROM user_master WHERE user_id='$id'");

    echo "<script>
            alert('User berhasil dihapus');
            window.location='index.php';
          </script>";
}

// =====================
// UPDATE USER
// =====================
if (isset($_POST['btn_update'])) {

    $id       = $_POST['user_id'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $name     = $_POST['name'];
    $area     = $_POST['area'];
    $process  = $_POST['process'];
    $role     = $_POST['role'];

    $query = "UPDATE user_master SET
                username = '$username',
                password = '$password',
                name     = '$name',
                area     = '$area',
                process  = '$process',
                role     = '$role'
              WHERE user_id='$id'";

    mysqli_query($conn, $query);

    echo "<script>
            alert('User berhasil diupdate');
            window.location='index.php';
          </script>";
}

// =====================
// GET DATA USER
// =====================
$dataUser = mysqli_query($conn, "SELECT * FROM user_master ORDER BY user_id DESC");
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - User Master</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

    <!-- NAVBAR -->
    <nav class="navbar navbar-dark bg-primary shadow">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">
                ADMIN PANEL - USER MASTER
            </span>

            <div class="text-white">
                <?= $_SESSION['name']; ?>
                |
                <a href="../../logout.php" class="btn btn-sm btn-light">
                    Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">

        <!-- CARD FORM -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                Tambah User
            </div>

            <div class="card-body">

                <form method="POST" autocomplete="off">

                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label>Username</label>
                            <input type="text"
                                name="username"
                                class="form-control"
                                required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Password</label>
                            <input type="password"
                                name="password"
                                class="form-control"
                                required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Nama</label>
                            <input type="text"
                                name="name"
                                class="form-control"
                                required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Area</label>

                            <select name="area" class="form-select" required>
                                <option value="">-- Pilih Area --</option>
                                <option value="IDU">IDU</option>
                                <option value="ODU">ODU</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Process</label>
                            <input type="text"
                                name="process"
                                class="form-control"
                                required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label>Role</label>

                            <select name="role" class="form-select" required>
                                <option value="">-- Pilih Role --</option>
                                <option value="OPERATOR">OPERATOR</option>
                                <option value="REPAIRMAN">REPAIRMAN</option>
                                <option value="LEADER">LEADER</option>
                                <option value="ADMIN">ADMIN</option>
                            </select>
                        </div>

                    </div>

                    <button type="submit"
                        name="btn_save"
                        class="btn btn-primary">
                        Simpan User
                    </button>

                </form>

            </div>
        </div>

        <!-- TABEL USER -->
        <div class="card shadow-sm">

            <div class="card-header bg-dark text-white">
                Data User
            </div>

            <div class="card-body table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-primary text-center">
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Name</th>
                            <th>Area</th>
                            <th>Process</th>
                            <th>Role</th>
                            <th>Created</th>
                            <th width="180">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($dataUser)) :
                        ?>

                            <tr>

                                <td class="text-center"><?= $no++; ?></td>

                                <td><?= $row['username']; ?></td>

                                <td><?= $row['name']; ?></td>

                                <td class="text-center">
                                    <?= $row['area']; ?>
                                </td>

                                <td><?= $row['process']; ?></td>

                                <td class="text-center">
                                    <?= $row['role']; ?>
                                </td>

                                <td>
                                    <?= $row['created_at']; ?>
                                </td>

                                <td class="text-center">

                                    <!-- BUTTON EDIT -->
                                    <button
                                        class="btn btn-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#edit<?= $row['user_id']; ?>">
                                        Edit
                                    </button>

                                    <!-- BUTTON DELETE -->
                                    <a href="?delete=<?= $row['user_id']; ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Yakin hapus user?')">
                                        Delete
                                    </a>

                                </td>

                            </tr>

                            <!-- MODAL EDIT -->
                            <div class="modal fade"
                                id="edit<?= $row['user_id']; ?>"
                                tabindex="-1">

                                <div class="modal-dialog modal-lg">

                                    <div class="modal-content">

                                        <form method="POST">

                                            <div class="modal-header bg-warning">
                                                <h5 class="modal-title">
                                                    Edit User
                                                </h5>

                                                <button type="button"
                                                    class="btn-close"
                                                    data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">

                                                <input type="hidden"
                                                    name="user_id"
                                                    value="<?= $row['user_id']; ?>">

                                                <div class="row">

                                                    <div class="col-md-6 mb-3">
                                                        <label>Username</label>

                                                        <input type="text"
                                                            name="username"
                                                            class="form-control"
                                                            value="<?= $row['username']; ?>"
                                                            required>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label>Password</label>

                                                        <input type="text"
                                                            name="password"
                                                            class="form-control"
                                                            value="<?= $row['password']; ?>"
                                                            required>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label>Name</label>

                                                        <input type="text"
                                                            name="name"
                                                            class="form-control"
                                                            value="<?= $row['name']; ?>"
                                                            required>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label>Process</label>

                                                        <input type="text"
                                                            name="process"
                                                            class="form-control"
                                                            value="<?= $row['process']; ?>"
                                                            required>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label>Area</label>

                                                        <select name="area"
                                                            class="form-select"
                                                            required>

                                                            <option value="IDU"
                                                                <?= ($row['area'] == 'IDU') ? 'selected' : ''; ?>>
                                                                IDU
                                                            </option>

                                                            <option value="ODU"
                                                                <?= ($row['area'] == 'ODU') ? 'selected' : ''; ?>>
                                                                ODU
                                                            </option>

                                                        </select>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label>Role</label>

                                                        <select name="role"
                                                            class="form-select"
                                                            required>

                                                            <option value="OPERATOR"
                                                                <?= ($row['role'] == 'OPERATOR') ? 'selected' : ''; ?>>
                                                                OPERATOR
                                                            </option>

                                                            <option value="LEADER"
                                                                <?= ($row['role'] == 'LEADER') ? 'selected' : ''; ?>>
                                                                LEADER
                                                            </option>

                                                            <option value="ADMIN"
                                                                <?= ($row['role'] == 'ADMIN') ? 'selected' : ''; ?>>
                                                                ADMIN
                                                            </option>

                                                        </select>
                                                    </div>

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

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>