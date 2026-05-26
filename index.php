<?php
require 'conn.php';
session_start();

// Redirect jika sudah login
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {
        header('Location: page/admin/index.php');
        exit;
    } elseif ($_SESSION['role'] == 'Repair') {
        header('Location: page/repair/index.php');
        exit;
    } elseif ($_SESSION['role'] == 'Leader') {
        header('Location: index.php');
        exit;
    }
}

// Fungsi Login
function loginUser($username, $password, $conn)
{
    $query = "SELECT * FROM user_master WHERE username = '$username'";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);

    if ($data && $password == $data['password']) {

        $_SESSION['username'] = $data['username'];
        $_SESSION['name']     = $data['name'];
        $_SESSION['area']     = $data['area'];
        $_SESSION['role']     = $data['role'];

        if ($data['role'] == 'ADMIN') {
            header('Location: page/admin/index.php');
        } elseif ($data['role'] == 'OPERATOR') {
            header('Location: page/operator/index.php');
        } elseif ($data['role'] == 'REPAIRMAN') {
            header('Location: page/repairman/index.php');
        } elseif ($data['role'] == 'LEADER') {
            header('Location: page/leader/index.php');
        } else {
            header('Location: index.php');
        }
        exit;
    } else {
        echo "<script>alert('Login gagal! Username atau password salah.')</script>";
    }
}

// Login via QR
if (isset($_GET['uid']) && isset($_GET['pwd'])) {
    $username = $_GET['uid'];
    $password = $_GET['pwd'];
    loginUser($username, $password, $conn);
}

// Login dari form
if (isset($_POST['btn_login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    loginUser($username, $password, $conn);
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TCS Production</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #0d6efd, #0dcaf0);
            min-height: 100vh;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            border-radius: 20px;
        }

        .logo-title {
            font-weight: bold;
            color: #0d6efd;
        }
    </style>
</head>

<body>

    <div class="container d-flex justify-content-center align-items-center vh-100">

        <div class="card shadow-lg login-card p-4">

            <div class="text-center mb-4">
                <h2 class="logo-title">TCS Production</h2>
                <p class="text-muted">Silakan login untuk melanjutkan</p>
            </div>

            <form method="POST" autocomplete="off">

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text"
                        name="username"
                        class="form-control"
                        placeholder="Masukkan username"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password"
                        name="password"
                        class="form-control"
                        placeholder="Masukkan password"
                        required>
                </div>

                <div class="d-grid">
                    <button type="submit"
                        name="btn_login"
                        class="btn btn-primary btn-lg">
                        Login
                    </button>
                </div>

            </form>

            <div class="text-center mt-4">
                <small class="text-muted">
                    © <?= date('Y') ?> TCS Production
                </small>
            </div>

        </div>

    </div>

</body>

</html>