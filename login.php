<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Iniciar Sesión</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: rgb(10, 10, 10);
            color: rgb(240, 240, 240);
            display: flex;
            align-items: center;
            flex-direction: column;
            width: 100vw;
            font-family: Verdana, Tahoma, sans-serif;
            height: 100vh; /* Full height */
        }

        .container {
            width: 90%;
            max-width: 400px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            background-color: rgb(37, 37, 37);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s; /* Animation */
        }

        .container:hover {
            transform: translateY(-5px); /* Slight lift on hover */
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px; /* Larger heading */
        }

        .form-control {
            border-radius: 5px;
            background-color: rgb(52, 52, 52);
            color: rgb(240, 240, 240);
            border: 1px solid rgb(80, 80, 80);
            transition: border-color 0.2s;
        }

        .form-control::placeholder {
            color: rgb(200, 200, 200);
        }

        .form-control:focus {
            border-color: rgb(100, 100, 100);
            background-color: rgb(40, 40, 40);
            color: rgb(240, 240, 240);
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
            outline: none;
        }

        .btn {
            background-color: rgb(240, 240, 240);
            color: rgb(0, 0, 0);
            border: none;
            border-radius: 5px;
            padding: 10px;
            font-size: 16px; /* Slightly larger text */
            transition: background-color 0.2s, transform 0.2s;
        }

        .btn:hover {
            background-color: rgb(220, 220, 220);
            transform: translateY(-2px); /* Lift effect on hover */
        }

        .text-info {
            color: rgb(240, 240, 240);
        }

        .alert {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Iniciar Sesión</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <input type="text" name="username" class="form-control" placeholder="Usuario" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
            </div>
            <button type="submit" class="btn btn-primary btn-lg btn-block">Iniciar sesión</button>
        </form>
        <p class="text-center mt-3">No tienes cuenta? <a href="register.php" class="text-info">Registrarse</a></p>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
