<?php
session_start();
include('../config/db.php');

if (isset($_SESSION['user_id'])) {
    // Redirect to respective dashboards if already logged in
    if ($_SESSION['role'] == 'admin') {
        header('Location: ../admin/admin_dashboard.php');
    } else {
        header('Location: ../users/dashboard.php');
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query to check if the user exists in the database
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $user = mysqli_fetch_assoc($result);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['branch_name'] = $user['branch_name'];

            // Redirect based on role
            if ($user['role'] == 'admin') {
                header('Location: ../admin/admin_dashboard.php');
            } else {
                header('Location: ../users/dashboard.php');
            }
            exit();
        } else {
            $error = "Invalid username or password";
        }
    } else {
        $error = "Something went wrong";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - STPI Pune</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1D2B64, #F8CDDA);
            height: 100vh;
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }
        .login-container {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 380px;
            animation: fadeIn 1.2s ease-out;
        }
        h2 {
            text-align: center;
            color: #fff;
            margin-bottom: 25px;
            font-size: 24px;
            font-weight: 600;
        }
        .form-label {
            color: #fff;
        }
        .form-control {
            border-radius: 25px;
            margin-bottom: 20px;
            padding: 12px;
            font-size: 16px;
            border: 2px solid #ddd;
        }
        .form-control:focus {
            border-color: #F8CDDA;
            box-shadow: 0 0 5px #F8CDDA;
        }
        .btn-primary {
            width: 100%;
            background-color: #F8CDDA;
            border-radius: 25px;
            padding: 14px;
            font-size: 18px;
            font-weight: 600;
            color: #1D2B64;
        }
        .btn-primary:hover {
            background-color: #F8A9D1;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
        }
        .error-message {
            color: red;
            text-align: center;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .footer {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            color: #fff;
            font-size: 14px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .error-message {
            display: inline-block;
            padding: 8px;
            background: rgba(255, 0, 0, 0.2);
            border-radius: 5px;
            font-weight: 600;
        }

    </style>
</head>
<body>

    <div class="login-container">
        <h2>Welcome to STPI Pune</h2>

        <?php if (isset($error)) { ?>
            <p class="error-message"><?= $error ?></p>
        <?php } ?>

        <form method="POST" action="login.php">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>

    <div class="footer">
        <p>&copy; 2025 STPI Pune</p>
    </div>

</body>
</html>
